<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanRegisterPembelianController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanregisterpembelian");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportpengadaanregisterpembelian' , [
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
    $NeedOto = $req->query('inputOto');
    $IDuser = '';
    $Tipe = '';
    $tipebayar = $req->query('inputTipebayar');
    $Pjasa = $req->query('inputPjasa');
    $PPN = $req->query('inputPPN');
    $DetOrRekap = $req->query('inputDetOrRekap');

    if ($DetOrRekap == '0') {
      $values = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto, $IDuser, $Tipe, $tipebayar, $Pjasa, $PPN];
      $res = DB::connection('SML')->select('exec Sp_reportBeliAccDet ?,?,?,?,?,?,?,?,?,?,?', $values);
  } else if ($DetOrRekap == '1'){
      $values = [$Ordr, $tgl1, $tgl2, $NeedOto, $tipebayar, $Pjasa];
      $res = DB::connection('SML')->select('exec Sp_reportBeliAccRek ?,?,?,?,?,?', $values);
  }
    return $res;
  }

  public function doGrafik(Request $req)
{
    $tgl1 = $req->query('date1');

    return DB::connection('SML')->select(
        'exec Sp_reportBeligrafik2 ?',
        [$tgl1]
    );
}

}
