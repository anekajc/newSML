<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class ReportOutReceivingController extends Controller
{




  public function index () {


// $outstanding = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]);
    $kodemenu = '210605';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $bln = $periode->bulan;
    $thn = $periode->tahun;
    $outstanding = DB::connection("SML")->select('exec Sp_ReportOutReceiving ?,? ',[$bln,$thn]);;





    return view('wms.reportoutreceiving' , [
      "periode" => $periode,
      "listData" => $outstanding
    ]);
  }

  public function spFilter (Request $req) {
  $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
  $bln = $periode->bulan;
  $thn = $periode->tahun;
  $values = [
		$bln,
        $thn
	];
    // Dinonaktifkan 2026-10-23: [$values] (array di dalam array) membuat query gagal -> tombol Filter tidak berbuat apa-apa.
    // return DB::connection("SML")->select('exec Sp_ReportOutReceiving ?,?',[$values]);
    return DB::connection("SML")->select('exec Sp_ReportOutReceiving ?,?',$values);
  }




}
