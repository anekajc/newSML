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

class MasterPiutangController extends Controller
{

  public function index(Request $req) {

    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    $listData = DB::connection('SML')->select("SELECT d.Perkiraan,
        b.Keterangan + ' (' + b.Perkiraan + ')' AS kodePerk,
        a.NoFaktur, a.NoRetur, a.TipeTrans, c.KodeCustSupp,
        a.NoBukti, a.NoMsk, a.Urut, a.Tanggal,
        a.JatuhTempo, a.Debet, a.Kredit, a.Saldo, a.Valas, a.Kurs, a.DebetD, a.KreditD, a.SaldoD,
        a.KodeSales, a.Tipe, a.Catatan,
        b.Keterangan, c.NamaCustSupp AS NAMACUST, A.POCUST, A.KodeKebun, F.Nama AS NamaKebun
        FROM dbcustsupp c
        LEFT OUTER JOIN dbperkcustsupp d ON d.kodecustSupp = c.kodecustSupp
        LEFT OUTER JOIN vwhutpiut a ON c.kodecustsupp = a.kodecustsupp AND a.tipetrans = 'AWL' AND a.perkiraan = d.perkiraan
        LEFT OUTER JOIN (
            SELECT a.Perkiraan, a.Kode, b.Keterangan
            FROM dbperkiraan b
            INNER JOIN DBPOSTHUTPIUT a ON a.Perkiraan = b.Perkiraan
            WHERE a.Kode = 'PT'
        ) b ON b.perkiraan = d.perkiraan
        LEFT OUTER JOIN DBKEBUNCUSTSUPP F ON A.kodecustSupp = F.KOdeCustSupp AND A.KodeKebun = F.KodeKebun
        WHERE a.TipeTrans = 'AWL'
        ORDER BY c.kodecustsupp");

    $listDataCustomer = DB::connection('SML')->select('SELECT a.Perkiraan,b.keterangan from dbposthutpiut a
                       left outer join dbperkiraan b on b.perkiraan=a.perkiraan
                       where a.Kode=\'PT\' order by a.Perkiraan');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(2);

    return view('master.masterpiutang' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      "listData" => $listData,
      "listDataCustomer" => $listDataCustomer
    ]);

  }

