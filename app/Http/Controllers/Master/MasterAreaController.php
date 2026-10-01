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

class MasterAreaController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBAREA');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterarea' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBAREA');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBAREA where KODEAREA = :kodearea' , ['kodearea' => $req->kodearea]);

    if ($check) {
      return 'Kode area sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBAREA (KODEAREA, NAMAAREA) values (:kodearea, :namaarea)' , ['kodearea' => $req->kodearea , 'namaarea' => $req->namaarea]);
    return 1;

  }

  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeArea = :kodearea' , ['kodearea' => $req->kodearea]);

    if ($check) {
      return 'Area digunakkan di Master Kota';
    }

    $delete = DB::connection('SML')->update('delete from DBAREA where KODEAREA = :kodearea' , ['kodearea' => $req->kodearea ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBAREA set NAMAAREA = :namaarea where KODEAREA = :kodearea' , ['kodearea' => $req->kodearea , 'namaarea' => $req->namaarea ]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBAREA where KODEAREA = :kodearea' , ['kodearea' => $req->kodearea]);
    return $detail;
  }



}
