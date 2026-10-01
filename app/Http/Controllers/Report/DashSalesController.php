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
use App\Traits\ReportTrait;

class DashSalesController extends Controller {
	use ReportTrait;

	public function index() {
		$getID = Menu::where('access', '/dashsales1')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 1) {
			User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
			$checkUser = User::where('id', \Auth::id())->first();
  		if ($checkUser->level == 3 && $checkUser->username == 'AJC') {
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      }else{
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
      }
			$periode = Periode::where('id_user', \Auth::id())->first();
			$select = 'kode, nama'; $where = 'deleted = 0'; $order = 'kode ASC';
			$col = ['kode', 'nama']; $len = [100, 200]; $count = []; $header = ['Kode', 'Nama'];
			$table = $this->loadQuery('sales', $select, $where, $order, '-', $col, $len, $count, $header);
			$kolom = $this->columnReport('report_sales');
			$fetch = $this->getColumnReport('report_sales');
			$title = 'Sales Forecast';
			$halaman = 'Sales Forecast';
			$breadcrumb = 'Dashboard / Sales Forecast';
			$settingtgl = 0;

			return view("dashsales")->with('menu', $menu)->with('table', $table)->with('periode', $periode)->with('akses', $check)->with('kolom', $kolom)->with('fetch', $fetch)->with('col', $col)->with('header', $header)
				->with('title', $title)->with('halaman', $halaman)->with('breadcrumb', $breadcrumb)->with('settingtgl', $settingtgl);
		} else {
			return redirect('/home');
		}
	}

	public function loadAll(Request $req) {
		$fetch = $req->input("fetch");
		$kolom = $req->input("kolom");
		$urut = $req->input("urut");
		$this->setColumnReport('report_sales', $fetch);

		$select = 'kode, nama'; $where = 'deleted = 0'; $order = $kolom.' '.$urut;
		$col = ['kode', 'nama']; $len = [100, 200]; $count = []; $header = ['Kode', 'Nama'];
		$table = $this->loadQuery('sales', $select, $where, $order, '-', $col, $len, $count, $header);
		return $table;
	}

	public function sales_spl() {
		// $getSales = AksesSales::where('id_user', \Auth::id())->where('pt', 'SPL')->where('akses', 1)->select('kodesls')->get()->toArray();
		$getID = Menu::where('access', '/dashsales')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 0) { return redirect('/home'); }
		User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
		$checkUser = User::where('id', \Auth::id())->first();
		if ($checkUser->level == 3 && $checkUser->username == 'AJC') {
			$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		}else{
			$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		}
		$list_tahun = OmsetSPL::distinct('tahun')->select('tahun')->get();
		$bulan = date("n");
		$list_sales = array();
		$list_kode = array();
		$omset_sales = array();
		$omset_sales_lalu = array();
		$tahunlalu = date("Y",strtotime("-1 year"));
		$tahun = date("Y");
		$omset_th_ini = array();
		$omset_th_lalu = array();
		$omset_sales = array();
		$cek = false;
		$cek2 = false;

		$listsales = OmsetSalesMerkSPL::distinct()->whereIn('kodesls', $getSales)->selectRaw('kodesls, Nama')->orderBy('Nama', 'ASC')->get();
		for ($i = 0; $i < count($listsales); $i++) {
			array_push($list_sales, $listsales[$i]->Nama); array_push($list_kode, $listsales[$i]->kodesls);
		}
		$omsetsales = OmsetSalesMerkSPL::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		// $omsetsaleslalu = OmsetSalesMerkSPL::where('tahun', $tahunlalu)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		$omset_sales[0] = array();
		$omset_sales[1] = array();
		$omset_sales[2] = array();
		$omset_sales_lalu[0] = array();
		$omset_sales_lalu[1] = array();
		$omset_sales_lalu[2] = array();
		for ($i = 0; $i < count($list_kode); $i++) {
			$cek = false; $cek2 = false;
			for ($j = 0; $j < count($omsetsales); $j++) {
				if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 0) {
					array_push($omset_sales[0], (float)$omsetsales[$j]->omset); $cek = true;
				} else if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 1) {
					array_push($omset_sales[1], (float)$omsetsales[$j]->omset); $cek2 = true;
				}
				if ($cek && $cek2) break;
			}
			if (!$cek) array_push($omset_sales[0], 0.000);
			if (!$cek2) array_push($omset_sales[1], 0.000);
			// $cek = false; $cek2 = false;
			// for ($j = 0; $j < count($omsetsaleslalu); $j++) {
			// 	if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 0) {
			// 		array_push($omset_sales_lalu[0], (float)$omsetsaleslalu[$j]->omset); $cek = true;
			// 	} else if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 1) {
			// 		array_push($omset_sales_lalu[1], (float)$omsetsaleslalu[$j]->omset); $cek2 = true;
			// 	}
			// 	if ($cek && $cek2) break;
			// }
			// if (!$cek) array_push($omset_sales_lalu[0], 0.000);
			// if (!$cek2) array_push($omset_sales_lalu[1], 0.000);
		}
		for ($i = 0; $i < count($omset_sales[0]); $i++) { array_push($omset_sales[2], $omset_sales[0][$i] + $omset_sales[1][$i]); }
		// for ($i = 0; $i < count($omset_sales_lalu[0]); $i++) { array_push($omset_sales_lalu[2], $omset_sales_lalu[0][$i] + $omset_sales_lalu[1][$i]); }
		$title = "Dashboard Sales SPL";
		$pt = "SPL";
		return view("dashsales")->with("akses", $check)->with('title', $title)->with('tahun', $tahun)->with('tahunlalu', $tahunlalu)->with('omset_sales', $omset_sales)->with('omset_sales_lalu', $omset_sales_lalu)->
			with('list_tahun', $list_tahun)->with('bulan', $bulan)->with('list_sales', $list_sales)->with('list_kodesls', $list_kode)->with('pt', $pt)->with('menu', $menu);
	}

	public function sales_spls() {
		$getSales = AksesSales::where('id_user', \Auth::id())->where('pt', 'SPLS')->where('akses', 1)->select('kodesls')->get()->toArray();
		$getID = Menu::where('access', '/dashsales2')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 0) { return redirect('/home'); }
		$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		$list_tahun = OmsetSPLS::distinct('tahun')->select('tahun')->get(); $bulan = date("n"); $list_sales = array(); $list_kode = array(); $omset_sales = array(); $omset_sales_lalu = array();
		$tahunlalu = date("Y",strtotime("-1 year")); $tahun = date("Y"); $omset_th_ini = array(); $omset_th_lalu = array(); $omset_sales = array(); $cek = false; $cek2 = false;

		$listsales = OmsetSalesMerkSPLS::distinct()->whereIn('kodesls', $getSales)->selectRaw('kodesls, Nama')->orderBy('Nama', 'ASC')->get();
		for ($i = 0; $i < count($listsales); $i++) {
			array_push($list_sales, $listsales[$i]->Nama); array_push($list_kode, $listsales[$i]->kodesls);
		}
		$omsetsales = OmsetSalesMerkSPLS::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		// $omsetsaleslalu = OmsetSalesMerkSPLS::where('tahun', $tahunlalu)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		$omset_sales[0] = array(); $omset_sales[1] = array(); $omset_sales[2] = array(); $omset_sales_lalu[0] = array(); $omset_sales_lalu[1] = array(); $omset_sales_lalu[2] = array();
		for ($i = 0; $i < count($list_kode); $i++) {
			$cek = false; $cek2 = false;
			for ($j = 0; $j < count($omsetsales); $j++) {
				if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 0) {
					array_push($omset_sales[0], (float)$omsetsales[$j]->omset); $cek = true;
				} else if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 1) {
					array_push($omset_sales[1], (float)$omsetsales[$j]->omset); $cek2 = true;
				}
				if ($cek && $cek2) break;
			}
			if (!$cek) array_push($omset_sales[0], 0.000);
			if (!$cek2) array_push($omset_sales[1], 0.000);
			// $cek = false; $cek2 = false;
			// for ($j = 0; $j < count($omsetsaleslalu); $j++) {
			// 	if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 0) {
			// 		array_push($omset_sales_lalu[0], (float)$omsetsaleslalu[$j]->omset); $cek = true;
			// 	} else if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 1) {
			// 		array_push($omset_sales_lalu[1], (float)$omsetsaleslalu[$j]->omset); $cek2 = true;
			// 	}
			// 	if ($cek && $cek2) break;
			// }
			// if (!$cek) array_push($omset_sales_lalu[0], 0.000);
			// if (!$cek2) array_push($omset_sales_lalu[1], 0.000);
		}
		for ($i = 0; $i < count($omset_sales[0]); $i++) { array_push($omset_sales[2], $omset_sales[0][$i] + $omset_sales[1][$i]); }
		// for ($i = 0; $i < count($omset_sales_lalu[0]); $i++) { array_push($omset_sales_lalu[2], $omset_sales_lalu[0][$i] + $omset_sales_lalu[1][$i]); }
		$title = "Dashboard Sales SPLS"; $pt = "SPLS";
		return view("dashsales")->with("akses", $check)->with('title', $title)->with('tahun', $tahun)->with('tahunlalu', $tahunlalu)->with('omset_sales', $omset_sales)->with('omset_sales_lalu', $omset_sales_lalu)->
			with('list_tahun', $list_tahun)->with('bulan', $bulan)->with('list_sales', $list_sales)->with('list_kodesls', $list_kode)->with('pt', $pt)->with('menu', $menu);
	}

	public function sales_sml() {
		$getSales = AksesSales::where('id_user', \Auth::id())->where('pt', 'SML')->where('akses', 1)->select('kodesls')->get()->toArray();
		$getID = Menu::where('access', '/dashsales3')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 0) { return redirect('/home'); }
		$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		$list_tahun = OmsetSML::distinct('tahun')->select('tahun')->get(); $bulan = date("n"); $list_sales = array(); $list_kode = array(); $omset_sales = array(); $omset_sales_lalu = array();
		$tahunlalu = date("Y",strtotime("-1 year")); $tahun = date("Y"); $omset_th_ini = array(); $omset_th_lalu = array(); $omset_sales = array(); $cek = false; $cek2 = false;

		$listsales = OmsetSalesMerkSML::distinct()->whereIn('kodesls', $getSales)->selectRaw('kodesls, Nama')->orderBy('Nama', 'ASC')->get();
		for ($i = 0; $i < count($listsales); $i++) {
			array_push($list_sales, $listsales[$i]->Nama); array_push($list_kode, $listsales[$i]->kodesls);
		}
		$omsetsales = OmsetSalesMerkSML::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		// $omsetsaleslalu = OmsetSalesMerkSML::where('tahun', $tahunlalu)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		$omset_sales[0] = array(); $omset_sales[1] = array(); $omset_sales[2] = array(); $omset_sales_lalu[0] = array(); $omset_sales_lalu[1] = array(); $omset_sales_lalu[2] = array();
		for ($i = 0; $i < count($list_kode); $i++) {
			$cek = false; $cek2 = false;
			for ($j = 0; $j < count($omsetsales); $j++) {
				if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 0) {
					array_push($omset_sales[0], (float)$omsetsales[$j]->omset); $cek = true;
				} else if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 1) {
					array_push($omset_sales[1], (float)$omsetsales[$j]->omset); $cek2 = true;
				}
				if ($cek && $cek2) break;
			}
			if (!$cek) array_push($omset_sales[0], 0.000);
			if (!$cek2) array_push($omset_sales[1], 0.000);
			// $cek = false; $cek2 = false;
			// for ($j = 0; $j < count($omsetsaleslalu); $j++) {
			// 	if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 0) {
			// 		array_push($omset_sales_lalu[0], (float)$omsetsaleslalu[$j]->omset); $cek = true;
			// 	} else if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 1) {
			// 		array_push($omset_sales_lalu[1], (float)$omsetsaleslalu[$j]->omset); $cek2 = true;
			// 	}
			// 	if ($cek && $cek2) break;
			// }
			// if (!$cek) array_push($omset_sales_lalu[0], 0.000);
			// if (!$cek2) array_push($omset_sales_lalu[1], 0.000);
		}
		for ($i = 0; $i < count($omset_sales[0]); $i++) { array_push($omset_sales[2], $omset_sales[0][$i] + $omset_sales[1][$i]); }
		// for ($i = 0; $i < count($omset_sales_lalu[0]); $i++) { array_push($omset_sales_lalu[2], $omset_sales_lalu[0][$i] + $omset_sales_lalu[1][$i]); }
		$title = "Dashboard Sales SML"; $pt = "SML";
		return view("dashsales")->with("akses", $check)->with('title', $title)->with('tahun', $tahun)->with('tahunlalu', $tahunlalu)->with('omset_sales', $omset_sales)->with('omset_sales_lalu', $omset_sales_lalu)->
			with('list_tahun', $list_tahun)->with('bulan', $bulan)->with('list_sales', $list_sales)->with('list_kodesls', $list_kode)->with('pt', $pt)->with('menu', $menu);
	}

	public function sales_mks() {
		$getSales = AksesSales::where('id_user', \Auth::id())->where('pt',  'MKS')->where('akses', 1)->select('kodesls')->get()->toArray();
		$getID = Menu::where('access', '/dashsales4')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 0) { return redirect('/home'); }
		$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		$list_tahun = OmsetMKS::distinct('tahun')->select('tahun')->get(); $bulan = date("n"); $list_sales = array(); $list_kode = array(); $omset_sales = array(); $omset_sales_lalu = array();
		$tahunlalu = date("Y",strtotime("-1 year")); $tahun = date("Y"); $omset_th_ini = array(); $omset_th_lalu = array(); $omset_sales = array(); $cek = false; $cek2 = false;

		$listsales = OmsetSalesMerkMKS::distinct()->whereIn('kodesls', $getSales)->selectRaw('kodesls, Nama')->orderBy('Nama', 'ASC')->get();
		for ($i = 0; $i < count($listsales); $i++) {
			array_push($list_sales, $listsales[$i]->Nama); array_push($list_kode, $listsales[$i]->kodesls);
		}
		$omsetsales = OmsetSalesMerkMKS::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		// $omsetsaleslalu = OmsetSalesMerkMKS::where('tahun', $tahunlalu)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		$omset_sales[0] = array(); $omset_sales[1] = array(); $omset_sales[2] = array(); $omset_sales_lalu[0] = array(); $omset_sales_lalu[1] = array(); $omset_sales_lalu[2] = array();
		for ($i = 0; $i < count($list_kode); $i++) {
			$cek = false; $cek2 = false;
			for ($j = 0; $j < count($omsetsales); $j++) {
				if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 0) {
					array_push($omset_sales[0], (float)$omsetsales[$j]->omset); $cek = true;
				} else if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 1) {
					array_push($omset_sales[1], (float)$omsetsales[$j]->omset); $cek2 = true;
				}
				if ($cek && $cek2) break;
			}
			if (!$cek) array_push($omset_sales[0], 0.000);
			if (!$cek2) array_push($omset_sales[1], 0.000);
			// $cek = false; $cek2 = false;
			// for ($j = 0; $j < count($omsetsaleslalu); $j++) {
			// 	if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 0) {
			// 		array_push($omset_sales_lalu[0], (float)$omsetsaleslalu[$j]->omset); $cek = true;
			// 	} else if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 1) {
			// 		array_push($omset_sales_lalu[1], (float)$omsetsaleslalu[$j]->omset); $cek2 = true;
			// 	}
			// 	if ($cek && $cek2) break;
			// }
			// if (!$cek) array_push($omset_sales_lalu[0], 0.000);
			// if (!$cek2) array_push($omset_sales_lalu[1], 0.000);
		}
		for ($i = 0; $i < count($omset_sales[0]); $i++) { array_push($omset_sales[2], $omset_sales[0][$i] + $omset_sales[1][$i]); }
		// for ($i = 0; $i < count($omset_sales_lalu[0]); $i++) { array_push($omset_sales_lalu[2], $omset_sales_lalu[0][$i] + $omset_sales_lalu[1][$i]); }
		$title = "Dashboard Sales MKS"; $pt = "MKS";
		return view("dashsales")->with("akses", $check)->with('title', $title)->with('tahun', $tahun)->with('tahunlalu', $tahunlalu)->with('omset_sales', $omset_sales)->with('omset_sales_lalu', $omset_sales_lalu)->
			with('list_tahun', $list_tahun)->with('bulan', $bulan)->with('list_sales', $list_sales)->with('list_kodesls', $list_kode)->with('pt', $pt)->with('menu', $menu);
	}

	public function sales_mab() {
		$getSales = AksesSales::where('id_user', \Auth::id())->where('pt', 'MAB')->where('akses', 1)->select('kodesls')->get()->toArray();
		$getID = Menu::where('access', '/dashsales5')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 0) { return redirect('/home'); }
		$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		$list_tahun = OmsetMAB::distinct('tahun')->select('tahun')->get(); $bulan = date("n"); $list_sales = array(); $list_kode = array(); $omset_sales = array(); $omset_sales_lalu = array();
		$tahunlalu = date("Y",strtotime("-1 year")); $tahun = date("Y"); $omset_th_ini = array(); $omset_th_lalu = array(); $omset_sales = array(); $cek = false; $cek2 = false;

		$listsales = OmsetSalesMerkMAB::distinct()->whereIn('kodesls', $getSales)->selectRaw('kodesls, Nama')->orderBy('Nama', 'ASC')->get();
		for ($i = 0; $i < count($listsales); $i++) {
			array_push($list_sales, $listsales[$i]->Nama); array_push($list_kode, $listsales[$i]->kodesls);
		}
		$omsetsales = OmsetSalesMerkMAB::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		// $omsetsaleslalu = OmsetSalesMerkMAB::where('tahun', $tahunlalu)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		$omset_sales[0] = array(); $omset_sales[1] = array(); $omset_sales[2] = array(); $omset_sales_lalu[0] = array(); $omset_sales_lalu[1] = array(); $omset_sales_lalu[2] = array();
		for ($i = 0; $i < count($list_kode); $i++) {
			$cek = false; $cek2 = false;
			for ($j = 0; $j < count($omsetsales); $j++) {
				if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 0) {
					array_push($omset_sales[0], (float)$omsetsales[$j]->omset); $cek = true;
				} else if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 1) {
					array_push($omset_sales[1], (float)$omsetsales[$j]->omset); $cek2 = true;
				}
				if ($cek && $cek2) break;
			}
			if (!$cek) array_push($omset_sales[0], 0.000);
			if (!$cek2) array_push($omset_sales[1], 0.000);
			// $cek = false; $cek2 = false;
			// for ($j = 0; $j < count($omsetsaleslalu); $j++) {
			// 	if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 0) {
			// 		array_push($omset_sales_lalu[0], (float)$omsetsaleslalu[$j]->omset); $cek = true;
			// 	} else if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 1) {
			// 		array_push($omset_sales_lalu[1], (float)$omsetsaleslalu[$j]->omset); $cek2 = true;
			// 	}
			// 	if ($cek && $cek2) break;
			// }
			// if (!$cek) array_push($omset_sales_lalu[0], 0.000);
			// if (!$cek2) array_push($omset_sales_lalu[1], 0.000);
		}
		for ($i = 0; $i < count($omset_sales[0]); $i++) { array_push($omset_sales[2], $omset_sales[0][$i] + $omset_sales[1][$i]); }
		// for ($i = 0; $i < count($omset_sales_lalu[0]); $i++) { array_push($omset_sales_lalu[2], $omset_sales_lalu[0][$i] + $omset_sales_lalu[1][$i]); }
		$title = "Dashboard Sales MAB"; $pt = "MAB";
		return view("dashsales")->with("akses", $check)->with('title', $title)->with('tahun', $tahun)->with('tahunlalu', $tahunlalu)->with('omset_sales', $omset_sales)->with('omset_sales_lalu', $omset_sales_lalu)->
			with('list_tahun', $list_tahun)->with('bulan', $bulan)->with('list_sales', $list_sales)->with('list_kodesls', $list_kode)->with('pt', $pt)->with('menu', $menu);
	}

	public function sales_pdw() {
		$getSales = AksesSales::where('id_user', \Auth::id())->where('pt', 'PDW')->where('akses', 1)->select('kodesls')->get()->toArray();
		$getID = Menu::where('access', '/dashsales6')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 0) { return redirect('/home'); }
		$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		$list_tahun = OmsetPDW::distinct('tahun')->select('tahun')->get(); $bulan = date("n"); $list_sales = array(); $list_kode = array(); $omset_sales = array(); $omset_sales_lalu = array();
		$tahunlalu = date("Y",strtotime("-1 year")); $tahun = date("Y"); $omset_th_ini = array(); $omset_th_lalu = array(); $omset_sales = array(); $cek = false; $cek2 = false;

		$listsales = OmsetSalesMerkPDW::distinct()->whereIn('kodesls', $getSales)->selectRaw('kodesls, Nama')->orderBy('Nama', 'ASC')->get();
		for ($i = 0; $i < count($listsales); $i++) {
			array_push($list_sales, $listsales[$i]->Nama); array_push($list_kode, $listsales[$i]->kodesls);
		}
		$omsetsales = OmsetSalesMerkPDW::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		// $omsetsaleslalu = OmsetSalesMerkPDW::where('tahun', $tahunlalu)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
		$omset_sales[0] = array(); $omset_sales[1] = array(); $omset_sales[2] = array(); $omset_sales_lalu[0] = array(); $omset_sales_lalu[1] = array(); $omset_sales_lalu[2] = array();
		for ($i = 0; $i < count($list_kode); $i++) {
			$cek = false; $cek2 = false;
			for ($j = 0; $j < count($omsetsales); $j++) {
				if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 0) {
					array_push($omset_sales[0], (float)$omsetsales[$j]->omset); $cek = true;
				} else if ($omsetsales[$j]->kodesls == $list_kode[$i] && $omsetsales[$j]->pAgen == 1) {
					array_push($omset_sales[1], (float)$omsetsales[$j]->omset); $cek2 = true;
				}
				if ($cek && $cek2) break;
			}
			if (!$cek) array_push($omset_sales[0], 0.000);
			if (!$cek2) array_push($omset_sales[1], 0.000);
			// $cek = false; $cek2 = false;
			// for ($j = 0; $j < count($omsetsaleslalu); $j++) {
			// 	if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 0) {
			// 		array_push($omset_sales_lalu[0], (float)$omsetsaleslalu[$j]->omset); $cek = true;
			// 	} else if ($omsetsaleslalu[$j]->kodesls == $list_kode[$i] && $omsetsaleslalu[$j]->pAgen == 1) {
			// 		array_push($omset_sales_lalu[1], (float)$omsetsaleslalu[$j]->omset); $cek2 = true;
			// 	}
			// 	if ($cek && $cek2) break;
			// }
			// if (!$cek) array_push($omset_sales_lalu[0], 0.000);
			// if (!$cek2) array_push($omset_sales_lalu[1], 0.000);
		}
		for ($i = 0; $i < count($omset_sales[0]); $i++) { array_push($omset_sales[2], $omset_sales[0][$i] + $omset_sales[1][$i]); }
		// for ($i = 0; $i < count($omset_sales_lalu[0]); $i++) { array_push($omset_sales_lalu[2], $omset_sales_lalu[0][$i] + $omset_sales_lalu[1][$i]); }
		$title = "Dashboard Sales PDW"; $pt = "PDW";
		return view("dashsales")->with("akses", $check)->with('title', $title)->with('tahun', $tahun)->with('tahunlalu', $tahunlalu)->with('omset_sales', $omset_sales)->with('omset_sales_lalu', $omset_sales_lalu)->
			with('list_tahun', $list_tahun)->with('bulan', $bulan)->with('list_sales', $list_sales)->with('list_kodesls', $list_kode)->with('pt', $pt)->with('menu', $menu);
	}

		public function getInvoiceSales(Request $req) {
			$getSales = AksesSales::where('id_user', \Auth::id())->where('pt', $req->input('pt'))->where('akses', 1)->select('kodesls')->get()->toArray();
			$tahun = $req->input('tahun'); $sales = $req->input('sales'); $omset_sales = array(); $omsetsales = ""; $cek = false; $cek2 = false;
			if ($req->input('tipe') == 0) {
				if ($req->input('pt') == "SPL") {
					$omsetsales = OmsetSalesMerkSPL::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
				} else if ($req->input('pt') == "SPLS") {
					$omsetsales = OmsetSalesMerkSPLS::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
				} else if ($req->input('pt') == "SML") {
					$omsetsales = OmsetSalesMerkSML::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
				} else if ($req->input('pt') == "MKS") {
					$omsetsales = OmsetSalesMerkMKS::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
				} else if ($req->input('pt') == "MAB") {
					$omsetsales = OmsetSalesMerkMAB::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
				} else if ($req->input('pt') == "PDW") {
					$omsetsales = OmsetSalesMerkPDW::where('tahun', $tahun)->whereIn('kodesls', $getSales)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
				}
			} else {
				if ($req->input('smt') == 1) {
					if ($req->input('pt') == "SPL") {
						$omsetsales = OmsetSalesMerkSPL::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 1)->where('bulan', '<=', 6)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "SPLS") {
						$omsetsales = OmsetSalesMerkSPLS::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 1)->where('bulan', '<=', 6)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "SML") {
						$omsetsales = OmsetSalesMerkSML::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 1)->where('bulan', '<=', 6)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "MKS") {
						$omsetsales = OmsetSalesMerkMKS::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 1)->where('bulan', '<=', 6)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "MAB") {
						$omsetsales = OmsetSalesMerkMAB::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 1)->where('bulan', '<=', 6)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "PDW") {
						$omsetsales = OmsetSalesMerkPDW::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 1)->where('bulan', '<=', 6)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					}
				} else {
					if ($req->input('pt') == "SPL") {
						$omsetsales = OmsetSalesMerkSPL::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 7)->where('bulan', '<=', 12)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "SPLS") {
						$omsetsales = OmsetSalesMerkSPLS::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 7)->where('bulan', '<=', 12)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "SML") {
						$omsetsales = OmsetSalesMerkSML::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 7)->where('bulan', '<=', 12)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "MKS") {
						$omsetsales = OmsetSalesMerkMKS::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 7)->where('bulan', '<=', 12)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "MAB") {
						$omsetsales = OmsetSalesMerkMAB::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 7)->where('bulan', '<=', 12)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					} else if ($req->input('pt') == "PDW") {
						$omsetsales = OmsetSalesMerkPDW::where('tahun', $tahun)->whereIn('kodesls', $getSales)->where('bulan', '>=', 7)->where('bulan', '<=', 12)->selectRaw('sum(Omset) as omset, kodesls, Nama, pAgen')->groupBy('kodesls')->groupBy('Nama')->groupBy('pAgen')->get();
					}
				}
			}
			$omset_sales[0] = array(); $omset_sales[1] = array(); $omset_sales[2] = array();
			for ($i = 0; $i < count($sales); $i++) {
				$cek = false; $cek2 = false;
				for ($j = 0; $j < count($omsetsales); $j++) {
					if ($omsetsales[$j]->kodesls == $sales[$i] && $omsetsales[$j]->pAgen == 0) {
						array_push($omset_sales[0], (float)$omsetsales[$j]->omset); $cek = true;
					} else if ($omsetsales[$j]->kodesls == $sales[$i] && $omsetsales[$j]->pAgen == 1) {
						array_push($omset_sales[1], (float)$omsetsales[$j]->omset); $cek2 = true;
					}
					if ($cek && $cek2) break;
				}
				if (!$cek) array_push($omset_sales[0], 0.000);
				if (!$cek2) array_push($omset_sales[1], 0.000);
			}
			for ($i = 0; $i < count($omset_sales[0]); $i++) { array_push($omset_sales[2], $omset_sales[0][$i] + $omset_sales[1][$i]); }
			return $omset_sales;
		}

		public function getDetailInvoiceMerk(Request $req) {
			$tahun1 = $req->input('tahun1'); $smt1 = $req->input('smt1'); $tahun2 = $req->input('tahun2'); $smt2 = $req->input('smt2'); $tahun3 = $req->input('tahun3'); $smt3 = $req->input('smt3');
			$sales = $req->input('sales'); $list_merk = array(); $omset_merk = array(); $omsetmerk1 = ""; $omsetmerk2 = ""; $omsetmerk3 = ""; $cek = false; $listmerk = "";
			if ($tahun2 != "" && $tahun3 != "") {
				if ($req->input('pt') == "SPL") {
					$listmerk = OmsetSalesMerkSPL::whereIn('tahun', [$tahun1, $tahun2, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "SPLS") {
					$listmerk = OmsetSalesMerkSPLS::whereIn('tahun', [$tahun1, $tahun2, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "SML") {
					$listmerk = OmsetSalesMerkSML::whereIn('tahun', [$tahun1, $tahun2, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "MKS") {
					$listmerk = OmsetSalesMerkMKS::whereIn('tahun', [$tahun1, $tahun2, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "MAB") {
					$listmerk = OmsetSalesMerkMAB::whereIn('tahun', [$tahun1, $tahun2, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "PDW") {
					$listmerk = OmsetSalesMerkPDW::whereIn('tahun', [$tahun1, $tahun2, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				}
			} else if ($tahun2 != "") {
				if ($req->input('pt') == "SPL") {
					$listmerk = OmsetSalesMerkSPL::whereIn('tahun', [$tahun1, $tahun2])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "SPLS") {
					$listmerk = OmsetSalesMerkSPLS::whereIn('tahun', [$tahun1, $tahun2])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "SML") {
					$listmerk = OmsetSalesMerkSML::whereIn('tahun', [$tahun1, $tahun2])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "MKS") {
					$listmerk = OmsetSalesMerkMKS::whereIn('tahun', [$tahun1, $tahun2])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "MAB") {
					$listmerk = OmsetSalesMerkMAB::whereIn('tahun', [$tahun1, $tahun2])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "PDW") {
					$listmerk = OmsetSalesMerkPDW::whereIn('tahun', [$tahun1, $tahun2])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				}
			} else if ($tahun3 != "") {
				if ($req->input('pt') == "SPL") {
					$listmerk = OmsetSalesMerkSPL::whereIn('tahun', [$tahun1, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "SPLS") {
					$listmerk = OmsetSalesMerkSPLS::whereIn('tahun', [$tahun1, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "SML") {
					$listmerk = OmsetSalesMerkSML::whereIn('tahun', [$tahun1, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "MKS") {
					$listmerk = OmsetSalesMerkMKS::whereIn('tahun', [$tahun1, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "MAB") {
					$listmerk = OmsetSalesMerkMAB::whereIn('tahun', [$tahun1, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "PDW") {
					$listmerk = OmsetSalesMerkPDW::whereIn('tahun', [$tahun1, $tahun3])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				}
			} else {
				if ($req->input('pt') == "SPL") {
					$listmerk = OmsetSalesMerkSPL::whereIn('tahun', [$tahun1])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "SPLS") {
					$listmerk = OmsetSalesMerkSPLS::whereIn('tahun', [$tahun1])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "SML") {
					$listmerk = OmsetSalesMerkSML::whereIn('tahun', [$tahun1])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "MKS") {
					$listmerk = OmsetSalesMerkMKS::whereIn('tahun', [$tahun1])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "MAB") {
					$listmerk = OmsetSalesMerkMAB::whereIn('tahun', [$tahun1])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				} else if ($req->input('pt') == "PDW") {
					$listmerk = OmsetSalesMerkPDW::whereIn('tahun', [$tahun1])->where('kodesls', $sales)->where('Omset', '>', 0)->select('Merk', 'pAgen')->distinct()->orderBy('pAgen', 'DESC')->orderBy('Merk', 'ASC')->get();
				}
			}
			for ($i = 0; $i < count($listmerk); $i++) { array_push($list_merk, $listmerk[$i]->Merk); }
			if ($req->input('tipe') == 0) {
				if ($req->input('pt') == "SPL") {
					$omsetmerk1 = OmsetSalesMerkSPL::where('tahun', $tahun1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkSPL::where('tahun', $tahun2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkSPL::where('tahun', $tahun3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "SPLS") {
					$omsetmerk1 = OmsetSalesMerkSPLS::where('tahun', $tahun1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkSPLS::where('tahun', $tahun2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkSPLS::where('tahun', $tahun3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "SML") {
					$omsetmerk1 = OmsetSalesMerkSML::where('tahun', $tahun1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkSML::where('tahun', $tahun2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkSML::where('tahun', $tahun3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "MKS") {
					$omsetmerk1 = OmsetSalesMerkMKS::where('tahun', $tahun1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkMKS::where('tahun', $tahun2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkMKS::where('tahun', $tahun3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "MAB") {
					$omsetmerk1 = OmsetSalesMerkMAB::where('tahun', $tahun1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkMAB::where('tahun', $tahun2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkMAB::where('tahun', $tahun3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "PDW") {
					$omsetmerk1 = OmsetSalesMerkPDW::where('tahun', $tahun1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkPDW::where('tahun', $tahun2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkPDW::where('tahun', $tahun3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				}
			} else {
				$start1 = 0; $start2 = 0; $start3 = 0; $end1 = 0; $end2 = 0; $end3 = 0;
				if ($req->input('smt1') == 1) { $start1 = 1; $end1 = 6; } else { $start1 = 7; $end1 = 12; }
				if ($req->input('smt2') == 1) { $start2 = 1; $end2 = 6; } else { $start2 = 7; $end2 = 12; }
				if ($req->input('smt3') == 1) { $start3 = 1; $end3 = 6; } else { $start3 = 7; $end3 = 12; }
				if ($req->input('pt') == "SPL") {
					$omsetmerk1 = OmsetSalesMerkSPL::where('tahun', $tahun1)->where('bulan', '>=', $start1)->where('bulan', '<=', $end1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkSPL::where('tahun', $tahun2)->where('bulan', '>=', $start2)->where('bulan', '<=', $end2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkSPL::where('tahun', $tahun3)->where('bulan', '>=', $start3)->where('bulan', '<=', $end3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "SPLS") {
					$omsetmerk1 = OmsetSalesMerkSPLS::where('tahun', $tahun1)->where('bulan', '>=', $start1)->where('bulan', '<=', $end1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkSPLS::where('tahun', $tahun2)->where('bulan', '>=', $start2)->where('bulan', '<=', $end2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkSPLS::where('tahun', $tahun3)->where('bulan', '>=', $start3)->where('bulan', '<=', $end3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "SML") {
					$omsetmerk1 = OmsetSalesMerkSML::where('tahun', $tahun1)->where('bulan', '>=', $start1)->where('bulan', '<=', $end1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkSML::where('tahun', $tahun2)->where('bulan', '>=', $start2)->where('bulan', '<=', $end2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkSML::where('tahun', $tahun3)->where('bulan', '>=', $start3)->where('bulan', '<=', $end3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "MKS") {
					$omsetmerk1 = OmsetSalesMerkMKS::where('tahun', $tahun1)->where('bulan', '>=', $start1)->where('bulan', '<=', $end1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkMKS::where('tahun', $tahun2)->where('bulan', '>=', $start2)->where('bulan', '<=', $end2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkMKS::where('tahun', $tahun3)->where('bulan', '>=', $start3)->where('bulan', '<=', $end3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "MAB") {
					$omsetmerk1 = OmsetSalesMerkMAB::where('tahun', $tahun1)->where('bulan', '>=', $start1)->where('bulan', '<=', $end1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkMAB::where('tahun', $tahun2)->where('bulan', '>=', $start2)->where('bulan', '<=', $end2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkMAB::where('tahun', $tahun3)->where('bulan', '>=', $start3)->where('bulan', '<=', $end3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				} else if ($req->input('pt') == "PDW") {
					$omsetmerk1 = OmsetSalesMerkPDW::where('tahun', $tahun1)->where('bulan', '>=', $start1)->where('bulan', '<=', $end1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun2 != "") $omsetmerk2 = OmsetSalesMerkPDW::where('tahun', $tahun2)->where('bulan', '>=', $start2)->where('bulan', '<=', $end2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
					if ($tahun3 != "") $omsetmerk3 = OmsetSalesMerkPDW::where('tahun', $tahun3)->where('bulan', '>=', $start3)->where('bulan', '<=', $end3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, Merk')->groupBy('Merk')->get();
				}
			}
			for ($i = 0; $i < count($list_merk); $i++) {
				$cek = false; $omset_merk[$i] = array(); array_push($omset_merk[$i], $list_merk[$i]);
				for ($j = 0; $j < count($omsetmerk1); $j++) {
					if ($omsetmerk1[$j]->Merk == $list_merk[$i]) {
						array_push($omset_merk[$i], (float)$omsetmerk1[$j]->omset); $cek = true;
					}
					if ($cek) break;
				}
				if ($cek == false) array_push($omset_merk[$i], 0.000);
				$cek = false;
				if ($tahun2 != "") {
					for ($j = 0; $j < count($omsetmerk2); $j++) {
						if ($omsetmerk2[$j]->Merk == $list_merk[$i]) {
							array_push($omset_merk[$i], (float)$omsetmerk2[$j]->omset); $cek = true;
						}
						if ($cek) break;
					}
				} else {
					array_push($omset_merk[$i], 0.000);
				}
				if ($cek == false) array_push($omset_merk[$i], 0.000);
				$cek = false;
				if ($tahun3 != "") {
					for ($j = 0; $j < count($omsetmerk3); $j++) {
						if ($omsetmerk3[$j]->Merk == $list_merk[$i]) {
							array_push($omset_merk[$i], (float)$omsetmerk3[$j]->omset); $cek = true;
						}
						if ($cek) break;
					}
					if ($cek == false) array_push($omset_merk[$i], 0.000);
				} else {
					array_push($omset_merk[$i], 0.000);
				}
			}
			return $omset_merk;
		}

		public function getDetailInvoiceBulanan(Request $req) {
			$tahun1 = $req->input('tahun1'); $smt1 = $req->input('smt1'); $tahun2 = $req->input('tahun2'); $smt2 = $req->input('smt2'); $tahun3 = $req->input('tahun3'); $smt3 = $req->input('smt3');
			$bulan1 = []; $bulan2 = []; $bulan3 = [];
			$sales = $req->input('sales'); $omset_bulanan = array(); $omsetth1 = ""; $omsetth2 = ""; $omsetth3 = ""; $cek = false;
			if ($req->input('tipe') == 0) {
				$bulan1 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
				if ($tahun2 != "") $bulan2 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
				if ($tahun3 != "") $bulan3 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
			} else {
				if ($smt1 == 1) { $bulan1 = [1, 2, 3, 4, 5, 6]; } else { $bulan1 = [7, 8, 9, 10, 11, 12]; }
				if ($tahun2 != "") {
					if ($smt2 == 1) { $bulan2 = [1, 2, 3, 4, 5, 6]; } else { $bulan2 = [7, 8, 9, 10, 11, 12]; }
				}
				if ($tahun3 != "") {
					if ($smt3 == 1) { $bulan3 = [1, 2, 3, 4, 5, 6]; } else { $bulan3 = [7, 8, 9, 10, 11, 12]; }
				}
			}
			if ($req->input('pt') == "SPL") {
				$omsetth1 = OmsetSalesMerkSPL::where('tahun', $tahun1)->whereIn('bulan', $bulan1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun2 != "") $omsetth2 = OmsetSalesMerkSPL::where('tahun', $tahun2)->whereIn('bulan', $bulan2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun3 != "") $omsetth3 = OmsetSalesMerkSPL::where('tahun', $tahun3)->whereIn('bulan', $bulan3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
			} else if ($req->input('pt') == "SPLS") {
				$omsetth1 = OmsetSalesMerkSPLS::where('tahun', $tahun1)->whereIn('bulan', $bulan1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun2 != "") $omsetth2 = OmsetSalesMerkSPLS::where('tahun', $tahun2)->whereIn('bulan', $bulan2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun3 != "") $omsetth3 = OmsetSalesMerkSPLS::where('tahun', $tahun3)->whereIn('bulan', $bulan3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
			} else if ($req->input('pt') == "SML") {
				$omsetth1 = OmsetSalesMerkSML::where('tahun', $tahun1)->whereIn('bulan', $bulan1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun2 != "") $omsetth2 = OmsetSalesMerkSML::where('tahun', $tahun2)->whereIn('bulan', $bulan2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun3 != "") $omsetth3 = OmsetSalesMerkSML::where('tahun', $tahun3)->whereIn('bulan', $bulan3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
			} else if ($req->input('pt') == "MKS") {
				$omsetth1 = OmsetSalesMerkMKS::where('tahun', $tahun1)->whereIn('bulan', $bulan1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun2 != "") $omsetth2 = OmsetSalesMerkMKS::where('tahun', $tahun2)->whereIn('bulan', $bulan2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun3 != "") $omsetth3 = OmsetSalesMerkMKS::where('tahun', $tahun3)->whereIn('bulan', $bulan3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
			} else if ($req->input('pt') == "MAB") {
				$omsetth1 = OmsetSalesMerkMAB::where('tahun', $tahun1)->whereIn('bulan', $bulan1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun2 != "") $omsetth2 = OmsetSalesMerkMAB::where('tahun', $tahun2)->whereIn('bulan', $bulan2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun3 != "") $omsetth3 = OmsetSalesMerkMAB::where('tahun', $tahun3)->whereIn('bulan', $bulan3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
			} else if ($req->input('pt') == "PDW") {
				$omsetth1 = OmsetSalesMerkPDW::where('tahun', $tahun1)->whereIn('bulan', $bulan1)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun2 != "") $omsetth2 = OmsetSalesMerkPDW::where('tahun', $tahun2)->whereIn('bulan', $bulan2)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
				if ($tahun3 != "") $omsetth3 = OmsetSalesMerkPDW::where('tahun', $tahun3)->whereIn('bulan', $bulan3)->where('kodesls', $sales)->selectRaw('sum(Omset) as omset, bulan, pAgen')->groupBy('pAgen')->groupBy('bulan')->orderBy('bulan')->get();
			}
			$omset_bulanan[0] = array(); $omset_bulanan[1] = array(); $omset_bulanan[2] = array();
			$omset_bulanan[3] = array(); $omset_bulanan[4] = array(); $omset_bulanan[5] = array();
			$omset_bulanan[6] = array(); $omset_bulanan[7] = array(); $omset_bulanan[8] = array();
			for ($i = 0; $i < count($bulan1); $i++) {
				$cek = false; $cek2 = false;
				for ($j = 0; $j < count($omsetth1); $j++) {
					if ($omsetth1[$j]->bulan == $bulan1[$i] && $omsetth1[$j]->pAgen == 0) {
						array_push($omset_bulanan[0], (float)$omsetth1[$j]->omset); $cek = true;
					} else if ($omsetth1[$j]->bulan == $bulan1[$i] && $omsetth1[$j]->pAgen == 1) {
						array_push($omset_bulanan[1], (float)$omsetth1[$j]->omset); $cek2 = true;
					}
					if ($cek && $cek2) break;
				}
				if ($cek == false) array_push($omset_bulanan[0], 0.000);
				if ($cek2 == false) array_push($omset_bulanan[1], 0.000);
				$cek = false; $cek2 = false;
				if ($tahun2 != "") {
					for ($j = 0; $j < count($omsetth2); $j++) {
						if ($omsetth2[$j]->bulan == $bulan2[$i] && $omsetth2[$j]->pAgen == 0) {
							array_push($omset_bulanan[3], (float)$omsetth2[$j]->omset); $cek = true;
						} else if ($omsetth2[$j]->bulan == $bulan2[$i] && $omsetth2[$j]->pAgen == 1) {
							array_push($omset_bulanan[4], (float)$omsetth2[$j]->omset); $cek2 = true;
						}
						if ($cek && $cek2) break;
					}
					if ($cek == false) array_push($omset_bulanan[3], 0.000);
					if ($cek2 == false) array_push($omset_bulanan[4], 0.000);
				} else {
					array_push($omset_bulanan[3], 0.000); array_push($omset_bulanan[4], 0.000);
				}
				$cek = false; $cek2 = false;
				if ($tahun3 != "") {
					for ($j = 0; $j < count($omsetth3); $j++) {
						if ($omsetth3[$j]->bulan == $bulan3[$i] && $omsetth3[$j]->pAgen == 0) {
							array_push($omset_bulanan[6], (float)$omsetth3[$j]->omset); $cek = true;
						} else if ($omsetth3[$j]->bulan == $bulan3[$i] && $omsetth3[$j]->pAgen == 1) {
							array_push($omset_bulanan[7], (float)$omsetth3[$j]->omset); $cek2 = true;
						}
						if ($cek && $cek2) break;
					}
					if ($cek == false) array_push($omset_bulanan[6], 0.000);
					if ($cek2 == false) array_push($omset_bulanan[7], 0.000);
				} else {
					array_push($omset_bulanan[6], 0.000); array_push($omset_bulanan[7], 0.000);
				}
			}
			for ($i = 0; $i < count($bulan1); $i++) {
				array_push($omset_bulanan[2], $omset_bulanan[0][$i] + $omset_bulanan[1][$i]);
				array_push($omset_bulanan[5], $omset_bulanan[3][$i] + $omset_bulanan[4][$i]);
				array_push($omset_bulanan[8], $omset_bulanan[6][$i] + $omset_bulanan[7][$i]);
			}
			return $omset_bulanan;
		}

}

?>
