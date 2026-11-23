<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class ReportPutawayController extends Controller
{




  public function index () {


// $outstanding = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]);
    $kodemenu = '210612';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $bln = $periode->bulan;
    $thn = $periode->tahun;


    $outstanding = DB::connection("SML")->select('select * from dbalokasibrgdet where MONTH(tanggal) = :bln and YEAR(tanggal) = :thn',["bln" => $bln, "thn" => $thn]);;





    return view('wms.reportputaway' , [
      "periode" => $periode,
      "listData" => $outstanding
    ]);
  }

  public function spFilter (Request $req) {
  $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
  $bln = $req->input('bln');
  $thn = $req->input('thn');
  // $values = [
	// 	$bln,
  //       $thn
	// ];
    //return DB::connection("SML")->select("exec Sp_ReportSoSpb :bulan,:tahun ", ["bulan" => $bulan,"tahun" => $tahun] );
    return DB::connection("SML")->select('select * from dbalokasibrgdet where MONTH(tanggal) = :bln and YEAR(tanggal) = :thn',["bln" => $bln, "thn" => $thn]);
  }




}
