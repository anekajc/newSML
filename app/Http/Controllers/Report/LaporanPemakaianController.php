<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanPemakaianController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function indexQty() {
    $akses = $this->cekAkses("laporanpemakaianqty");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportpemakaian' , [
        "akses" => $akses,
        "mode_menu" => "QTY"
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function indexRp() {
    $akses = $this->cekAkses("laporanpemakaianrp");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('reportpemakaian' , [
        "akses" => $akses,
        "mode_menu" => "RP"
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $SReport = "T";
    $Ordr    = $req->get('inputOrd');
    $tgl1    = $req->get('date1');
    $tgl2    = $req->get('date2');
    $isiList = "";
    $NeedOto = $req->get('inputOto');

    $values  = [$SReport, $Ordr, $tgl1, $tgl2, $isiList, $NeedOto];

    $res = DB::connection('MGL')->select('exec Sp_reportBP ?,?,?,?,?,?',
      $values);

    return $res;
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'NoBukti, Tanggal' : 'KodeBrg, NamaBrg';
    $listData = DB::connection('MGL')->select('select ' . $kolom . ' from VwreportBP where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'NoBukti' : 'KodeBrg';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('MGL')->select('select * from VwreportBP where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);

      for ($j=0; $j < count($row); $j++) {
        $res = array_add($res, $i+$j, $row[$j]);
      }
    }

    return $res;
  }


}
