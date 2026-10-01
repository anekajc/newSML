<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanPenerimaanGudangController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanpenerimaangudang");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportpengadaanpenerimaangudang' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $SReport = "T";
    $Ordr    = $req->query('inputOrd');
    $tgl1    = $req->query('date1');
    $tgl2    = $req->query('date2');
    $isiList = "";
    $x = 1;
    $IDuser = '';
    $Tipe = '';

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $x, $IDuser, $Tipe];

    $res = DB::connection('SML')->select('exec Sp_reportBeliGudangdet ?,?,?,?,?,?,?,?',
      $values);

    //  exec Sp_reportBeliGudangdet 'T', 'N', '2026-07-01', '2026-09-30', '', 1, '', ''

    return $res;
  }


}
