<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Model\Menu;
use App\Model\User;
use App\Model\BahanBarang;
use App\Model\StockBarang;
use App\Model\BahanBarang2;
use App\Model\Periode;
use App\Model\AksesMenu;

class SearchController extends Controller {

	public function index(Request $req) {
		User::where('id', \Auth::id())->update(['status' => 1, 'hostid' => gethostbyaddr(\Request::ip()), 'ipaddress' => \Request::ip()]);
		$checkUser = User::where('id', \Auth::id())->first();
		if ($checkUser->level == 3 && $checkUser->username == 'SA') {
			$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		}else{
			$menu = Menu::join('akses_menu', 'menu.id', '=', 'akses_menu.id_menu')->where('akses_menu.tampil', 1)->where('menu.grup','>', 0)->where('menu.show_acc', 1)->where('akses_menu.id_user', \Auth::id())->select('menu.*')->get();
		}
		$periode = Periode::where('id_user', \Auth::id())->first();
		return view('search')->with('menu', $menu)->with('periode', $periode);
	}

	public function searchBarang(Request $req) {
		$term = htmlspecialchars($req->input('term'), ENT_QUOTES);
		if ($req->has('grup') && $req->input('grup') != "-") {
			$data = BahanBarang::where('grup', $req->input('grup'))->where(function ($query) use ($term) {
				$query->where('kode', 'LIKE', $term.'%')->orWhere('nama', 'LIKE', $term.'%');
			})->where('deleted', 0)->select('kode', 'nama')->get();
			foreach ($data as $row) {
			  $row->label = htmlspecialchars_decode($row->kode.' - '.$row->nama);
			}
			return response()->json($data);
		} else {
			$data = BahanBarang::where('kode', 'LIKE', $term.'%')->orWhere('nama', 'LIKE', $term.'%')->where('deleted', 0)->select('kode', 'nama')->get();
			foreach ($data as $row) {
			  $row->label = htmlspecialchars_decode($row->kode.' - '.$row->nama);
			}
			return response()->json($data);
		}
	}

	public function searchBarangProduksi(Request $req) {
		$term = htmlspecialchars($req->input('term'), ENT_QUOTES);
		$data = BahanBarang::whereIn('grup', ['BB', 'WIP'])->where(function ($query) use ($term) {
			$query->where('kode', 'LIKE', $term.'%');
		})->where('deleted', 0)->select('kode', 'nama')->get();
		foreach ($data as $row) {
		  $row->label = htmlspecialchars_decode($row->kode.' - '.$row->nama);
		}
		return response()->json($data);
	}

	public function searchBarangGudang(Request $req) {
		$term = htmlspecialchars($req->input('term'), ENT_QUOTES);
		$periode = Periode::where('id_user', \Auth::id())->first();
		$id = StockBarang::where('gudang', $req->input('gudang'))->where('bulan', $periode->bulan)->where('tahun', $periode->tahun)->groupBy('kode_barang')->selectRaw('max(id)')->get()->toArray();
		if ($req->has('grup') && $req->input('grup') != "-") {
			$data = StockBarang::join('bahanbarang', 'bahanbarang.kode', '=', 'stockbrg.kode_barang')->whereIn('stockbrg.id', $id)->where('bahanbarang.grup', $req->input('grup'))->where(function ($query) use ($term) {
				$query->where('bahanbarang.kode', 'LIKE', $term.'%');
			})->where('stockbrg.akhir_qty', '>', 0)->where('deleted', 0)->select('bahanbarang.kode', 'bahanbarang.nama', 'stockbrg.akhir_qty')->get();
			foreach ($data as $row) {
			  $row->label = htmlspecialchars_decode($row->kode.' - '.$row->nama);
			}
			return response()->json($data);
		} else {
			$data = StockBarang::join('bahanbarang', 'bahanbarang.kode', '=', 'stockbrg.kode_barang')->whereIn('stockbrg.id', $id)->where(function ($query) use ($term) {
				$query->where('bahanbarang.kode', 'LIKE', $term.'%');
			})->where('stockbrg.akhir_qty', '>', 0)->where('deleted', 0)->select('bahanbarang.kode', 'bahanbarang.nama', 'stockbrg.akhir_qty')->get();
			foreach ($data as $row) {
			  $row->label = htmlspecialchars_decode($row->kode.' - '.$row->nama);
			}
			return response()->json($data);
		}
	}

}

?>
