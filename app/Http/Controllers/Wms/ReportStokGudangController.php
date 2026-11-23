<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class ReportStokGudangController extends Controller
{




  public function index () {


// $outstanding = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]);
    $kodemenu = '210603';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $outstanding = DB::connection("SML")->select('exec sp_reportStockGDGWeb ?',[ date("Y-m-d") ]);;





    return view('wms.reportstokgudang' , [
      "periode" => $periode,
      "listData" => $outstanding
    ]);
  }

  public function spFilter (Request $req) {
    return DB::connection("SML")->select('exec sp_reportStockGDGWeb ?',[ $req->tanggal ]);
  }




}
