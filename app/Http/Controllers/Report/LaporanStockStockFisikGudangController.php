<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanStockStockFisikGudangController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanstockstockfisikgudang");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockstockfisikgudang' , [
        "akses" => $akses,
        "gudang" => url('functionbrowse_doBrowseGudang'),
        "grup" => url('functionbrowse_doBrowseHdGroup')
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tanggal = $req->query('date1');
    $KOdegdg = $req->query('inputGudang');
    $KodeHdGrp = $req->query('inputGrup');

    $values  = [$tanggal, $KOdegdg, $KodeHdGrp];

    $res = DB::connection('SML')->select('exec Sp_ReportStockFisikGudang ?,?,?',
      $values);

    return $res;
  }


}
