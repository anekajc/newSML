<?php


namespace App\Http\Controllers\Accounting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;





class MemorialKoreksiController extends Controller


{

  // Nama href untuk DBHEADERTABLE - dipatok, harus sama persis dengan MK_HREF di blade.
  const HREF = 'memorialkoreksi';

  // Kolom default tabel daftar Memorial/Koreksi. Nama key HARUS sama persis dengan alias
  // kolom di queryDaftar() karena dipakai juga sebagai nama field data di JS.
  // tipe: 0 = varchar, 1 = float, 2 = date.
  // IsOtorisasi1/OtoUser1/TglOto1 sengaja TIDAK dimasukkan - itu kolom teknis untuk filter
  // otorisasi dan kolom Oto/User Oto/Tgl Oto yang selalu ditambahkan lewat JS.
  private function kolomDefault () {
    return [
      'No Bukti'   => 0,
      'Tanggal'    => 2,
      'Trans'      => 0,
      'Perkiraan'  => 0,
      'Keterangan' => 0,
      'Jumlah Rp'  => 1,
    ];
  }

  // Rentang tanggal default = satu bulan penuh periode kerja user, sama seperti
  // PengajuanDPPController@periodeRange.
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  // Rentang tanggal yang diminta browser, dijatuhkan ke periode kerja bila tidak valid.
  private function rentangTanggal (Request $req, $periode) {
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
    return [$tglawal, $tglakhir];
  }

  /**
   * Susunan/tampil/desimal kolom milik user untuk tabel daftar Memorial/Koreksi.
   * Sama persis polanya dengan PengajuanDPPController@headerTable supaya blade bisa
   * memakai ReportTable (geser & sembunyikan kolom) lewat route 'saveheadertable' generik.
   */
  private function headerTable ($reset = false) {
    $username = \Auth::user()->username;

    if ($reset) {
      DB::connection("SML")->update(
        "delete from DBHEADERTABLE where username = :username and href = :href",
        ["username" => $username, "href" => self::HREF]
      );
    }

    $headertable = DB::connection("SML")->select(
      "select * from dbheadertable where href = :href and username = :username",
      ["username" => $username, "href" => self::HREF]
    );

    $headertableheader = [];
    $headertablevalue = [];
    $isnumberheadertable = [];
    $headerisshown = [];
    $isparsed = 0;

    if (count($headertable) > 0) {
      $isnumberheadertable = json_decode($headertable[0]->isnumber);
      $headertablevalue    = json_decode($headertable[0]->value);
      $headertableheader   = json_decode($headertable[0]->header);
      $headerisshown       = json_decode($headertable[0]->isshown);
    } else {
      $isparsed = 1;
      foreach ($this->kolomDefault() as $key => $tipe) {
        array_push($headertablevalue, $key);
        array_push($headertableheader, $key);
        array_push($headerisshown, 1);
        $isnumberheadertable[] = $tipe;
      }
    }

    $aliasOrdered = [];
    foreach ($headertablevalue as $header) {
      array_push($aliasOrdered, ["value" => $header, "alias" => $header]);
    }

    // Desimal dititipkan sebagai JSON array di kolom `tipe` DBHEADERTABLE.
    $tersimpan = [];
    if (count($headertable) > 0) {
      $decoded = json_decode($headertable[0]->tipe);
      if (is_array($decoded)) { $tersimpan = $decoded; }
    }

    $desimal = [];
    foreach ($isnumberheadertable as $i => $tipe) {
      if (isset($tersimpan[$i]) && is_numeric($tersimpan[$i])) {
        $nilai = (int) $tersimpan[$i];
        if ($nilai < 0) { $nilai = 0; }
        if ($nilai > 4) { $nilai = 4; }
        array_push($desimal, $nilai);
      } else {
        array_push($desimal, ((int) $tipe === 1) ? 2 : 0);
      }
    }

    return [
      "aliasordered"      => $aliasOrdered,
      "headertableheader" => $headertableheader,
      "headertablevalue"  => $headertablevalue,
      "isnumeric"         => $isnumberheadertable,
      "isshown"           => $headerisshown,
      "isparsed"          => $isparsed,
      "desimal"           => $desimal,
    ];
  }

  // Dipanggil tombol "Reset kolom" di bar kolom tersembunyi (ReportTable).
  public function resetHeader () {
    return $this->headerTable(true);
  }

