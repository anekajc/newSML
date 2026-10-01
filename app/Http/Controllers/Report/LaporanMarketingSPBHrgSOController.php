<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanMarketingSPBHrgSOController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanmarketingspbhrgso");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportlaporanmarketingspbhrgso' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $SReport = "T";
    $Ordr    = $req->get('inputOrd', 'N');
    $tgl1    = $req->get('date1');
    $tgl2    = $req->get('date2');
    $isiList = "";
    $NeedOto = $req->get('inputOto');
    $IDuser = '';
    $Tipe = '';
    $tglterima = $req->get('inputTerima');

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto, $IDuser, $Tipe, $tglterima];

    $res = DB::connection('SML')->select('exec Sp_ReportSPBDet ?,?,?,?,?,?,?,?,?',
      $values);

    return $res;
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
    $listData = DB::connection('SML')->select('select ' . $kolom . ' from VwReportPODet where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('S L')->select('select * from VwReportPODet where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);

      for ($j=0; $j < count($row); $j++) {
        $res = array_add($res, $i+$j, $row[$j]);
      }
    }

    return $res;
  }


}
