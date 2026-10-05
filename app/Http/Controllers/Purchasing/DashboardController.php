<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;
use App\Models\Merk;
use App\Models\Sales;
use App\Models\AksesMenu;
use App\Models\AksesSales;
use App\Models\OmsetSPL;
use App\Models\OmsetSalesMerkSPL;
use App\Models\OmsetSPLS;
use App\Models\OmsetSalesMerkSPLS;
use App\Models\OmsetSML;
use App\Models\OmsetSalesMerkSML;
use App\Models\OmsetMKS;
use App\Models\OmsetSalesMerkMKS;
use App\Models\OmsetMAB;
use App\Models\OmsetSalesMerkMAB;
use App\Models\OmsetPDW;
use App\Models\OmsetSalesMerkPDW;

class DashboardController extends Controller {

	public function index() {
		User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
		$checkUser = User::where('id', \Auth::id())->first();
		if ($checkUser->level == 3 && $checkUser->username == 'AJC') {
			$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		}else{
			$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		}
		return view("dashboard")->with('menu', $menu);
	}

	public function getMenu() {
		$row = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		return $row;
	}


}


?>
