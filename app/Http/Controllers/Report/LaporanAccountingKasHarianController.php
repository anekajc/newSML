<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanAccountingKasHarianController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountingkasharian");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingkasharian' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {

    $TglAw = $req->query('date1');
    $TglAk = $req->query('date2');
    $Perkiraan    = $req->query('inputPerkiraan');
    $IDuser = '';
    $TipeTrans = '';
    $Valas = $req->query('detOrRekap');
    $Divisi = '01';

    $values  = [$Perkiraan, $TglAw, $TglAk, $Divisi, $IDuser, $TipeTrans, $Valas];

    $res = DB::connection('SML')->select('exec Sp_LapKasHarian ?,?,?,?,?,?,?',
      $values);

    return response()->json([
        "res1" => $res
    ]);
  }

  public function doReportSaldoAwal(Request $req)
  {
      $TglAw = $req->query('date1');
      $TglAk = $req->query('date2');
      $Perkiraan = $req->query('inputPerkiraan');
      $Divisi = '01';

      $values2  = [$Perkiraan, $TglAw, $TglAk, $Divisi];

      $res2 = DB::connection('SML')->select(
          "exec Sp_LapSaldoAwal ?,?,?,?",
          $values2
      );

      return response()->json([
          "res2" => $res2
      ]);
  }

  public function loadPerkiraan()
  {
      $user = auth()->user();
      if (! $user) { return response()->json([], 401); }

      $kode = 'KAS';
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

}
