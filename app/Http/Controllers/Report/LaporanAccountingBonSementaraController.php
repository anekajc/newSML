<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;

class LaporanAccountingBonSementaraController extends Controller {
  use AksesTrait;
  use GlobalTrait;

  public function index() {
    $akses = $this->cekAkses("reportaccountingbonsementara");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingbonsementara' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req) {
    // Sp_ReportBon scans from 01/01/2011 to the selected date; with Perkiraan '-'
    // (Semua / all accounts) this can take well over PHP's default 30s
    // max_execution_time. Give it more headroom so it isn't cut off mid-query.
    set_time_limit(300);

    $Divisi = '01';
    $Perkiraan = $req->get('inputPerkiraan');
    $tanggal = '01/01/2011';
    $Tanggall = $req->get('date2');

    $values  = [$Divisi, $Perkiraan, $tanggal, $Tanggall];
    
    $res = DB::connection('SML')->select('exec Sp_ReportBon ?,?,?,?',
      $values);

    return $res;
  }

  public function loadPerkiraan()
  {
      $kode = 'KAS';
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
          ORDER BY a.Perkiraan", [$kode, $userid]);

      return $listData;
  }

}


