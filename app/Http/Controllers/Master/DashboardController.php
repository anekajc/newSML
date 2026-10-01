<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Model\Menu;
use App\Model\Merk;
use App\Model\Sales;
use App\Model\AksesMenu;
use App\Model\AksesSales;
use App\Model\OmsetSPL;
use App\Model\OmsetSalesMerkSPL;
use App\Model\OmsetSPLS;
use App\Model\OmsetSalesMerkSPLS;
use App\Model\OmsetSML;
use App\Model\OmsetSalesMerkSML;
use App\Model\OmsetMKS;
use App\Model\OmsetSalesMerkMKS;
use App\Model\OmsetMAB;
use App\Model\OmsetSalesMerkMAB;
use App\Model\OmsetPDW;
use App\Model\OmsetSalesMerkPDW;

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
