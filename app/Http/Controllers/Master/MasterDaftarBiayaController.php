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

class MasterDaftarBiayaController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBBIAYA');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterdaftarbiaya' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT Kodebiaya, Keterangan, Perkiraan FROM DBBIAYA');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBBIAYA where Kodebiaya = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode Data sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBBIAYA (Kodebiaya, Keterangan, Perkiraan ) VALUES (:kode, :nama, :perkiraan)' , ['kode' => $req->kode , 'nama' => $req->nama, 'perkiraan' => $req->perkiraan]);
    return 1;

  }

  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBKOTA where KodeArea = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Area digunakkan di Master Kota';
    // }

    $delete = DB::connection('SML')->update('delete from DBBIAYA where Kodebiaya = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBBIAYA set Keterangan = :nama, Perkiraan = :perkiraan where Kodebiaya = :kode' , ['kode' => $req->kode , 'nama' => $req->nama , 'perkiraan'=>$req->perkiraan]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT Kodebiaya, Keterangan, Perkiraan FROM DBBIAYA where Kodebiaya = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function selectPerkiraan(Request $req) {
      // Dulu "SELECT Perkiraan, Keterangan AS perkiraan, keterangan": blade membaca item.perkiraan,
      // jadi yang terisi ke field Perkiraan adalah nama akunnya (mis. "ASET"), bukan kodenya.
      $detail = DB::connection('SML')->select('SELECT Perkiraan AS perkiraan, Keterangan AS keterangan from DBPERKIRAAN');
      return response()->json($detail);
  }


}
