<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingBukuBesarController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  use ReportVoucherTrait;  // doLedger, doKasharian, doInvoice, doLpb, doFilter, doReportFilter, loadDivisi

  public function index() {
    $akses = $this->cekAkses("laporanaccountingbukubesar");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingbukubesar' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    // The report uses '-' (and empty) to mean "all divisions"; Sp_ReportBukuTambahan
    // filters divisions with LIKE, so translate that to the '%' wildcard.
    $divisi = ($req->divisi === null || $req->divisi === '' || $req->divisi === '-') ? '%' : $req->divisi;

    $res = DB::connection('SML')->select("exec Sp_ReportBukuTambahan :awal, :akhir, :date1, :date2, :divisi, 'sa', 'y', 0",
    ['awal'=> $req->inputPerkiraan1, 'akhir' => $req->inputPerkiraan2, 'date1' => $req->date1,'date2' => $req->date2,'divisi' => $divisi]);
    return $res;
  }

  // doLedger, doKasharian, doInvoice, doLpb, doFilter, doReportFilter, loadDivisi
  // come from App\Traits\ReportVoucherTrait.

  public function loadPerkiraan () {
    $listData = DB::connection('SML')->select("select Perkiraan,Keterangan from dbperkiraan where tipe=1");
    return $listData;
  }
}
