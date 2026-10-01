<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanStockSaldoStockController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanstocksaldostock");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstocksaldostock' , [
        "akses" => $akses,
        "gudang" => url('functionbrowse_doBrowseGudang'),
        "grup" => url('functionbrowse_doBrowseHdGroup'),
        "kategori" => url('functionbrowse_doBrowseSubGroup'),
        "subkategori" => url('functionbrowse_doBrowseSubGroupJnsTambah'),
        "merk" => url('functionbrowse_doBrowseMerk')
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $Nosat = $req->query('inputIsi');
    $tanggal = $req->query('date1');
    $KOdegdg = $req->query('inputGudang');
    $KodeHDGroup = $req->query('inputGrup');
    $Kategori = $req->query('inputKategori');
    $SubKategori = $req->query('inputSubKategori');
    $KodeMerk = $req->query('inputMerk');

    $values  = [$Nosat, $tanggal, $KOdegdg, $KodeHDGroup, $Kategori, $SubKategori, $KodeMerk];

    $res = DB::connection('SML')->select('exec Sp_ReportStockAkhir ?,?,?,?,?,?,?',
      $values);

    return $res;
  }


}
