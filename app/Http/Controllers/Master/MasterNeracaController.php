<?php


namespace App\Http\Controllers\Master;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
// use App\Model\VWPerkiraan;



// use App\Http\Controllers\NewMenuController;

class MasterNeracaController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('
    SELECT *,
        CASE
            WHEN kelompok = 0 THEN \'Aktiva\'
            WHEN kelompok = 1 THEN \'Kewajiban\'
            WHEN kelompok = 2 THEN \'Modal\'
            WHEN kelompok = 3 THEN \'Pendapatan\'
            WHEN kelompok = 4 THEN \'Biaya\'
        END AS mKelompok,
        CASE
            WHEN Tipe = 0 THEN \'General\'
            WHEN Tipe = 1 THEN \'Detail\'
        END AS mTipe,
        CASE
            WHEN DK = 0 THEN \'Debet\'
            WHEN DK = 1 THEN \'Kredit\'
        END AS mDK
    FROM dbPerkiraan
    WHERE kelompok <= 2
    ORDER BY Perkiraan ASC');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterneraca' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('
    SELECT *,
        CASE
            WHEN kelompok = 0 THEN \'Aktiva\'
            WHEN kelompok = 1 THEN \'Kewajiban\'
            WHEN kelompok = 2 THEN \'Modal\'
            WHEN kelompok = 3 THEN \'Pendapatan\'
            WHEN kelompok = 4 THEN \'Biaya\'
        END AS mKelompok,
        CASE
            WHEN Tipe = 0 THEN \'General\'
            WHEN Tipe = 1 THEN \'Detail\'
        END AS mTipe,
        CASE
            WHEN DK = 0 THEN \'Debet\'
            WHEN DK = 1 THEN \'Kredit\'
        END AS mDK
    FROM dbPerkiraan
    WHERE kelompok <= 2
    ORDER BY Perkiraan');
    return $listData;
  }

  public function onChangeNeraca (Request $req) {
      try {
          // Get the input values from the request
          $neraca = $req->tempNeraca;
          $perkiraan = $req->Perkiraan;

          // Update the Neraca column in the database
          $result = DB::connection('SML')->update("
              UPDATE DBPERKIRAAN
              SET Neraca = :neraca
              WHERE Perkiraan = :perkiraan
          ", [
              'neraca' => $neraca,
              'perkiraan' => $perkiraan
          ]);

          return response()->json(['success' => true, 'message' => 'Data updated successfully'], 200);
      } catch (\Exception $e) {
          return response()->json(['success' => false, 'message' => 'Failed to update data: ' . $e->getMessage()], 500);
      }
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT Perkiraan, Keterangan, Kelompok, Tipe, Neraca,
      CASE
        WHEN kelompok = 0 THEN \'Aktiva\'
        WHEN kelompok = 1 THEN \'Kewajiban\'
        WHEN kelompok = 2 THEN \'Modal\'
        WHEN kelompok = 3 THEN \'Pendapatan\'
        WHEN kelompok = 4 THEN \'Biaya\'
    END AS mKelompok,
    CASE
        WHEN Tipe = 0 THEN \'General\'
        WHEN Tipe = 1 THEN \'Detail\'
    END AS mTipe,
    CASE
        WHEN DK = 0 THEN \'Debet\'
        WHEN DK = 1 THEN \'Kredit\'
    END AS mDK  FROM DBPERKIRAAN where Perkiraan = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
