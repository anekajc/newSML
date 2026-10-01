<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanPengadaanClosingPRController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanpengadaanclosingpr");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportpengadaanclosingpr' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $SReport = "T";
    $Ordr    = "N";
    $tgl1    = $req->get('date1');
    $tgl2    = $req->get('date2');
    $isiList = "";
    $NeedOto = 2;
    $idUser = "";
    $tipe = "";

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto, $idUser, $tipe];

    $res = DB::connection('SML')->select('exec Sp_ReportPurchasingReqDetClose ?,?,?,?,?,?,?,?',
      $values);

    return $res;
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'NoBukti, Tanggal' : 'KodeBrg, NamaBrg';
    $listData = DB::connection('MGL')->select('select ' . $kolom . ' from Vwreportpurchasingreqdetclose where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'NoBukti' : 'KodeBrg';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('MGL')->select('select * from Vwreportpurchasingreqdetclose where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);

      for ($j=0; $j < count($row); $j++) {
        $res = array_add($res, $i+$j, $row[$j]);
      }
    }

    return $res;
  }


}
