<?php

namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Model\VWPerkiraan;



// use App\Http\Controllers\NewMenuController;

class MasterNilaiPPNController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBNilaiPPN');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masternilaippn' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBNilaiPPN');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBNilaiPPN where Urut = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode Data sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBNilaiPPN (Urut, TglAwal, TglAkhir, NilaiPPN) values (:kode, :tglAwal, :tglAkhir, :PPN)' , ['kode' => $req->kode ,'tglAwal' => $req->tglAwal, 'tglAkhir' =>$req->tglAkhir, 'PPN' => $req->PPN]);
    return 1;

  }

  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeArea = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Area digunakkan di Master Kota';
    // }
    $delete = DB::connection('SML')->update('delete from DBNilaiPPN where Urut = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBNilaiPPN set TglAwal = :tglAwal, TglAkhir = :tglAkhir, NilaiPPN = :PPN where Urut = :kode' , ['kode' => $req->kode , 'tglAwal' => $req->tglAwal , 'tglAkhir' =>$req->tglAkhir, 'PPN'=>$req->PPN]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBNilaiPPN where Urut = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function spDefaultUrut(Request $req) {
      $detail = DB::connection('SML')->select('SELECT MAX(Urut) AS highestUrut FROM DBNilaiPPN');
      return response()->json($detail[0]);
  }



}
