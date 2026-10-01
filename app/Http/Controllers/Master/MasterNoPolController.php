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

class MasterNoPolController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBKENDARAAN');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masternopol' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBKENDARAAN');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBKENDARAAN where KODEKEND = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode Data sudah ada di database';
    }
    $listData = DB::connection('SML')->insert('insert into DBKENDARAAN (KODEKEND, KODEJENISKEND, NAMAKEND, NOCHASIS, MERKKEND, IsAktif, KodeCost ) VALUES (?, ?, ?, ?, ?, ?, ?)', [$req->kode, '', $req->nopol, $req->nochasis, $req->merk, $req->nama, $req->kodecost]);

    return 1;

  }

  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeArea = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Area digunakkan di Master Kota';
    // }

    $delete = DB::connection('SML')->update('delete from DBKENDARAAN where KODEKEND = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit(Request $req) {
    $edit = DB::connection('SML')->update('UPDATE DBKENDARAAN SET NAMAKEND = :nopol, MERKKEND = :merk, IsAktif = :nama, KodeCost = :kodecost, KODEJENISKEND = \'\' WHERE KODEKEND = :kode', [
        'kode' => $req->kode,
        'nopol' => $req->nopol,
        'merk' => $req->merk,
        'nama' => $req->nama,
        'kodecost' => $req->kodecost
    ]);

    return $edit;
}

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBKENDARAAN where KODEKEND = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function selectKodeCost(Request $req) {
      $detail = DB::connection('SML')->select('SELECT KodeCost AS kodecost, NamaCost AS namacost from DBCOST');
      return response()->json($detail);
  }



}
