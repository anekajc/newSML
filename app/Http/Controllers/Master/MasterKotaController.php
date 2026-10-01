<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class MasterKotaController extends Controller
{

  public function index(Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBKOTA');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterkota' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function spListArea () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBAREA');
    return $listData;

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBKOTA');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeKota = :kodekota' , ['kodekota' => $req->kodekota]);

    if ($check) {
      return 'Kode kota sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBKOTA (KodeArea, NamaKota, KodeKota) values (:kodearea, :namakota, :kodekota)' , ['kodearea' => $req->kodearea , 'namakota' => $req->namakota, 'kodekota' => $req->kodekota]);
    return 1;

  }

  public function spDelete (Request $req) {

    $check = DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where Kota = :kodekota' , ['kodekota' => $req->kodekota]);

    if ($check) {
      return 'Kota digunakkan di Master Supplier';
    }

    $delete = DB::connection('SML')->update('delete from DBKOTA where KodeKota = :kodekota' , ['kodekota' => $req->kodekota ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBKOTA set NamaKota= :namakota , KodeArea= :kodearea where KodeKota = :kodekota' , ['namakota' => $req->namakota , 'kodearea' => $req->kodearea , 'kodekota' => $req->kodekota  ]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeKota = :kodekota' , ['kodekota' => $req->kodekota]);
    return $detail;
  }



}
