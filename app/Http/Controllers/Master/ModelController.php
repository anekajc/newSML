<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Model\Menu;
use App\Model\User;
use App\Model\Models;
use App\Model\Periode;
use App\Model\AksesMenu;

class ModelController extends Controller {

	public function index() {
		$getID = Menu::where('access', '/model')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 1) {
			User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
			$checkUser = User::where('id', \Auth::id())->first();
			if ($checkUser->level == 3 && $checkUser->username == 'SA') {
				$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
			}else{
				$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
			}
			$model = Models::where('deleted', 0)->get();
			$periode = Periode::where('id_user', \Auth::id())->first();
			return view("model")->with('menu', $menu)->with('model', $model)->with('periode', $periode)->with('akses', $check);
		} else {
			return redirect('/home');
		}
	}

	public function loadAll() {
		return Models::where('deleted', 0)->get();
	}

	public function add(Request $req) {
		$check = Models::where('kode', $req->input('kode'))->where('deleted', 0)->count();
		if ($check == 0) {
			Models::create([
				'kode' => $req->input('kode'),
				'nama' => $req->input('nama')
			]);
			return 1;
		} else {
			return 0;
		}
	}

	public function edit(Request $req) {
		$check = Models::where('kode', $req->input('kode'))->where('deleted', 0)->where('id', '!=', $req->input('id'))->count();
		if ($check == 0) {
			Models::where('id', $req->input('id'))->update(['kode' => $req->input('kode'), 'nama' => $req->input('nama')]);
			return 1;
		} else {
			return 0;
		}
	}

	public function erase(Request $req) {
		Models::where('id', $req->input('id'))->update(['deleted' => 1]);
	}

}

?>
