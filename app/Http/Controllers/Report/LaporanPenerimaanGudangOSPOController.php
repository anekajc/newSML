<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanPenerimaanGudangOSPOController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanpenerimaangudangospo");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportpengadaanpgoutstandingpo' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $SReport = "T";
    $Ordr    = $req->query('inputOrd');
    // $tgl1    = $req->query('date1');
    $tgl1    = '2019-01-01';
    $tgl2    = $req->query('date2');
    $isiList = "";
    $IDuser = '';
    $Tipe = '';

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $IDuser, $Tipe];

    $res = DB::connection('SML')->select('exec Sp_reportoutStandingPOdet ?,?,?,?,?,?,?',
      $values);

    return $res;
  }


}
