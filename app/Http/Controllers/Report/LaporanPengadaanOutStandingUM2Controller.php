<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanPengadaanOutStandingUM2Controller extends Controller {
  use AksesTrait;
  use GlobalTrait;
  use ReportVoucherTrait; // doKasharian -- No BBK opens the Bukti Kas/Bank voucher panel

  public function index() {
    $akses = $this->cekAkses("laporanpengadaanoutstandingum2");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportpengadaanoutstandingum2' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tgl2    = $req->query('date2');

    $values  = [$tgl2];

    $res = DB::connection('SML')->select('exec SPReportOutUMN ?',
      $values);

    return $res;
  }

  public function doFilter(Request $req) {
    $kolom = ($req->query('inputOrd') == "N") ? 'NoBukti, Tanggal' : 'KodeBrg, NamaBrg';
    $listData = DB::connection('SML')->select('select ' . $kolom . ' from VwREPORTOUTUMBELIDET where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->query('date1'), 'tgl2' => $req->query('date2')]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->query('inputOrd') == "N") ? 'NoBukti' : 'KodeBrg';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('SML')->select('select * from VwREPORTOUTUMBELIDET where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);

      for ($j=0; $j < count($row); $j++) {
        $res[] = $row[$j];
      }
    }

    return $res;
  }


}