  public function loadAll (Request $req) {
    $listData = DB::connection('SML')->select("SELECT d.Perkiraan,
        (b.Keterangan + ' (' + b.Perkiraan + ')') AS kodePerk,
        a.NoFaktur, a.NoRetur, a.TipeTrans, c.KodeCustSupp,
        a.NoBukti, a.NoMsk, a.Urut, a.Tanggal,
        a.JatuhTempo, a.Debet, a.Kredit, a.Saldo, a.Valas, a.Kurs, a.DebetD, a.KreditD, a.SaldoD,
        a.KodeSales, a.Tipe,  a.Catatan,
        b.Keterangan, c.NamaCustSupp AS NAMACUST, A.POcust
        FROM dbcustsupp c
        LEFT OUTER JOIN dbperkcustsupp d ON d.KodecustSupp = c.KODECUSTSUPP
        LEFT OUTER JOIN vwhutpiut a ON c.kodecustsupp = a.kodecustsupp and a.TipeTrans = 'AWL'
        LEFT OUTER JOIN (
            SELECT a.Perkiraan, a.Kode, b.Keterangan
            FROM dbperkiraan b
            INNER JOIN DBPOSTHUTPIUT a ON a.Perkiraan = b.Perkiraan
            WHERE a.Kode = 'PT'
        ) b ON b.perkiraan = d.perkiraan
        WHERE (b.Keterangan + ' (' + b.Perkiraan + ')') = :filter 
        ORDER BY c.kodecustsupp", ['filter' => $req->filter]);
    return $listData;
  }

  public function loadValas () {
    $listData = DB::connection('SML')->select('SELECT KODEVLS, NAMAVLS, KURS FROM DBVALAS');
    return $listData;
  }

  public function loadLokasiPenerima (Request $req) {
    $listData = DB::connection('SML')->select('SELECT KodeKebun,nama namaKebun from DbKebunCustSupp where kodecustsupp = :kodeCustSupp', ['kodeCustSupp' => $req->kodeCustSupp]);
    return $listData;
  }

  public function spAdd (Request $req) {
    $check = DB::connection('SML')->select("select top 1 1 as ada from DBHUTPIUT where NoFaktur = :noFaktur and KodeCustSupp = :kode and Tipe = 'PT' and TipeTrans = 'AWL'",
      ['noFaktur' => $req->noFaktur, 'kode' => $req->kodeSupplier]);
    if ($check) {
      return 'No. Faktur ini sudah ada';
    }
    $listData = DB::connection('SML')->update(
    'INSERT INTO DBHUTPIUT (NoFaktur, Perkiraan, NoRetur, KodeCustSupp, NoBukti, NoMsk, Urut, Tipe, TipeTrans, KodeSales,  Tanggal, JatuhTempo, Valas, Kurs, Debet, DebetD, Kredit, KreditD, POCUST, KODEKEBUN)
    VALUES (:noFaktur, :perkiraanSupplier , \'\', :kodeSupplier, \'\', 0, 1, \'PT\', \'AWL\', \'\', :tanggalFaktur, :jatuhTempo, :valas, :kurs, :jumlahRp, :jumlah, :kredit, :kreditD, :noPo, :lokasiPenerima)',
    [
        'noFaktur' => $req->noFaktur,
        'kodeSupplier' => $req->kodeSupplier,
        'perkiraanSupplier' => $req->perkiraanSupplier,
        'tanggalFaktur' => $req->tanggalFaktur,
        'jatuhTempo' => $req->jatuhTempo,
        'valas' => $req->valas,
        'kurs' => $req->kurs,
        'jumlah' => $req->jumlah,
        'jumlahRp' => $req->jumlahRp,
        'kredit' => $req->kredit,
        'kreditD' => $req->kreditRp,
        'noPo' => $req->noPo,
        'lokasiPenerima' => $req->lokasiPenerima
    ]
);
    return 1;

  }

  public function spDelete (Request $req) {


    //$check = DB::connection('SML')->select('SELECT * FROM DBDEPARTEMEN where KodeDepartemen = :kode' , ['kode' => $req->kode]);
    //if ($check) {
      //return 'ga bisa hapus';
    //}
    // Dulu: "delete from DBHUTPIUT where NoFaktur = :noFaktur" - ikut menghapus baris pelunasan dan
    // baris lain yang kebetulan bernomor faktur sama. Sekarang hanya baris saldo awal (AWL) milik
    // menu ini, dan ditolak kalau faktur itu sudah punya transaksi lain.
    $awal = DB::connection('SML')->select("select KodeCustSupp from DBHUTPIUT where NoFaktur = :noFaktur and Tipe = 'PT' and TipeTrans = 'AWL'
      and (:kode = '' or KodeCustSupp = :kode2)", ['noFaktur' => $req->noFaktur, 'kode' => (string) $req->kodeCustSupp, 'kode2' => (string) $req->kodeCustSupp]);
    if (!$awal) {
      return 'Data saldo awal tidak ditemukan';
    }
    if (count($awal) > 1) {
      return 'No. Faktur ini dipakai lebih dari satu, silakan refresh halaman lalu hapus dari barisnya';
    }
    $kode = $awal[0]->KodeCustSupp;
    $lain = DB::connection('SML')->select("select top 1 1 as ada from DBHUTPIUT where NoFaktur = :noFaktur and KodeCustSupp = :kode
      and Tipe = 'PT' and isnull(TipeTrans,'') <> 'AWL'", ['noFaktur' => $req->noFaktur, 'kode' => $kode]);
    if ($lain) {
      return 'Faktur ini sudah punya transaksi (pelunasan dll), tidak bisa dihapus';
    }
    DB::connection('SML')->update("delete from DBHUTPIUT where NoFaktur = :noFaktur and KodeCustSupp = :kode and Tipe = 'PT' and TipeTrans = 'AWL'",
      ['noFaktur' => $req->noFaktur, 'kode' => $kode]);
    return 1;
  }

  public function spEdit (Request $req) {
    $edit = DB::connection('SML')->update('
            UPDATE DBHUTPIUT
            SET Tanggal = :tanggalFaktur,
                JatuhTempo = :jatuhTempo,
                Valas = :valas,
                Kurs = :kurs,
                Debet = :jumlahRp,
                DebetD = :jumlah,
                POCUST = :noPo,
                KODEKEBUN = :lokasiPenerima
            WHERE NoFaktur = :noFaktur and Tipe = \'PT\' and TipeTrans = \'AWL\'
              and (:kodeCustSupp = \'\' or KodeCustSupp = :kodeCustSupp2)',
            // Dulu WHERE hanya NoFaktur - ikut mengubah baris pelunasan & baris lain dengan nomor
            // faktur yang sama. Sekarang hanya baris saldo awal (AWL) piutang ini.
            [
                'tanggalFaktur' => $req->tanggalFaktur,
                'jatuhTempo' => $req->jatuhTempo,
                'valas' => $req->valas,
                'kurs' => $req->kurs,
                'jumlah' => $req->jumlah,
                'jumlahRp' => $req->jumlahRp,
                'noPo' => $req->noPo,
                'noFaktur' => $req->noFaktur,
                'lokasiPenerima' => $req->lokasiPenerima,
                'kodeCustSupp' => (string) $req->kodeCustSupp,
                'kodeCustSupp2' => (string) $req->kodeCustSupp
            ]
        );

        return $edit;
  }

  public function spDetail (Request $req) {
    $detail = DB::connection('SML')->select("SELECT d.Perkiraan,
        (b.Keterangan + ' (' + b.Perkiraan + ')') AS kodePerk,
        a.NoFaktur, a.NoRetur, a.TipeTrans, c.KodeCustSupp,
        a.NoBukti, a.NoMsk, a.Urut, a.Tanggal,
        a.JatuhTempo, a.Debet, a.Kredit, a.Saldo, a.Valas, a.Kurs, a.DebetD, a.KreditD, a.SaldoD,
        a.KodeSales, a.Tipe,  a.Catatan,
        b.Keterangan, c.NamaCustSupp AS NAMACUST, A.POcust
        FROM dbcustsupp c
        LEFT OUTER JOIN dbperkcustsupp d ON d.KodecustSupp = c.KODECUSTSUPP
        LEFT OUTER JOIN vwhutpiut a ON c.kodecustsupp = a.kodecustsupp
        LEFT OUTER JOIN (
            SELECT a.Perkiraan, a.Kode, b.Keterangan
            FROM dbperkiraan b
            INNER JOIN DBPOSTHUTPIUT a ON a.Perkiraan = b.Perkiraan
            WHERE a.Kode = 'PT'
        ) b ON b.perkiraan = d.perkiraan
        WHERE a.NoFaktur = :kode and c.KodeCustSupp = :kodeCustSupp and a.TipeTrans = 'AWL'
        ORDER BY c.kodecustsupp", ['kode' => $req->kode, 'kodeCustSupp' => $req->kodeCustSupp]);
    // TipeTrans AWL: dulu tanpa filter ini res[0] bisa baris pelunasan faktur yang sama.
    return $detail;
  }

  public function spLokPenerima (Request $req) {
    $detail = DB::connection('SML')->select("SELECT d.Perkiraan,
        b.Keterangan + ' (' + b.Perkiraan + ')' AS kodePerk,
        a.NoFaktur, a.NoRetur, a.TipeTrans, c.KodeCustSupp,
        a.NoBukti, a.NoMsk, a.Urut, a.Tanggal,
        a.JatuhTempo, a.Debet, a.Kredit, a.Saldo, a.Valas, a.Kurs, a.DebetD, a.KreditD, a.SaldoD,
        a.KodeSales, a.Tipe, a.Catatan,
        b.Keterangan, c.NamaCustSupp AS NAMACUST, A.POCUST, A.KodeKebun, F.Nama AS NamaKebun
        FROM dbcustsupp c
        LEFT OUTER JOIN dbperkcustsupp d ON d.kodecustSupp = c.kodecustSupp
        LEFT OUTER JOIN vwhutpiut a ON c.kodecustsupp = a.kodecustsupp AND a.tipetrans = 'AWL' AND a.perkiraan = d.perkiraan
        LEFT OUTER JOIN (
            SELECT a.Perkiraan, a.Kode, b.Keterangan
            FROM dbperkiraan b
            INNER JOIN DBPOSTHUTPIUT a ON a.Perkiraan = b.Perkiraan
            WHERE a.Kode = 'PT'
        ) b ON b.perkiraan = d.perkiraan
        LEFT OUTER JOIN DBKEBUNCUSTSUPP F ON A.kodecustSupp = F.KOdeCustSupp AND A.KodeKebun = F.KodeKebun
        where c.KodeCustSupp = :kodeCustSupp and NoFaktur = :noFaktur
        ORDER BY c.kodecustsupp", ['kodeCustSupp' => $req->kodeCustSupp, 'noFaktur' => $req->noFaktur]);
    return $detail;
  }



}
