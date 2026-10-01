<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingPiutangOutstandingJTController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  // doLedger / doKasharian / doInvoice / doLpb for the bottom voucher panel.
  // NOTE: routes/report.php also has a _doBp route for this controller, but no
  // doBp() exists here, in the trait, or anywhere else in the codebase, and no
  // matching stored procedure was found on SML either - unimplemented, not a
  // wiring bug. Left as-is; the "Bp" voucher-panel button will still 404.
  use ReportVoucherTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountingpiutangoutstandingJT");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingpiutangoutstandingJT' , [
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
    $devisi = 0;
    $tipe = $req->query('inputOrd');
    $Perkiraan = $req->query('inputPerkiraan');
    $KodeVls = $req->query('valas_value');
    $IsiList = '';
    $Tanggal1 = '12-30-1899';
    $IsGroup = $req->query('inputGroup');
    $Lokasi = $req->query('inputLokasi');
    $PIC = $req->query('inputCust');

    $res = DB::connection('SML')->select("exec sp_ReportSisaPiutangN '''{$tanggal}''','''{$awal}''','''{$akhir}''','{$KodeVls}',{$devisi},'''{$Perkiraan}''',{$tipe},'{$IsiList}','''{$Tanggal1}''',{$IsGroup},'''{$Lokasi}''','''{$PIC}''' ");

    return $res;
  }

    public function loadPerkiraan()
  {
      $user = auth()->user();
      if (! $user) { return response()->json([], 401); }

      $kode = 'PT';
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
      $perkiraan = $request->input('perkiraan');

      $listData = DB::connection('SML')->select("
          select a.KodeCustsupp,
                a.NamaCustSupp as NamaCust,
                a.Alamat,
                a.Telpon
          from vwBrowsCust A
          where a.isaktif = 1
            and a.PERKIRAAN = ?
          order by a.KodeCustsupp
      ", [$perkiraan]);

      return $listData;
  }

  public function loadLokasi () {
    $listData = DB::connection('SML')->select('Select KodeKebun,nama namaKebun from DbKebunCustSupp Group By KodeKebun,nama');
    return $listData;
  }

  public function loadCustomer () {
    $listData = DB::connection('SML')->select("
        SELECT
            KodePIC,
            Nama AS NamaPIC
        FROM DBPICCUSTSUPP
        ORDER BY Nama ASC");

    return $listData;
  }

}


