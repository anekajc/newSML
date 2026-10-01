<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Model\VWPerkiraan;

class MasterValasController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM dbVALAS');

    $akses = app('App\Http\Controllers\GlobalController')->getAkses('' , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
	}

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastervalas' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData,
      "akses" => $akses
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM dbVALAS');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM dbVALAS where KODEVLS = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode valas sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into dbVALAS (KODEVLS, NAMAVLS, KURS, Simbol) values (:kode, :nama, :kurs, :simbol)' , ['kode' => $req->kode , 'nama' => $req->nama, 'kurs' => $req->kurs, 'simbol' => $req->simbol]);
    return 1;

  }

  public function spDelete (Request $req) {
    // Dulu mengecek DBPPL.KDDep (kode departemen - salinan dari master departemen), jadi
    // valas yang sedang dipakai tetap bisa terhapus. Sekarang dicek ke tabel yang memang
    // menyimpan kode valas.
    $pemakai = [
      ['DBPERKIRAAN', 'Valas',   'Perkiraan'],
      ['DBCUSTSUPP',  'KodeVls', 'Customer/Supplier'],
      ['dbTransaksi', 'Valas',   'Transaksi Accounting'],
      ['DBHUTPIUT',   'Valas',   'Hutang/Piutang'],
      ['DBPO',        'KODEVLS', 'Purchase Order'],
      ['DBSO',        'KODEVLS', 'Sales Order'],
    ];
    foreach ($pemakai as $p) {
      $check = DB::connection('SML')->select("SELECT top 1 1 as ada FROM {$p[0]} where {$p[1]} = :kode" , ['kode' => $req->kode]);
      if ($check) {
        return 'Valas digunakan di ' . $p[2];
      }
    }

    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    DB::connection('SML')->update('delete from dbVALAS where KODEVLS = :kode' , ['kode' => $req->kode ]);
    return 1;
  }

  public function spEdit (Request $req) {
    // update() mengembalikan jumlah baris; 0 (kode tidak ditemukan) dulu tampil sebagai
    // peringatan "0" tanpa penjelasan.
    $edit = DB::connection('SML')->update('update dbVALAS set NAMAVLS = :nama, KURS = :kurs, Simbol = :simbol where KODEVLS = :kode' , ['kode' => $req->kode , 'nama' => $req->nama , 'kurs' => $req->kurs , 'simbol' => $req->simbol ]);

    return $edit ? 1 : 'Kode valas tidak ditemukan';
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM dbVALAS where KODEVLS = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
