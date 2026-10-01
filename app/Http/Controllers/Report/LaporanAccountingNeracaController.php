<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanAccountingNeracaController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanaccountingneraca");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingneraca' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    
    $res = DB::connection('MGL')->select("exec sp_ReportNeracaAktiva :divisi, :inputBulan, :inputTahun",
    ['inputBulan' => $req->inputBulan,'inputTahun' => $req->inputTahun,'divisi' => $req->divisi]);

    $res2 = DB::connection('MGL')->select("exec sp_ReportNeracaPasivaWeb :divisi,:inputBulan,:inputTahun",
    ['inputBulan' => $req->inputBulan,'inputTahun' => $req->inputTahun,'divisi' => $req->divisi]);
        
    return response()->json([
        "res1" => $res,
        "res2" => $res2
    ]);
  }

  public function doReportSaldoAwal(Request $req) {
    
    $res2 = DB::connection('MGL')->select("exec sp_ReportNeracaPasiva :divisi,:inputBulan,:inputTahun",
    ['inputBulan' => $req->inputBulan,'inputTahun' => $req->inputTahun,'divisi' => $req->divisi]);
        
    return response()->json([
        "res2" => $res2
    ]);
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
    $listData = DB::connection('MGL')->select('select ' . $kolom . ' from VwreporttransferPR where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('MGL')->select('select * from VwreporttransferPR where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);
      
      for ($j=0; $j < count($row); $j++) {
        $res = array_add($res, $i+$j, $row[$j]);
      }
    }
    
    return $res;
  }

  public function loadDivisi () {
    $listData = DB::connection('SML')->select('select Devisi, NamaDevisi from DBDEVISI');
    return $listData;
  }


}
