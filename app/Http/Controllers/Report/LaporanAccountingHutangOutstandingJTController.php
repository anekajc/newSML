<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingHutangOutstandingJTController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  // doLedger / doKasharian / doInvoice / doLpb for the bottom voucher panel.
  // NOTE: routes/report.php also has a _doBp route for this controller, but no
  // doBp() exists here, in the trait, or anywhere else in the codebase, and no
  // matching stored procedure was found on SML either - unimplemented, not a
  // wiring bug. Left as-is; the "Bp" voucher-panel button will still 404.
  use ReportVoucherTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountinghutangoutstandingJT");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountinghutangoutstandingJT' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    $tanggal = $req->query('date1');
    $awal = $req->query('inputSuppAwal');
    $akhir= $req->query('inputSuppAkhir');
    $devisi = '01';
    $tipe = $req->query('inputOrd');
    $Perkiraan = $req->query('inputPerkiraan');
    $KodeVls = $req->query('valas_value');

    $values  = [$tanggal, $awal, $akhir, $devisi, $tipe, $Perkiraan, $KodeVls];

    $res = DB::connection('SML')->select('exec sp_ReportSisaHutang ?,?,?,?,?,?,?',
      $values);

    return $res;
  }

    public function loadPerkiraan()
  {
      $user = auth()->user();
      if (! $user) { return response()->json([], 401); }

      $kode = 'HT';
      $userid = $user->username;

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


