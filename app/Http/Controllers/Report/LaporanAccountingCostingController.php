<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanAccountingCostingController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountingcosting");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingcosting' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req)
  {
    $Tipe = 0;
    $KodeCost = $req->get('inputPerkiraan');
    $KodeSubCost = $req->get('inputSubCosting');

    $dateMonth = $req->get('date2'); 
    [$Tahun, $Bulan] = explode('-', $dateMonth);

    $values = [
        $Tipe,
        $KodeCost,
        $KodeSubCost,
        $Bulan,
        $Tahun
    ];

    $res = DB::connection('SML')->select(
        'exec Sp_ReportCosting ?,?,?,?,?',
        $values
    );

    return response()->json([
        "res1" => $res
    ]);
  }

  public function doReportSaldoAwal(Request $req)
  {
    $Tipe = 0;
    $KodeCost = $req->get('inputPerkiraan');
    $KodeSubCost = $req->get('inputSubCosting');

    $dateMonth = $req->get('date2'); 
    [$Tahun, $Bulan] = explode('-', $dateMonth);

    $values2 = [
        $Tipe,
        $KodeCost,
        $KodeSubCost,
        $Bulan,
        $Tahun
    ];

    $res2 = DB::connection('SML')->select(
        'exec Sp_ReportCostingRekThn ?,?,?,?,?',
        $values2
    );

    return response()->json([
        "res2" => $res2
    ]);
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
    $listData = DB::connection('MGL')->select('select ' . $kolom . ' from VwREPORTHISPO where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('MGL')->select('select * from VwREPORTHISPO where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);
      
      for ($j=0; $j < count($row); $j++) {
        $res = array_add($res, $i+$j, $row[$j]);
      }
    }
    
    return $res;
  }

  public function loadPerkiraan()
  {
      $listData = DB::connection('SML')->select("
          SELECT 
              a.KodeCost, 
              a.NamaCost
          FROM dbCost a
          ORDER BY a.KodeCost
      ");

      return $listData;
  }

  public function loadSubCosting(Request $request)
  {
      $noKira = $request->NoKira; // KodeCost

      $listData = DB::connection('SML')->select("
          SELECT 
              a.KodeSubCost,
              a.NamaSubCost
          FROM vwSubCost a
          WHERE a.KodeCost = ?
          ORDER BY a.KodeSubCost
      ", [$noKira]);

      return $listData;
  }


}

