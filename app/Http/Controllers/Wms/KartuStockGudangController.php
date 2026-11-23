<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class KartuStockGudangController extends Controller
{




  public function index () {

    // Dinonaktifkan 2026-10-23: kode menu 051 adalah Kartu Stok. Menu ini 059 (new_menureport).
    // $kodemenu = '051';
    $kodemenu = '210609';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();





    return view('wms.kartustockgudang' , [
      "periode" => $periode,
      "akses" => $akses
    ]);
  }

  public function listBarang () {
    $list = DB::connection('SML')->select('select KODEBRG, NAMABRG from DBBARANG order by KODEBRG, NAMABRG');
    return $list;
  }

  public function listLokasi () {
    $list = DB::connection('SML')->select('select KodeGdg, Nama  from dbgudang order by kodegdg');
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
