<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

trait GlobalTrait {
	public function coba() {
		return 1;
	}

	public function getQuerySP($param, $query) {
		$counter = count($param);
		$query .= " ";
    	while($counter != 0) { $query .= '?'; $query .= ($counter-1 != 0) ? ',' : ''; $counter--; }
    	return $query;
	}

	public function getDummyRequest() {
		// untuk bypass loadall pertama kali untuk menu yang punya filter tabel
    	return request()->merge(['dummy' => '']);
	}

	public function getNow($time = false) {
		return ($time) ? Carbon::now()->toDateTimeString() : Carbon::now()->toDateString();
	}

	public function getHari($date) {
		return Str::substr($date, 8, 2);
	}

	public function getBulan($date) {
		return Str::substr($date, 5, 2);
	}

	public function getTahun($date, $short = false) {
		return ($short) ? Str::substr($date, 2, 2) : Str::substr($date, 0, 4);
	}

	public function getJam($datetime) {
		return Str::substr($datetime, 11, 2);
	}

	public function getMenit($datetime) {
		return Str::substr($datetime, 14, 2);
	}

	public function getDetik($datetime) {
		return Str::substr($datetime, 17, 4);
	}

	public function getTimeStamp() {
		return Carbon::now()->timestamp;
	}

	public function createTempTable($table, $delColumn) {
		// Untuk select semua kolom kecuali kolom tertentu
		// Ada kolom yang tidak kompetible di program web seperti kolom MyID
		DB::connection('SML')->update('select * into temp_' . $table . ' from ' . $table);
    	DB::connection('SML')->update('alter table temp_' . $table . ' drop column ' . $delColumn);
	}

	public function dropTempTable($table) {
    	DB::connection('SML')->update('drop table temp_' . $table);
	}

	public function setNumber($hasilQuery, $property, $desimal = 0) {
		return (count($hasilQuery) > 0) ? number_format($hasilQuery[0]->{$property}, $desimal) : number_format(0, $desimal);
	}

	public function getNumber($hasilQuery, $property) {
		return (count($hasilQuery) > 0) ? $hasilQuery[0]->{$property} : 0;
	}

	public function doAddDBNOMORPK ($tipe, $nb, $user, $bulan, $tahun, $flagtipe = 0) {
		DB::beginTransaction();

		try {
			$flagQuery = 'insert DBNOMORPK';
			DB::connection('SML')->update('insert into DBNOMORPK (Tipe, NOBUKTI, USERID, Bulan, Tahun, flagtipe) values (:tipe, :nb, :user, :bulan, :tahun, :flagtipe)' , ['tipe' => "POS", 'nb' => $nb, 'user' => $user, 'bulan' => $bulan, 'tahun' => $tahun, 'flagtipe' => $flagtipe]);

			DB::commit();
			return "S";
		} catch (\Exception $e) {
		    DB::rollback();
		    return "E".";;".$flagQuery.";;".$e;
		}

	}

	public function doDeleteDBNOMORPK ($tipe, $user) {
		DB::beginTransaction();

		try {
			$flagQuery = 'delete DBNOMORPK';
			DB::connection('SML')->update('delete from DBNOMORPK where Tipe = :tipe and USERID = :user' , ['tipe' => "POS", 'user' => $user]);

			DB::commit();
			return "S";
		} catch (\Exception $e) {
		    DB::rollback();
		    return "E".";;".$flagQuery.";;".$e;
		}

	}
  
}
