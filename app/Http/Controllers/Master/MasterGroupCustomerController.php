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

class MasterGroupCustomerController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBGROUPCUSTSUPP');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastergroupcustomer' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBGROUPCUSTSUPP');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBGROUPCUSTSUPP where KODEGROUPCUSTSUPP = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode group sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBGROUPCUSTSUPP (KODEGROUPCUSTSUPP, NAMAGROUPCUSTSUPP) values (:kode, :nama)' , ['kode' => $req->kode , 'nama' => $req->nama]);
    return 1;

  }

  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeArea = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Area digunakkan di Master Kota';
    // }
    $check = DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where Agent = :kode' , ['kode' => $req->kode]);
    if ($check) {
      return 'Jenis digunakkan di Master Supplier';
    }

    $delete = DB::connection('SML')->update('delete from DBGROUPCUSTSUPP where KODEGROUPCUSTSUPP = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBGROUPCUSTSUPP set NAMAGROUPCUSTSUPP = :nama where KODEGROUPCUSTSUPP = :kode' , ['kode' => $req->kode , 'nama' => $req->nama ]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBGROUPCUSTSUPP where KODEGROUPCUSTSUPP = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
