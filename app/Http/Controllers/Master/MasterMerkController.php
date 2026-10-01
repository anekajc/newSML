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

class MasterMerkController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastermerk' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBMERK where KODEMERK = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode Merk sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBMERK (KODEMERK, NAMAMERK , pAgen) values (:kode, :nama , :isagen)' , ['kode' => $req->kode , 'nama' => $req->nama , 'isagen' => $req->isagen]);
    return 1;

  }

  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBBARANG where KodeMerk = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Merk digunakkan di Master Barang';
    }

    // $check = DB::connection('SML')->select('SELECT * FROM DBMERK where JenisCustSupp = :kode' , ['kode' => $req->kode]);
    // if ($check) {
    //   return 'Jenis digunakkan di Master Supplier';
    // }
    $delete = DB::connection('SML')->update('delete from DBMERK where KODEMERK = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBMERK set NAMAMERK = :nama , pAgen = :isagen where KODEMERK = :kode' , ['kode' => $req->kode , 'isagen' => $req->isagen , 'nama' => $req->nama ]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBMERK where KODEMERK = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
