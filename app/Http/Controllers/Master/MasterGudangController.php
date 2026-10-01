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

class MasterGudangController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT KODEGDG, NAMA, Alamat, istakeinout, pSampit, pPusat, IsAktif FROM DBGUDANG');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastergudang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT KODEGDG , NAMA , Alamat, istakeinout, pSampit , pPusat FROM DBGUDANG');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBGUDANG where KODEGDG = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode Gudang sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBGUDANG (KODEGDG, NAMA, Alamat, istakeinout, pSampit , pPusat, IsAktif) values (:kode, :nama , :alamat, :issample , :issampit , :ispusat, :isAktif )' , ['kode' => $req->kode , 'nama' => $req->nama , 'alamat' => $req->alamat , 'issample' => $req->issample , 'issampit' => $req->issampit , 'ispusat' => $req->ispusat, 'isAktif' => $req->isAktif  ]);
    return 1;

  }

  public function spDelete (Request $req) {


    // $check = DB::connection('SML')->select('SELECT * FROM DBCUSTSUPP where JenisCustSupp = :kode' , ['kode' => $req->kode]);
    // if ($check) {
    //   return 'Jenis digunakkan di Master Supplier';
    // }
    $delete = DB::connection('SML')->update('delete from DBGUDANG where KODEGDG = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBGUDANG set NAMA = :nama  , Alamat = :alamat , istakeinout = :issample, pSampit = :issampit , pPusat = :ispusat, IsAktif = :isAktif where KODEGDG = :kode' , [ 'nama' => $req->nama , 'alamat' => $req->alamat , 'issample' => $req->issample , 'issampit' => $req->issampit , 'ispusat' => $req->ispusat ,'kode' => $req->kode, 'isAktif' => $req->isAktif]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT KODEGDG , NAMA , Alamat, istakeinout, pSampit , pPusat, IsAktif FROM DBGUDANG where KODEGDG = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
