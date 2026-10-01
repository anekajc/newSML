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


// use App\Http\Controllers\NewMenuController;

class MasterJabatanController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBJABATAN');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterjabatan' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT KODEJAB, NamaJab FROM DBJABATAN');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT KODEJAB, NamaJab FROM DBJABATAN where KODEJAB = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode Jabatan sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBJABATAN (KODEJAB, NamaJab) values (:kode, :nama)' , ['kode' => $req->kode , 'nama' => $req->nama]);
    return 1;

  }

  public function spDelete (Request $req) {
    // Jabatan yang masih dipakai karyawan tidak boleh dihapus.
    $check = DB::connection('SML')->select('SELECT top 1 1 as ada FROM dbKaryawan where KodeJab = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Jabatan digunakan di Master Karyawan';
    }

    $delete = DB::connection('SML')->update('delete from DBJABATAN where KODEJAB = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBJABATAN set NamaJab = :nama where KODEJAB = :kode' , ['kode' => $req->kode , 'nama' => $req->nama ]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT KODEJAB, NamaJab FROM DBJABATAN where KODEJAB = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
