<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Model\Menu;
use App\Model\User;
use App\Model\Periode;
use App\Model\AksesMenu;

class PeriodeController extends Controller {

	public function index() {
		$getID = Menu::where('access', '/setupperiodekerja')->first();
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
			return view("setperiode")->with('menu', $menu)->with('periode', $periode)->with('akses', $check);
		} else {
			return redirect('/home');
		}
	}

	public function edit(Request $req) {
		Periode::where('id_user', \Auth::id())->update(['bulan' => (int)$req->input('bulan'), 'tahun' => (int)$req->input('tahun')]);
	}
}

?>
