<?php


namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\AksesTrait;
use App\Traits\GlobalTrait;
use App\Traits\ReportVoucherTrait;

class LaporanAccountingPiutangUmurController extends Controller {
  use AksesTrait;
  use GlobalTrait;
  use ReportVoucherTrait;   // doLedger / doKasharian / doInvoice / doLpb (bottom voucher panel)

  public function index() {
    $akses = $this->cekAkses("reportaccountingpiutangumur");
    if ($akses['userLoggedOut']) { return redirect('/'); }

    if ($akses['akses']->Access) {
      return view('report.reportaccountingpiutangumur' , [
        "akses" => $akses
      ]);
    } else {
      return redirect('/home');
    }
  }

  public function doReport(Request $req)
  {
      $tanggal    = $req->query('date1');
      $tipe       = (int) $req->query('inputOrd');
      $devisi     = '01';
      $perkiraan  = $req->query('inputPerkiraan');
      $KodeVls    = $req->query('valas_value');

      $awal  = $req->query('inputSuppAwal');
      $akhir = $req->query('inputSuppAkhir');

      $selectedCust = $req->query('selectedCust', []);

      $db = DB::connection('SML');

      if (is_array($selectedCust) && count($selectedCust) > 0) {

          $db->table('DBCUSTOMIZE')
              ->where('Tipe', 20405)
              ->delete();
          foreach ($selectedCust as $agent) {
              $db->table('DBCUSTOMIZE')->insert([
                  'Tipe' => 20405,
                  'Id'   => $agent
              ]);
          }
          $result = $db->select(
              'exec sp_ReportUmurPiutang ?,?,?,?,?,?,?,?',
              [
                  $tanggal,
                  $tipe,
                  $awal,    
                  $akhir,    
                  $devisi,
                  $perkiraan,
                  $KodeVls,
                  'F'
              ]
          );
      }
      else {
          $result = $db->select(
              'exec sp_ReportUmurPiutang ?,?,?,?,?,?,?,?',
              [
                  $tanggal,
                  $tipe,
                  $awal,
                  $akhir,
                  $devisi,
                  $perkiraan,
                  $KodeVls,
                  'T'
              ]
          );
      }
      return response()->json($result);
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


