<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Model\Menu;
use App\Model\AksesMenu;
use App\Model\User;
use App\Model\Periode;
use App\Model\PermintaanPemakaian;
use App\Model\DetailPermintaanPemakaian;
use App\Model\PemakaianBarang;
use App\Model\DetailPemakaianBarang;
use App\Model\Nomor;
use App\Model\BahanBarang;
use App\Model\Departemen;
use App\Model\Batch;
use App\Model\StockBarang;
use App\Model\PostJurnalOto;
use App\Model\Trans;
use App\Traits\ActivityTrait;
use App\Model\GudangSQL;
use App\Model\RakSQL;
use App\Model\LokasiSQL;
use App\Model\Koreksi;
use App\Model\KoreksiDet;
use App\Model\VWStkBrg;
use App\Model\VWDBBarang;
use App\Model\VWOutPenerimaanBrg;
use App\Model\vwShowOpnameBrg;
use App\Model\vwShowDetOpnameBrg;

class OpnameBrgController extends Controller {
  use ActivityTrait;

	public function index() {
		$getID = Menu::where('access', '/opnamebrg')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 1) {
			User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
      $checkUser = User::where('id', \Auth::id())->first();
  		if ($checkUser->level == 3 && $checkUser->username == 'SA') {
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      }else{
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      }
      $periode = Periode::where('id_user', \Auth::id())->first();
      $gudang = GudangSQL::All();
			$penerimaan = VWOutPenerimaanBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
          orderBy('NoBukti1','kodebrg', 'DESC')->selectRaw('VWOUTPENERIMAANBRG.*')->get();
      $opnamebrg = vwShowOpnameBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
          orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowOpnameBrg.*')->get();


      return view("opnamebrg")->with('menu', $menu)->with('periode', $periode)->with('akses', $check)->with('penerimaan', $penerimaan)->
             with('gudang', $gudang)->with('opnamebrg', $opnamebrg);
		} else {
			return redirect('/home');
		}
	}

	public function loadAll() {
		$periode = Periode::where('id_user', \Auth::id())->first();
    return vwShowOpnameBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        orderBy('NOBUKTI', 'DESC')->selectRaw('vwShowOpnameBrg.*')->get();
	}

	public function loadAllOutPenerimaan() {
    $periode = Periode::where('id_user', \Auth::id())->first();
		return VWOutPenerimaanBrg::whereMonth('TANGGAL', '=', $periode->bulan)->whereYear('TANGGAL', '=', $periode->tahun)->
        orderBy('NoBukti1','kodebrg', 'DESC')->selectRaw('VWOUTPENERIMAANBRG.*')->get();
  }

	public function generateNomorBukti() {
		$periode = Periode::where('id_user', \Auth::id())->first();
		$nomor = Nomor::where('id', 1)->first();
		$str = "";
		$nourut = "";

		if ($nomor->reset == 1) {
			$getCount = Koreksi::whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		} else {
			$getCount = Koreksi::whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		}

		$nourut = str_pad($nourut, 4, "0", STR_PAD_LEFT);

		if ($nomor->format1 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format1 == 2) { $str .= $nomor->opname_barang; }
		else if ($nomor->format1 == 3) { $str .= $nourut; }
		else if ($nomor->format1 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format1 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format1 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format1 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		$str .= $nomor->pemisah;
		if ($nomor->format2 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format2 == 2) { $str .= $nomor->opname_barang; }
		else if ($nomor->format2 == 3) { $str .= $nourut; }
		else if ($nomor->format2 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format2 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format2 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format2 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		$str .= $nomor->pemisah;
		if ($nomor->format3 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format3 == 2) { $str .= $nomor->opname_barang; }
		else if ($nomor->format3 == 3) { $str .= $nourut; }
		else if ($nomor->format3 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format3 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format3 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format3 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		$str .= $nomor->pemisah;
		if ($nomor->format4 == 1) { $str .= $nomor->inisial_perusahaan; }
		else if ($nomor->format4 == 2) { $str .= $nomor->opname_barang; }
		else if ($nomor->format4 == 3) { $str .= $nourut; }
		else if ($nomor->format4 == 4) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).substr($periode->tahun, -2); }
		else if ($nomor->format4 == 5) { $str .= str_pad($periode->bulan, 2, "0", STR_PAD_LEFT).$periode->tahun; }
		else if ($nomor->format4 == 6) { $str .= substr($periode->tahun, -2).str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }
		else if ($nomor->format4 == 7) { $str .= $periode->tahun.str_pad($periode->bulan, 2, "0", STR_PAD_LEFT); }

		return $str;
	}

	public function generateNomorUrut() {
		$periode = Periode::where('id_user', \Auth::id())->first();
		$nomor = Nomor::where('id', 1)->first();
		$nourut = "";

		if ($nomor->reset == 1) {
			$getCount = Koreksi::whereMonth('tanggal', '=', $periode->bulan)->whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		} else {
			$getCount = Koreksi::whereYear('tanggal', '=', $periode->tahun)->count();
			$nourut = (string)($getCount + 1);
		}

		$nourut = str_pad($nourut, 4, "0", STR_PAD_LEFT);

		return $nourut;
	}

	public function add(Request $req) {
    $getUser = User::where('id', \Auth::id())->first();
    if ($req->input('choice')=='I'){
  		$count = Koreksi::where('NOBUKTI', $req->input('no_bukti'))->count();
  		if ($count == 0) {
  			$detail = $req->input('opnamebrg');
  			for ($i = 0; $i < count($detail); $i++) {
          $values = [
            $req->input('choice'),
            $req->input('no_bukti'),
            $req->input('no_urut'),
            $req->input('tanggal'),
            $req->input('gudang'),
            $req->input('rak'),
            $req->input('lokasi'),
            $req->input('keterangan'),
            $i,//urutdet
            $detail[$i][0],
            $detail[$i][1],
            $detail[$i][5],//qnt
            $detail[$i][8],//nosat
            $detail[$i][9],//satuan
            $detail[$i][10],//isi
            $detail[$i][4],//qntkomputer
            $detail[$i][7],//selisih
            $getUser->name,
            now(),
            $req->input('barcode')
          ];
          DB::connection("SPLSIG")->statement('exec Sp_OpnameBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
  			}

  			// $batch = $req->input("batch");
  			// if (!is_array($batch)) {
  			//     $batch = array();
  			// }
  			// for ($i = 0; $i < count($batch); $i++) {
  			// 	if ($batch[$i][4] > 0) {
  			// 		Batch::create([
  			// 			'no_bukti' => $req->input('no_bukti'),
  			// 			'no_batch' => $batch[$i][0],
  			// 			'urut' => $batch[$i][5],
  			// 			'gudang' => $req->input('gudang'),
  			// 			'kode_barang' => $batch[$i][1],
  			// 			'tanggal' => $batch[$i][2],
  			// 			'qty' => $batch[$i][4] * $batch[$i][6] * -1
  			// 		]);
  			// 	}
  			// }
  			$this->logActivity('ADD', \Auth::id(), $req->input('no_bukti'));
  			return "1;;1;;".$req->input('no_bukti');
  		} else {
  			// $detail = $req->input('opnamebrg');
  			// for ($i = 0; $i < count($detail); $i++) {
  			// 	$getPenerimaan = VWOutPenerimaanBrg::where('NoBukti1', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('Satuan', $detail[$i][8])->first();
  			// 	if ($getPenerimaan->QNTSISA < $detail[$i][5]) {
  			// 		return $detail[$i][1];
  			// 	}
  			// }
  			return "0";
  		}
    } elseif ($req->input('choice')=='U') {
      $count = Koreksi::where('NOBUKTI', $req->input('no_bukti'))->count();
  		if ($count != 0) {
  			$detail = $req->input('opnamebrg');
  			for ($i = 0; $i < count($detail); $i++) {
          $values = [
            $req->input('choice'),
            $detail[$i][12],
            $req->input('no_urut'),
            $req->input('tanggal'),
            $req->input('gudang'),
            $req->input('rak'),
            $req->input('lokasi'),
            $req->input('keterangan'),
            $detail[$i][11],//urutdet
            $detail[$i][0],
            $detail[$i][1],
            $detail[$i][5],//qnt
            $detail[$i][8],//nosat
            $detail[$i][9],//satuan
            $detail[$i][10],//isi
            $detail[$i][4],//qntkomputer
            $detail[$i][7],//selisih
            $getUser->name,
            now(),
            $req->input('barcode')
          ];
          DB::connection("SPLSIG")->statement('exec Sp_OpnameBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
  			}

  			// $batch = $req->input("batch");
  			// if (!is_array($batch)) {
  			//     $batch = array();
  			// }
  			// for ($i = 0; $i < count($batch); $i++) {
  			// 	if ($batch[$i][4] > 0) {
  			// 		Batch::create([
  			// 			'no_bukti' => $req->input('no_bukti'),
  			// 			'no_batch' => $batch[$i][0],
  			// 			'urut' => $batch[$i][5],
  			// 			'gudang' => $req->input('gudang'),
  			// 			'kode_barang' => $batch[$i][1],
  			// 			'tanggal' => $batch[$i][2],
  			// 			'qty' => $batch[$i][4] * $batch[$i][6] * -1
  			// 		]);
  			// 	}
  			// }
  			$this->logActivity('UPD', \Auth::id(), $req->input('no_bukti'));
  			return 1;
  		} else {
  			// $detail = $req->input('opnamebrg');
  			// for ($i = 0; $i < count($detail); $i++) {
  			// 	$getPenerimaan = VWOutPenerimaanBrg::where('NoBukti1', $detail[$i][0])->where('Kodebrg', $detail[$i][1])->where('Satuan', $detail[$i][8])->first();
  			// 	if ($getPenerimaan->QNTSISA < $detail[$i][5]) {
  			// 		return $detail[$i][1];
  			// 	}
  			// }
  			return 0;
  		}
    } else {
      $count = KoreksiDet::where('NOBUKTI', $req->input('no_bukti'))->count();
  		if ($count != 0) {
        $values = [
          $req->input('choice'),
          $req->input('no_bukti'),
          "",
          now(),
          "",
          "",
          "",
          "",
          $req->input('urut'),//urutdet
          "",
          "",
          0,
          1,
          "",
          0,
          "",
          0,
          $getUser->name,
          now(),
          ""
        ];
        DB::connection("SPLSIG")->statement('exec Sp_OpnameBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

        $this->logActivity('DEL', \Auth::id(), $req->input('no_bukti'));
  			return 1;
  		} else {
  			return 0;
  		}
    }
	}

	public function changeAuth(Request $req) {
		$getUser = User::where('id', \Auth::id())->first();
		Koreksi::where('NOBUKTI', $req->input('nobukti'))->update([
			'IsOtorisasi1' => 1,
			'OtoUser1' => $getUser->name,
			'TglOto1' => now(),
			'IsBatal' => 0,
			'UserBatal' => '',
			'TglBatal' => null
		]);

    $this->logActivity('OTO', \Auth::id(), $req->input('nobukti'));
	}

	public function changeBatal(Request $req) {
		$getUser = User::where('id', \Auth::id())->first();
		Koreksi::where('NOBUKTI', $req->input('nobukti'))->update([
      'IsOtorisasi1' => 0,
			'OtoUser1' => '',
			'TglOto1' => null,
			'IsBatal' => 1,
			'UserBatal' => $getUser->name,
			'TglBatal' => now()
		]);
    $this->logActivity('BTL', \Auth::id(), $req->input('nobukti'));
	}

	public function show(Request $req) {
		return vwShowOpnameBrg::where('NOBUKTI', $req->input('nobukti'))->orderBy('NOBUKTI', 'DESC')->first();
	}

  public function showDetOpnameBrg(Request $req) {
		return vwShowDetOpnameBrg::where('NOBUKTI', $req->input('id'))->orderBy('URUT','NOBUKTI', 'ASC')->get();
	}

  public function showDetPenerimaanOpnameBrg(Request $req) {
		return vwShowOpnameBrg::where('NOBUKTI', $req->input('id'))->orderBy('URUT','NOBUKTI', 'ASC')->get();
	}

  public function showPilihRak(Request $req) {
		return RakSQL::where('KodeGdg', $req->input('kodegdg'))->orderBy('KodeRak', 'ASC')->get();
	}

  public function showPilihLokasi(Request $req) {
		return LokasiSQL::where('KodeRak', $req->input('koderak'))->where('KodeGdg', $req->input('kodegdg'))->orderBy('KodeLokasi', 'ASC')->get();
	}

  public function showPilihBarcode(Request $req) {
		return LokasiSQL::where('BarcodeLoc', $req->input('barcode'))->first();
	}

	public function showDet(Request $req) {
    if ($req->input('tipe')=="0"){
      return VWStkBrg::where('BARCODE', $req->input('kodegdg'))->orderBy('KODEBRG', 'ASC')->get();
      // return VWStkBrg::where('KODEBRG', $req->input('kodebrg'))->where('BARCODE', $req->input('kodegdg'))->orderBy('KODEBRG', 'ASC')->get();
    }else{
      return VWDBBarang::where('KodeBarcode', $req->input('kodebrg'))->orderBy('KodeBarcode', 'ASC')->get();
    }
	}

	public function edit(Request $req) {
		$getPrev = PemakaianBarang::where('id', $req->input('id'))->first();
		$getPrevDet = DetailPemakaianBarang::where('id_pemakaian', $req->input('id'))->get();
		for ($i = 0; $i < count($getPrevDet); $i++) {
			$getDetPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->first();
			DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->update([
				'qty_done' => $getDetPermintaan->qty_done - $getPrevDet[$i]->qty
			]);

			// update stok barang
			$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->first();
			StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->update([
				'qty_pakai' => $oldStock->qty_pakai - ($getPrevDet[$i]->qty * $getPrevDet[$i]->isi)
			]);
		}
		$detail = $req->input('pemakaian');
		for ($a = 0; $a < count($detail); $a++) {
			$getPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $req->input('id_permintaan'))->where('kode_barang', $detail[$a][0])->where('satuan', $detail[$a][3])->first();
			if ($getPermintaan->selisih < $detail[$a][2]) {
				for ($i = 0; $i < count($getPrevDet); $i++) {
					$getDetPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->first();
					DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->update([
						'qty_done' => $getDetPermintaan->qty_done + $getPrevDet[$i]->qty
					]);

					// update stok barang
					$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
						where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->first();
					StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
						where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->update([
						'qty_pakai' => $oldStock->qty_pakai + ($getPrevDet[$i]->qty * $getPrevDet[$i]->isi)
					]);
				}
				return $detail[$a][0];
			}
		}
		DetailPemakaianBarang::where('id_pemakaian', $req->input('id'))->delete();
		Batch::where('no_bukti', $req->input('no_bukti'))->delete();
		PemakaianBarang::where('id', $req->input('id'))->update([
			'tanggal' => $req->input('tanggal'),
			'departemen' => $req->input('departemen'),
			'gudang' => $req->input('gudang'),
			'keterangan' => $req->input('keterangan')
		]);
		for ($i = 0; $i < count($detail); $i++) {
			DetailPemakaianBarang::create([
				'id_pemakaian' => $detail[$i][6],
				'no_pemakaian' => $detail[$i][7],
				'perkiraan' => $detail[$i][8],
				'kode_barang' => $detail[$i][0],
				'nama_barang' => $detail[$i][1],
				'qty' => $detail[$i][2],
				'satuan' => $detail[$i][3],
				'isi' => $detail[$i][4]
			]);

			$getPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $req->input('id_permintaan'))->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][3])->first();
			DetailPermintaanPemakaian::where('id_permintaan', $req->input('id_permintaan'))->where('kode_barang', $detail[$i][0])->where('satuan', $detail[$i][3])->update([
				'qty_done' => $getPermintaan->qty_done + $detail[$i][2]
			]);

			// update stok barang
			$cek_jml = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
				where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->count();
			if ($cek_jml == 0) {
				$bulanlalu = (int)date('m', strtotime($req->input('tanggal'))) - 1;
				$taun = (int)date('Y', strtotime($req->input('tanggal')));
				if ($bulanlalu == 0) {
					$bulanlalu = 12;
					$taun = $taun - 1;
				}
				$cekstock = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->count();
				if ($cekstock != 0) {
					$saldoAwal = StockBarang::where('bulan', $bulanlalu)->where('tahun', $taun)->where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->first();
					StockBarang::create([
						'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
						'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
						'kode_barang' => $detail[$i][0],
						'gudang' => $req->input('gudang'),
						'awal_qty' => $saldoAwal->akhir_qty,
						'awal_harga' => $saldoAwal->akhir_harga,
						'qty_pakai' => $detail[$i][2] * $detail[$i][4]
					]);
				} else {
					StockBarang::create([
						'bulan' => (int)date('m', strtotime($req->input('tanggal'))),
						'tahun' => (int)date('Y', strtotime($req->input('tanggal'))),
						'kode_barang' => $detail[$i][0],
						'gudang' => $req->input('gudang'),
						'qty_pakai' => $detail[$i][2] * $detail[$i][4]
					]);
				}
			}
			else {
				$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
					where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->first();
				StockBarang::where('bulan', (int)date('m', strtotime($req->input('tanggal'))))->where('tahun', (int)date('Y', strtotime($req->input('tanggal'))))->
					where('kode_barang', $detail[$i][0])->where('gudang', $req->input('gudang'))->update([
					'qty_pakai' => $oldStock->qty_pakai + ($detail[$i][2] * $detail[$i][4])
				]);
			}
		}

		$batch = $req->input("batch");
		if (!is_array($batch)) {
				$batch = array();
		}
		for ($i = 0; $i < count($batch); $i++) {
			if ($batch[$i][4] > 0) {
				Batch::create([
					'no_bukti' => $req->input('no_bukti'),
					'no_batch' => $batch[$i][0],
					'urut' => $batch[$i][5],
					'gudang' => $req->input('gudang'),
					'kode_barang' => $batch[$i][1],
					'tanggal' => $batch[$i][2],
					'qty' => $batch[$i][4] * $batch[$i][6] * -1
				]);
			}
		}
		$this->logActivity('UPD', \Auth::id(), $getPrev->no_bukti);
		return 1;
	}

	public function erase(Request $req) {
		$getPrev = PemakaianBarang::where('id', $req->input('id'))->first();
		$getPrevDet = DetailPemakaianBarang::where('id_pemakaian', $req->input('id'))->get();
		for ($i = 0; $i < count($getPrevDet); $i++) {
			$getDetPermintaan = DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->first();
			DetailPermintaanPemakaian::where('id_permintaan', $getPrev->id_permintaan)->where('kode_barang', $getPrevDet[$i]->kode_barang)->where('satuan', $getPrevDet[$i]->satuan)->update([
				'qty_done' => $getDetPermintaan->qty_done - $getPrevDet[$i]->qty
			]);

			// update stok barang
			$oldStock = StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->first();
			StockBarang::where('bulan', (int)date('m', strtotime($getPrev->tanggal)))->where('tahun', (int)date('Y', strtotime($getPrev->tanggal)))->
				where('kode_barang', $getPrevDet[$i]->kode_barang)->where('gudang', $getPrev->gudang)->update([
				'qty_pakai' => $oldStock->qty_pakai - ($getPrevDet[$i]->qty * $getPrevDet[$i]->isi)
			]);
		}
		DetailPemakaianBarang::where('id_pemakaian', $req->input('id'))->delete();
		Batch::where('no_bukti', $getPrev->no_bukti)->delete();
		PemakaianBarang::where('id', $req->input('id'))->update(['deleted' => 1]);
		$this->logActivity('DEL', \Auth::id(), $getPrev->no_bukti);
	}

}

?>
