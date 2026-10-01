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

class MasterLokasiBarangController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM dbVALAS');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterlokasibarang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM dbMlokasi');
    return $listData;
  }

  public function spAdd (Request $req) {

    $Choice = "I";

    $values = [
      $Choice,
      $req->kode,
      $req->nama,
      '',
      ''
    ];
    DB::connection('SML')->update('exec sp_MLokasi ?,?,?,?,?',$values);
    return 1;
  }

  public function spDelete (Request $req) {
    $Choice = "D";

    $values = [
      $Choice,
      $req->kode,
      $req->nama,
      $req->kode,
      ''
    ];
    $res = DB::connection('SML')->update('exec sp_MLokasi ?,?,?,?,?',$values);
    return $res;
    }

    public function spEdit (Request $req) {
    $Choice = "U";

    $values = [
      $Choice,
      $req->kode,
      $req->nama,
      $req->kode,
      ''
    ];
    $res = DB::connection('SML')->update('exec sp_MLokasi ?,?,?,?,?',$values);
    return $res;
    }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM dbMlokasi where KODELOKASI = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
