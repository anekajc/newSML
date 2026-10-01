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

class MasterNomorFakturPajakController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBNomorFP');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masternomorfakturpajak' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBNomorFP');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBNomorFP where Kode = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode faktur pajak sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBNomorFP (Kode, NoSeri, NoAwal, NoAkhir, IsPenuh) values (:kode, :nama, :noawal, :noakhir, :isPenuh)' , ['kode' => $req->kode , 'nama' => $req->nama, 'noawal' => $req->noawal, 'noakhir' => $req->noakhir, 'isPenuh'=>$req->isPenuh]);
    return 1;

  }

  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeArea = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Area digunakkan di Master Kota';
    // }

    $delete = DB::connection('SML')->update('delete from DBNomorFP where Kode = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBNomorFP set NoSeri = :nama, NoAwal = :noawal, NoAkhir = :noakhir, isPenuh = :isPenuh where Kode = :kode' , ['kode' => $req->kode , 'nama' => $req->nama, 'noawal' => $req->noawal, 'noakhir' => $req->noakhir, 'isPenuh'=>$req->isPenuh]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBNomorFP where Kode = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function spDefaultUrut(Request $req) {
      $detail = DB::connection('SML')->select('SELECT MAX(Kode) AS highestUrut FROM DBNomorFP');
      return response()->json($detail[0]);
  }

  public function updateIsPenuh(Request $request)
  {
      $update = DB::connection('SML')->update('update DBNomorFP set IsPenuh = :isPenuh where Kode = :kode', ['isPenuh' => $request->isPenuh, 'kode' => $request->kode]);

      return $update;
  }


}
