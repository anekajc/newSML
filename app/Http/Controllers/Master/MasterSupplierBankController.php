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

class MasterSupplierBankController extends Controller
{

  public function index() {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    
    $listData = DB::connection('SML')->select('SELECT * FROM dbVALAS');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastersupplierbank' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select("Select KODECUSTSUPP,NAMACUSTSUPP,bankTemp,NoAccTemp,ATNTemp ,
  bank,NoAcc,ATN
  from DBCUSTSUPP
  where isnull(JENIS,0)=0
   and ((ISnull(bank,'')<>ISnull(BankTemp,'')) or (ISnull(NoAcc,'')<>ISnull(NoaccTemp,'')) or  
   (ISnull(ATN,'')<>ISnull(ATNTemp,'')))
   order by KODECUSTSUPP");
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM dbVALAS where KODEVLS = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode jenis sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into dbVALAS (KODEVLS, NAMAVLS, KURS, Simbol) values (:kode, :nama, :kurs, :simbol)' , ['kode' => $req->kode , 'nama' => $req->nama, 'kurs' => $req->kurs, 'simbol' => $req->simbol]);
    return 1;

  }
  public function spUpdate (Request $req) {
    $update = DB::connection('SML')->update('
      Update DbCustSupp set Bank=BankTemp,NOacc=NoAccTemp,ATN=ATNTemp
      where kodecustSupp= :kode ' , ['kode' => $req->kode ]);
    return $update;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('Update DBCUSTSUPP set 
    Banktemp= :bankTemp,
    NOacctemp= :noAccTemp,
    atntemp= :atnTemp
	where KODECUSTSUPP= :kode' , ['kode' => $req->kode , 'bankTemp' => $req->bankTemp , 'noAccTemp' => $req->noAccTemp , 'atnTemp' => $req->atnTemp ]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('select KodeCustSupp, BankTemp, NoaccTemp, ATNTemp from dbcustsupp where KODECUSTSUPP = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function spDelete (Request $req){
    $delete = DB::connection('SML')->delete("exec Sp_UpdBankTemp '',:kode,'','','','' " ,['kode' => $req->kode]);
    return $delete;
  }



}
