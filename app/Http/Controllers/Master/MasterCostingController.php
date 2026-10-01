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

class MasterCostingController extends Controller
{

  public function index() {


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM DBCOST');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastercosting' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM DBCOST');
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBCOST where KodeCost = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode costing sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into DBCOST (KodeCost, NamaCost) values (:kode, :nama)' , ['kode' => $req->kode , 'nama' => $req->nama]);
    return 1;

  }

  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBPPL where KDDep = :kode' , ['kode' => $req->kode]);

    // if ($check) {
    //   return 'Dept digunakkan di Pembelian';
    // }

    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    // Costing yang masih dipakai tidak boleh dihapus (dulu tidak dicek sama sekali).
    $pemakai = [
      ['dbPerkCost',   'KodeCost', 'Detail Akun costing ini'],
      ['DBKENDARAAN',  'KodeCost', 'Master No. Pol'],
      ['DBSOPIR',      'KODESG',   'Master Sopir'],
    ];
    foreach ($pemakai as $p) {
      $check = DB::connection('SML')->select("SELECT top 1 1 as ada FROM {$p[0]} where {$p[1]} = :kode" , ['kode' => $req->kode]);
      if ($check) {
        return 'Costing digunakan di ' . $p[2];
      }
    }

    $delete = DB::connection('SML')->update('delete from DBCOST where KodeCost = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update DBCOST set NamaCost = :nama where KodeCost = :kode' , ['kode' => $req->kode , 'nama' => $req->nama ]);

    return $edit;
  }

  public function listPerkiraanSubCost (){
    $list = DB::connection('SML')->select('select Perkiraan,keterangan from dbPerkiraan where tipe=1 and 
perkiraan not in (select Perkiraan from dbposthutpiut) order by Perkiraan');

    return $list;
  }

  public function listDetailAkun (Request $req){
    $list = DB::connection('SML')->select('select A.*, B.Keterangan NamaPerkiraan
      from 	dbPerkCost A
      left outer join dbPerkiraan B on B.Perkiraan=A.Perkiraan
      where KodeCost = :kodeCost',['kodeCost'=>$req->kodeCost]);

    return $list;
  }

    public function listDetailAkunEdit (Request $req){
    $list = DB::connection('SML')->select('select A.*, B.Keterangan NamaPerkiraan
      from 	dbPerkCost A
      left outer join dbPerkiraan B on B.Perkiraan=A.Perkiraan
      where KodeCost = :kodeCost and Urut = :urut',['kodeCost'=>$req->kodeCost, 'urut'=>$req->urut]);

    return $list;
  }

  public function spPerkiraan (Request $req){
    $hasilSP = DB::connection('SML')->update('
    exec sp_PerkCost :Choice, :KodeCost, :Urut, :Perkiraan',['Choice'=>$req->Choice, 'KodeCost'=>$req->KodeCost, 'Urut'=>$req->Urut, 'Perkiraan'=>$req->Perkiraan]);

    return $hasilSP;
  }



  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBCOST where KodeCost = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
