<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanPengadaanPRController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanpengadaanpr");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
	$menul0 = app('App\Http\Controllers\NewMenuController')
                    ->getMenuL0Report(5);
$periode = app('App\Http\Controllers\GlobalController')->getPeriode();
      return view('report.reportpengadaanpr' , [
        "akses" => $akses,
	"menul0" => $menul0,
"periode" => $periode
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
    $idUser = "";
    $tipe = "";

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto, $idUser, $tipe];

    $res = DB::connection('SML')->select('exec Sp_ReportPurchasingReqDet ?,?,?,?,?,?,?,?',
      $values);

    return $res;
  }


}
