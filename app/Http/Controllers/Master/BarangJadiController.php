<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
// use App\Model\Menu;
use App\Model\User;
// use App\Model\BahanBarang;
// use App\Model\GrupJadi;
// use App\Model\SubgrupJadi;
// use App\Model\MerkJadi;
// use App\Model\WarnaJadi;
// use App\Model\Dimensi;
// use App\Model\Periode;
// use App\Model\AksesMenu;
use App\Model\VWDBBarang;
use App\Model\DBBarangLokasi;
use App\Model\LokasiSQL;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;

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
			// $periode = NewPeriode::where('user_id' , \Auth::id())->first();
	    $menul0 = NewMenu::where('l0' , 0)->orderBy('KODEMENU')->get();
	    $menul1 = NewMenu::where('l0' , 1)->orderBy('KODEMENU')->get();
	    $menul2 = NewMenu::where('l0' , 2)->orderBy('KODEMENU')->get();

	    foreach ($menul1 as $menu1) {
	      $array = [];
	      $kodecheck = $menu1['KODEMENU'];
	      foreach ($menul2 as $menu2) {
	          // array_push($array, $kodecheck);
	          if (substr($menu2['KODEMENU'],0, strlen($kodecheck)) == $kodecheck) {
	            array_push($array, $menu2);
	          }
	      }
	      $menu1->child = $array;

	    }

	    foreach ($menul0 as $menu0) {

	      $array = [];
	      $kodecheck = $menu0['KODEMENU'];
	      foreach ($menul1 as $menu1) {
	        if (substr($menu1['KODEMENU'],0,strlen($kodecheck)) == $kodecheck) {
	          array_push($array, $menu1);
	        }
	      }
	        $menu0->child = $array;
	    }

			$barangjadi = VWDBBarang::where('KODEGRP', 'BJ')->paginate(100000);
			$design = LokasiSQL::all();
			$keterangan = LokasiSQL::all();
			$merk = LokasiSQL::all();
			$warna = LokasiSQL::all();
			$dimensi = LokasiSQL::all();
			$lokasi = LokasiSQL::all();
			$periode = NewPeriode::where('user_id', \Auth::id())->first();
	 		return view("barangjadi")
				->with('menul0', $menul0)
				->with('barangjadi', $barangjadi)
				->with('design', $design)
				->with('keterangan', $keterangan)
				->with('merk', $merk)
				->with('warna', $warna)
				->with('dimensi', $dimensi)
				->with('periode', $periode)
				->with('lokasi', $lokasi)
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

	public function addBarangLokasi(Request $req) {
		if ($req->input('choice')=='I'){
			$check = DBBarangLokasi::where('KodeBarang', $req->input('kode'))->where('Urut', $req->input('urut'))->count();
			if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('urut'),
					$req->input('kode'),
					$req->input('barcode')
				];
				DB::connection("sqlsrv")->statement('exec Sp_BarangLokasi ?,?,?,?',$values);
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
					$req->input('urutold')
				];
				DB::connection("sqlsrv")->statement('exec Sp_BarangLokasi ?,?,?,?,?',$values);
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
					$req->input('barcode')
				];
				DB::connection("sqlsrv")->statement('exec Sp_BarangLokasi ?,?,?,?',$values);
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
