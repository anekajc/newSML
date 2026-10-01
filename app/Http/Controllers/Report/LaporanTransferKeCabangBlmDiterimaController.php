<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanTransferKeCabangBlmDiterimaController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporantransferkecabangblmditerima");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reporttransferkecabangblmditerima' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    // 0 = Semua, 1 = Diterima, 2 = Outstanding (lihat inputJenis di blade)
    $jenis = $req->get('inputJenis', 0);
    $tgl2 = $req->get('date2');

    // Outstanding = snapshot "per tanggal2", jadi date1 dipatok = date2. Blade sengaja
    // tidak mengirim date1 sama sekali di mode ini.
    if ( $jenis == 2) {
        $tgl1 = $tgl2;
    } else {
        $tgl1 = $req->get('date1');
    }

    $values  = [$tgl1, $tgl2, (int) $jenis];

    $res = DB::connection('SML')->select('exec SP_TransferBlmTerima ?, ?, ?',
      $values);

    return $res;
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
    $listData = DB::connection('SML')->select('select ' . $kolom . ' from VWREPORToutSERAHSAMPLE where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('SML')->select('select * from VWREPORToutSERAHSAMPLE where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);

      for ($j=0; $j < count($row); $j++) {
        $res = array_add($res, $i+$j, $row[$j]);
      }
    }

    return $res;
  }


}
