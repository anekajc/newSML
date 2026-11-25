<?php


namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanPermintaanSampleController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("laporanpermintaansample");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportpermintaansample' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  // 2026-11-25: filter Sales (KeyNIK dbKaryawan) + Order By No Bukti / Barang.
  // Query dasar dari user: ... where tanggal between :0 and :1
  //   and cast(KodeSls as varchar(2)) like '%PRS%' order by tanggal,nobukti
  // PRS/PRK = jenis no bukti (VWREPORTSERAHSAMPLEPR berisi Permintaan Sample SML/PRS/... dan
  // Permintaan Konsinyasi SML/PRK/...), dikonfirmasi user. Sales difilter terpisah: kosong =
  // semua sales; bila dipilih dicocokkan persis supaya sales 8 tidak ikut menarik 80/83.
  public function doReport(Request $req) {
    return $this->reportSerahSamplePR($req, 'PRS');
  }

  // Dipakai juga oleh LaporanGudangPermintaanKonsinyasiController (jenis 'PRK').
  public function reportSerahSamplePR(Request $req, string $jenis) {
    $tgl1  = $req->query('date1');
    $tgl2  = $req->query('date2');
    $sales = trim((string) $req->query('inputSales', ''));
    if ($sales === '-') { $sales = ''; }

    $order = ($req->query('inputOrd') == 'B') ? 'KODEBRG, TANGGAL, NOBUKTI' : 'TANGGAL, NOBUKTI';

    $sql    = 'select * from VWREPORTSERAHSAMPLEPR where TANGGAL between ? and ? and NOBUKTI like ?';
    $values = [$tgl1, $tgl2, '%/' . $jenis . '/%'];
    if ($sales !== '') {
      $sql     .= ' and cast(KODESLS as varchar(10)) = ?';
      $values[] = $sales;
    }

    $res = DB::connection('MGL')->select($sql . ' order by ' . $order, $values);

    // Nama gudang kosong (mis. kode '-') -> tampilkan kodenya.
    foreach ($res as $r) {
      $r->GudangAsal  = $r->NAMAGDGASAL ?? $r->GDGASAL;
      $r->GudangTujuan = $r->NAMAGDGTUJUAN ?? $r->GDGTUJUAN;
    }

    return $res;
  }

  public function loadSales() {
    return DB::connection('MGL')->select('select KeyNIK, Nama from dbKaryawan where IsSales=1 and Aktif=1 order by Nama');
  }

  public function doFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
    $listData = DB::connection('MGL')->select('select ' . $kolom . ' from VWREPORTSERAHSAMPLEPR where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
    return $listData;
  }

  public function doReportFilter(Request $req) {
    $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
    $res = [];

    for ($i=0; $i < count($req->listdata); $i++) {
      $row = DB::connection('MGL')->select('select * from VWREPORTSERAHSAMPLEPR where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);
      
      for ($j=0; $j < count($row); $j++) {
        $res = array_add($res, $i+$j, $row[$j]);
      }
    }
    
    return $res;
  }


}
