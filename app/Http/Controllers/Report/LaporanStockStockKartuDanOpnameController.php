<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanStockStockKartuDanOpnameController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanstockstockkartudanopname");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockstockkartudanopname' , [
        "akses" => $akses,
        "gudang" => url('functionbrowse_doBrowseGudang')
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $TANGGAL        = $req->query('date1');
    $kodegdg        = $req->query('inputGudang');
    $TGLBATASOPNAME = $req->query('date2');

    $values  = [$TANGGAL, $kodegdg, $TGLBATASOPNAME];

    $res = DB::connection('SML')->select('exec SP_REPORTKARTUOPNAME ?,?,?',
      $values);

    return $res;
  }


}
