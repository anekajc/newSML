<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanClosingOpnameController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanopname");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportclosingopname' , [
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
    $userid  = "";
    $Tipe    = "";

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto, $userid, $Tipe];

    $res = DB::connection('SML')->select('exec Sp_ReportOpnameBarang ?,?,?,?,?,?,?,?',
      $values);

    return $res;
  }


}
