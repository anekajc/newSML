<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanMarketingSalesAnalisaController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanmarketingsalesanalisa");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportmarketingsalesanalisa' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    // $SReport = "T";
    // $Ordr    = $req->query('inputOrd');
    $bulan    = $req->query('date1');
    $tahun   = $req->query('date2');
    // $isiList = "";
    // $NeedOto = $req->query('inputOto');

    $values  = [$bulan, $tahun];

    $res = DB::connection('SML')->select('exec Sp_ReportAnalisaSales ?,?',
      $values);

    return $res;
  }


}
