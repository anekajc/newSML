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

class MasterSetPostingController extends Controller
{

  public function index(Request $req) {



    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT * FROM dbVALAS');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastersetposting' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

  public function loadAll () {
    $listData = DB::connection('SML')->select('SELECT * FROM dbVALAS');
    return $listData;
  }

  public function loadSelectPostingKas () {
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                       left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                       where a.Kode=\'KAS\' order by a.Perkiraan');
    return $listData;
  }
  public function loadSelectPostingBank () {
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                       left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                       where a.Kode=\'BANK\' ORDER BY a.Perkiraan');
    return $listData;
  }

  public function loadPerkiraanAkumulasi () {
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           WHERE a.Kode=\'AKM\' order by a.Perkiraan');
    return $listData;
  }

  public function loadPerkiraanAkumulasiSelect () {
    $listData = DB::connection('SML')->select('SELECT Perkiraan,Keterangan FROM dbPerkiraan WHERE tipe=1 AND
                       perkiraan not in (select Perkiraan FROM dbposthutpiut) order by Perkiraan');
    return $listData;
  }

  public function loadPerkiraanAkumulasiSelectEdit () {
    $listData = DB::connection('SML')->select('SELECT Perkiraan,Keterangan FROM dbPerkiraan WHERE tipe=1 AND
                       perkiraan not in (select Perkiraan FROM dbposthutpiut) order by Perkiraan');
    return $listData;
  }

  public function loadPerkiraanAktivaSelect () {
    $listData = DB::connection('SML')->select('SELECT Perkiraan,Keterangan FROM dbPerkiraan WHERE tipe=1 AND
                       perkiraan not in (select Perkiraan FROM dbposthutpiut) order by Perkiraan');
    return $listData;
  }

  public function loadAkumulasiAktivaSelect () {
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           WHERE a.Kode=\'AKM\' order by a.Perkiraan');
    return $listData;
  }
  public function loadBiayaPenyusutanAktivaSelect1 () {
    $listData = DB::connection('SML')->select('SELECT Perkiraan, Keterangan from dbPerkiraan where  tipe=1  order by Perkiraan');
    return $listData;
  }

  public function loadBiayaPenyusutanAktivaSelect2 () {
    $listData = DB::connection('SML')->select('SELECT Perkiraan, Keterangan from dbPerkiraan where  tipe=1  order by Perkiraan');
    return $listData;
  }


  // public function loadPostingAktiva() {
  //     $listData = DB::connection('SML')->select("SELECT a.*, b.Keterangan, c.Keterangan,
  //         CASE
  //             WHEN a.Tipe = 'L' THEN '[L]urus'
  //             WHEN a.Tipe = 'M' THEN '[M]enurun'
  //             WHEN a.Tipe = 'P' THEN '[P]ajak'
  //             ELSE ''
  //         END AS Metode
  //         FROM dbPostHutPiut a, dbPerkiraan b, dbperkiraan c
  //         WHERE a.Perkiraan = b.Perkiraan AND c.Perkiraan = a.Akumulasi
  //         ORDER BY a.Kode, a.Perkiraan");
  //
  //     return $listData;
  // }

  public function loadPostingAktiva() {
      $listData = DB::connection('SML')->select('SELECT * FROM DBPOSTHUTPIUT where Kode = \'AKV\' ');

      return $listData;
  }

  public function loadAktiva() {
      $listData = DB::connection('SML')->select('SELECT * FROM DBAKTIVA');

      return $listData;
  }

  public function loadPostingKas() {
      $listData = DB::connection('SML')->select("SELECT a.Perkiraan,b.keterangan FROM dbposthutpiut a
                       LEFT OUTER JOIN dbperkiraan b ON b.perkiraan=a.perkiraan
                       WHERE a.Kode='KAS' ORDER BY a.Perkiraan");

      return $listData;
  }
  public function loadPostingBank() {
      $listData = DB::connection('SML')->select("SELECT a.Perkiraan,b.keterangan FROM dbposthutpiut a
                       LEFT OUTER JOIN dbperkiraan b ON b.perkiraan=a.perkiraan
                       WHERE a.Kode='KAS' ORDER BY a.Perkiraan");

      return $listData;
  }



  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM dbVALAS where KODEVLS = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Kode jenis sudah ada di database';
    }
    $listData = DB::connection('SML')->update('insert into dbVALAS (KODEVLS, NAMAVLS, KURS, Simbol) values (:kode, :nama, :kurs, :simbol)' , ['kode' => $req->kode , 'nama' => $req->nama, 'kurs' => $req->kurs, 'simbol' => $req->simbol]);
    return 1;

  }

  public function spAddAkumulasi(Request $req)
  {
      $check = DB::connection('SML')->select('SELECT * FROM dbVALAS where KODEVLS = :kode', ['kode' => $req->kode]);

      if ($check) {
          return 'Kode jenis sudah ada di database';
      }

      $listData = DB::connection('SML')->update('INSERT INTO DBPOSTHUTPIUT (Perkiraan, Kode) VALUES (:kode,  \'AKM\')', ['kode' => $req->kode]);

      return 1;
  }

  public function spAddAktiva(Request $req)
  {
      // Check if the kode already exists in the database
      $check = DB::connection('SML')->select('SELECT * FROM dbVALAS WHERE KODEVLS = :kode', ['kode' => $req->kode]);

      if ($check) {
          return 'Kode jenis sudah ada di database'; // Assuming you want to return a string when the kode already exists
      }

      // Insert new data into DBPOSTHUTPIUT table
      $listData = DB::connection('SML')->insert(
          'INSERT INTO DBPOSTHUTPIUT (Perkiraan, Kode, Persen, Tipe, Akumulasi, Biaya1, PersenBiaya1, Biaya2, PersenBiaya2) VALUES (:perkiraanAktiva, \'AKV\', :persenPenyusutanAktiva, :metode, :AkumulasiAktiva, :BP1Aktiva, :PersenBP1Aktiva, :BP2Aktiva, :PersenBP2Aktiva)',
          [
              'perkiraanAktiva' => $req->perkiraanAktiva,
              'persenPenyusutanAktiva' => $req->persenPenyusutanAktiva,
              'metode' => $req->metode,
              'AkumulasiAktiva' => $req->AkumulasiAktiva,
              'BP1Aktiva' => $req->BP1Aktiva,
              'PersenBP1Aktiva' => $req->PersenBP1Aktiva,
              'BP2Aktiva' => $req->BP2Aktiva,
              'PersenBP2Aktiva' => $req->PersenBP2Aktiva
          ]
      );

      return 1;
  }


  public function spDelete (Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBPPL where KDDep = :kode' , ['kode' => $req->kode]);

    if ($check) {
      return 'Dept digunakkan di Pembelian';
    }


    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    $delete = DB::connection('SML')->update('delete from dbVALAS where KODEVLS = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spDeleteAkumulasi (Request $req) {
    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    $delete = DB::connection('SML')->update('DELETE FROM DBPOSTHUTPIUT where Perkiraan = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('update dbVALAS set NAMAVLS = :nama, KURS = :kurs, Simbol = :simbol where KODEVLS = :kode' , ['kode' => $req->kode , 'nama' => $req->nama , 'kurs' => $req->kurs , 'simbol' => $req->simbol ]);

    return $edit;
  }

  public function spEditPostingAkumulasi (Request $req) {
    $edit = DB::connection('SML')->update('update DBPOSTHUTPIUT set Perkiraan = :kode where Perkiraan = :kode' , ['kode' => $req->kode]);

    return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM dbVALAS where KODEVLS = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function spDetailPostingAkumulasi (Request $req) {
    $detail = DB::connection('SML')->select('SELECT Perkiraan FROM DBPOSTHUTPIUT where Perkiraan = :kode' , ['kode' => $req->kode]);
    return $detail;
  }



}
