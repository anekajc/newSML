<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanAccountingBankHarianController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountingbankharian");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingbankharian' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    $TglAw = $req->get('date1');
    $TglAk = $req->get('date2');
    $Perkiraan    = $req->get('inputPerkiraan');
    $IDuser = '';
    $TipeTrans = '';
    $Valas = $req->get('detOrRekap');
    $Divisi = '01';

    $values  = [$Perkiraan, $TglAw, $TglAk, $Divisi, $IDuser, $TipeTrans, $Valas];
    
    $res = DB::connection('SML')->select('exec Sp_LapKasHarian ?,?,?,?,?,?,?',
      $values);

    // $values2  = [$Perkiraan, $TglAw, $TglAk, $Divisi];
    
    // $res2 = DB::connection('SML')->select(
    //     "EXEC Sp_LapSaldoAwal ?,?,?,?",
    //     $values2
    // );

    return response()->json([
        "res1" => $res
    ]);

    // return $res;
  }

  public function doReportSaldoAwal(Request $req)
  {
      $TglAw = $req->get('date1');
      $TglAk = $req->get('date2');
      $Perkiraan = $req->get('inputPerkiraan');
      $Divisi = '01';

      $values2  = [$Perkiraan, $TglAw, $TglAk, $Divisi];

      $res2 = DB::connection('SML')->select(
          "exec Sp_LapSaldoAwal ?,?,?,?",
          $values2
      );

      return response()->json([
          "res2" => $res2
      ]);
      // return $res2;
  }

  public function doReportSaldoAkhir(Request $req)
  {
      $SaldoAwalRp = '0';
      $SaldoAwalUS = '0';
      $TglAw = $req->get('date1');
      $TglAk = $req->get('date2');
      $Perkiraan = $req->get('inputPerkiraan');

      $values3  = [$SaldoAwalRp, $SaldoAwalUS, $TglAw, $TglAk, $Perkiraan];

      $res3 = DB::connection('SML')->select(
          "exec Sp_ReportBankharian ?,?,?,?,?",
          $values3
      );

      return response()->json([
          "res3" => $res3
      ]);
      // return $res3;
  }

  public function loadPerkiraan()
  {
      $kode = 'BANK';
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

}


