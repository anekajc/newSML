<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\PenjagaTransaksi;


class PembelianPermintaanDebetNoteController extends Controller

// contoh desktop ce006
// sp_ProsesPostingHutPiut
// sp_ProsesPostingJurnalOto
{
  use PenjagaTransaksi;


  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Uang Muka Beli).
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index(Request $req) {
    $kodemenu = '0307';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    list($dnTglAwal, $dnTglAkhir) = $this->periodeRange($periode);

    // Baris tabel digambar JS lewat loadAll() (lihat pembelianpermintaandebetnote.blade.php),
    // jadi index() tidak lagi perlu menyiapkan tempOutstanding.
    return view('purchasing.pembelianpermintaandebetnote' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "dnTglAwal" => $dnTglAwal,
      "dnTglAkhir" => $dnTglAkhir,
      "akses" => $akses
    ]);

  }

  public function loadAll(Request $req)
{
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    // Konfigurasi kolom (susunan/lebar/desimal tersimpan per user) - sama seperti
    // UangMukaBeliController@loadAll.
    $reqHeader = new Request(['href' => 'pembelianpermintaandebetnote']);
    $header = app('App\Http\Controllers\HeaderTableController')->getHeaderTable($reqHeader);

    // Satu tabel gabungan (belum maupun sudah diotorisasi) - penyaringan otorisasi
    // dikerjakan di browser lewat modal Filter, sama seperti Uang Muka Beli.
    // Kolom dialiaskan supaya sama persis dengan alias di
    // HeaderTableController@getHeaderTable cabang 'pembelianpermintaandebetnote'.
    $tempData = DB::connection("SML")->select("
        SELECT
            A.NOBUKTI,
            A.NOBUKTI                              as [No Bukti],
            Convert(varchar(10), A.TANGGAL, 23)    as [Tanggal],
            A.KodeSupp                             as [Kode Supp],
            B.NAMACUSTSUPP                         as [Supplier],
            Isnull(D.NilaiRp,0)                    as [Nilai DN],
            A.IsOtorisasi1, A.OtoUser1, A.TglOto1
        FROM dbdebetnote A
        JOIN DBCUSTSUPP B ON A.KodeSupp = B.KODECUSTSUPP
        LEFT OUTER JOIN (select NoBukti, Sum(NilaiRp) NilaiRp from dbDebetNoteDet group by NoBukti) D
               ON D.NoBukti = A.NoBukti
        WHERE A.TANGGAL BETWEEN :tglawal AND :tglakhir
        ORDER BY A.TANGGAL DESC, A.NOBUKTI DESC
    ", [
        "tglawal" => $tglawal,
        "tglakhir" => $tglakhir
    ]);

    return [
      "listData1"         => $tempData,
      "aliasordered"      => $header['aliasordered'],
      "headertableheader" => $header['headertableheader'],
      "isnumeric"         => $header['isnumeric'],
      "headertablevalue"  => $header['headertablevalue'],
      "isparsed"          => $header['isparsed'],
      "isshown"           => $header['isshown'],
      "desimal"           => $header['desimal'],
    ];
}

  public function getDetail (Request $req ) {
        $tempOutstanding = DB::connection("SML")->select("Select  c.KodeSupp,d.NamaCustSupp,d.Alamat1,Right(a.noBukti,4)NoUrut,a.Urut,a.NoBukti,c.tanggal,Keterangan,A.NoInv, b.Saldo, b.SaldoD,
        a.Nilai,
        Case when a.kodevls='IDR' then 0.00 else a.Nilai end NilaiValas,
        a.kodeVls, a.Kurs, a.NilaiRp , c.IsOtorisasi1
  From  dbDebetNoteDet a
  Left Outer Join dbDebetNote c On c.NoBukti=a.NoBukti
  Left Outer Join (select a.NoFaktur,Sum(Saldo) Saldo, Sum(SaldoD) SaldoD
                  from dbo.vwHutPiut a
                  where a.Tipe='HT'
                  Group by a.NoFaktur) b On a.NoInv=B.Nofaktur
  Left Outer Join dbCustSupp d On d.KodeCustSupp=c.KodeSupp
  where a.NOBUKTI = :nobukti
  order by a.urut" , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $inisial = DB::connection("SML")->select('select DN from DBNOMOR');

    $values = [
        $inisial[0]->DN,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    return $noBukti;
  }


  public function listCustomer (Request $req) {

    $listData = DB::connection('SML')->select("select * from vwBrowsCustSupp where IsCustomer=0");
    return $listData;
  }

  public function listInvoice (Request $req) {

    // Hanya invoice yang belum lunas. Saldo dijumlah per NoFaktur saja: dulu dikelompokkan
    // per NoFaktur+Valas+Kurs, sehingga pelunasan valas dengan kurs lain (dan baris selisih
    // kurs IDR) jadi kelompok sendiri dan invoice lunas tetap tampil (mis. supplier K015).
    // Lunas dinilai per mata uang invoice (IDR: Saldo, valas: SaldoD). Sisa < 1 (hanya selisih
    // pembulatan di belakang koma, mis. 0,07) dianggap lunas, keputusan user 22 Okt 2026.
    // Valas/Kurs diambil dari baris pertama invoice dengan valas tsb.
    $listData = DB::connection('SML')->select("declare @awal Varchar(15)
    Select @Awal= :kodecustsupp
    select Cast(1 as Bit) Pilih,Convert(Numeric(18,2),0) Nilai, isnull(v.Valas,'IDR') KodeVls, isnull(v.Kurs,1) Kurs, s.NoFaktur, s.Tanggal, s.JatuhTempo,
           s.Saldo, s.SaldoD
    from (select a.NoFaktur, Min(a.Tanggal) Tanggal, Min(a.JatuhTempo) JatuhTempo, SUM(a.Saldo) Saldo, SUM(a.SaldoD) SaldoD,
                 nullif(max(case when isnull(a.Valas,'') not in ('','IDR') then a.Valas else '' end),'') ValasAsing
          from DBHUTPIUT a
          where a.KodeCustSupp=@Awal and a.Tipe='HT'
            and not exists (select 1 from dbdebetNoteDet b where b.NoInv=a.NoFaktur)
          Group by a.NoFaktur) s
    outer apply (select top 1 h.Valas, h.Kurs from DBHUTPIUT h
                 where h.NoFaktur=s.NoFaktur and h.KodeCustSupp=@Awal and h.Tipe='HT'
                   and isnull(h.Valas,'IDR')=isnull(s.ValasAsing,'IDR')
                 order by h.Tanggal, h.Urut) v
    where case when s.ValasAsing is null then s.Saldo else s.SaldoD end >= 1
    Order by s.Nofaktur" ,["kodecustsupp" => $req->kodecustsupp]);
    return $listData;

  }

public function updateOtorisasi(Request $req) {
    $dn = $this->headerDokumen('DBDEBETNOTE', $req->nobukti);
    if (!$dn) {
      return $this->tolak('Debet note ' . $req->nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    if ((int) $dn->isotorisasi1 === 1) {
      return $this->tolak('Debet note ' . $req->nobukti . ' sudah diotorisasi oleh ' . $dn->otouser1 . '.');
    }
    $tanggal = now();
    $res = DB::connection('SML')->update(
        "UPDATE dbdebetnote
         SET IsOtorisasi1 = 1, OtoUser1 = :username, TglOto1 = :tanggal
         WHERE NoBukti = :nobukti AND isnull(IsOtorisasi1, 0) = 0",
        [
            "username" => \Auth::user()->username,
            "tanggal" => $tanggal,
            "nobukti" => $req->nobukti
        ]
    );
    return response()->json(['success' => $res > 0]);
}


public function updateBatalOtorisasi(Request $req) {
    // Hak akses Batal dicek juga di server (bukan hanya di JS), dan alasan wajib diisi -
    // alasan dicatat ke dbLogFile seperti PembelianPermintaanAgenController@updateBatalOtorisasi.
    $akses = app('App\Http\Controllers\GlobalController')->getAkses('0307', 'pembelianpermintaandebetnote');
    if (!$akses || !$akses->IsBatal) {
      return response()->json(['message' => 'No access'], 403);
    }
    if (trim((string) $req->pket) === '') {
      return response()->json(['message' => 'Keterangan harus diisi'], 422);
    }
    $dn = $this->headerDokumen('DBDEBETNOTE', $req->nobukti);
    if (!$dn) {
      return $this->tolak('Debet note ' . $req->nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
    }
    if ((int) $dn->isotorisasi1 !== 1) {
      return $this->tolak('Debet note ' . $req->nobukti . ' belum diotorisasi.');
    }
    foreach (['NoInvoice', 'NOFAKTUR'] as $kolom) {
      $dph = $this->dipakaiOleh('DBDPHDET', $kolom, $req->nobukti);
      if ($dph) {
        return $this->tolak('Debet note ' . $req->nobukti . ' sudah dipakai di pelunasan ' . $dph . ', otorisasi tidak bisa dibatalkan.');
      }
    }

    $res = DB::connection('SML')->update(
        "UPDATE dbdebetnote SET isOtorisasi1 = 0, maxol = -1, OtoUser1 = '', TglOto1 = NULL WHERE NoBukti = :nobukti",
        [
            "nobukti" => $req->nobukti
        ]
    );

    app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'DN', $req->nobukti, $req->pket, 0, 'dbdebetnote');
    return $res;
}

  public function spAdd (Request $req) {

      $tempData = $req->input('tempData');
      $username = \Auth::user()->username;


      if ($req->tipeform == 'add' ) {
        $check = DB::connection('SML')->select('select * from DBDEBETNOTE where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
          if ($check) {
            return 2;
        }
      }

      if ($req->tipeform != 'add') {
        $dn = $this->headerDokumen('DBDEBETNOTE', $req->nobukti);
        if ($dn && (int) $dn->isotorisasi1 === 1) {
          return $this->tolak('Debet note ' . $req->nobukti . ' sudah diotorisasi, tidak bisa diubah.');
        }
      }
      if (empty($tempData) || !is_array($tempData)) {
        return response()->json(['message' => 'Tidak ada item dipilih'], 422);
      }

      // Semua invoice disimpan dalam SATU transaksi: kalau satu gagal, semuanya dibatalkan
      // (tidak ada DN yang tersimpan setengah). Sengaja memakai BEGIN TRAN eksplisit, bukan
      // $conn->beginTransaction(): pdo_sqlsrv memakai mode implicit transaction sehingga
      // @@TRANCOUNT masih 0 saat SP pertama dipanggil, dan Sp_DebetNote (yang punya
      // BEGIN/COMMIT TRAN sendiri) bisa memicu error 266. Dengan BEGIN TRAN di sini, COMMIT di
      // dalam SP hanya menurunkan @@TRANCOUNT; ROLLBACK di dalam SP (JikaSalah) membatalkan
      // semuanya, termasuk transaksi luar ini.
      $conn = DB::connection('SML');
      $conn->unprepared('BEGIN TRAN');
      try {
        foreach ($tempData as $d)  {
          $conn->statement('exec Sp_DebetNote ?,?,?,?,?,?,?,?,?,?,?,?', [
            "I",
            $req->nobukti,
            $req->tanggal,
            $d['Keterangan'] ?? '',
            0,
            $d['NoFaktur'],
            $req->kodecustsupp,
            $d['inputNilai'],
            $d['KodeVls'],
            $d['inputKurs'],
            $d['inputNilaiRp'],
            $req->nourut
          ]);

          // SP bisa me-ROLLBACK diam-diam (goto JikaSalah) tanpa melempar error ke PHP;
          // kalau itu terjadi transaksi luar ikut hilang, jadi dianggap gagal.
          $tc = $conn->select('select @@TRANCOUNT as tc');
          if (!$tc || (int) $tc[0]->tc < 1) {
            throw new \Exception('Sp_DebetNote membatalkan transaksi (invoice ' . $d['NoFaktur'] . ')');
          }
        }
        $conn->unprepared('COMMIT TRAN');
      } catch (\Throwable $e) {
        try { $conn->unprepared('IF @@TRANCOUNT > 0 ROLLBACK TRAN'); } catch (\Throwable $e2) {}
        \Log::error('Debet Note spAdd gagal (' . $req->nobukti . '): ' . $e->getMessage());
        return response()->json(['message' => 'Gagal menyimpan, tidak ada invoice yang tersimpan. Silakan ulangi.'], 500);
      }

      return 1;

  }

  public function spKoreksi (Request $req) {
  $dn = $this->headerDokumen('DBDEBETNOTE', $req->nobukti);
  if (!$dn) {
    return $this->tolak('Debet note ' . $req->nobukti . ' tidak ditemukan, mungkin sudah dihapus.');
  }
  if ((int) $dn->isotorisasi1 === 1) {
    return $this->tolak('Debet note ' . $req->nobukti . ' sudah diotorisasi, tidak bisa diubah.');
  }
  try {
    DB::connection('SML')->statement('exec Sp_DebetNote ?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $req->tanggal,
      $req->keterangan,
      $req->urut,
      $req->noinvoice,
      $req->kodecustsupp,
      $req->nilai,
      $req->kodevls,
      $req->kurs,
      $req->nilairp,
      $req->nourut,
    ]);

    return 1;

  } catch (\Exception $e) {
    \Log::error('SP ERROR: '.$e->getMessage());
    return response()->json(['error' => $e->getMessage()], 500);
  }
}

}
