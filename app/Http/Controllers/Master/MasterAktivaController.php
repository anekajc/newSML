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

class MasterAktivaController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan AS KodeAktiva, a.Tanggal, a.Devisi, c.NamaDevisi,
       CASE
           WHEN a.TipeAktiva = 0 THEN \'Aktiva Tetap\'
           WHEN a.TipeAktiva = 1 THEN \'Aktiva Yang Dibiayakan\'
           WHEN a.TipeAktiva = 2 THEN \'Aktiva Dalam Penyelesaian\'
           ELSE \'\'
       END AS MyTipe, a.TipeAktiva, a.NoMuka AS GroupAktiva, b.Keterangan AS NamaPerkiraan,
       a.Keterangan, a.kodeBag, d.Namabag, a.Quantity, a.Persen AS Susut, a.Tipe,
       CASE
           WHEN a.Tipe = \'L\' THEN \'[L]urus\'
           WHEN a.Tipe = \'M\' THEN \'[M]enurun\'
           WHEN a.Tipe = \'P\' THEN \'[P]ajak\'
           ELSE \'\'
       END AS Metode,
       a.akumulasi, a.Biaya, a.PersenBiaya1, a.Biaya2, a.PersenBiaya2,
       a.Biaya3, a.PErsenBiaya3, a.Biaya4, a.persenbiaya4,
       e.Keterangan AS KetAkm, a.nobelakang, a.NoBelakang2, a.Kelompok, a.TglPeroleh
  FROM dbaktiva a
  LEFT OUTER JOIN dbperkiraan b ON b.perkiraan = a.NoMuka
  LEFT OUTER JOIN dbdevisi c ON c.devisi = a.devisi
  LEFT OUTER JOIN dbbagian d ON d.kodebag = a.kodebag
  LEFT OUTER JOIN dbperkiraan e ON e.perkiraan = a.akumulasi
  ORDER BY a.NoMuka, a.Perkiraan, a.Devisi;
  ');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masteraktiva' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData
    ]);

  }

    public function getNoAktiva (Request $req) {
    $perkiraan = $req->Perkiraan;
    $values = [
        $perkiraan
    ];

    $noUrut = DB::connection('SML')->select('exec SP_getNewAktiva ?',$values);

    return $noUrut;
  }

  public function loadAll () {
    $listData = DB::connection('SML')->select("SELECT a.Perkiraan AS KodeAktiva, a.Tanggal, a.Devisi, c.NamaDevisi,
       CASE
           WHEN a.TipeAktiva = 0 THEN 'Aktiva Tetap'
           WHEN a.TipeAktiva = 1 THEN 'Aktiva Yang Dibiayakan'
           WHEN a.TipeAktiva = 2 THEN 'Aktiva Dalam Penyelesaian'
           ELSE ''
       END AS MyTipe, a.TipeAktiva, a.NoMuka AS GroupAktiva, b.Keterangan AS NamaPerkiraan,
       a.Keterangan, a.kodeBag, d.Namabag, a.Quantity, a.Persen AS Susut, a.Tipe,
       CASE
           WHEN a.Tipe = 'L' THEN '[L]urus'
           WHEN a.Tipe = 'M' THEN '[M]enurun'
           WHEN a.Tipe = 'P' THEN '[P]ajak'
           ELSE ''
       END AS Metode,
       a.akumulasi, a.Biaya, a.PersenBiaya1, a.Biaya2, a.PersenBiaya2,
       a.Biaya3, a.PErsenBiaya3, a.Biaya4, a.persenbiaya4,
       e.Keterangan AS KetAkm, a.nobelakang, a.NoBelakang2, a.Kelompok, a.TglPeroleh
    FROM dbaktiva a
    LEFT OUTER JOIN dbperkiraan b ON b.perkiraan = a.NoMuka
    LEFT OUTER JOIN dbdevisi c ON c.devisi = a.devisi
    LEFT OUTER JOIN dbbagian d ON d.kodebag = a.kodebag
    LEFT OUTER JOIN dbperkiraan e ON e.perkiraan = a.akumulasi
    ORDER BY KodeAktiva");
    return $listData;
  }

  public function loadGroupAktiva () {
    $listData = DB::connection('SML')->select('SELECT a.*, b.keterangan from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           WHERE a.Kode=\'AKV\' order by a.Perkiraan');
    return $listData;
  }

  public function loadBiayaPenyusutan () {
    $listData = DB::connection('SML')->select("select Perkiraan, Keterangan, Kelompok, Tipe, Valas, DK, Neraca, FlagCashFlow, Simbol, IsPPN, GroupPerkiraan, Lokasi, iskirim, IsAktif from dbperkiraan where tipe = '1' and kelompok = '4'");
    return $listData;
  }

  public function loadEditGroupAktiva () {
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           WHERE a.Kode=\'AKV\' order by a.Perkiraan');
    return $listData;
  }

  public function loadDevisi () {
    $listData = DB::connection('SML')->select('SELECT Devisi, NamaDevisi FROM DBDEVISI');
    return $listData;
  }

    public function loadValas () {
    $listData = DB::connection('SML')->select('SELECT KODEVLS, KURS FROM DBVALAS');
    return $listData;
  }

  public function loadAkumulasiPenyusutan () {
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                           left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                           WHERE a.Kode=\'AKM\' order by a.Perkiraan');
    return $listData;
  }

  public function spAdd(Request $req) {
    try {
        // Get the inputs from the request
        $groupAktiva = $req->groupAktiva;
        $keterangan = $req->keterangan;
        $tglPemakaian = $req->tglPemakaian;
        $daftarDevisi = $req->daftarDevisi;
        $tipeAktiva = $req->tipeAktiva;

        // Get the maximum value of NoBelakang from the database
        $maxNoBelakang = DB::connection('SML')->table('dbAKTIVA')->max('NoBelakang');
        // Increment the maximum value to get the next number
        $nextNoUrut = $maxNoBelakang + 1;
        // Pad the number with leading zeros
        $noUrut = str_pad($nextNoUrut, 5, '0', STR_PAD_LEFT);

        // Generate noAktiva by concatenating groupAktiva with noUrut
        $noAktiva = $groupAktiva . '.' . $noUrut;

        // Remaining fields from the request
        $kuantum = $req->kuantum;
        $susut = $req->susut;
        $metodePenyusutan = $req->metodePenyusutan;
        $akumulasiPenyusutan = $req->akumulasiPenyusutan;
        $BP1 = $req->BP1;
        $PersenBP1 = $req->PersenBP1;
        $BP2 = $req->BP2;
        $PersenBP2 = $req->PersenBP2;
        $BP3 = $req->BP3;
        $PersenBP3 = $req->PersenBP3;
        $tglPerolehan = $req->tglPerolehan;

        // Insert the data into the database
        $result = DB::connection('SML')->insert("
            INSERT INTO DBAKTIVA (
                KodeBag, 
                Kelompok, 
                GroupAktiva, 
                Perkiraan, 
                NoBelakang, 
                Keterangan, 
                Tanggal, 
                Devisi, 
                TipeAktiva, 
                NoMuka, 
                Quantity, 
                Persen, 
                Tipe, 
                Akumulasi, 
                Biaya, 
                PersenBiaya1, 
                Biaya2, 
                PersenBiaya2, 
                Biaya3, 
                PersenBiaya3, 
                Biaya4, 
                PersenBiaya4, 
                TglPeroleh
            )
            VALUES (
                :kodebag, 
                :kelompok, 
                :groupAktiva, 
                :perkiraan, 
                :nobelakang, 
                :keterangan, 
                :tanggal, 
                :devisi, 
                :tipeAktiva, 
                :nomuka, 
                :quantity, 
                :persen, 
                :tipe, 
                :akumulasi, 
                :biaya, 
                :persenBiaya1, 
                :biaya2, 
                :persenBiaya2, 
                :biaya3, 
                :persenBiaya3, 
                :biaya4, 
                :persenBiaya4, 
                :tglPeroleh
            )
        ", [
            'kodebag' => '',
            'kelompok' => 0,
            'groupAktiva' => $groupAktiva,
            'perkiraan' => $noAktiva,
            'nobelakang' => $noUrut,
            'keterangan' => $keterangan,
            'tanggal' => $tglPemakaian,
            'devisi' => $daftarDevisi,
            'tipeAktiva' => $tipeAktiva,
            'nomuka' => $groupAktiva,
            'quantity' => $kuantum,
            'persen' => $susut,
            'tipe' => $metodePenyusutan,
            'akumulasi' => $akumulasiPenyusutan,
            'biaya' => $BP1,
            'persenBiaya1' => $PersenBP1,
            'biaya2' => $BP2,
            'persenBiaya2' => $PersenBP2,
            'biaya3' => $BP3,
            'persenBiaya3' => $PersenBP3,
            'biaya4' => 0,
            'persenBiaya4' => 0,
            'tglPeroleh' => $tglPerolehan
        ]);

        return response()->json(['success' => true, 'message' => 'Data added successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Failed to add data: ' . $e->getMessage()], 500);
    }
}


  public function spDelete (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBPPL where Perkiraan = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Dept digunakkan di Pembelian';
    // }

    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    // Aktiva yang sudah dipakai transaksi atau sudah punya mutasi (penambahan/penyusutan) tidak boleh
    // dihapus - dulu tidak dicek sama sekali.
    $check = DB::connection('SML')->select('select top 1 1 as ada from dbTransaksi where NoAktivaP = :kode or NoAktivaL = :kode2', ['kode' => $req->kode, 'kode2' => $req->kode]);
    if ($check) {
      return 'Aktiva sudah dipakai di transaksi accounting';
    }
    $check = DB::connection('SML')->select('select top 1 1 as ada from DBAKTIVADET where Perkiraan = :kode
      and (isnull(MD,0) <> 0 or isnull(MK,0) <> 0 or isnull(SD,0) <> 0 or isnull(SK,0) <> 0)', ['kode' => $req->kode]);
    if ($check) {
      return 'Aktiva sudah punya mutasi/penyusutan';
    }

    $delete = DB::connection('SML')->update('delete from DBAKTIVA where Perkiraan = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEdit(Request $req) {
    try {
        // Get the inputs from the request
        $noAktiva = $req->noAktiva; // Assuming noAktiva is provided in the request

        // Update the record in the database
        $edit = DB::connection('SML')->table('DBAKTIVA')
            ->where('Perkiraan', $noAktiva)
            ->update([
                'GroupAktiva' => $req->groupAktiva,
                'NoBelakang' => $req->noUrut,
                'Keterangan' => $req->keterangan,
                'Tanggal' => $req->tglPemakaian,
                'Devisi' => $req->daftarDevisi,
                'TipeAktiva' => $req->tipeAktiva,
                'Quantity' => $req->kuantum,
                'Persen' => $req->susut,
                'Tipe' => $req->metodePenyusutan,
                'Akumulasi' => $req->akumulasiPenyusutan,
                'Biaya' => $req->BP1,
                'PersenBiaya1' => $req->PersenBP1,
                'Biaya2' => $req->BP2,
                'PersenBiaya2' => $req->PersenBP2,
                'Biaya3' => $req->BP3,
                'PersenBiaya3' => $req->PersenBP3,
                'TglPeroleh' => $req->tglPerolehan
            ]);

        return response()->json(['success' => true, 'message' => 'Data updated successfully'], 200);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Failed to update data: ' . $e->getMessage()], 500);
    }
}

public function spAddSaldoAwal (Request $req) {
  
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

      $values = [
       $req->Choice,
       $req->Divisi,
       $req->Perkiraan,
       $periode->bulan,
       $periode->tahun,
       $req->Valas,
       $req->kurs,
       $req->Awal,
       $req->AwalSusut
      ];
      DB::connection('SML')->statement('exec sp_AktivaDet ?,?,?,?,?,?,?,?,?', $values);
      return 1;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select('SELECT * FROM DBAKTIVA where Perkiraan = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function spDetailSaldoAwal (Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $detail = DB::connection('SML')->select("select Perkiraan, Bulan, Tahun, Devisi, Valas, Kurs, Awal, AwalSusut, AwalD, AwalSusutD from DBAKTIVADET where Perkiraan = :perkiraan and Bulan = :bulan and Tahun = :tahun" , ['perkiraan' => $req->perkiraan, 'bulan' => $periode->bulan, 'tahun' => $periode->tahun]);
    return $detail;
  }



}
