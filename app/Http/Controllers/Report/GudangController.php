<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
// use App\Model\Menu;
// use App\Model\User;
use App\Model\GudangSQL;
use App\Model\RakSQL;
use App\Model\LokasiSQL;
// use App\Model\Periode;
// use App\Model\AksesMenu;
use App\Model\AlokasiBrg;
use App\Model\AlokasiBrgDet;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;

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

			$gudang = GudangSQL::All();
			$periode = NewPeriode::where('user_id', \Auth::id())->first();
			return view("gudang")
			->with('menul0', $menul0)
			->with('gudang', $gudang)
			->with('periode', $periode)
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
			$tingkat = intval($req->input('tingkat'));
			$kolom = intval($req->input('kolom'));
			if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$tingkat,
					$kolom
				];
				DB::connection("sqlsrv")->statement('exec Sp_Rak ?,?,?,?,?,?',$values);

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
								DB::connection("sqlsrv")->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
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
			$tingkat = intval($req->input('tingkat'));
			$kolom = intval($req->input('kolom'));
			if ($check != 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$tingkat,
					$kolom
				];
				DB::connection("sqlsrv")->statement('exec Sp_Rak ?,?,?,?,?,?',$values);

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
								DB::connection("sqlsrv")->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
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
			$tingkat = intval($req->input('tingkat'));
			$kolom = intval($req->input('kolom'));
			if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$tingkat,
					$kolom
				];
				DB::connection("sqlsrv")->statement('exec Sp_Rak ?,?,?,?,?,?',$values);

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
								DB::connection("sqlsrv")->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
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
			$check = LokasiSQL::where('KodeLokasi', $req->input('kode'))->where('KodeRak', $req->input('koderak'))->where('KodeGdg', $req->input('kodegdg'))->count();
			if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$req->input('koderak')
				];
				DB::connection("sqlsrv")->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
				return 1;
			} else {
				return 0;
			}
		} elseif ($req->input('choice')=='U'){
			$check = LokasiSQL::where('KodeLokasi', $req->input('kode'))->where('KodeRak', $req->input('koderak'))->where('KodeGdg', $req->input('kodegdg'))->count();
			if ($check != 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$req->input('koderak')
				];
				DB::connection("sqlsrv")->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
				return 1;
			} else {
				return 0;
			}
		} else {
			$check = AlokasiBrg::where('KodeLokasi', $req->input('kode'))->where('KodeRak', $req->input('koderak'))->where('KodeGdg', $req->input('kodegdg'))->count();
			if ($check == 0) {
				$values = [
					$req->input('choice'),
					$req->input('kode'),
					$req->input('nama'),
					$req->input('kodegdg'),
					$req->input('koderak')
				];
				DB::connection("sqlsrv")->statement('exec Sp_Lokasi ?,?,?,?,?',$values);
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
