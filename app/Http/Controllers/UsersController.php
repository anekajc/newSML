<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Model\Menu;
use App\Model\User;
use App\Model\AksesMenu;
use App\Model\AksesGudang;
use App\Model\AksesPerkiraan;
use App\Model\AksesLaporanPerkiraan;
use App\Model\Periode;
use App\Model\Gudang;
use App\Model\Perkiraan;

class UsersController extends Controller {

	public function index() {
		$getID = Menu::where('access', '/setpemakai')->first();
		$check = AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getID->id)->first();
		if ($check->tampil == 1) {
			User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
			$checkUser = User::where('id', \Auth::id())->first();
  		if ($checkUser->level == 3 && $checkUser->username == 'SA') {
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
			}else{
        $menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
			}
			$users = User::where('level','<', 3)->where('deleted', 0)->get();
			$periode = Periode::where('id_user', \Auth::id())->first();
			$allmenu = Menu::where('grup','>', 0)->where('show_acc', 1)->get();
			$allgudang = Gudang::where('deleted', 0)->get();
			$allperkiraan = Perkiraan::where('deleted', 0)->orderBy('kode', 'ASC')->where('tipe', 1)->get();
			return view("setpemakai")->with('menu', $menu)->with('user', $users)->with('allmenu', $allmenu)->with('periode', $periode)->with('akses', $check)->with('allgudang', $allgudang)->with('allperkiraan', $allperkiraan);
		} else {
			return redirect('/home');
		}
	}

	public function loadAll() {
		return User::where('level','<', 3)->where('deleted', '=', 0)->get();
	}

	public function add(Request $req) {
		$check = User::where('username', $req->input('username'))->where('deleted', 0)->count();
		if ($check == 0) {
			$newUser = User::create([
				'name' => $req->input('fullname'),
	      'username' => $req->input('username'),
				'password' => Hash::make($req->input('password')),
				'level' => $req->input('level'),
				'hostid' => "",
				'ipaddress' => ""
	    ]);
			$menu = Menu::all();
			foreach($menu as $row){
				AksesMenu::create([
					'id_user' => $newUser->id,
					'id_menu' => $row->id
				]);
			}
			$gudang = Gudang::where('deleted', 0)->get();
			foreach($gudang as $row){
				AksesGudang::create([
					'id_user' => $newUser->id,
					'id_gudang' => $row->id,
					'akses' => 0
				]);
			}
			$perkiraan = Perkiraan::where('deleted', 0)->where('tipe', 1)->get();
			foreach($perkiraan as $row){
				AksesPerkiraan::create([
					'id_user' => $newUser->id,
					'id_perkiraan' => $row->id,
					'akses' => 0
				]);
				AksesLaporanPerkiraan::create([
					'id_user' => $newUser->id,
					'id_perkiraan' => $row->id,
					'akses' => 0
				]);
			}
			Periode::create([
				'id_user' => $newUser->id,
				'bulan' => (int)date('m'),
				'tahun' => (int)date('Y')
			]);
			return 1;
		} else {
			return 0;
		}
	}

	public function edit(Request $req) {
		$check = User::where('username', $req->input('username'))->where('deleted', 0)->where('id', '!=', $req->input('id'))->count();
		if ($check == 0) {
			User::where('id', $req->input('id'))->update(['name' => $req->input('fullname'), 'username' => $req->input('username'), 'level' => $req->input('level')]);
			return 1;
		} else {
			return 0;
		}
	}

	public function erase(Request $req) {
		User::where('id', $req->input('id'))->delete();
	}

	public function getAkses(Request $req) {
		return AksesMenu::join('menu', 'menu.id', '=', 'akses_menu.id_menu')->where('menu.grup','>', 0)->where('akses_menu.id_user', $req->input('id'))->selectRaw('akses_menu.*')->get();
	}

	public function getAksesGudang(Request $req) {
		return AksesGudang::where('id_user', $req->input('id'))->get();
	}

	public function getAksesPerkiraan(Request $req) {
		return AksesPerkiraan::where('id_user', $req->input('id'))->get();
	}

	public function getAksesLaporanPerkiraan(Request $req) {
		return AksesLaporanPerkiraan::where('id_user', $req->input('id'))->get();
	}

	public function editAkses(Request $req) {
		$data = $req->input('data');
		for ($i = 0; $i < count($data); $i++) {
			AksesMenu::where('id_user', $req->input('id'))->where('id_menu', $i + 1)
				->update(['tampil' => $data[$i]['tampil'],
									'tambah' => $data[$i]['tambah'],
									'koreksi' => $data[$i]['koreksi'],
									'hapus' => $data[$i]['hapus'],
									'cetak' => $data[$i]['cetak'],
									'export' => $data[$i]['export'],
									'otorisasi1' => $data[$i]['otorisasi1'],
									'otorisasi2' => $data[$i]['otorisasi2'],
									'otorisasi3' => $data[$i]['otorisasi3'],
									'otorisasi4' => $data[$i]['otorisasi4'],
									'otorisasi5' => $data[$i]['otorisasi5'],
									'batalotorisasi' => $data[$i]['batalotorisasi'],
									'bataltransaksi' => $data[$i]['bataltransaksi']]);
		}
	}

	public function editAksesGudang(Request $req) {
		$data = $req->input('data');
		for ($i = 0; $i < count($data); $i++) {
			$cek = AksesGudang::where('id_user', $req->input('id'))->where('id_gudang', $data[$i][0])->count();
			if ($cek > 0) {
				AksesGudang::where('id_user', $req->input('id'))->where('id_gudang', $data[$i][0])->update(['akses' => $data[$i][1]]);
			} else {
				AksesGudang::create(['id_user' => $req->input('id'), 'id_gudang' => $data[$i][0], 'akses' => $data[$i][1]]);
			}
		}
	}

	public function editAksesPerkiraan(Request $req) {
		$data = $req->input('data');
		for ($i = 0; $i < count($data); $i++) {
			$cek = AksesPerkiraan::where('id_user', $req->input('id'))->where('id_perkiraan', $data[$i][0])->count();
			if ($cek > 0) {
				AksesPerkiraan::where('id_user', $req->input('id'))->where('id_perkiraan', $data[$i][0])->update(['akses' => $data[$i][1]]);
			} else {
				AksesPerkiraan::create(['id_user' => $req->input('id'), 'id_perkiraan' => $data[$i][0], 'akses' => $data[$i][1]]);
			}
		}
	}

	public function editAksesLaporanPerkiraan(Request $req) {
		$data = $req->input('data');
		for ($i = 0; $i < count($data); $i++) {
			$cek = AksesLaporanPerkiraan::where('id_user', $req->input('id'))->where('id_perkiraan', $data[$i][0])->count();
			if ($cek > 0) {
				AksesLaporanPerkiraan::where('id_user', $req->input('id'))->where('id_perkiraan', $data[$i][0])->update(['akses' => $data[$i][1]]);
			} else {
				AksesLaporanPerkiraan::create(['id_user' => $req->input('id'), 'id_perkiraan' => $data[$i][0], 'akses' => $data[$i][1]]);
			}
		}
	}

	public function checkOnline(Request $req) {
		$user = User::where('username', $req->input('username'))->where('deleted', 0)->first();
		return $user->status;
	}

	public function offline(Request $req) {
		User::where('id', $req->input('id'))->update(['status' => 0, 'hostid' => '', 'ipaddress' => '']);
	}

	public function getAksesByMenu(Request $req) {
		$getMenu = Menu::where('access', $req->input('menu'))->first();
		return AksesMenu::where('id_user', \Auth::id())->where('id_menu', $getMenu->id)->first();
	}
}

?>
