<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FunctionBrowseController extends Controller {

	private function doSetResult($table, $kolom, $title) {
		/*
			Format kolom:
			[kolom_db, header_kolom, tipe_data, jumlah_desimal]
		*/

	    return [
	        'table' => $table,
	        'kolom' => $kolom,
	        'title' => $title
	    ];
	}
	
	public function doBrowseGudang(Request $req) {
		$table = DB::connection('SML')->select('select KODEGDG, NAMA from DBGUDANG');
		$kolom = [
                    ['KODEGDG', 'Kode', 'varchar', 0],
                    ['NAMA', 'Nama', 'varchar', 0]
                 ];
        $title = "Gudang";

		return $this->doSetResult($table, $kolom, $title);
	}

	public function doLoadGudang(Request $req) {
		$kode = $req->query('kode');
		$table = DB::connection('SML')->select('select KODEGDG, NAMA from DBGUDANG where KODEGDG = ?', [$kode]);

		return $table;
	}

	public function doBrowseHdGroup(Request $req) {
		$table = DB::connection('SML')->select('select KODEHDGRP, NAMAHDGRP from DBHDGROUP');
		$kolom = [
                    ['KODEHDGRP', 'Kode', 'varchar', 0],
                    ['NAMAHDGRP', 'Nama', 'varchar', 0]
                 ];
        $title = "Grup";

		return $this->doSetResult($table, $kolom, $title);
	}

	public function doBrowseSubGroup(Request $req) {
		$table = DB::connection('SML')->select('select KodeSubGrp, NamaSubGrp from dbSubGroup order by NamaSubGrp');
		$kolom = [
                    ['KodeSubGrp', 'Kode', 'varchar', 0],
                    ['NamaSubGrp', 'Nama', 'varchar', 0]
                 ];
        $title = "Kategori";

		return $this->doSetResult($table, $kolom, $title);
	}

	public function doBrowseSubGroupJnsTambah(Request $req) {
		$table = DB::connection('SML')->select('select KodeSubGrp, Keterangan from DBSubGroupJnsTambah');
		$kolom = [
                    ['KodeSubGrp', 'Kode', 'varchar', 0],
                    ['Keterangan', 'Nama', 'varchar', 0]
                 ];
        $title = "Sub Kategori";

		return $this->doSetResult($table, $kolom, $title);
	}

	public function doBrowseMerk(Request $req) {
		$table = DB::connection('SML')->select('select KODEMERK, NAMAMERK from DBMERK order by NAMAMERK');
		$kolom = [
                    ['KODEMERK', 'Kode', 'varchar', 0],
                    ['NAMAMERK', 'Nama', 'varchar', 0]
                 ];
        $title = "Merk";

		return $this->doSetResult($table, $kolom, $title);
	}

	public function doBrowseBarang(Request $req) {
		$filter = $req->query('filter');

		if (!$filter) {
	        $table = DB::connection('SML')->select('select KODEBRG, NAMABRG from DBBARANG order by KODEBRG');
	    } else {
	        $query = "select KODEBRG, NAMABRG from DBBARANG where KODEBRG like ? or NAMABRG like ? order by KODEBRG";
	        $params = ["%$filter%", "%$filter%"];

	        $table = DB::connection('SML')->select($query, $params);
		}

		$kolom = [
                    ['KODEBRG', 'Kode', 'varchar', 0],
                    ['NAMABRG', 'Nama', 'varchar', 0]
                 ];
        $title = "Barang";

		return $this->doSetResult($table, $kolom, $title);
	}

	public function doLoadBarang(Request $req) {
		$kode = $req->query('kode');
		$table = DB::connection('SML')->select('select * from DBBARANG where KODEBRG = ?', [$kode]);

		return $table;
	}

	public function doLoadMLokasi(Request $req) {
		$kode = $req->query('kode');
		$table = DB::connection('SML')->select('select mlokasi.* from DBMLOKASI mlokasi left outer join DBLOKASI lokasi on mlokasi.KODELOKASI = lokasi.lokasi left outer join DBBARANG brg ON brg.MLOKASI = lokasi.LOKASI where brg.KODEBRG = ?', [$kode]);

		return $table;
	}
}
