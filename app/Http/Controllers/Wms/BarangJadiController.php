<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
// use App\Models\Menu;
use App\Models\User;
// use App\Models\BahanBarang;
// use App\Models\GrupJadi;
// use App\Models\SubgrupJadi;
// use App\Models\MerkJadi;
// use App\Models\WarnaJadi;
// use App\Models\Dimensi;
// use App\Models\Periode;
// use App\Models\AksesMenu;
use App\Models\VWDBBarang;
use App\Models\DBBarangLokasi;
use App\Models\LokasiSQL;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;

class BarangJadiController extends Controller {

	public function index() {
		// $getID = Menu::where('access', '/barangjadi')->first();
		// $check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		// if ($check->tampil == 1) {
			// User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
			// $checkUser = User::where('id', \Auth::id())->first();
  		// if ($checkUser->level == 3 && $checkUser->username == 'SA') {
      //   $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      // }else{
      //   $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      // }
			// $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
			$kodemenu = '210102';

	    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();

			if(!$akses || !$akses->HASACCESS) {
	       return redirect('/home');
	    }


			// Diubah 2026-10-20 (lemot): 10.000 baris tidak lagi dirender di HTML - tabel diisi per halaman
			// lewat dataTabel(); LokasiSQL cukup dibaca sekali (6 variabel di bawah isinya sama).
			// $barangjadi = VWDBBarang::where('KODEGRP', 'BJ')->paginate(10000);
			// $design = LokasiSQL::all();
			// $keterangan = LokasiSQL::all();
			// $merk = LokasiSQL::all();
			// $warna = LokasiSQL::all();
			// $dimensi = LokasiSQL::all();
			// $lokasi = LokasiSQL::all();
			$barangjadi = collect();
			$lokasi = LokasiSQL::orderBy('BarcodeLoc')->get(['BarcodeLoc', 'NamaLokasi']);
			$design = $keterangan = $merk = $warna = $dimensi = $lokasi;
			$periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
	 		return view("wms.barangjadi")
				->with('barangjadi', $barangjadi)
				->with('design', $design)
				->with('keterangan', $keterangan)
				->with('merk', $merk)
				->with('warna', $warna)
				->with('dimensi', $dimensi)
				->with('periode', $periode)
				->with('lokasi', $lokasi)
				->with('akses', $akses)
				// ->with('akses', $check)
				;
		// } else {
		// 	return redirect('/home');
		// }
	}

	public function loadAll(Request $req) {
		$page = $req->input('page');
		return VWDBBarang::where('KODEGRP', 'BJ')->paginate(100000, ['*'], 'page', $page);
	}

	// Ditambahkan 2026-10-20: data tabel Barang per halaman untuk DataTables (serverSide) - cari,
	// urut, dan Tampilkan dikerjakan di SQL. Paging pakai ROW_NUMBER karena SQL Server 2008 R2
	// belum mengenal OFFSET/FETCH yang dipakai paginate() Laravel 13.
	public function dataTabel(Request $req) {
		$kolomUrut = [1 => 'KODEBRG', 2 => 'NAMABRG', 3 => 'pAgen', 4 => 'SAT1', 5 => 'ISI1', 6 => 'SAT2', 7 => 'ISI2',
			8 => 'SAT3', 9 => 'ISI3', 10 => 'ISAKTIF', 11 => 'QntMin', 12 => 'QntMax'];
		$kolomAmbil = 'KODEBRG, NAMABRG, pAgen, SAT1, ISI1, SAT2, ISI2, SAT3, ISI3, ISAKTIF, QntMin, QntMax, Berat';

		$urut = $req->input('order.0', []);
		$kolom = $kolomUrut[(int) ($urut['column'] ?? 1)] ?? 'KODEBRG';
		$arah = strtolower($urut['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

		$where = "KODEGRP = 'BJ'";
		$bind = [];
		$cari = trim((string) $req->input('search.value', ''));
		if ($cari !== '') {
			$pola = '%' . str_replace(['[', '%', '_'], ['[[]', '[%]', '[_]'], $cari) . '%';
			$where .= ' and (KODEBRG like cast(? as varchar(200)) or NAMABRG like cast(? as varchar(200)))';
			$bind = [$pola, $pola];
		}

		$db = DB::connection('SML');
		$total = (int) $db->selectOne("select count(*) as n from vwDBBarang where KODEGRP = 'BJ'")->n;
		$tersaring = $cari === '' ? $total : (int) $db->selectOne("select count(*) as n from vwDBBarang where $where", $bind)->n;

		$mulai = max(0, (int) $req->input('start', 0));
		$panjang = (int) $req->input('length', 10);
		$sql = "select $kolomAmbil from (select row_number() over (order by $kolom $arah, KODEBRG asc) as rn_urut, $kolomAmbil"
			. " from vwDBBarang where $where) x";
		if ($panjang > 0) {
			$sql .= ' where rn_urut between ? and ?';
			$bind[] = $mulai + 1;
			$bind[] = $mulai + $panjang;
		}
		$sql .= ' order by rn_urut';

		return [
			'draw' => (int) $req->input('draw', 0),
			'recordsTotal' => $total,
			'recordsFiltered' => $tersaring,
			'data' => $db->select($sql, $bind),
		];
	}


	public function loadkodebrg(Request $req) {
		
		// return VWDBBarang::where('KODEBRG', $req->input('kode'));
	//  return VWDBBarang::where('KODEBRG', '10102011780001');	
	return VWDBBarang::where('KODEBRG', $req->input('kode'))->get();

	}

	public function onChangeHeader (Request $req) {
    // Ditambahkan 2026-10-20 (keamanan): nama kolom masuk langsung ke SQL, jadi dibatasi ke kolom
    // yang memang diubah dari halaman ini (Berat/Volume).
    if (! in_array(strtolower((string) $req->field), ['berat'], true)) {
      return response('Kolom tidak diizinkan', 422);
    }
    $query = 'update dbbarang set ' . $req->field . ' = :value where kodebrg = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }

	public function addBarangLokasi(Request $req) {
		if ($req->input('choice')=='I'){
			$check = DBBarangLokasi::where('KodeBarang', $req->input('kode'))->where('Urut', $req->input('urut'))->count();
			if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('urut'),
					$req->input('kode'),
					$req->input('barcode'),
					0,
					$req->input('qntMax')
				];
				DB::connection('SML')->statement('exec Sp_BarangLokasi ?,?,?,?,?,?',$values);
				return 1;
			} else {
				return 0;
			}
		} elseif ($req->input('choice')=='U'){
			$check = DBBarangLokasi::where('KodeBarang', $req->input('kode'))->where('Urut', $req->input('urutold'))->count();
			if ($check != 0) {
				$values = [
					$req->input('choice'),
					$req->input('urut'),
					$req->input('kode'),
					$req->input('barcode'),
					$req->input('urutold'),
					$req->input('qntMax'),
				];
				DB::connection('SML')->statement('exec Sp_BarangLokasi ?,?,?,?,?,?',$values);
				return 1;
			} else {
				return 0;
			}
		} else {
			// $check = AlokasiBrg::where('KodeLokasi', $req->input('kode'))->where('KodeRak', $req->input('koderak'))->where('KodeGdg', $req->input('kodegdg'))->count();
			// if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('urut'),
					$req->input('kode'),
					$req->input('barcode'),
					0,
					$req->input('qntMax')
				];
				DB::connection('SML')->statement('exec Sp_BarangLokasi ?,?,?,?,?,?',$values);
				return 1;
			// } else {
				// return 0;
			// }
		}
	}

