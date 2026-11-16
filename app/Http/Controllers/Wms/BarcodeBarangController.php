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
use App\Models\VWBarcodeBarang;

class BarcodeBarangController extends Controller {

	public function index() {


			$kodemenu = '210103';
	    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
	    if(!$akses || !$akses->HASACCESS) {
	       return redirect('/home');
	    }


			//$gudang = 	VWBarcodeBarang::where('KODEGRP', 'BJ');
			//$gudang = GudangSQL::All();
			// Diubah 2026-10-20 (lemot): seluruh barang (±19 ribu baris) tidak lagi dirender di HTML -
			// tabel diisi per halaman lewat dataTabel().
			// $gudang = VWBarcodeBarang::All();
			$gudang = collect();
			$periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
			return view("wms.barcodebarang")
			->with('gudang', $gudang)
			->with('periode', $periode)
			->with('akses', $akses)
			// ->with('akses', $check)
			;

	}

	public function loadAll() {
		return 	VWBarcodeBarang::All();
		//return GudangSQL::All();

	}

	// Ditambahkan 2026-10-20: data tabel Barcode Barang per halaman untuk DataTables (serverSide) -
	// cari, urut, dan Tampilkan dikerjakan di SQL (ROW_NUMBER, SQL Server 2008 R2 tanpa OFFSET).
	public function dataTabel(Request $req) {
		$kolomUrut = [0 => 'KODEBRG', 1 => 'NAMABRG'];
		$urut = $req->input('order.0', []);
		$kolom = $kolomUrut[(int) ($urut['column'] ?? 0)] ?? 'KODEBRG';
		$arah = strtolower($urut['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

		$where = '1 = 1';
		$bind = [];
		$cari = trim((string) $req->input('search.value', ''));
		if ($cari !== '') {
			$pola = '%' . str_replace(['[', '%', '_'], ['[[]', '[%]', '[_]'], $cari) . '%';
			$where = 'KODEBRG like cast(? as varchar(200)) or NAMABRG like cast(? as varchar(200))';
			$bind = [$pola, $pola];
		}

		$db = DB::connection('SML');
		$total = (int) $db->selectOne('select count(*) as n from vwDBBarang')->n;
		$tersaring = $cari === '' ? $total : (int) $db->selectOne("select count(*) as n from vwDBBarang where $where", $bind)->n;

		$mulai = max(0, (int) $req->input('start', 0));
		$panjang = (int) $req->input('length', 10);
		$sql = "select KODEBRG, NAMABRG from (select row_number() over (order by $kolom $arah, KODEBRG asc) as rn_urut, KODEBRG, NAMABRG"
			. " from vwDBBarang where $where) x";
		if ($panjang > 0) {
			$sql .= ' where rn_urut between ? and ?';
			$bind[] = $mulai + 1;
			$bind[] = $mulai + $panjang;
		}
		$sql .= ' order by rn_urut';

		return [
			'draw' => (int) $req->input('draw', 0),
			'recordsTotal' => $total,
			'recordsFiltered' => $tersaring,
			'data' => $db->select($sql, $bind),
		];
	}





}

?>
