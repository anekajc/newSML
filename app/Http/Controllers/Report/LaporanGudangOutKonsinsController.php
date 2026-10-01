<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanGudangOutKonsinsController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporangudangoutkonsin");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportgudangoutkonsin' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $res = DB::connection('SML')->select("
select * from VWREPORToutSERAHSAMPLE where
                 tanggal <= :date1 and Sisa >0  and (nobukti like '%SPK%') order by tanggal,Nobukti",
['date1' => $req->query('date1')]);
    return $res;
  }


}
