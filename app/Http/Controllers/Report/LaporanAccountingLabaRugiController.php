<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanAccountingLabaRugiController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanaccountinglabarugi");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountinglabarugi' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    
    $res = DB::connection('SML')->select("exec sp_ReportLabaRugi :inputBulan,:inputTahun,:divisi,0,:totalA,:totalB,:totalC ",
    ['inputBulan' => $req->inputBulan,'inputTahun' => $req->inputTahun,'divisi' => $req->divisi,'totalA' => $req->totalA,'totalB' => $req->totalB,'totalC' => $req->totalC]);
    return $res;
  }

  public function loadDivisi () {
    $listData = DB::connection('SML')->select('select Devisi, NamaDevisi from DBDEVISI');
    return $listData;
  }

  public function triggerSp (Request $req) {
    $listData = DB::connection('SML')->select("SELECT totalA,totalB,totalC
      from dbLRHPP
      where persen='A' and tahun=:tahun and Bulan=:bulan and devisi=:divisi
      ",['tahun'=>$req->tahun, 'bulan'=>$req->bulan, 'divisi'=>$req->divisi]);
    return $listData;
  }


}
