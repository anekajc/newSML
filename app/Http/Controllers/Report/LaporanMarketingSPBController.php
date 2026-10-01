<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanMarketingSPBController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanmarketingspb");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportlaporanmarketingspb' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $SReport = "T";
    $Ordr    = $req->query('inputOrd');
    $tgl1    = $req->query('date1');
    $tgl2    = $req->query('date2');
    $isiList = "";
    $NeedOto = $req->query('inputOto');
    $IDuser = '';
    $Tipe = '';
    $tglterima = $req->query('inputTerima');

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto, $IDuser, $Tipe, $tglterima];

    $res = DB::connection('SML')->select('exec Sp_ReportSPBDet ?,?,?,?,?,?,?,?,?',
      $values);

    return $res;
  }


}
