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

class MasterGiroController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();

    $listData = DB::connection('SML')->select('SELECT A.*,b.Keterangan AS NamaBank,c.NamaVls
      from dbgiro a
           left outer join dbPerkiraan b on b.perkiraan=a.Bank
           left outer join dbValas c on c.kodevls=a.kodevls
      where A.Tipe=\'HT\'
      Order by a.Bank,a.Nogiro,a.TglGiro');

    $listDataTerima = DB::connection('SML')->select('SELECT A.*,b.Keterangan AS NamaBank,c.NamaVls
      from dbgiro a
           left outer join dbPerkiraan b on b.perkiraan=a.Bank
           left outer join dbValas c on c.kodevls=a.kodevls
      where A.Tipe=\'PT\'
      Order by a.Bank,a.Nogiro,a.TglGiro');

    $listDataValas = DB::connection('SML')->select('SELECT KODEVLS, KURS FROM dbVALAS');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.mastergiro' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData,
      "listDataTerima" => $listDataTerima,
      "listDataValas" => $listDataValas

    ]);

  }

  public function loadAllBuka () {
    $listData = DB::connection('SML')->select('SELECT A.NoGiro, A.Bank, A.TglGiro, A.Debet, A.Kredit, A.DebetRp, A.KreditRp, A.Keterangan, A.TglBuka, A.BuktiBuka, A.UrutBuktiBuka, A.TglCair, A.BuktiCair, A.KeteranganCair, A.UrutBuktiCair, A.Kodevls, A.Kurs, A.Jumlah, A.Tipe, A.FlagSimbol, A.Kas,b.Keterangan AS NamaBank,c.NamaVls
      from dbgiro a
           left outer join dbPerkiraan b on b.perkiraan=a.Bank
           left outer join dbValas c on c.kodevls=a.kodevls
      where A.Tipe=\'HT\'
      Order by a.Bank,a.Nogiro,a.TglGiro');
    return $listData;
}

    public function loadAllTerima () {
      $listData = DB::connection('SML')->select('SELECT A.NoGiro, A.Bank, A.TglGiro, A.Debet, A.Kredit, A.DebetRp, A.KreditRp, A.Keterangan, A.TglBuka, A.BuktiBuka, A.UrutBuktiBuka, A.TglCair, A.BuktiCair, A.KeteranganCair, A.UrutBuktiCair, A.Kodevls, A.Kurs, A.Jumlah, A.Tipe, A.FlagSimbol, A.Kas,b.Keterangan AS NamaBank,c.NamaVls
        from dbgiro a
             left outer join dbPerkiraan b on b.perkiraan=a.Bank
             left outer join dbValas c on c.kodevls=a.kodevls
        where A.Tipe=\'PT\'
        Order by a.Bank,a.Nogiro,a.TglGiro');
      return $listData;
    }

  public function spAddTerima(Request $req) {
    $check = DB::connection('SML')->select('SELECT * FROM DBGIRO where NoGiro = :noGiro' , ['noGiro' => $req->noGiro]);

    if ($check) {
      return 'No. Giro sudah ada di database';
    }
    DB::connection('SML')->insert('INSERT INTO DBGIRO (NoGiro, Bank, TglGiro, Debet, Kredit, DebetRp, KreditRp, Keterangan, TglBuka, BuktiBuka, UrutBuktiBuka, BuktiCair, KeteranganCair, UrutBuktiCair, Kodevls, Kurs, Jumlah, Tipe, Kas)
    VALUES (:noGiro, :bank, :tglGiro, :debet, :kredit, :debetRp, :kreditRp, :keterangan, :tglTerima, :buktiTerima, :urutBuktiBuka, :buktiCair, :keteranganCair, :urutBuktiCair, :valas, :kurs, :jumlah, :tipe, :perkiraanKas)',
    [
        'noGiro' => $req->noGiro,
        'bank' => $req->bank,
        'tglGiro' => $req->tglGiro,
        'debet' => $req->nilaiGiro,
        'kredit' => 0,
        'debetRp' => $req->nilaiGiroRp,
        'kreditRp' => 0,
        'keterangan' => $req->keterangan,
        'tglTerima' => $req->tglTerima,
        'buktiTerima' => '',
        'urutBuktiBuka' => 0,
        'buktiCair' => '',
        'keteranganCair' => '',
        'urutBuktiCair' => 0,
        'valas' => $req->valas,
        'kurs' => $req->kurs,
        'jumlah' => 0,
        'tipe' => 'PT',
        'perkiraanKas' => $req->perkiraanKas
    ]);

    return 1;
}



public function spAddBuka(Request $req) {
  $check = DB::connection('SML')->select('SELECT * FROM DBGIRO where NoGiro = :noGiro' , ['noGiro' => $req->noGiro]);

  if ($check) {
    return 'No. Giro sudah ada di database';
  }
  DB::connection('SML')->insert('INSERT INTO DBGIRO (NoGiro, Bank, TglGiro, Debet, Kredit, DebetRp, KreditRp, Keterangan, TglBuka, BuktiBuka, UrutBuktiBuka, BuktiCair, KeteranganCair, UrutBuktiCair, Kodevls, Kurs, Jumlah, Tipe, Kas)
  VALUES (:noGiro, :bank, :tglGiro, :debet, :kredit, :debetRp, :kreditRp, :keterangan, :tglBuka, :buktiBuka, :urutBuktiBuka, :buktiCair, :keteranganCair, :urutBuktiCair, :valas, :kurs, :jumlah, :tipe, :perkiraanKas)',
  [
      'noGiro' => $req->noGiro,
      'bank' => $req->bank,
      'tglGiro' => $req->tglGiro,
      'debet' => 0,
      'kredit' => $req->nilaiGiro,
      'debetRp' => 0,
      'kreditRp' => $req->nilaiGiroRp,
      'keterangan' => $req->keterangan,
      'tglBuka' => $req->tglBuka,
      'buktiBuka' => '',
      'urutBuktiBuka' => 0,
      'buktiCair' => '',
      'keteranganCair' => '',
      'urutBuktiCair' => 0,
      'valas' => $req->valas,
      'kurs' => $req->kurs,
      'jumlah' => 0,
      'tipe' => 'HT',
      'perkiraanKas' => ''
  ]);

  return 1;
}

  public function spDeleteBuka (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBPPL where KDDep = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Dept digunakkan di Pembelian';
    // }

    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    // Giro yang sudah dicairkan (punya bukti pencairan) tidak boleh dihapus dari master.
    $cair = DB::connection('SML')->select("select top 1 1 as ada from DBGIRO where NoGiro = :kode and isnull(BuktiCair,'') <> ''", ['kode' => $req->kode]);
    if ($cair) {
      return 'Giro sudah dicairkan, tidak bisa dihapus';
    }
    $delete = DB::connection('SML')->update('delete from DBGIRO where NoGiro = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spDeleteTerima (Request $req) {
    // $check = DB::connection('SML')->select('SELECT * FROM DBPPL where KDDep = :kode' , ['kode' => $req->kode]);
    //
    // if ($check) {
    //   return 'Dept digunakkan di Pembelian';
    // }

    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    // Giro yang sudah dicairkan (punya bukti pencairan) tidak boleh dihapus dari master.
    $cair = DB::connection('SML')->select("select top 1 1 as ada from DBGIRO where NoGiro = :kode and isnull(BuktiCair,'') <> ''", ['kode' => $req->kode]);
    if ($cair) {
      return 'Giro sudah dicairkan, tidak bisa dihapus';
    }
    $delete = DB::connection('SML')->update('delete from DBGIRO where NoGiro = :kode' , ['kode' => $req->kode ]);
    return $delete;
  }

  public function spEditBuka (Request $req) {
    $edit = DB::connection('SML')->update('
           UPDATE DBGIRO
           SET Bank = :bank,
               TglGiro = :tanggalGiro,
               KreditRp = :nilaiGiro,
               Kredit = :nilaiGiroRp,
               Keterangan = :keterangan,
               Kodevls = :valas,
               Kurs = :kurs,
               TglBuka = :tanggalBuka,
               NoGiro = :noGiro
              WHERE NoGiro = :noGiroOld',
           [
               'bank' => $req->bank,
               'tanggalGiro' => $req->tanggalGiro,
               'nilaiGiro' => $req->nilaiGiroRp,
               'nilaiGiroRp' => $req->nilaiGiro,
               'keterangan' => $req->keterangan,
               'valas' => $req->valas,
               'kurs' => $req->kurs,
               'tanggalBuka' => $req->tanggalBuka,
               'noGiro' => $req->noGiro,
               'noGiroOld' => $req->noGiroOld
           ]
       );

       return $edit;
  }

  public function spEditTerima (Request $req) {
    $edit = DB::connection('SML')->update('
           UPDATE DBGIRO
           SET Bank = :bank,
               TglGiro = :tanggalGiro,
               Debet = :nilaiGiro,
               DebetRp = :nilaiGiroRp,
               Keterangan = :keterangan,
               Kodevls = :valas,
               Kurs = :kurs,
               TglBuka = :tanggalTerima,
               kas = :perkiraanKas,
               NoGiro = :noGiro
           WHERE NoGiro = :noGiroOld',
           [
               'bank' => $req->bank,
               'tanggalGiro' => $req->tanggalGiro,
               'nilaiGiro' => $req->nilaiGiro,
               'nilaiGiroRp' => $req->nilaiGiroRp,
               'keterangan' => $req->keterangan,
               'valas' => $req->valas,
               'kurs' => $req->kurs,
               'tanggalTerima' => $req->tanggalTerima,
               'perkiraanKas' => $req->perkiraanKas,
               'noGiro' => $req->noGiro,
               'noGiroOld' => $req->noGiroOld
           ]
       );

       return $edit;
  }

  public function spDetailTerima (Request $req) {
    $detail = DB::connection('SML')->select('SELECT NoGiro, Bank, TglGiro, Debet, Kredit, DebetRp, KreditRp, Keterangan, TglBuka, BuktiBuka, UrutBuktiBuka, TglCair, BuktiCair, KeteranganCair, UrutBuktiCair, Kodevls, Kurs, Jumlah, Tipe, FlagSimbol, Kas FROM DBGIRO where NoGiro = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function spDetailBuka (Request $req) {
    $detail = DB::connection('SML')->select('SELECT NoGiro, Bank, TglGiro, Debet, Kredit, DebetRp, KreditRp, Keterangan, TglBuka, BuktiBuka, UrutBuktiBuka, TglCair, BuktiCair, KeteranganCair, UrutBuktiCair, Kodevls, Kurs, Jumlah, Tipe, FlagSimbol, Kas FROM DBGIRO where NoGiro = :kode' , ['kode' => $req->kode]);
    return $detail;
  }

  public function loadAllValas () {
    $listData = DB::connection('SML')->select('SELECT KODEVLS, KURS from DBVALAS');
    return $listData;
  }

  public function loadPostingBank () {
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                       left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                       where a.Kode=\'BANK\' order by a.Perkiraan');
    return $listData;
  }

  public function loadPostingKas () {
    $listData = DB::connection('SML')->select('SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                       left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                       where a.Kode=\'KAS\' order by a.Perkiraan');
    return $listData;
  }


}
