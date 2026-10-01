<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanPengadaanRegisterRekapOSInvoiceController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanpengadaanregisteroutstandinginvoice");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportpengadaanregisterosinvoice' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $SReport = "T";
    $Ordr    = "N";
    $tgl1    = $req->query('date1');
    $tgl2    = $req->query('date2');
    $isiList = "";
    $NeedOto = 2;
    $idUser = "";
    $tipe = "";
    $pPPN = $req->query('inputPpn');

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto, $idUser, $tipe, $pPPN];

    $res = DB::connection('SML')->select('exec Sp_OUTreportInvoicedet ?,?,?,?,?,?,?,?,?',
      $values);

    return $res;
  }


}
