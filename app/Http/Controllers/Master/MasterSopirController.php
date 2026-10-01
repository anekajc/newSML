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

class MasterSopirController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBSOPIR');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastersopir' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBSOPIR');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBSOPIR where KODESOPIR = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode Data sudah ada di database';
    }
    $listData = DB::connection('SML')->insert('insert into DBSOPIR (KODESOPIR, NAMASOPIR, KODESG, IsAktif) VALUES (?, ?, ?, ?)', [$req->kode, '', $req->kodecost, $req->nama]);

    return 1;

  }

  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeArea = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Area digunakkan di Master Kota';
    // }

    $delete = DB::connection('SML')->update('delete from DBSOPIR where KODESOPIR = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit(Request $req) {
    $edit = DB::connection('SML')->update('UPDATE DBSOPIR SET IsAktif = :nama, KODESG = :kodecost WHERE KODESOPIR = :kode', [
        'kode' => $req->kode,
        'nama' => $req->nama,
        'kodecost' => $req->kodecost
    ]);

    return $edit;
}

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBSOPIR where KODESOPIR = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function selectKodeCost(Request $req) {
      $detail = DB::connection('SML')->select('SELECT KodeCost AS kodecost, NamaCost AS namacost from DBCOST');
      return response()->json($detail);
  }



}
