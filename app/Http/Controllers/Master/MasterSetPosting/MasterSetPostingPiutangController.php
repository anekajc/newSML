<?php

namespace App\Http\Controllers\Master\MasterSetPosting;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
// use App\Model\VWPerkiraan;


// use App\Http\Controllers\NewMenuController;

class MasterSetPostingPiutangController extends Controller
{

  public function index(Request $req) {
    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan,a.IsLokalorExim,b.keterangan from dbposthutpiut a
                       left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                       where a.Kode=\'PT\' order by a.Perkiraan');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master/masterSetPosting/mastersetpostingpiutang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select("SELECT a.Perkiraan,a.IsLokalorExim,b.keterangan from dbposthutpiut a
                       left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                       where a.Kode='PT' order by a.Perkiraan");
    return $listData;
  }

  public function spAdd(Request $req)
  {
      // $check = DB::connection('SML')->select('SELECT * FROM dbVALAS where KODEVLS = :kode', ['kode' => $req->kode]);
      //
      // if ($check) {
      //     return 'Kode jenis sudah ada di database';
      // }

      $listData = DB::connection('SML')->update('INSERT INTO DBPOSTHUTPIUT (Perkiraan, Kode, IsLokalorExim, IsUM) VALUES (:kode,  \'PT\', :nama, :uangMuka)', ['kode' => $req->kode, 'nama' => $req->nama, 'uangMuka' => $req->uangMuka]);

      return 1;
  }

  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBPPL where KDDep = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Dept digunakkan di Pembelian';
    }

    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    $delete = DB::connection('SML')->update('delete from DBPOSTHUTPIUT where Perkiraan = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit(Request $req)
  {
      $listData = DB::connection('SML')->update("UPDATE DBPOSTHUTPIUT SET Perkiraan = :kode, IsLokalorExim = :nama, IsUM = :uangMuka where Perkiraan = :kodeLama and Kode = 'PT' ", 
      ['kode' => $req->kode, 'kodeLama' => $req->kodeLama, 'uangMuka' => $req->uangMuka, 'nama' => $req->nama]);

      return 1;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBPOSTHUTPIUT where Perkiraan = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function loadPerkiraan () {
    $listData = DB::connection('SML')->select('SELECT Perkiraan,Keterangan FROM dbPerkiraan WHERE tipe=1 AND
                       perkiraan not in (select Perkiraan FROM dbposthutpiut) order by Perkiraan');
    return $listData;
  }

}
