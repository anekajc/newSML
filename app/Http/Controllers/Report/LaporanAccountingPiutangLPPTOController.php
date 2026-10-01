<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanAccountingPiutangLPPTOController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountingpiutanglppto");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingpiutanglppto' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    $Perkiraan = $req->query('inputPerkiraan');
    $Tanggal1 = $req->query('date1');
    $Tanggal2 = $req->query('date2');
    $Awal = $req->query('inputSuppAwal');
    $Akhir = $req->query('inputSuppAkhir');
    $Devisi = '01';
    $Tipe = 1;
    $KodeVls = $req->query('valas_value');

    $values  = [$Perkiraan, $Tanggal1, $Tanggal2, $Awal, $Akhir, $Devisi, $Tipe, $KodeVls];

    $res = DB::connection('SML')->select('exec sp_ReportSaldoHutangTO ?,?,?,?,?,?,?,?',
      $values);

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


}


