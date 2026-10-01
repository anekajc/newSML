<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingTrialBalanceController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  use ReportVoucherTrait;  // doLedger, doKasharian, doInvoice, doLpb, doFilter, doReportFilter, loadDivisi

  public function index() {
    $akses = $this->cekAkses("laporanaccountingtrialbalance");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingtrialbalance' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    $res = DB::connection('SML')->select("exec Sp_ReportMutasi :inputBulan,:inputTahun,:divisi,'' ",
    ['inputBulan' => $req->inputBulan,'inputTahun' => $req->inputTahun,'divisi' => $req->divisi]);
    return $res;
  }

  // doLedger, doKasharian, doInvoice, doLpb, doFilter, doReportFilter, loadDivisi
  // come from App\Traits\ReportVoucherTrait.
}
