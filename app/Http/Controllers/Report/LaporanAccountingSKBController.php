<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingSKBController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  // Voucher panel (doKasharian/doInvoice/doLpb/doBp). The controller's own
  // doFilter/doReportFilter take precedence over the trait's same-named methods.
  use ReportVoucherTrait;

  public function index() {
    $akses = $this->cekAkses("laporanaccountingskb");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingskb' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tglAwal  = $req->get('date1');
    $tglAkhir = $req->get('date2');

    $values  = [$tglAwal, $tglAkhir];
    
    $res = DB::connection('SML')->select('exec sp_ReportBukuTambahanSKB ?,?',
      $values);

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
