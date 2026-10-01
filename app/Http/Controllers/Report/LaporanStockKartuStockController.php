<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanStockKartuStockController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  private function index($akses, $mode_menu) {
    return [
      "akses" => $akses,
      "mode_menu" => $mode_menu,
      "gudang" => url('functionbrowse_doBrowseGudang'),
      "barang" => url('functionbrowse_doBrowseBarang')
    ];
  }

  public function indexqty() {
    $akses = $this->cekAkses("laporanstockkartustockqty");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockkartustock' , $this->index($akses, "QTY"));
    } else {
      return redirect('/home');
    }
  }

  public function indexqtyrp() {
    $akses = $this->cekAkses("laporanstockkartustockqtyrp");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportstockkartustock' , $this->index($akses, "QTYRP"));
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    $date1 = $req->query('date1');
    $date2 = $req->query('date2');
    $date1Parts = explode('-', $date1);
    $date2Parts = explode('-', $date2);

    $kodegdg  = $req->query('inputGudang');
    $Kodebrg  = $req->query('inputBarang');
    $bulan1   = (int)$date1Parts[1];
    $bulan2   = (int)$date2Parts[1];
    $tahun1   = $date1Parts[0];
    $tahun2   = $date2Parts[0];
    $periode1 = $tahun1 . str_pad($bulan1, 2, '0', STR_PAD_LEFT);
    $periode2 = $tahun2 . str_pad($bulan2, 2, '0', STR_PAD_LEFT);
    $satuan   = $req->query('inputIsi');

    $values  = [$kodegdg, $Kodebrg, $bulan1, $bulan2, $tahun1, $tahun2, $periode1, $periode2, $satuan];

    $res = DB::connection('SML')->select('exec Sp_reportkartuStock ?,?,?,?,?,?,?,?,?',
      $values);
    // exec Sp_reportkartuStock 'G07', '10202010880044', 9, 9, '2026', '2026', '202609', '202609', 1

    return $res;
  }


}
