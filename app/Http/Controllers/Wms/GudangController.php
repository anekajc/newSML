<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
// use App\Models\Menu;
// use App\Models\User;
use App\Models\GudangSQL;
use App\Models\RakSQL;
use App\Models\LokasiSQL;
// use App\Models\Periode;
// use App\Models\AksesMenu;
use App\Models\AlokasiBrg;
use App\Models\AlokasiBrgDet;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;

class GudangController extends Controller {

	public function index() {
		// $getID = Menu::where('access', '/gudang')->first();
		// $check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		// if ($check->tampil == 1) {
			// User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
			// $checkUser = User::where('id', \Auth::id())->first();
  		// if ($checkUser->level == 3 && $checkUser->username == 'SA') {
      //   $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      // }else{
      //   $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      // }

			$kodemenu = '210101';
	    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
	    if(!$akses || !$akses->HASACCESS) {
	       return redirect('/home');
	    }


			$gudang = GudangSQL::All();
			$periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
			return view("wms.gudang")
			->with('gudang', $gudang)
			->with('periode', $periode)
			->with('akses', $akses)
			// ->with('akses', $check)
			;
		// } else {
		// 	return redirect('/home');
		// }
	}

	public function loadAll() {
		return GudangSQL::All();
	}

	public function addRak(Request $req) {
		if ($req->input('choice')=='I'){
			$check = RakSQL::where('kodeRak', $req->input('kode'))->where('kodegdg', $req->input('kodegdg'))->count();
			$tingkat = $req->input('tingkat');
			$kolom = intval($req->input('kolom'));
			$baris = $req->input('baris');
			if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$tingkat,
					$kolom,
					$baris
				];
				DB::connection('SML')->statement('exec Sp_Rak ?,?,?,?,?,?,?',$values);

				for ($i = 0; $i < $tingkat; $i++) {
					// if ($i != 0 ) {
						$t="";
						$t=strval($i+1);
						for ($j = 0; $j < $kolom; $j++) {
							// if ($j != 0 ) {
								$kdl="";
								$nml="";
								$k="";
								$kl="";
								$klm="";

								$klm=strval($j+1);
								$kl.="00";
								$kl.=$klm;
								$k=substr($kl,-2);;
								$kdl.=$t;
								$kdl.=$k;
								$nml.="Lokasi ";
								$nml.=$kdl;

								$values = [
									$req->input('choice'),
									$kdl,
									$nml,
									$req->input('kodegdg'),
									$req->input('kode')
								];
								DB::connection('SML')->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
							// }
						}
					// }
				}
				return 1;
			} else {
				return 0;
			}
		} elseif ($req->input('choice')=='U') {
			$check = RakSQL::where('kodeRak', $req->input('kode'))->where('kodegdg', $req->input('kodegdg'))->count();
			$tingkat = $req->input('tingkat');
			$kolom = intval($req->input('kolom'));
			$baris = $req->input('baris');
			if ($check != 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$tingkat,
					$kolom,
					$baris
				];
				DB::connection('SML')->statement('exec Sp_Rak ?,?,?,?,?,?,?',$values);

				for ($i = 0; $i < $tingkat; $i++) {
					// if ($i != 0 ) {
						$t="";
						$t=strval($i+1);
						for ($j = 0; $j < $kolom; $j++) {
							// if ($j != 0 ) {
								$kdl="";
								$nml="";
								$k="";
								$kl="";
								$klm="";

								$klm=strval($j+1);
								$kl.="00";
								$kl.=$klm;
								$k=substr($kl,-2);;
								$kdl.=$t;
								$kdl.=$k;
								$nml.="Lokasi ";
								$nml.=$kdl;

								$values = [
									"I",
									$kdl,
									$nml,
									$req->input('kodegdg'),
									$req->input('kode')
								];
								DB::connection('SML')->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
							// }
						}
					// }
				}
				return 1;
			} else {
				return 0;
			}
		} else {
			$check = AlokasiBrg::where('kodeRak', $req->input('kode'))->where('kodegdg', $req->input('kodegdg'))->count();
			$tingkat = $req->input('tingkat');
			$kolom = intval($req->input('kolom'));
			$baris = $req->input('baris');
			if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$tingkat,
					$kolom,
					$baris
				];
				DB::connection('SML')->statement('exec Sp_Rak ?,?,?,?,?,?,?',$values);

				for ($i = 0; $i < $tingkat; $i++) {
					// if ($i != 0 ) {
						$t="";
						$t=strval($i+1);
						for ($j = 0; $j < $kolom; $j++) {
							// if ($j != 0 ) {
								$kdl="";
								$nml="";
								$k="";
								$kl="";
								$klm="";

								$klm=strval($j+1);
								$kl.="00";
								$kl.=$klm;
								$k=substr($kl,-2);;
								$kdl.=$t;
								$kdl.=$k;
								$nml.="Lokasi ";
								$nml.=$kdl;

								$values = [
									$req->input('choice'),
									$kdl,
									$nml,
									$req->input('kodegdg'),
									$req->input('kode')
								];
								DB::connection('SML')->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
							// }
						}
					// }
				}
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function addLokasi(Request $req) {
		if ($req->input('choice')=='I'){

			// select * from dblokasisig where KodeLokasi = ''

			$checkSQL = DB::connection('SML')->select('select * from dblokasisig where KodeLokasi = :kode',["kode" => $req->input('kode')]);
			if ($checkSQL) {
				return 0;
			}



			$check = LokasiSQL::where('KodeLokasi', $req->input('kode'))->where('KodeRak', $req->input('koderak'))->where('KodeGdg', $req->input('kodegdg'))->count();
			if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$req->input('koderak'),
					$req->input('urutLokasi'),
					$req->input('qntMax'),
					$req->input('tingkat') ? $req->input('tingkat') : '',
					$req->input('kolom') ? $req->input('kolom') : '',
					$req->input('baris') ? $req->input('baris') : '',
				];
				DB::connection('SML')->statement('exec Sp_Lokasi ?,?,?,?,?,?,?,?,?,?',$values);
				return 1;
			} else {
				return 0;
			}
		} elseif ($req->input('choice')=='U'){
			// $checkSQL = DB::connection('SML')->select('select * from dblokasisig where KodeLokasi = :kode',["kode" => $req->input('kode')]);
			// if ($checkSQL) {
			// 	return 0;
			// }

			$check = LokasiSQL::where('KodeLokasi', $req->input('kode'))->where('KodeRak', $req->input('koderak'))->where('KodeGdg', $req->input('kodegdg'))->count();
			if ($check != 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$req->input('koderak'),
					$req->input('urutLokasi'),
					$req->input('qntMax'),
					$req->input('tingkat') ? $req->input('tingkat') : '',
					$req->input('kolom') ? $req->input('kolom') : '',
					$req->input('baris') ? $req->input('baris') : '',
				];
				DB::connection('SML')->statement('exec Sp_Lokasi ?,?,?,?,?,?,?,?,?,?',$values);
				return 1;
			} else {
				return 0;
			}
		} else {

			$check = DB::connection('SML')->select('SELECT BARCODE FROM vwKartuStockSIG WHERE Barcode= :kode',["kode" => $req->input('kode')]);
      // if ($check) {
      //   return 2;
      // }
			// $check = AlokasiBrg::where('KodeLokasi', $req->input('kode'))->where('KodeRak', $req->input('koderak'))->where('KodeGdg', $req->input('kodegdg'))->count();
			if (! $check) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$req->input('koderak')
				];
				DB::connection('SML')->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
				return 1;
			} else {
				return 0;
			}
		}
	}

	public function showRak(Request $req) {
		return RakSQL::where('KodeGdg', $req->input('KodeGdg'))->get();
	}

	public function showLokasi(Request $req) {
		return LokasiSQL::where('KodeGdg', $req->input('Kodegdg'))->where('KodeRak', $req->input('Koderak'))->get();
	}

}

?>
