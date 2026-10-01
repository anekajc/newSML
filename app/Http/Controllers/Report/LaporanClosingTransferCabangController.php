<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanClosingTransferCabangController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanclosingtransfercabang");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportclosingtransfercabang' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $tgl1  = $req->query('date1');
    $bulan = $this->getBulan($tgl1);
    $tahun = $this->getTahun($tgl1);

    $values  = [$bulan, $tahun];

    $res = DB::connection('SML')->select('exec SP_ClosingTransfer ?,?',
      $values);

    return $res;
  }


}
