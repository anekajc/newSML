<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingHutangUmurController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  use ReportVoucherTrait;   // doKasharian / doInvoice / doLpb / doBp (bottom voucher panel)

  public function index() {
    $akses = $this->cekAkses("reportaccountinghutangumur");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountinghutangumur' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    $Tanggal = $req->query('date1');
    $Tipe = $req->query('inputOrd');
    $Awal = $req->query('inputSuppAwal');
    $Akhir= $req->query('inputSuppAkhir');
    $Devisi = '01';
    $Perkiraan = $req->query('inputPerkiraan');
    $KodeVls = $req->query('valas_value');

    $values  = [$Tanggal, $Tipe, $Awal, $Akhir, $Devisi, $Perkiraan, $KodeVls];

    $res = DB::connection('SML')->select('exec sp_ReportUmurHutang ?,?,?,?,?,?,?',
      $values);

    return $res;
  }

  // Ledger (kartu hutang) untuk SATU supplier — dipanggil saat baris supplier diklik.
  // SP sama dengan report Hutang Kartu; kodesupp awal = akhir = supplier yang diklik.
  // Rentang penuh sampai tanggal 'Per Tanggal' umur (date1 = jauh ke belakang, date2 = umur).
  public function doKartu(Request $req) {
    $awal      = $req->query('date1');            // jauh ke belakang (mis. 2000-01-01)
    $akhir     = $req->query('date2');            // tanggal 'Per Tanggal' umur
    $kode      = $req->query('kode');             // kode supplier yang diklik
    $devisi    = '01';
    $Urut      = '0';                           // urut tanggal
    $Perkiraan = $req->query('inputPerkiraan');
    $rekap     = '0';
    $KodeVls   = $req->query('valas_value');

    $values = [$awal, $akhir, $kode, $kode, $devisi, $Urut, $Perkiraan, $rekap, $KodeVls];

    $res = DB::connection('SML')->select('exec Sp_ReportKartuHutang ?,?,?,?,?,?,?,?,?',
      $values);

    return $res;
  }

  // public function doFilter(Request $req) {
  //   $kolom = ($req->get('inputOrd') == "N") ? 'nobukti, Tanggal' : 'KODEBRG, NAMABRG';
  //   $listData = DB::connection('MGL')->select('select ' . $kolom . ' from VwREPORTHISPO where tanggal between :tgl1 and :tgl2 group by ' . $kolom , ['tgl1' => $req->date1, 'tgl2' => $req->date2]);
  //   return $listData;
  // }

  // public function doReportFilter(Request $req) {
  //   $kolom = ($req->get('inputOrd') == "N") ? 'nobukti' : 'KODEBRG';
  //   $res = [];

  //   for ($i=0; $i < count($req->listdata); $i++) {
  //     $row = DB::connection('MGL')->select('select * from VwREPORTHISPO where ' . $kolom . ' = :list' , ['list' => $req->listdata[$i]]);

  //     for ($j=0; $j < count($row); $j++) {
  //       $res = array_add($res, $i+$j, $row[$j]);
  //     }
  //   }

  //   return $res;
  // }

    public function loadPerkiraan()
  {
      $kode = 'HT';
      $userid = auth()->user()->username;

      // $kode = $request->input('kode');
      // $userid = $request->input('userid');

      $listData = DB::connection('SML')->select("
          SELECT a.Perkiraan, b.Keterangan
          FROM dbposthutpiut a
          LEFT OUTER JOIN dbperkiraan b ON b.Perkiraan = a.Perkiraan
          WHERE a.Kode = ?
            AND a.Perkiraan IN (
                SELECT Perkiraan
                FROM DBAKSESPERKIRAANR
                WHERE UserID = ?
            )
          ORDER BY a.Perkiraan
      ", [$kode, $userid]);

      return $listData;
  }

public function loadValas()
  {
      $listData = DB::connection('SML')->select("
          select Kodevls,NamaVls,Kurs from dbValas order by kodevls
      ");

      return $listData;
  }

  public function loadSuppAwal(Request $request)
  {
    //   $perkiraan = $request->input('perkiraan');

      $listData = DB::connection('SML')->select("
          select a.KodeCustsupp,
                a.NamaCustSupp as NamaCust,
                a.Alamat,
                a.Telpon
          from vwBrowsSupp a
          where a.isaktif = 1
          order by a.KodeCustsupp
      "
    //   , [$perkiraan]

      );

      return $listData;
  }


}


