<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;





class GlobalController extends Controller


{
  
  public function getStockAkhir(Request $req) {
    $stock = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?',[$req->nosat, $req->date, $req->kodegdg,  $req->kodebrg]);

    return [
      "stock" => $stock
    ];
  }

  public function getNoBuktiSimbol (Request $req) {
    // return 1;
    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $kode = $req->kode;
    $inisial = DB::connection("SML")->select('select ' . $kode . ' from DBNOMOR');
    // $inisialx = 'KN';
    // return $inisialx;
    $values = [
        $inisial[0]->$kode,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
        $req->simbol
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobuktiSimbol ?,?,?,?,?',$values);

    return $noBukti;
  }

  public function getNoBukti (Request $req) {
    // return 1;
    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $kode = $req->kode;
    $inisial = DB::connection("SML")->select('select ' . $kode . ' from DBNOMOR');
    // $inisialx = 'KN';
    // return $inisialx;
    $values = [
        $inisial[0]->$kode,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    return $noBukti;
  }

  public function getPeriode () {
    $username = \Auth::user()->username;

    $periode = DB::connection('SML')->select('select top 1 user_id, bulan, tahun from dbperiode where user_id = :username', ["username" => $username] );

    return (object) [
      "bulan" => $periode[0]->bulan,
      "tahun" => $periode[0]->tahun,
    ];
    // return $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
  }

  public function getAkses ($kodemenu) {

    $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();

    return $akses;
  }











}
