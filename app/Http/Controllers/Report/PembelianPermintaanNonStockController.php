<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\VwPPL;
use App\Model\DBFLMENU;




// use App\Http\Controllers\NewMenuController;

class PembelianPermintaanNonStockController extends Controller
{

  public function index() {
    $kodemenu = '030013';
    $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();


    $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 1)->groupBy('NoBukti');
    $tempOutstanding = [];
    foreach ($outstanding as $p) {
      // code...
      array_push($tempOutstanding, $p);
    }

    return view('pembelianpermintaannonstock' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $inisial = DB::connection("SML")->select('select PNS from DBNOMOR');
    $values = [
        $inisial[0]->PNS,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listBarang (Request $req) {
    $listData = DB::connection('SML')->select("select b.NAMAMERK ,  a.* from dbbarang a left outer join DBMERK b on a.KodeMerk = b.KODEMERK where a.KODEGRP = 'JS'");
    return $listData;
  }



  public function loadAll () {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 1)->groupBy('NoBukti');
    $tempOutstanding = [];
    foreach ($outstanding as $p) {
      // code...
      array_push($tempOutstanding, $p);
    }

    return [
      "listData" => $tempOutstanding
    ];
  }



}
