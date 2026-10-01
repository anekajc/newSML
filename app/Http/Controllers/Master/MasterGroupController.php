<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Models\VWPerkiraan;



// use App\Http\Controllers\NewMenuController;

class MasterGroupController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBGROUP');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastergroup' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBGROUP');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBGROUP where KODEGRP = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode Group sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBGROUP (KODEGRP, NAMA) values (:kode, :nama)' , ['kode' => $req->kode , 'nama' => $req->nama]);
    return 1;

  }

  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBHDGROUP where KODEGRP = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Group digunakkan di Master Headgroup';
    }

    $check = DB::connection('SML')->select('SELECT * FROM DBBARANG where KODEGRP = :kode' , ['kode' => $req->kode]);
    if ($check) {
      return 'Group digunakkan di Master Barang';
    }
    $delete = DB::connection('SML')->update('delete from DBGROUP where KODEGRP = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBGROUP set NAMA = :nama where KODEGRP = :kode' , ['kode' => $req->kode , 'nama' => $req->nama ]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBGROUP where KODEGRP = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
