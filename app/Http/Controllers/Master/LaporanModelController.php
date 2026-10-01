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

class LaporanModelController extends Controller {
	use ReportTrait;

	public function index() {
		$getID = Menu::where('access', '/laporanmodel')->first();
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
			$select = 'kode, nama'; $where = 'deleted = 0'; $order = 'kode ASC';
			$col = ['kode', 'nama']; $len = [100, 200]; $count = []; $header = ['Kode', 'Nama'];
			$table = $this->loadQuery('model', $select, $where, $order, '-', $col, $len, $count, $header);
			$kolom = $this->columnReport('report_model');
			$fetch = $this->getColumnReport('report_model');
			$title = 'Laporan Model Bahan Baku';
			$halaman = 'Model Bahan Baku';
			$breadcrumb = 'Laporan / Master Data / Master Bahan, Barang & Jasa / Master Bahan Baku / Model';
			$settingtgl = 0;

			return view("laporan")->with('menu', $menu)->with('table', $table)->with('periode', $periode)->with('akses', $check)->with('kolom', $kolom)->with('fetch', $fetch)->with('col', $col)->with('header', $header)
				->with('title', $title)->with('halaman', $halaman)->with('breadcrumb', $breadcrumb)->with('settingtgl', $settingtgl);
		} else {
			return redirect('/home');
		}
	}

	public function loadAll(Request $req) {
		$fetch = $req->input("fetch");
		$kolom = $req->input("kolom");
		$urut = $req->input("urut");
		$this->setColumnReport('report_model', $fetch);

		$select = 'kode, nama'; $where = 'deleted = 0'; $order = 'kode ASC';
		$col = ['kode', 'nama']; $len = [100, 200]; $count = []; $header = ['Kode', 'Nama'];
		$table = $this->loadQuery('model', $select, $where, $order, '-', $col, $len, $count, $header);
		return $table;
	}
}

?>