  // Daftar Memorial/Koreksi dalam rentang tanggal, satu baris per NoBukti, terbaru di atas.
  // Kolom Perkiraan diambil dari baris detail pertama (dbTransaksi.Urut) tiap NoBukti -
  // memorial bisa punya banyak perkiraan, jadi nilai ini bersifat indikatif.
  private function queryDaftar ($tglawal, $tglakhir, $username) {
    return DB::connection("SML")->select("
select  A.NoUrut, A.NoBukti,
        A.NoBukti                            as [No Bukti],
        convert(varchar(10), A.Tanggal, 23)  as [Tanggal],
        A.TipeTransHd                        as [Trans],
        isnull((select top 1 B2.Perkiraan from dbTransaksi B2
                where B2.NoBukti = A.NoBukti order by B2.Urut), '') as [Perkiraan],
        isnull(A.Note, '')                   as [Keterangan],
        sum((B.Debet) * B.Kurs)              as [Jumlah Rp],
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1
from dbTrans A
left outer join dbTransaksi B on B.NoBukti=A.NoBukti
where A.Tanggal between :tglawal and :tglakhir and
 A.TipeTransHd in ('BMM','BJK') and isnull(A.Jenis,0)=0
       And
       A.nobukti Not in (
        select A.NoBukti
        from dbtrans A
        left outer join dbTransaksi b on a.NoBukti=b.NoBukti
        where A.TipeTransHd in  ('BMM','BJK')
        and
        (B.Perkiraan not in (select Perkiraan from DBAKSESPERKIRAAN where UserID=:username1)
        or
        B.lawan not in (select Perkiraan from DBAKSESPERKIRAAN where UserID=:username2))
        group by A.NoBukti
       )
group by A.NoUrut, A.NoBukti, A.Tanggal, A.Note, A.TipeTransHd,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1
order by A.Tanggal desc, A.NoBukti desc
" , [
      "tglawal" => $tglawal,
      "tglakhir" => $tglakhir,
      "username1" => $username,
      "username2" => $username,
    ]);
  }

  public function index(Request $req) {
    $kodemenu = '02015';

    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu,$req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);
    $username = \Auth::user()->username;

    list($mkTglAwal, $mkTglAkhir) = $this->periodeRange($periode);

    $devisi = DB::connection("SML")->select("select devisi, namadevisi from dbdevisi");

    return view('accounting.memorialkoreksi' , array_merge([
      "menul0" => $menul0,
      "periode" => $periode,
      "akses" => $akses,
      "devisi" => $devisi,
      "mkTglAwal" => $mkTglAwal,
      "mkTglAkhir" => $mkTglAkhir,
    ], $this->headerTable()));

  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakMemo ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function loadAll (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->rentangTanggal($req, $periode);

    $tempOutstanding = $this->queryDaftar($tglawal, $tglakhir, $username);

    return array_merge(["tempOutstanding" => $tempOutstanding], $this->headerTable());
  }



  public function getDetail (Request $req ) {



        $tempOutstanding = DB::connection("SML")->select("select a.*,b.keterangan as namaPerkiraan,d.Keterangan NamaLawan, '' MyID,
case when a.valas<>'IDR' then a.debet+ a.kredit
     else 0
end as Jumlah,
a.DebetRp+a.KreditRp as JumlahRp,c.NamaDevisi,
Case when a.TPHC='C' then '[C]ash'
     When a.TPHC='T' then '[T]ransfer'
     when a.TPHC='H' then '[H]utang Giro'
     when a.TPHC='P' then '[P]iutang Giro'
     else ''
end MyTPHC,e.NamaBag,f.nourut,g.NAMACUSTSUPP NamaCustSuppP,h.NAMACUSTSUPP NamaCustSuppL,
i.Kode KodePerkiraan
from dbtransaksi a
     left outer join dbperkiraan b on a.perkiraan=b.perkiraan
     left outer join dbdevisi c on c.Devisi=a.Devisi
     left outer join dbPerkiraan d on d.Perkiraan=a.Lawan
     left outer join dbBagian e on e.kodebag=a.kodebag
     left outer join dbTrans f on f.nobukti=a.nobukti
     left outer join dbCustSupp g on g.KODECUSTSUPP=a.CustSuppP
     left outer join dbCustSupp h on h.KODECUSTSUPP=a.CustSuppL
     -- Kode perkiraan sisi Debet dari dbPostHutPiut, dipakai blade untuk mengenali perkiraan
     -- Titipan Customer ('PTS') tanpa mematok nomor perkiraannya. Di-group dulu supaya satu
     -- perkiraan tidak menggandakan baris kalau punya lebih dari satu entri.
     left outer join (select Perkiraan, min(Kode) Kode from dbPostHutPiut group by Perkiraan) i
          on i.Perkiraan=a.Perkiraan
where a.nobukti= :nobukti
Order by a.Nobukti,a.Urut
        " , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }


    // Daftar perkiraan SAMA untuk semua jenis transaksi (BMM & BJK). Perkiraan piutang usaha
    // (Kode 'PT'), titipan customer ('PTS'), dan hutang usaha ('HT') ikut tampil di keduanya.
    //
    // Yang membedakan BMM & BJK BUKAN daftarnya, melainkan apa yang terjadi SETELAH dipilih:
    //   BMM - memicu rantai browse Customer/Supplier -> modal Kartu (lihat loadKartuPT()).
    //   BJK - tidak ada rantai itu; perkiraannya dipakai sebagai perkiraan biasa saja.
    // Pembatasan itu dikerjakan di blade lewat mkAlurKartuAktif(), BUKAN dengan menyembunyikan
    // perkiraannya di sini. $req->transaksi sengaja tidak dipakai lagi di method ini.
    public function listPerkiraan (Request $req) {

      $username = \Auth::user()->username;

      return DB::connection('SML')->select("
        select a.Perkiraan, a.Keterangan, a.Simbol, C.Kode, C.IsLokalOrExim from dbPerkiraan a
              left Outer join dbAksesPerkiraan b on b.Perkiraan=a.Perkiraan
               Left Outer Join (select perkiraan,kode,IsLokalOrExim from dbPOSTHUTPIUT group by perkiraan,kode,IsLokalOrExim) C on A.Perkiraan=C.Perkiraan
              where a.Tipe=1 and b.UserID = :username
              order by a.Perkiraan
      ", [ "username" => $username ]);
    }

    /* ==================================================================================
       AKTIVA TETAP ('AKV') DAN AKUMULASI PENYUSUTAN ('AKM') - dbPOSTHUTPIUT.Kode
       ----------------------------------------------------------------------------------
       'P' = kolom Perkiraan dbTransaksi = sisi Debet, 'L' = kolom Lawan = sisi Kredit -
       lihat pola yang sama di public/js/kas.js. Tidak ada dbTransaksiDet di database ini;
       nomor aktivanya menempel langsung di baris dbTransaksi.

       AKV (aktiva tetap):
         Sisi DEBET  : user MENAMBAH aktiva baru ke master dbAktiva (daftar yang tampil cuma
                       informasi, barisnya tidak bisa diklik), lalu aktiva baru itu langsung
                       dipakai sebagai NoAktivaP dengan StatusAktivaP = 'AKV+'.
         Sisi KREDIT : user MEMILIH aktiva yang sudah ada -> NoAktivaL + 'AKV-'.
         Dalam SATU No. Bukti hanya boleh ada satu aktiva AKV.

       AKM (akumulasi penyusutan):
         Kedua sisi sama: user MEMILIH aktiva yang sudah ada, tidak ada penambahan master.
         Debet -> NoAktivaP + 'AKM+', Kredit -> NoAktivaL + 'AKM-'.
         TIDAK ada batasan satu per bukti, dan Debet + Kredit boleh sama-sama AKM asal
         perkiraannya berbeda - itu sudah dijamin aturan "Debet dan Kredit tidak boleh
         perkiraan yang sama" yang lebih dulu jalan di blade.
       ================================================================================== */

    // Isi modal browse aktiva untuk satu perkiraan AKV (mis. '121102' Bangunan) atau AKM
    // (mis. '121202' Akum. Penyusutan Bangunan).
    //
    // Setting default form aktiva baru (Akumulasi, % susut, metode, biaya penyusutan) diambil
    // dari dbPostHutPiut - nomor perkiraannya TIDAK pernah di-hardcode di kode, sama seperti
    // yang sudah dilakukan untuk 'PT'/'HT'/'PTS' di listPerkiraan().
    public function listAktiva (Request $req) {

      $setting = DB::connection('SML')->select("
        select a.Perkiraan, a.Kode, a.Akumulasi, a.Persen, a.Tipe,
               a.Biaya1, a.PersenBiaya1, a.Biaya2, a.PersenBiaya2,
               b.Keterangan NamaAkumulasi
        from dbPostHutPiut a
        left outer join dbPerkiraan b on b.Perkiraan = a.Akumulasi and b.Tipe = 1
        where a.Perkiraan = :perkiraan and a.Kode in ('AKV', 'AKM')
      ", [ "perkiraan" => $req->perkiraan ]);

      // Bukan perkiraan aktiva (atau set postingnya belum diisi di master) - blade akan
      // memperlakukannya sebagai perkiraan biasa.
      if (!$setting) {
        return [ "setting" => null, "rows" => [] ];
      }

      // Daftar aktiva dikenali lewat perkiraan AKUMULASI PENYUSUTAN-nya. Untuk perkiraan AKV
      // akumulasi itu diambil dari set postingnya (121102 -> 121202); untuk perkiraan AKM,
      // perkiraan itu SENDIRI yang jadi akumulasinya (121202 -> 121202). Query di bawah sama
      // persis untuk keduanya, hanya nilai parameternya yang berbeda.
      $kode = strtoupper(trim((string)$setting[0]->Kode));
      $akumulasi = $kode === 'AKM' ? $setting[0]->Perkiraan : $setting[0]->Akumulasi;

      // Sengaja TIDAK disaring per Devisi - semua divisi ikut tampil.
      $rows = DB::connection('SML')->select("
        Select A.Devisi,b.NamaBag,A.Perkiraan, A.Keterangan,A.Tanggal,
         Case when A.Tipe='L' then '[L]urus'
           when A.Tipe='M' then '[M]enurun'
           when A.Tipe='P' then '[P]ajak'
           else ''
         end Metode,A.Persen,A.Quantity,A.Kodebag,
         A.Akumulasi, D.Keterangan NamaAkumulasi,
         A.Nomuka,C.Keterangan NamaGroupAktiva,A.noBelakang,A.NoBelakang2, A.Biaya,a.biaya2,a.persenbiaya1,a.persenbiaya2,
         E.NamaDevisi,a.TipeAktiva,a.Kelompok
         From DBAktiva A
         left outer join dbBagian b on b.kodebag=a.kodebag
         left outer join dbperkiraan c on c.perkiraan=a.Nomuka and c.tipe=1
         left outer join dbperkiraan d on d.perkiraan=a.Akumulasi and d.Tipe=1
         left outer join dbDevisi e on e.Devisi=a.Devisi
         Where A.NoMuka='' or A.Akumulasi = :akumulasi
         order by A.Perkiraan
      ", [ "akumulasi" => $akumulasi ]);

      return [ "setting" => $setting[0], "rows" => $rows ];
    }

    // No. urut berikutnya untuk satu group aktiva -> jadi NoBelakang ('00003') dan bagian
    // belakang No. Aktiva ('121102.00003'). Query identik dengan KasController::getNoUrutAktiva().
    public function getNoUrutAktiva (Request $req) {

      return DB::connection('SML')->select("
        SELECT RIGHT('00000' + (CAST(ISNULL(MAX(CONVERT(int, NoBelakang)), 0) + 1 AS VARCHAR(10))), 5) AS NoUrut
        FROM dbAktiva where nomuka = :nomuka
      ", [ "nomuka" => $req->nomuka ]);
    }

    // Simpan aktiva baru ke master lewat SP_AktivaTetap (SP yang sudah ada, tidak diubah).
    // Return 2 = No. Aktiva sudah dipakai (user lain keburu memakai no. urut itu), blade
    // tinggal mengambil ulang no. urutnya. Return 1 = berhasil.
    public function spAddNewAktiva (Request $req) {

      $check = DB::connection('SML')->select('select * from dbaktiva where Perkiraan = :noaktiva', [ "noaktiva" => $req->noaktiva ]);
      if ($check) {
        return 2;
      }

      DB::connection('SML')->statement('exec SP_AktivaTetap ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
        $req->choice,                              // @Choice
        $req->devisi,                              // @Devisi
        $req->noaktiva,                            // @Perkiraan
        $req->keterangan,                          // @Keterangan
        $req->kuantum,                             // @Quantity
        $req->persen,                              // @Persen
        $req->tglpemakaian,                        // @Tanggal
        $req->metodepenyusutan,                    // @Tipe
        $req->akumulasi,                           // @Akumulasi
        $req->biaya1 ? $req->biaya1 : '',          // @Biaya
        $req->groupaktiva,                         // @NoMuka
        $req->nobelakang,                          // @NoBelakang
        $req->biaya2 ? $req->biaya2 : '',          // @biaya2
        $req->persen1 ? $req->persen1 : '0.00',    // @persen1
        $req->persen2 ? $req->persen2 : '0.00',    // @persen2
        $req->biaya3 ? $req->biaya3 : '',          // @biaya3
        $req->persen3 ? $req->persen3 : '0.00',    // @persenbiaya3
        '',                                        // @biaya4
        0,                                         // @persenbiaya4
        $req->tipeaktiva,                          // @TipeAktiva
        '',                                        // @Bagian
        '',                                        // @NoBelakang2
        0,                                         // @IsHeader
        '',                                        // @NoAktivaHd
        $req->tglperolehan,                        // @TglPeroleh
      ]);

      return 1;
    }

    // Nama kolom mutasi dbAktivaDet untuk satu status aktiva + sisi. Mengembalikan '' kalau
    // statusnya bukan aktiva, sehingga baris itu tidak dibukukan sama sekali.
    //
    //   AKV (nilai perolehan)      -> MD (Debet) / MK (Kredit)
    //   AKM (akumulasi penyusutan) -> SD (Debet) / SK (Kredit)
    //
    // Pemetaan AKM -> SK terbaca dari data yang sudah ada: 6.434 baris ber-StatusAktivaL
    // 'AKM-' nilainya persis sama dengan dbAktivaDet.SK pada bulan/tahun transaksinya,
    // sementara MD hanya dipakai baris 'AKV+'.
    private function kolomAktivaDet ($status, $sisi) {
      $jenis = strtoupper(substr(trim((string)$status), 0, 3));
      if ($jenis !== 'AKV' && $jenis !== 'AKM') { return ''; }
      return ($jenis === 'AKV' ? 'M' : 'S') . ($sisi === 'P' ? 'D' : 'K');
    }

    // Mutasi satu baris dbAktivaDet (per No. Aktiva + Bulan + Tahun). $kolom hanya boleh
    // salah satu dari MD/MK/SD/SK - di-whitelist, bukan dari request mentah, karena ia
    // disisipkan ke SQL sebagai nama kolom.
    //
    // Baris yang sudah ada DIAKUMULASIKAN (bukan sekadar insert kalau belum ada seperti
    // KasController), supaya edit/hapus item bisa membalik nilainya dengan benar.
    // DMD/DMK/Valas/Kurs dibiarkan 0/kosong - mengikuti bentuk baris yang sudah ada di tabel.
    private function mutasiAktivaDet ($noaktiva, $kolom, $nilai, $devisi, $bulan, $tahun) {

      if (!$noaktiva || !in_array($kolom, ['MD', 'MK', 'SD', 'SK']) || !(float)$nilai) { return; }

      $check = DB::connection('SML')->select("
        select Perkiraan from dbAktivaDet where Perkiraan = :perkiraan and Bulan = :bulan and Tahun = :tahun
      ", [ "perkiraan" => $noaktiva, "bulan" => $bulan, "tahun" => $tahun ]);

      if ($check) {
        DB::connection('SML')->update("
          update dbAktivaDet set $kolom = $kolom + :nilai
          where Perkiraan = :perkiraan and Bulan = :bulan and Tahun = :tahun
        ", [ "nilai" => $nilai, "perkiraan" => $noaktiva, "bulan" => $bulan, "tahun" => $tahun ]);
        return;
      }

      DB::connection('SML')->statement("
        insert into DBAKTIVADET (Perkiraan, Bulan, Tahun, Devisi, Valas, Kurs, Awal, AwalSusut, MD, DMD, MK, DMK, SD, DSD, SK, DSK)
        values (:perkiraan, :bulan, :tahun, :devisi, '', 0, 0, 0, :md, 0, :mk, 0, :sd, 0, :sk, 0)
      ", [
        "perkiraan" => $noaktiva, "bulan" => $bulan, "tahun" => $tahun, "devisi" => $devisi,
        "md" => $kolom === 'MD' ? $nilai : 0,
        "mk" => $kolom === 'MK' ? $nilai : 0,
        "sd" => $kolom === 'SD' ? $nilai : 0,
        "sk" => $kolom === 'SK' ? $nilai : 0,
      ]);
    }

    /* ==================================================================================
       PENAMBAHAN PIUTANG USAHA (Debet = perkiraan ber-Kode 'PT' di dbPOSTHUTPIUT) DAN
       PELUNASAN/PENAMBAHAN HUTANG USAHA (perkiraan ber-Kode 'HT') - dua alur kembar yang
       memakai modal, endpoint, dan tabel kerja yang SAMA, dibedakan lewat $req->jenis
       ('PT'/'HT', lihat jenisHP()).
       ----------------------------------------------------------------------------------
       Alur: browse Perkiraan -> pilih perkiraan PT/HT -> browse Customer/Supplier -> modal
       Kartu. Di modal Kartu, faktur outstanding ditampilkan sebagai informasi (read-only)
       dan user bisa menambah baris baru sebesar nilai memorial.

       Piutang (PT): Debet = MENAMBAH piutang, Kredit = PELUNASAN piutang.
       Hutang  (HT): Debet = PELUNASAN hutang, Kredit = MENAMBAH hutang - PERSIS KEBALIKANNYA.
       Outstanding piutang = sisa Debet (sum(Debet-Kredit)>0), outstanding hutang = sisa
       Kredit (sum(Kredit-Debet)>0) - lihat loadKartuPT(). Kedua fakta ini dikonfirmasi
       langsung dari data lama program desktop, bukan tebakan.

       Cara datanya tersimpan (dibaca langsung dari definisi SP di SQL Server):

       - Baris kerja ditampung di dbTempHutPiut, dipisah per user lewat kolom IDUser (jadi
         satu user hanya bisa punya SATU kartu terbuka - piutang atau hutang - dalam satu
         waktu; lihat validasi "satu sisi" di blade).
       - Baris outstanding di-seed dengan INSERT biasa sehingga StatusUID-nya NULL.
       - Baris yang DITAMBAH user lewat sp_TempHutPiut choice 'I' otomatis ber-StatusUID 'I',
         choice 'D' menandai StatusUID 'D' (soft delete).
       - sp_TransaksiMemorial (dipanggil di spAdd()) yang MEMINDAHKAN baris temp ke DBHUTPIUT
         permanen, dengan filter: NoBukti=@Nobukti and NoMsk=@Urut and StatusUID in ('I','U').
         Jadi hanya baris tambahan user yang ikut diposting - baris outstanding yang StatusUID
         NULL tidak pernah dobel-posting. Tidak ada pemanggilan SP posting terpisah di sini.
         SP ini generik - @Tipe disalin apa adanya dari dbTempHutPiut, jadi HT jalan tanpa
         perubahan apa pun di SQL Server.

       Konvensi nilai di bawah ini mengikuti data memorial lama yang memakai fitur yang sama
       (mis. NoBukti SMX/BMM/00003/0622 untuk PT, SMX/BMM/00004/0316 untuk HT): TipeTrans='L'
       (SAMA untuk PT & HT), Tipe='PT'/'HT', Valas IDR/Kurs 1, KursBayar 1, DebetD/KreditD 0.
       NoInvoice='TBH' menandai baris TAMBAHAN (Debet untuk PT, Kredit untuk HT), 'LNS'
       menandai baris PELUNASAN (Kredit untuk PT, Debet untuk HT) - lihat penandaBaris().
       NoInvoice sekaligus jadi penanda baris buatan user, supaya tombol Hapus hanya muncul
       di baris itu.

       PT, PTS (titipan customer), dan HT sengaja HANYA berlaku di transaksi BMM - lihat
       filter Kode in ('HT','PT','PTS') di cabang BJK pada listPerkiraan().
       ================================================================================== */

    // Browse Customer/Supplier untuk perkiraan piutang/hutang usaha. vwBrowsCustSupp.Perkiraan
    // sudah berisi perkiraan piutang/hutang milik masing-masing customer/supplier, jadi tinggal
    // disaring dengan perkiraan yang dipilih user - jangan dipatok '113100'/'21201', kode
    // perkiraan tidak pernah dihardcode di project ini.
    public function listCustomerPT (Request $req) {

      $listData = DB::connection('SML')->select("
        select A.KODECUSTSUPP, A.NAMACUSTSUPP, A.ALAMAT, A.NAMAKOTA, A.ALAMATKOTA, A.PPN, A.Hari
        from vwBrowsCustSupp A
        where A.Perkiraan = :perkiraan
        order by A.KodeCustSupp
      ", [ "perkiraan" => $req->perkiraan ]);

      return $listData;
    }

    /**
     * NoMsk yang dipakai baris dbTempHutPiut supaya nanti terangkut oleh sp_TransaksiMemorial.
     *
     * sp_TransaksiMemorial memindahkan baris temp ke DBHUTPIUT dengan syarat NoMsk = Urut baris
     * dbTransaksi milik item memorial ini. Pada choice 'I' SP menghitung sendiri urutnya
     * (MAX(Urut)+1 untuk NoBukti tsb) dan MENGABAIKAN urut yang dikirim client - karena itu
     * untuk item BARU nilainya harus ditebak dengan rumus yang sama persis. Untuk item yang
     * sedang diedit, urutnya sudah pasti dan dikirim apa adanya oleh blade.
     */
    private function nomskItem ($nobukti, $urut) {
      if ((int) $urut > 0) { return (int) $urut; }

      $row = DB::connection('SML')->select("
        select isnull(max(Urut),0) + 1 as NoMsk from dbTransaksi where NoBukti = :nobukti
      ", [ "nobukti" => $nobukti ]);

      return $row ? (int) $row[0]->NoMsk : 1;
    }

    // Isi tabel kartu: baris temp milik user untuk perkiraan & tipe D. Query sama persis dengan
    // yang dipakai form Penambahan Piutang di aplikasi desktop, termasuk kolom MyKey sebagai
    // identitas baris dan urutan berdasarkan tanggal faktur terawal.
    private function queryKartuPT ($username, $perkiraan, $tipedk) {
      return DB::connection('SML')->select("
        declare @IDUser varchar(30), @Perkiraan varchar(30), @TipeDK varchar(1)

        select @IDUser = :username, @Perkiraan = :perkiraan, @TipeDK = :tipedk

        select A.NoFaktur+convert(varchar(8),A.Tanggal,102)+right('00000000'+cast(A.Urut as varchar(8)),8)+A.NoRetur MyKey,
               A.NoFaktur, A.NoRetur, A.TipeTrans, A.KodeCustSupp, A.NoBukti, A.NoMsk, A.Urut,
               A.Tanggal, A.JatuhTempo, A.Debet, A.Kredit, A.Saldo, A.Valas, A.Kurs,
               A.DebetD, A.KreditD, A.SaldoD, A.KodeSales, A.Tipe, A.Perkiraan, A.Catatan,
               A.IDUser, A.StatusUID, A.TipeDK, A.NoInvoice, A.Valas_, A.Kurs_, A.KursBayar
        from dbTempHutPiut A
        left outer join
                (select NoFaktur, KodeCustSupp, min(Tanggal) Tanggal from dbTempHutPiut
                where IDUser=@IDUser and Perkiraan=@Perkiraan and TipeDK=@TipeDK
                group by NoFaktur, KodeCustSupp
                ) B on B.NoFaktur=A.NoFaktur and B.KodeCustSupp=A.KodeCustSupp
        where A.IDUser=@IDUser and A.Perkiraan=@Perkiraan and A.TipeDK=@TipeDK and isnull(A.StatusUID,'')<>'D'
        order by B.Tanggal, A.NoFaktur, A.Urut
      ", [ "username" => $username, "perkiraan" => $perkiraan, "tipedk" => $tipedk ]);
    }

    // 'D' (menambah piutang, dari sisi Debet) atau 'K' (pelunasan, dari sisi Kredit). Dipakai
    // untuk memisahkan baris kerja kedua alur di dbTempHutPiut. Nilai apa pun selain 'K'
    // dianggap 'D' supaya request lama/tanpa parameter tetap berperilaku seperti semula.
    private function tipeDK (Request $req) {
      return $req->tipedk === 'K' ? 'K' : 'D';
    }

    // Penanda baris buatan user di kolom NoInvoice: 'TBH' (Tambah) untuk alur yang MENAMBAH,
    // 'LNS' (Lunas) untuk alur PELUNASAN. Konvensi ini diambil dari data memorial lama yang
    // memakai fitur yang sama - lihat catatan besar di atas.
    //
    // Piutang (PT): Debet=menambah ('TBH'), Kredit=pelunasan ('LNS').
    // Hutang  (HT): Debet=pelunasan ('LNS'), Kredit=menambah ('TBH') - PERSIS KEBALIKANNYA,
    // dikonfirmasi dari data lama ber-NoBukti SMX/BMM/00004/0316 (Debet+NoInvoice='LNS',
    // Kredit+NoInvoice='TBH' untuk Tipe='HT').
    private function penandaBaris ($tipedk, $jenis = 'PT') {
      if ($jenis === 'HT') {
        return $tipedk === 'K' ? 'TBH' : 'LNS';
      }
      return $tipedk === 'K' ? 'LNS' : 'TBH';
    }

    // 'PT' (piutang usaha) atau 'HT' (hutang usaha). Nilai lain diabaikan dan dianggap 'PT'
    // supaya request lama/tanpa parameter tetap berperilaku seperti semula.
    private function jenisHP (Request $req) {
      return $req->jenis === 'HT' ? 'HT' : 'PT';
    }

    // Dipanggil sekali saat modal Kartu dibuka: bersihkan baris temp milik user, lalu seed
    // faktur outstanding customer tsb. Faktur yang berasal dari item memorial ini sendiri
    // dikecualikan supaya tidak ikut terhitung sebagai outstanding.
    public function loadKartuPT (Request $req) {

      $username = \Auth::user()->username;
      $nomsk = $this->nomskItem($req->nobukti, $req->urut);
      $tipedk = $this->tipeDK($req);
      $jenis = $this->jenisHP($req);

      // Kunci baris milik item memorial ini: NoBukti + NoMsk yang dipad 4 digit, sama dengan
      // bentuk yang dipakai di query aslinya.
      $kunciBukti = $req->nobukti . str_pad($nomsk, 4, '0', STR_PAD_LEFT);

      // Outstanding piutang = sisa Debet (belum dibayar). Outstanding hutang = sisa Kredit
      // (belum dilunasi) - kebalikannya, dikonfirmasi lewat query langsung ke vwHutPiut.
      $havingOutstanding = $jenis === 'HT' ? 'sum(Kredit-Debet)>0' : 'sum(Debet-Kredit)>0';

      DB::connection('SML')->update("delete dbTempHutPiut where IDUser = :username", [ "username" => $username ]);

      DB::connection('SML')->update("
        insert into dbTempHutPiut (NoFaktur, NoRetur, TipeTrans, KodeCustSupp, NoBukti, NoMsk, Urut, Tanggal, JatuhTempo,
        Debet, Kredit, Valas, Kurs, DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, IDUser, TipeDK,
        NoInvoice, Valas_, Kurs_)

        select Y.NoFaktur, Y.NoRetur, Y.TipeTrans, Y.KodeCustSupp, Y.NoBukti, Y.NoMsk, Y.Urut, Y.Tanggal, Y.JatuhTempo,
        Y.Debet, Y.Kredit, Y.Valas, Y.Kurs, Y.DebetD, Y.KreditD, Y.KodeSales, Y.Tipe, Y.Perkiraan, Y.Catatan, :username IDUser, :tipedk,
        Y.NoInvoice, Y.KodeVls_, Y.Kurs_
        from
        (select NoFaktur, KodeCustSupp, Perkiraan
        from vwHutPiut
        where KodeCustSupp = :kodecustsupp and Perkiraan = :perkiraan
        and NoBukti+right('0000'+cast(NoMsk as varchar(4)),4) <> :kuncibukti
            group by NoFaktur, KodeCustSupp, Perkiraan
            having {$havingOutstanding}  ) X
            left outer join vwHutPiut Y on Y.NoFaktur=X.NoFaktur and Y.KodeCustSupp=X.KodeCustSupp and Y.Perkiraan=X.Perkiraan
            where Y.KodeCustSupp = :kodecustsupp2
            and Y.Perkiraan = :perkiraan2
            and Y.NoBukti+right('0000'+cast(Y.NoMsk as varchar(4)),4) <> :kuncibukti2
      ", [
        "username"      => $username,
        "tipedk"        => $tipedk,
        "kodecustsupp"  => $req->kodecustsupp,
        "perkiraan"     => $req->perkiraan,
        "kuncibukti"    => $kunciBukti,
        "kodecustsupp2" => $req->kodecustsupp,
        "perkiraan2"    => $req->perkiraan,
        "kuncibukti2"   => $kunciBukti,
      ]);

      // MODE EDIT: muat kembali rincian yang SUDAH tersimpan untuk item ini, ditandai
      // StatusUID='U'. Tanda itu yang membuat sp_TransaksiMemorial choice 'U' menghapus baris
      // DBHUTPIUT lama lalu menulisnya ulang - jadi baris yang user pertahankan tetap utuh,
      // yang dihapus (jadi 'D') hilang, dan yang baru ditambah ('I') masuk. Tanpa ini, rincian
      // lama akan tertinggal di DBHUTPIUT dan baris baru menumpuk di atasnya.
      //
      // Baris ini sengaja TIDAK ikut tersaring filter outstanding di atas: seed outstanding
      // justru mengecualikan bukti+nomsk item ini, sehingga saldo faktur terbaca seolah item ini
      // belum ada - lalu rincian item ini ditambahkan di sini sebagai baris yang bisa diedit.
      if ($req->edit) {
        DB::connection('SML')->update("
          insert into dbTempHutPiut (NoFaktur, NoRetur, TipeTrans, KodeCustSupp, NoBukti, NoMsk, Urut,
          Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, DebetD, KreditD, KodeSales, Tipe, Perkiraan,
          Catatan, IDUser, TipeDK, StatusUID, NoInvoice, Valas_, Kurs_, KursBayar)

          select NoFaktur, NoRetur, TipeTrans, KodeCustSupp, NoBukti, NoMsk, Urut,
          Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, DebetD, KreditD, KodeSales, Tipe, Perkiraan,
          Catatan, :username, :tipedk, 'U', NOINVOICE, KodeVls_, Kurs_, KursBayar
          from dbHutPiut
          where NoBukti = :nobukti and NoMsk = :nomsk
            and Perkiraan = :perkiraan and KodeCustSupp = :kodecustsupp
        ", [
          "username"     => $username,
          "tipedk"       => $tipedk,
          "nobukti"      => $req->nobukti,
          "nomsk"        => $nomsk,
          "perkiraan"    => $req->perkiraan,
          "kodecustsupp" => $req->kodecustsupp,
        ]);
      }

      return [
        "nomsk" => $nomsk,
        "data"  => $this->queryKartuPT($username, $req->perkiraan, $tipedk),
      ];
    }

    // Refresh isi tabel kartu tanpa seed ulang - dipakai setelah tambah/hapus baris.
    public function getKartuPT (Request $req) {
      return $this->queryKartuPT(\Auth::user()->username, $req->perkiraan, $this->tipeDK($req));
    }

    // Tambah satu baris faktur ke dbTempHutPiut lewat sp_TempHutPiut choice 'I'.
    // Catatan penting soal parameter SP:
    //  - @Urut diabaikan saat 'I'; SP mengisinya sendiri (MAX+1 per KodeCustSupp+NoFaktur).
    //  - SP menyimpan Debet = @Debet * @Kurs, jadi @Debet dikirim dalam nilai valas.
    public function addKartuPT (Request $req) {

      $username = \Auth::user()->username;
      $nomsk = $this->nomskItem($req->nobukti, $req->urut);
      $tipedk = $this->tipeDK($req);
      $jenis = $this->jenisHP($req);

      // Alur Debet menaruh nilainya di kolom Debet, alur Kredit di kolom Kredit - ini TIDAK
      // berubah antara piutang & hutang, hanya MAKNANYA yang dibalik (lihat penandaBaris()).
      $debet  = $tipedk === 'K' ? 0 : $req->jumlah;
      $kredit = $tipedk === 'K' ? $req->jumlah : 0;

      $values = [
        'I',                                    // @Choice
        $req->nofaktur,                         // @NoFaktur
        '',                                     // @NoRetur
        'L',                                    // @TipeTrans - dikonfirmasi sama untuk PT & HT
        $req->kodecustsupp,                     // @KodeCustSupp
        $req->nobukti,                          // @NoBukti  - bukti memorial ini
        $nomsk,                                 // @NoMsk    - Urut item memorial ini
        0,                                       // @Urut     - diisi sendiri oleh SP
        $req->tanggal,                          // @Tanggal
        $req->jatuhtempo,                       // @JatuhTempo
        $debet,                                 // @Debet
        $kredit,                                // @Kredit
        $req->valas,                            // @Valas
        $req->kurs,                             // @Kurs
        '',                                     // @KodeSales
        $jenis,                                 // @Tipe - 'PT' atau 'HT'
        $req->perkiraan,                        // @Perkiraan
        $req->catatan ?? '',                    // @Catatan
        $username,                              // @IDUser
        $tipedk,                                // @TipeDK
        $this->penandaBaris($tipedk, $jenis),   // @NoInvoice - 'TBH' (tambah) / 'LNS' (pelunasan)
        '',                              // @Valas_
        0,                               // @Kurs_
        1,                               // @KursBayar
        0,                               // @DebetD
        0,                               // @KreditD
        '',                              // @FlagSimbol
        '',                              // @NODPh
        0,                               // @UrutDPH
      ];

      DB::connection('SML')->statement('exec sp_TempHutPiut ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

      return $this->queryKartuPT($username, $req->perkiraan, $tipedk);
    }

    // Hapus baris tambahan (soft delete StatusUID='D'). Kunci pencocokan di SP memakai
    // NoFaktur+NoRetur+TipeTrans+KodeCustSupp+NoBukti+Perkiraan+IDUser+NoMsk+Urut, jadi semuanya
    // dikirim apa adanya dari baris yang dipilih di tabel.
    public function deleteKartuPT (Request $req) {

      $username = \Auth::user()->username;
      $tipedk = $this->tipeDK($req);
      $jenis = $this->jenisHP($req);

      $values = [
        'D',                                    // @Choice
        $req->nofaktur,                         // @NoFaktur
        $req->noretur ?? '',                    // @NoRetur
        $req->tipetrans,                        // @TipeTrans
        $req->kodecustsupp,                     // @KodeCustSupp
        $req->nobukti,                          // @NoBukti
        $req->nomsk,                            // @NoMsk
        $req->urut,                             // @Urut - urut baris di dbTempHutPiut
        $req->tanggal,                          // @Tanggal
        $req->jatuhtempo,                       // @JatuhTempo
        0,                                       // @Debet
        0,                                       // @Kredit
        $req->valas,                            // @Valas
        $req->kurs,                             // @Kurs
        '',                                     // @KodeSales
        $jenis,                                 // @Tipe - 'PT' atau 'HT'
        $req->perkiraan,                        // @Perkiraan
        '',                                     // @Catatan
        $username,                              // @IDUser
        $tipedk,                                // @TipeDK
        $this->penandaBaris($tipedk, $jenis),   // @NoInvoice
        '',                              // @Valas_
        0,                               // @Kurs_
        1,                               // @KursBayar
        0,                               // @DebetD
        0,                               // @KreditD
        '',                              // @FlagSimbol
        '',                              // @NODPh
        0,                               // @UrutDPH
      ];

      DB::connection('SML')->statement('exec sp_TempHutPiut ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);

      return $this->queryKartuPT($username, $req->perkiraan, $tipedk);
    }

    /**
     * Samakan NoBukti/NoMsk baris tambahan dengan bukti & urut yang BENAR-BENAR dipakai saat
     * simpan, dipanggil blade sesaat sebelum submit item.
     *
     * No Bukti memorial bisa berubah setelah kartu disusun: user mengganti jenis transaksi
     * (BMM/BJK) sehingga blade meminta bukti baru, atau spAdd() mengembalikan 2 karena buktinya
     * sudah terpakai dan blade me-refresh buktinya lalu meminta user submit ulang. Kalau baris
     * kerja masih memakai bukti lama, sp_TransaksiMemorial tidak menemukannya
     * (NoBukti=@Nobukti and NoMsk=@Urut) dan rincian piutangnya hilang tanpa pesan error -
     * jurnalnya tersimpan tapi kartu piutangnya kosong.
     *
     * Hanya baris tambahan user (NoInvoice='TBH') yang disamakan. Baris outstanding hasil seed
     * harus tetap memakai NoBukti/NoMsk transaksi asalnya.
     */
    public function retagKartuPT (Request $req) {

      $username = \Auth::user()->username;
      $nomsk = $this->nomskItem($req->nobukti, $req->urut);

      // Disaring lewat StatusUID, bukan NoInvoice. Baris hasil seed bisa saja ber-NoInvoice
      // 'TBH'/'LNS' juga kalau faktur itu dulu pernah ditambah/dilunasi lewat memorial LAIN -
      // baris seperti itu milik transaksi lain dan NoBukti/NoMsk-nya tidak boleh ditimpa.
      // Hanya baris kerja sesi ini ('I' baru, 'U' hasil muat ulang) yang ikut disamakan.
      DB::connection('SML')->update("
        update dbTempHutPiut set NoBukti = :nobukti, NoMsk = :nomsk
        where IDUser = :username and isnull(StatusUID,'') in ('I','U')
      ", [
        "nobukti"  => $req->nobukti,
        "nomsk"    => $nomsk,
        "username" => $username,
      ]);

      return [ "nomsk" => $nomsk ];
    }

    // Bersihkan baris kerja milik user. Dipanggil saat user membatalkan alur piutang atau
    // setelah item memorial tersimpan, supaya temp tidak terbawa ke pemakaian berikutnya.
    public function clearKartuPT (Request $req) {
      return DB::connection('SML')->update("delete dbTempHutPiut where IDUser = :username", [ "username" => \Auth::user()->username ]);
    }

    // Sisa efektif SATU titipan saat mode edit item: rumus sama persis dengan listTitipan(),
    // hanya subquery E (total Debet ber-NOTITIPAN) mengecualikan baris yang sedang diedit
    // sendiri, supaya baris itu tidak dobel-mengurangi sisanya sendiri. Dipakai blade lewat
    // mkAmbilSisaTitipan() supaya validasi Jumlah <= Sisa tetap berlaku waktu edit.
    public function sisaTitipan (Request $req) {

      $listData = DB::connection('SML')->select("
        select 	A.NOBUKTI,  A.TANGGAL,A.Valas ,C.namaCustSupp,
          (A.Debet+A.Kredit)*A.Kurs JumlahRp ,A.Keterangan  ,
         A.debet - (isnull(d.Dibayar,0)+isnull(D.LB,0))-ISNULL(E.DEBET,0) Sisa,A.URUT,A.Debet,C.KOdeCustSupp
         from dbTransaksi A
         LEFT OUTER JOIN DBTRANS B ON A.NoBukti=B.NoBukti
         LEFT OUTER JOIN DBCUSTSUPP C ON A.CustSuppL=C.KODECUSTSUPP
         LEFT OUTER JOIN (select UrutDPP,NODPP,sum(dibayar) Dibayar,sum(LB) LB
                          from DBTerimaDPPDET group by UrutDPP,NODPP) D ON A.NObukti=D.NoDPP AND A.urut=D.UrutDPP
         LEFT OUTER JOIN (SELECT NOTITIPAN,URUTTITIPAN,SUM(Debet) DEBET
                          FROM dbTransaksi
                          WHERE (NoBukti <> :nobuktiedit OR Urut <> :urutedit)
                          GROUP BY NOTITIPAN,URUTTITIPAN) E ON A.NoBukti=E.NOTITIPAN AND A.Urut=E.URUTTITIPAN
         where A.NoBukti = :notitipan AND A.Urut = :uruttitipan
         AND A.Lawan in (select Perkiraan from dbPostHutPiut where Kode='PTS')
      ", [
        "nobuktiedit" => $req->nobukti,
        "urutedit" => $req->urut,
        "notitipan" => $req->notitipan,
        "uruttitipan" => $req->uruttitipan,
      ]);

      return $listData;
    }

    public function listValas (Request $req) {

      $listData = DB::connection('SML')->select("select * from DBVALAS");
      return $listData;
    }

    // Browse No Titipan untuk Debet = perkiraan Titipan Customer. Perkiraannya diturunkan dari
    // dbPostHutPiut (Kode='PTS'), tidak lagi dipatok '113400'. Query sesuai yang dipakai
    // PelunasanPiutangDPPController::queryOutstanding, tanpa filter tanggal akhir.
    public function listTitipan (Request $req) {

      $listData = DB::connection('SML')->select("
        select 	A.NOBUKTI,  A.TANGGAL,A.Valas ,C.namaCustSupp,
          (A.Debet+A.Kredit)*A.Kurs JumlahRp ,A.Keterangan  ,
         A.debet - (isnull(d.Dibayar,0)+isnull(D.LB,0))-ISNULL(E.DEBET,0) Sisa,A.URUT,A.Debet,C.KOdeCustSupp
         from dbTransaksi A
         LEFT OUTER JOIN DBTRANS B ON A.NoBukti=B.NoBukti
         LEFT OUTER JOIN DBCUSTSUPP C ON A.CustSuppL=C.KODECUSTSUPP
         LEFT OUTER JOIN (select UrutDPP,NODPP,sum(dibayar) Dibayar,sum(LB) LB
                          from DBTerimaDPPDET group by UrutDPP,NODPP) D ON A.NObukti=D.NoDPP AND A.urut=D.UrutDPP
         LEFT OUTER JOIN (SELECT NOTITIPAN,URUTTITIPAN,SUM(Debet) DEBET
                          FROM dbTransaksi GROUP BY NOTITIPAN,URUTTITIPAN) E ON A.NoBukti=E.NOTITIPAN AND A.Urut=E.URUTTITIPAN
         where A.Lawan in (select Perkiraan from dbPostHutPiut where Kode='PTS')
         AND A.CustSuppL<>''  and A.TANGGAL>'03/28/2016'
         and    A.debet - (isnull(d.Dibayar,0)+isnull(D.LB,0))-ISNULL(E.DEBET,0) >0
         order by A.TANGGAL desc, A.NOBUKTI desc
      ");

      return $listData;
    }

  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbtrans set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbtrans set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'MK', $req->nobukti, $req->pket, 0, 'DBTRANS');
    return $res;
  }

  public function spAdd (Request $req) {

    $username = \Auth::user()->username;

    $jmlrecord = $req->jmlrecord;


    // return [
    //   $req->choice,
    //   $req->nobukti,
    //   $req->nourut,
    //   $req->tanggal ,
    //   $req->note  ?? '',
    //   0,
    //   $req->kodedevisi ,
    //   $req->perkiraan ,
    //   $req->lawan ,
    //   $req->keterangan  ?? '', // 10
    //   $req->keterangan2  ?? '',
    //   $req->debet,
    //   $req->kredit,
    //   $req->valas,
    //   $req->kurs,
    //   $req->debetRp,
    //   $req->kreditRp,
    //
    //   $req->transaksi,
    //   $req->tphc,
    //   $req->custsuppP ?? '',
    //   $req->custsuppL ?? '', //20
    //   $req->urut,
    //   $req->noaktivaP ?? '',
    //   $req->noaktivaL ?? '',
    //   $req->statusaktivaP ?? '',
    //   $req->statusaktivaL ?? '',
    //   $req->nobon ?? '',
    //   $req->kodebag ?? '',
    //   $req->kodeP ?? '',
    //   $req->kodeL ?? '', // 30
    //   $req->statusgiro ?? '', // 30
    //   $req->simbol ?? '', // 30
    //   $username,
    //   $req->notitipan ?? '',
    //   $req->uruttitipan,
    //   $req->keterangandetail ?? ''
    //
    // ];

    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from dbtrans where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

        DB::connection('SML')->statement('exec sp_TransaksiMemorial ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal ,
          $req->note  ?? '',
          0,
          $req->kodedevisi ,
          $req->perkiraan ,
          $req->lawan ,
          $req->keterangan  ?? '', // 10
          $req->keterangan2  ?? '',
          $req->debet,
          $req->kredit,
          $req->valas,
          $req->kurs,
          $req->debetRp,
          $req->kreditRp,

          $req->transaksi,
          $req->tphc,
          $req->custsuppP ?? '',
          $req->custsuppL ?? '', //20
          $req->urut,
          $req->noaktivaP ?? '',
          $req->noaktivaL ?? '',
          $req->statusaktivaP ?? '',
          $req->statusaktivaL ?? '',
          $req->nobon ?? '',
          $req->kodebag ?? '',
          $req->kodeP ?? '',
          $req->kodeL ?? '', // 30
          $req->statusgiro ?? '', // 30
          $req->simbol ?? '', // 30
          $username,
          $req->notitipan ?? '',
          $req->uruttitipan,
          $req->keterangandetail ?? ''

        ]);

        // $jmlrecord = 1;

        // DINONAKTIFKAN - mutasi aktiva ke dbAktivaDet SUDAH dibukukan otomatis oleh trigger
        // TRI_ADD/TRI_UPD/TRI_DEL_DBTRANSAKSI di tabel dbTransaksi (jalan saat sp_TransaksiMemorial
        // insert/update/delete baris), memakai StatusAktivaP/L + DebetRp. Kalau blok di bawah ikut
        // jalan, MD/MK/SD/SK tercatat DUA KALI (contoh SML/BMM/00005/0926: MD 24 jt padahal 12 jt).
        // kolomAktivaDet()/mutasiAktivaDet() sengaja dibiarkan, hanya pemanggilannya yang dimatikan.
        /*
        // Mutasi aktiva ke dbAktivaDet. sp_TransaksiMemorial HANYA menyimpan NoAktivaP/L +
        // StatusAktivaP/L di baris dbTransaksi, ia tidak menyentuh dbAktivaDet sama sekali -
        // jadi pembukuan mutasinya dikerjakan di sini (pola yang sama dipakai KasController).
        //
        // Sisi Debet (kolom Perkiraan)  -> MD, sisi Kredit (kolom Lawan) -> MK.
        // Nilainya selalu diambil dari $req->debet, karena di memorial satu item hanya punya
        // satu angka Jumlah yang selalu masuk ke kolom Debet (Kredit selalu 0) - lihat
        // submitAdd() di blade.
        //
        // choice 'I' tambah, 'D' balikkan, 'U' balikkan nilai lama lalu terapkan nilai baru.
        //
        // Kolom tujuannya ditentukan kolomAktivaDet() dari StatusAktiva - status selain
        // AKV/AKM menghasilkan '' dan baris itu dilewati begitu saja.
        $kolP     = $this->kolomAktivaDet($req->statusaktivaP ?? '', 'P');
        $kolL     = $this->kolomAktivaDet($req->statusaktivaL ?? '', 'L');
        $kolPLama = $this->kolomAktivaDet($req->statusaktivaPLama ?? '', 'P');
        $kolLLama = $this->kolomAktivaDet($req->statusaktivaLLama ?? '', 'L');

        $barangP     = $kolP     ? trim((string)($req->noaktivaP ?? ''))     : '';
        $barangL     = $kolL     ? trim((string)($req->noaktivaL ?? ''))     : '';
        $barangPLama = $kolPLama ? trim((string)($req->noaktivaPLama ?? '')) : '';
        $barangLLama = $kolLLama ? trim((string)($req->noaktivaLLama ?? '')) : '';

        if ($barangP || $barangL || $barangPLama || $barangLLama) {
          $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
          $bulan   = $periode->bulan;
          $tahun   = $periode->tahun;
          $devisi  = $req->kodedevisi;

          $nilai     = (float)$req->debet;
          $nilaiLama = (float)($req->debetLama ?? 0);

          if ($req->choice === 'U' || $req->choice === 'D') {
            $this->mutasiAktivaDet($barangPLama, $kolPLama, -$nilaiLama, $devisi, $bulan, $tahun);
            $this->mutasiAktivaDet($barangLLama, $kolLLama, -$nilaiLama, $devisi, $bulan, $tahun);
          }

          if ($req->choice === 'I' || $req->choice === 'U') {
            $this->mutasiAktivaDet($barangP, $kolP, $nilai, $devisi, $bulan, $tahun);
            $this->mutasiAktivaDet($barangL, $kolL, $nilai, $devisi, $bulan, $tahun);
          }
        }
        */

        // Baris kerja piutang/hutang sudah dipindahkan ke DBHUTPIUT oleh sp_TransaksiMemorial
        // di atas (filter StatusUID 'I'/'U'), jadi sisa isi dbTempHutPiut tinggal dibuang supaya
        // tidak terbawa ke item atau transaksi berikutnya. Termasuk untuk choice 'D', karena SP
        // juga menghapus baris DBHUTPIUT milik item yang dibatalkan.
        // KodeP/KodeL berisi 'PT' atau 'HT' tergantung sisinya - lihat catatan besar di atas.
        if (in_array($req->kodeP ?? '', ['PT', 'HT']) || in_array($req->kodeL ?? '', ['PT', 'HT'])) {
          DB::connection('SML')->update("delete dbTempHutPiut where IDUser = :username", [ "username" => $username ]);
        }

      // }

      return 1;

  }

  public function spkoreksi (Request $req) {

    $username = \Auth::user()->username;
    $jmlrecord = $req->jmlrecord;

//     select * from dbdph where NoBukti like '%0525%'
//


    DB::connection('SML')->statement('exec sp_dph ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $req->nourut,
      $req->tanggal ,
      $req->valas,
      '',
      $req->tipe ,
      $req->urut,
      $req->kodecustsupp, //
      $req->nofaktur, //
      $req->dibayar, //
      NULL,
      -1 ,
      $req->perkiraan, //
      $req->kl, //
      $req->lb, //
      0 ,
      0 , //
      $req->noinvoice ? $req->noinvoice : '', //
      $req->tglinvoice, //
      $req->pcopy, //
      1,
      $username

    ]);


  }




}
