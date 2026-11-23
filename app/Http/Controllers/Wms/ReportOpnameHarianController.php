<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class ReportOpnameHarianController extends Controller
{




  public function index () {


// $outstanding = DB::connection("SML")->select('exec sp_sosiapkirim ?,?,?',[ $periode->bulan , $periode->tahun ,\Auth::user()->username ]);
    $kodemenu = '210611';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $bln = $periode->bulan;
    $thn = $periode->tahun;


    $outstanding = DB::connection("SML")->select('select c.NAMABRG , YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where  YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan
     ',["tahun" => $thn , "bulan" => $bln]);;





    return view('wms.reportopnameharian' , [
      "periode" => $periode,
      "listData" => $outstanding
    ]);
  }

  public function spFilter (Request $req) {
  $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
  $bln = $req->input('bln');
  $thn = $req->input('thn');
  $values = [
		$bln,
        $thn
	];
    //return DB::connection("SML")->select("exec Sp_ReportSoSpb :bulan,:tahun ", ["bulan" => $bulan,"tahun" => $tahun] );
    return DB::connection("SML")->select('select c.NAMABRG , YEAR(a.tanggal) TAHUN, MONTH(a.tanggal) BULAN, a.tanggal, a.nourut , b.* from dbkoreksiopndet b
    left outer join dbkoreksiopn a   on a.nobukti = b.nobukti
    left outer join dbbarang c on c.kodebrg = b.kodebrg
    where  YEAR(a.tanggal) = :tahun and MONTH(a.tanggal) = :bulan
     ',["tahun" => $thn , "bulan" => $bln]);;
  }




}