	public function showBarangLokasi(Request $req) {
		return DBBarangLokasi::where('KodeBarang', $req->input('kode'))->get();
	}

	// public function add(Request $req) {
	// 	$check = BahanBarang::where('kode', $req->input('kode'))->where('deleted', 0)->count();
	// 	if ($check == 0) {
	// 		BahanBarang::create([
	// 			'agen' => $req->input('agen'),
	// 			'kode_grup' => $req->input('vargs1'),
	// 			'kode_subgrup' => $req->input('vargs2'),
	// 			'kode_merk' => $req->input('vargs3'),
	// 			'kode_warna' => $req->input('vargs4'),
	// 			'kode_dimensi' => $req->input('vargs5'),
	// 			'kode' => $req->input('kode'),
	// 			'nama' => $req->input('nama'),
	// 			'satuan1' => $req->input('satuan1'),
	// 			'satuan2' => $req->input('satuan2'),
	// 			'satuan3' => $req->input('satuan3'),
	// 			'isi1' => $req->input('isi1'),
	// 			'isi2' => $req->input('isi2'),
	// 			'isi3' => $req->input('isi3'),
	// 			'harga1' => $req->input('harga1'),
	// 			'harga2' => $req->input('harga2'),
	// 			'harga3' => $req->input('harga3'),
	// 			'aktif' => $req->input('aktif'),
	// 			'minimum' => $req->input('minimum'),
	// 			'maksimum' => $req->input('maksimum'),
	// 			'konversi' => $req->input('konversi'),
	// 			'grup' => "BJ"
	// 		]);
	// 		return 1;
	// 	} else {
	// 		return 0;
	// 	}
	// }

	// public function edit(Request $req) {
	// 	$check = BahanBarang::where('kode', $req->input('kode'))->where('deleted', 0)->where('id', '!=', $req->input('id'))->count();
	// 	if ($check == 0) {
	// 		BahanBarang::where('id', $req->input('id'))->update([
	// 			'agen' => $req->input('agen'),
	// 			'kode_grup' => $req->input('vargs1'),
	// 			'kode_subgrup' => $req->input('vargs2'),
	// 			'kode_merk' => $req->input('vargs3'),
	// 			'kode_warna' => $req->input('vargs4'),
	// 			'kode_dimensi' => $req->input('vargs5'),
	// 			'kode' => $req->input('kode'),
	// 			'nama' => $req->input('nama'),
	// 			'satuan1' => $req->input('satuan1'),
	// 			'satuan2' => $req->input('satuan2'),
	// 			'satuan3' => $req->input('satuan3'),
	// 			'isi1' => $req->input('isi1'),
	// 			'isi2' => $req->input('isi2'),
	// 			'isi3' => $req->input('isi3'),
	// 			'harga1' => $req->input('harga1'),
	// 			'harga2' => $req->input('harga2'),
	// 			'harga3' => $req->input('harga3'),
	// 			'aktif' => $req->input('aktif'),
	// 			'minimum' => $req->input('minimum'),
	// 			'maksimum' => $req->input('maksimum'),
	// 			'konversi' => $req->input('konversi')
	// 		]);
	// 		return 1;
	// 	} else {
	// 		return 0;
	// 	}
	// }

	// public function erase(Request $req) {
	// 	BahanBarang::where('id', $req->input('id'))->update(['deleted' => 1]);
	// }

	public function show(Request $req) {
		return BahanBarang::where('id', $req->input('id'))->first();
	}

}

?>
