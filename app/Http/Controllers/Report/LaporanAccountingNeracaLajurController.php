<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingNeracaLajurController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  use ReportVoucherTrait;  // doLedger, doKasharian, doInvoice, doLpb, loadDivisi

  public function index() {
    $akses = $this->cekAkses("laporanaccountingneracalajur");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingneracalajur' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    // SET NOCOUNT ON so the proc's internal row-count messages don't become the
    // first PDO result set (which would make ->select() return nothing even
    // though the proc works in SSMS).
    $res = DB::connection('SML')->select("SET NOCOUNT ON; exec Sp_NerajaLajur 'D',:inputBulan,:inputTahun,'-','sa' ",
    ['inputBulan' => (int) $req->inputBulan, 'inputTahun' => (int) $req->inputTahun]);
    return $res;
  }

  // doLedger, doKasharian, doInvoice, doLpb, loadDivisi come from App\Traits\ReportVoucherTrait.
}
