<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanAccountingLaporanArusController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountinglaporanarus");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountinglaporanarus' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    $Devisi = $req->get('inputPerkiraan');
    $date   = $req->get('date2');

    $Tahun = substr($date, 0, 4);            // ambil tahun
    $bulan = substr($date, 5, 2);            // ambil bulan

    $values  = [$Devisi, $bulan, $Tahun];

    $res = DB::connection('SML')->select(
        'exec sp_LapPenerimaanKeuangan ?,?,?',
        $values
    );

    return $res;
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
    try {
        $rows = DB::connection('SML')->select("
            SELECT Devisi, NamaDevisi
            FROM dbo.DBDEVISI
            ORDER BY Devisi
        ");

        // paksa UTF-8 per field
        $data = array_map(function ($row) {
            return [
                'Devisi'     => $row->Devisi,
                'NamaDevisi' => utf8_encode($row->NamaDevisi),
            ];
        }, $rows);

        return response()->json($data);

    } catch (\Exception $e) {
        return response()->json([
            'error'   => true,
            'message' => $e->getMessage()
        ], 500);
    }
}


}


