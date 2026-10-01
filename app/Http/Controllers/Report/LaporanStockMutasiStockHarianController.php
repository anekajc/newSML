<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanStockMutasiStockHarianController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanstockmutasistockharian");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockmutasistockharian' , [
        "akses" => $akses,
        "gudang" => url('functionbrowse_doBrowseGudang')
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $awal = $req->get('date1');
    $akhir = $req->get('date2');
    $gudang = $req->get('inputGudang');
    $nosat = $req->get('inputIsi');

    $values  = [$awal, $akhir, $gudang, $nosat];
    
    $res = DB::connection('MGL')->select('exec SP_ReportStockHarian ?,?,?,?',
      $values);

    return $res;
  }


}
