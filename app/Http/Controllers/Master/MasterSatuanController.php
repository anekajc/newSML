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

class MasterSatuanController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBgroup');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastersatuan' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT A.*,B.NamaTax FROM Dbsatuan A Left Outer JOin dbSattax B on A.kodesattax=B.KodeTax');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM dbsatuan where kodesatuan = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode satuan sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into dbsatuan (KodeSatuan, KodeSatTax) values (:kode, :nama)' , ['kode' => $req->kode , 'nama' => $req->nama]);
    return 1;

  }

  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM dbbarang where sat1 = :kode1 or sat2=:kode2 or sat3=:kode3' , ['kode1' => $req->kode,'kode2' => $req->kode,'kode3' => $req->kode]);

    if ($check) {
      return 'Satuan digunakkan di Master Barang';
    }

    $delete = DB::connection('SML')->update('delete from dbsatuan where kodeSatuan = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update dbsatuan set kodesattax = :nama where kodesatuan = :kode' , ['kode' => $req->kode , 'nama' => $req->nama ]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT A.*,B.NamaTax FROM Dbsatuan A Left Outer JOin dbSattax B on A.kodesattax=B.KodeTax where A.kodesatuan = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

public function AmbilSatTax(){
    $detail = DB::connection('SML')->select('SELECT KODETAX, NAMATAX FROM DBSATTAX');
    return response()->json($detail);
}


}
