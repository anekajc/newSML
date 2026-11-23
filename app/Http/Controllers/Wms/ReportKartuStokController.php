<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class ReportKartuStokController extends Controller
{




  public function index () {

    $kodemenu = '210601';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();





    return view('wms.reportkartustok' , [
      "periode" => $periode,
    ]);
  }

  public function listBarang () {
    $list = DB::connection('SML')->select('select KODEBRG, NAMABRG from DBBARANG order by KODEBRG, NAMABRG');
    return $list;
  }

  public function listLokasi () {
    $list = DB::connection('SML')->select('select KodeGdg, NamaLokasi , BarcodeLoc from dblokasisig order by BarcodeLoc');
    return $list;
  }

  public function spReport (Request $req) {
    // return 1;



        $values = [
          $req->get('kodelokasi'),
          $req->get('kodebrg'),
          $req->get('awalBulan'),
          $req->get('akhirBulan'),
          $req->get('awalTahun'),
          $req->get('akhirTahun'),
          $req->get('satuan')
        ];
        $res = DB::connection('SML')->select('exec Sp_reportkartuStockLokasi ?,?,?,?,?,?,?',$values);
        return $res;



  }


}
