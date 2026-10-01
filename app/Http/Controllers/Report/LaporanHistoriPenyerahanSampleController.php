<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanHistoriPenyerahanSampleController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanhistoripenyerahansample");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reporthistoripenyerahansample' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tgl1   = $req->query('date1');
    $tgl2   = $req->query('date2');
    $IDuser = "";

    $values  = [$tgl1, $tgl2, $IDuser];

    $res = DB::connection('SML')->select('exec ReportHisSerahSampleN ?,?,?',
      $values);

    return $res;
  }


}
