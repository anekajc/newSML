<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanGudangPermintaanKonsinyasiController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporangudangpermintaankonsinyasi");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('reportgudangpermintaankonsinyasi' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tglAwal  = $req->get('date1');
    $tglAkhir = $req->get('date2');
    $Ordr     = $req->get('inputOrd');

    $values  = [$tglAwal, $tglAkhir, $Ordr];
    
    $res = DB::connection('MGL')->select("select * from VWREPORTSERAHSAMPLEPR where tanggal between :date1 and :date2   
                                          and cast (KodeSls as varchar(2)) like '%%' and NObukti LIke '%PRK%' order by tanggal,nobukti
",['date1' => $req->date1, 'date2' => $req->date2]);
    return $res;
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


}
