<?php


namespace App\Http\Controllers\Marketing;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;


class KreditNoteController extends Controller


// contoh desktop ce006
// sp_ProsesPostingHutPiut
// sp_ProsesPostingJurnalOto
{

  public function index(Request $req) {
    $kodemenu = '04110';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }





    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);



    $tglawal = \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $tempOutstanding = $this->queryKreditNote($tglawal, $tglakhir, 0);

    return view('marketing.kreditnote' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "akses" => $akses
    ]);

  }

  // Satu query dipakai bareng oleh index() dan loadAll() -- dulu tabel (Belum,
  // tombol Koreksi/Otorisasi) dan tabel2 (Sudah, tombol Batal Otorisasi) adalah
  // 2 tab terpisah dengan query nyaris identik (cuma beda isOtorisasi1<>1/=1).
  // Digabung jadi satu dengan filterkn yang menyaring di server, port 1:1 dari
  // pola queryOutstanding() milik PerintahReturJualController.
  //   0 = Semua, 1 = Belum Otorisasi, 2 = Sudah Otorisasi
  private function queryKreditNote ($tglawal, $tglakhir, $filterkn) {
    return DB::connection("SML")->select("
      select * from (
        -- Di KN, Kredit disimpan plus dan Debet disimpan minus (kebalikan Debet Note). Di layar
        -- dipisah jadi kolom Debet (NILAIDEBETKN) & Kredit (NILAIKN), keduanya tampil positif.
        -- Nama field NILAIKN dipertahankan supaya susunan kolom tersimpan tetap cocok.
        select a.* , b.NAMACUSTSUPP, isnull(d.Debet,0) NILAIDEBETKN, isnull(d.Kredit,0) NILAIKN
        from dbkreditnote a join DBCUSTSUPP b on a.KodeSupp = b.KODECUSTSUPP
        left outer join (select NoBukti,
                                Sum(case when NilaiRp < 0 then -NilaiRp else 0 end) Debet,
                                Sum(case when NilaiRp > 0 then NilaiRp else 0 end) Kredit
                         from dbKreditNoteDet group by NoBukti) d
               on d.NoBukti = a.NoBukti
        where a.Tanggal between ? and ?
      ) x
      where (? = 0)
         or (? = 1 and isnull(x.isOtorisasi1,0) <> 1)
         or (? = 2 and isnull(x.isOtorisasi1,0) = 1)
    ", [$tglawal, $tglakhir, $filterkn, $filterkn, $filterkn]);
  }

  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tglawal = $this->tanggalFilter($req->tglawal) ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->startOfMonth()->format('Y-m-d');
    $tglakhir = $this->tanggalFilter($req->tglakhir) ?: \Carbon\Carbon::now()->month((int) $periode->bulan)->endOfMonth()->format('Y-m-d');
    $filterkn = $req->filterkn ?: 0;
    $tempOutstanding = $this->queryKreditNote($tglawal, $tglakhir, $filterkn);

    return ["tempOutstanding" => $tempOutstanding];
  }


  public function getDetail (Request $req ) {


        // Saldo invoice dulu diagregasi dari SEMUA faktur di vwHutPiut (64 ribu faktur, 3 dtk
        // tiap buka Detail/Edit/Otorisasi), sekarang hanya faktur milik KN ini.
        // Sisa (kolom Nilai) berjalan per baris = nilai invoice SEBELUM baris KN ini, sama
        // seperti Debet Note: saldo DBHUTPIUT tanpa posting KN (NoInvoice 'KN'), dikurangi semua
        // baris KN (sudah/belum diotorisasi) untuk invoice itu yang urutannya lebih dulu
        // (Tanggal, NoBukti, Urut). Di KN Kredit = Nilai plus (mengurangi), Debet = Nilai minus
        // (menambah). Baris pertama = saldo invoice.
        $tempOutstanding = DB::connection("SML")->select("Select  c.KodeSupp,d.NamaCustSupp,d.Alamat1,Right(a.noBukti,4)NoUrut,a.Urut,a.NoBukti,convert(varchar(10), c.tanggal, 23) tanggal,Keterangan,A.NoInv,
        a.Nilai,
        Case when a.kodevls='IDR' then 0.00 else a.Nilai end NilaiValas,
        a.kodeVls, a.Kurs, a.NilaiRp , c.IsOtorisasi1,
        Convert(Numeric(18,2), isnull(s.SaldoInv,0) - isnull(p.Sebelum,0)) Sisa,
        s.TglInv, s.JatuhTempoInv
From  dbKreditNoteDet a
Left Outer Join dbKreditNote c On c.NoBukti=a.NoBukti
Left Outer Join dbCustSupp d On d.KodeCustSupp=c.KodeSupp
Outer Apply (select case when max(case when isnull(h.Valas,'') not in ('','IDR') then 1 else 0 end)=1
                         then Sum(h.SaldoD) else Sum(h.Saldo) end SaldoInv,
                    Min(h.Tanggal) TglInv, Min(h.JatuhTempo) JatuhTempoInv
             from DBHUTPIUT h
             where h.NoFaktur=a.NoInv and h.KodeCustSupp=c.KodeSupp and h.Tipe='PT'
               and isnull(h.NoInvoice,'')<>'KN') s
Outer Apply (select sum(x.Nilai) Sebelum
             from dbKreditNoteDet x
             join dbKreditNote n on n.NoBukti=x.NoBukti
             where x.NoInv=a.NoInv and n.KodeSupp=c.KodeSupp
               and (n.Tanggal < c.Tanggal
                    or (n.Tanggal = c.Tanggal and n.NoBukti < c.NoBukti)
                    or (n.NoBukti = c.NoBukti and x.Urut < a.Urut))) p
where a.NOBUKTI = cast(:nobukti as varchar(100))
order by a.urut" , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }

  private function headerKN ($nobukti) {
    $header = DB::connection('SML')->select("select NOBUKTI, convert(varchar(10), TANGGAL, 23) TANGGAL, isnull(IsOtorisasi1,0) IsOtorisasi1 from DBKREDITNOTE where NOBUKTI = cast(? as varchar(100))", [(string) $nobukti]);
    return $header ? $header[0] : null;
  }

  // Sisa invoice (mata uang invoice) dan akun piutangnya. Akun dipakai untuk perkHP (lawan
  // jurnal KN); kalau tidak ketemu pakai akun piutang posting (DBPOSTHUTPIUT PT urut 2).
  // Sisa = saldo DBHUTPIUT tanpa posting KN, dikurangi SEMUA baris KN (sudah/belum diotorisasi)
  // untuk invoice itu - rumus yang sama dengan kolom Nilai di modal Invoice. Baris yang sedang
  // dikoreksi ($nobukti + $urut) tidak ikut dihitung, supaya nilainya bisa diganti.
  private function infoInvoice ($nofaktur, $kodecust, $nobukti = '', $urut = 0) {
    $saldo = DB::connection('SML')->select("
      select case when max(case when isnull(h.Valas,'') not in ('','IDR') then 1 else 0 end)=1
                  then isnull(Sum(h.SaldoD),0) else isnull(Sum(h.Saldo),0) end SaldoInv
      from DBHUTPIUT h
      where h.NoFaktur = cast(? as varchar(30)) and h.KodeCustSupp = cast(? as varchar(15))
        and h.Tipe = 'PT' and isnull(h.NoInvoice,'') <> 'KN'", [(string) $nofaktur, (string) $kodecust]);
    $kn = DB::connection('SML')->select("
      select isnull(sum(x.Nilai),0) Nilai
      from dbKreditNoteDet x join dbKreditNote n on n.NoBukti = x.NoBukti
      where x.NoInv = cast(? as varchar(30)) and n.KodeSupp = cast(? as varchar(15))
        and not (x.NoBukti = cast(? as varchar(100)) and x.Urut = ?)", [(string) $nofaktur, (string) $kodecust, (string) $nobukti, (int) $urut]);
    $perk = DB::connection('SML')->select("select top 1 Perkiraan from DBHUTPIUT where NoFaktur = cast(? as varchar(30)) and Tipe = 'PT' and isnull(NOINVOICE,'') <> 'KN' and isnull(Perkiraan,'') <> '' order by Tanggal, Urut", [(string) $nofaktur]);
    if (!$perk) {
      $perk = DB::connection('SML')->select("select top 1 Perkiraan from DBPOSTHUTPIUT where Kode = 'PT' and Urut = 2");
    }
    return (object) [
      "Sisa" => round((float) ($saldo ? $saldo[0]->SaldoInv : 0) - (float) $kn[0]->Nilai, 2),
      "Perkiraan" => $perk ? $perk[0]->Perkiraan : '',
    ];
  }

  // Nilai KN bertanda: Kredit plus (mengurangi sisa invoice), Debet minus (menambah). Harus
  // diisi (bukan 0), dan sisa sesudahnya tidak boleh minus (Kredit tidak melebihi Nilai).
  private function cekNilai ($noinv, $nilai, $kurs, $info) {
    if (abs((float) $nilai) < 0.005) {
      return "Debet / Kredit invoice " . $noinv . " harus diisi";
    }
    if ((float) $kurs < 0) {
      return "Kurs invoice " . $noinv . " tidak boleh kurang dari 0";
    }
    if ($info->Sisa - (float) $nilai < -0.005) {
      return "Kredit invoice " . $noinv . " melebihi Nilai (" . number_format($info->Sisa, 2, '.', ',') . ")";
    }
    return '';
  }

  // Input angka di form tampil dengan separator ribuan (koma); dibersihkan lagi di sini -
  // (float) '1,000.00' terbaca 1, sehingga cekNilai bisa salah menilai.
  private function angka ($nilai) {
    $bersih = str_replace(',', '', trim((string) $nilai));
    return is_numeric($bersih) ? (float) $bersih : 0;
  }

  private function tanggalServer () {
    return DB::connection('SML')->select("select convert(varchar(10), getdate(), 23) tanggal")[0]->tanggal;
  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select KN from DBNOMOR');


      $values = [
          $inisial[0]->KN,
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;

    } else {
      $values = [
          'KNN',
          $periode->bulan,
          $periode->tahun,
          $username,
      ];

      $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

      return $noBukti;
    }

  }


  public function listCustomer (Request $req) {

    $listData = DB::connection('SML')->select("select b.NamaKota, a.KODECUSTSUPP, a.NAMACUSTSUPP , a.ALAMAT1 , a.PPN from dbcustsupp a
left outer join DBKOTA b on a.KOTA = b.KodeKota  where a.Jenis = 1");
    return $listData;
  }

  public function listInvoice (Request $req) {

    // Isi vwHutPiut ditulis langsung dengan filter customer di tabel dasar: di view kolomnya
    // upper(KodeCustSupp) sehingga index tidak terpakai. Join view (invoice PL/SO/kebun/PIC)
    // tidak dipakai kolomnya, jadi dilepas.
    // Hanya invoice yang belum lunas. Saldo dijumlah per NoFaktur saja (sama dengan
    // infoInvoice/cekNilai): dulu per NoFaktur+Valas+Kurs, sehingga pelunasan valas dengan
    // kurs lain dan baris selisih kurs jadi kelompok sendiri dan invoice lunas tetap tampil.
    // Lunas dinilai per mata uang invoice (IDR: Saldo, valas: SaldoD). Sisa < 1 (hanya selisih
    // pembulatan di belakang koma, mis. 0,07) dianggap lunas, keputusan user 22 Okt 2026.
    //
    // Sisa (kolom Nilai di modal) = saldo invoice (mata uang invoice) dikurangi KN yang BELUM
    // diotorisasi untuk invoice itu (Kredit = Nilai plus mengurangi, Debet = Nilai minus
    // menambah). KN yang sudah diotorisasi sudah terposting ke DBHUTPIUT. Sama seperti Debet
    // Note: invoice yang sudah pernah di-KN tetap tampil selama sisanya belum habis (dulu satu
    // invoice hanya bisa dipakai di satu KN).
    $listData = DB::connection('SML')->select("declare @awal Varchar(15)
    Select @Awal= cast(:kodecustsupp as varchar(15))
    select Cast(1 as Bit) Pilih,Convert(Numeric(18,2),0) Nilai, isnull(v.Valas,'IDR') KodeVls, isnull(v.Kurs,1) Kurs, s.NoFaktur, s.Tanggal, s.JatuhTempo,
           s.Saldo, s.SaldoD,
           Convert(Numeric(18,2), case when s.ValasAsing is null then s.Saldo else s.SaldoD end - isnull(p.Pending,0)) Sisa
    from (select a.NoFaktur, Min(a.Tanggal) Tanggal, Min(a.JatuhTempo) JatuhTempo, SUM(a.Saldo) Saldo, SUM(a.SaldoD) SaldoD,
                 nullif(max(case when isnull(a.Valas,'') not in ('','IDR') then a.Valas else '' end),'') ValasAsing
          from DBHUTPIUT a
          where a.KodeCustSupp=@Awal and a.Tipe='PT'
          Group by a.NoFaktur) s
    outer apply (select top 1 h.Valas, h.Kurs from DBHUTPIUT h
                 where h.NoFaktur=s.NoFaktur and h.KodeCustSupp=@Awal and h.Tipe='PT'
                   and isnull(h.Valas,'IDR')=isnull(s.ValasAsing,'IDR')
                 order by h.Tanggal, h.Urut) v
    outer apply (select sum(d.Nilai) Pending
                 from dbKreditNoteDet d
                 join dbKreditNote n on n.NoBukti=d.NoBukti
                 where d.NoInv=s.NoFaktur and n.KodeSupp=@Awal and isnull(n.IsOtorisasi1,0)=0) p
    where case when s.ValasAsing is null then s.Saldo else s.SaldoD end - isnull(p.Pending,0) >= 1
    Order by s.Nofaktur
" , ["kodecustsupp" => (string) $req->kodecustsupp]);
    return $listData;

  }



  // Pengaman server (otorisasi/batal/simpan): dulu KN yang sudah diotorisasi user lain bisa
  // diotorisasi lagi (posting hutpiut/jurnal diulang), dibatalkan dua kali, atau itemnya
  // diubah/dihapus. Jawaban ditolak berbentuk {status:0, pesan}.
  public function spOtorisasi (Request $req) {
    $header = $this->headerKN($req->nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "KN " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 === 1) {
      return ["status" => 0, "pesan" => "KN " . $req->nobukti . " sudah diotorisasi"];
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // TglOto1 memakai jam SQL Server (getdate), sama seperti tanggal KN; dulu date() PHP web server.
    $res = DB::connection('SML')->update("update dbkreditnote set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = getdate() where nobukti = cast(:nobukti as varchar(100)) and isnull(isOtorisasi1,0) <> 1", ["username" => \Auth::user()->username , "nobukti" => $req->nobukti]);
    if (!$res) {
      return ["status" => 0, "pesan" => "KN " . $req->nobukti . " sudah diotorisasi"];
    }

     $values = [
      '',
      'DBKREDITNOTE',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      1
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);


    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    if (trim((string) $req->pket) === '') {
      return ["status" => 0, "pesan" => "Keterangan batal otorisasi harus diisi"];
    }
    $header = $this->headerKN($req->nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "KN " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 !== 1) {
      return ["status" => 0, "pesan" => "KN " . $req->nobukti . " belum diotorisasi"];
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $res = DB::connection('SML')->update("update dbkreditnote set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = cast(:nobukti as varchar(100)) and isOtorisasi1 = 1", [ "nobukti" => $req->nobukti]);
    if (!$res) {
      return ["status" => 0, "pesan" => "KN " . $req->nobukti . " belum diotorisasi"];
    }


     $values = [
      '',
      'DBKREDITNOTE',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      0
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);

    app('App\Http\Controllers\GlobalController')->LoggingData('btloto', 'KN', $req->nobukti, $req->pket, 0, 'DBKREDITNOTE');

    return $res;
  }

  public function spAdd (Request $req) {

      $tempData = $req->input('tempData') ?: [];
      $username = \Auth::user()->username;

      if (!count($tempData)) {
        return ["status" => 0, "pesan" => "Tidak ada invoice dipilih"];
      }
      if (trim((string) $req->kodecustsupp) === '' || trim((string) $req->nobukti) === '') {
        return ["status" => 0, "pesan" => "Customer / No Bukti belum terisi, silahkan pilih customer"];
      }
      // Invoice yang sama boleh dipakai di beberapa KN selama Nilai (sisa) belum habis - sama
      // seperti Debet Note. Sisa dihitung ulang di server (bisa keduluan user lain).
      $infoInv = [];
      foreach ($tempData as $d) {
        $noinv = (string) ($d['NoFaktur'] ?? '');
        $infoInv[$noinv] = $this->infoInvoice($noinv, $req->kodecustsupp);
        $salah = $this->cekNilai($noinv, $this->angka($d['inputNilai'] ?? 0), $this->angka($d['inputKurs'] ?? 0), $infoInv[$noinv]);
        if ($salah !== '') {
          return ["status" => 0, "pesan" => $salah];
        }
      }

      if ($req->tipeform == 'add' ) {
        $check = DB::connection('SML')->select('select * from DBKREDITNOTE where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
          if ($check) {
            return 2;
        }
        // KN baru: tanggal selalu tanggal server (input tanggal di form disabled & diisi dari
        // gettanggalserver) dan harus masuk periode kerja aktif.
        $tanggal = $this->tanggalServer();
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
        if ((int) substr($tanggal, 0, 4) !== (int) $periode->tahun || (int) substr($tanggal, 5, 2) !== (int) $periode->bulan) {
          return ["status" => 0, "pesan" => "Tanggal server (" . date('d/m/Y', strtotime($tanggal)) . ") tidak sesuai periode kerja aktif"];
        }
      } else {
        // Tambah invoice ke KN yang sudah ada: tanggal tetap tanggal KN.
        $header = $this->headerKN($req->nobukti);
        if (!$header) {
          return ["status" => 0, "pesan" => "KN " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
        }
        if ((int) $header->IsOtorisasi1 === 1) {
          return ["status" => 0, "pesan" => "KN " . $req->nobukti . " sudah diotorisasi, tidak bisa diubah"];
        }
        $tanggal = $header->TANGGAL;
      }

      // Semua invoice disimpan dalam SATU batch transaksi (sama seperti Debet Note): kalau satu
      // gagal, semuanya dibatalkan. BEGIN/COMMIT harus di batch yang sama - koneksi pdo_sqlsrv
      // memakai MARS, transaksi yang masih terbuka di akhir batch langsung di-rollback server.
      // Sesudah tiap EXEC: kalau Sp_KreditNote me-ROLLBACK diam-diam (@@TRANCOUNT 0) batch
      // dihentikan dengan error. Sp_KreditNote tidak mengisi perkHP, jadi baris yang baru masuk
      // (Urut terbesar milik invoice itu) diisi akun piutang invoice (lawan jurnal KN).
      $sql = "SET NOCOUNT ON; SET XACT_ABORT ON; BEGIN TRAN;\n";
      $bindings = [];
      foreach ($tempData as $d)  {
        $noinv = (string) $d['NoFaktur'];
        $sql .= "EXEC Sp_KreditNote ?,?,?,?,?,?,?,?,?,?,?,?;\n"
              . "IF @@TRANCOUNT = 0 BEGIN RAISERROR('Sp_KreditNote membatalkan transaksi', 16, 1); RETURN; END;\n"
              . "UPDATE dbKreditNoteDET SET perkHP = ? WHERE NOBUKTI = cast(? as varchar(100)) AND URUT = "
              . "(select max(URUT) from dbKreditNoteDET where NOBUKTI = cast(? as varchar(100)) and NoInv = cast(? as varchar(30)));\n"
              . "IF @@ROWCOUNT = 0 BEGIN RAISERROR('Item KN gagal tersimpan', 16, 1); RETURN; END;\n";
        array_push($bindings,
          "I",
          $req->nobukti,
          $tanggal,
          $d['Keterangan'] ?? '',
          0,
          $noinv,
          $req->kodecustsupp,
          $this->angka($d['inputNilai'] ?? 0),
          $d['KodeVls'],
          $this->angka($d['inputKurs'] ?? 0),
          $this->angka($d['inputNilaiRp'] ?? 0),
          $req->nourut,
          $infoInv[$noinv]->Perkiraan,
          (string) $req->nobukti,
          (string) $req->nobukti,
          $noinv
        );
      }
      $sql .= "COMMIT TRAN;";

      $conn = DB::connection('SML');
      try {
        $conn->statement($sql, $bindings);
      } catch (\Throwable $e) {
        try { $conn->unprepared('IF @@TRANCOUNT > 0 ROLLBACK TRAN'); } catch (\Throwable $e2) {}
        \Log::error('Kredit Note spAdd gagal (' . $req->nobukti . '): ' . $e->getMessage());
        return ["status" => 0, "pesan" => "Gagal menyimpan, tidak ada invoice yang tersimpan. Silahkan ulangi."];
      }

      return 1;

  }

  public function spKoreksi (Request $req) {

    if (!in_array($req->choice, ['U', 'D'], true)) {
      return ["status" => 0, "pesan" => "Data tidak lengkap, silahkan ulangi"];
    }
    $header = $this->headerKN($req->nobukti);
    if (!$header) {
      return ["status" => 0, "pesan" => "KN " . $req->nobukti . " tidak ditemukan, mungkin sudah dihapus"];
    }
    if ((int) $header->IsOtorisasi1 === 1) {
      return ["status" => 0, "pesan" => "KN " . $req->nobukti . " sudah diotorisasi, tidak bisa diubah"];
    }
    $item = DB::connection('SML')->select("select a.NoInv, a.KodeVLS, b.KodeSupp from dbKreditNoteDET a join dbKreditNote b on b.NoBukti = a.NoBukti where a.NOBUKTI = cast(? as varchar(100)) and a.URUT = ?", [(string) $req->nobukti, (int) $req->urut]);
    if (!$item) {
      return ["status" => 0, "pesan" => "Item KN tidak ditemukan, mungkin sudah dihapus"];
    }
    if ($req->choice == 'U') {
      // Sisa dihitung tanpa baris yang sedang dikoreksi.
      $salah = $this->cekNilai($item[0]->NoInv, $this->angka($req->nilai), $this->angka($req->kurs), $this->infoInvoice($item[0]->NoInv, $item[0]->KodeSupp, $req->nobukti, (int) $req->urut));
      if ($salah !== '') {
        return ["status" => 0, "pesan" => $salah];
      }
    }

    DB::connection('SML')->statement('exec Sp_KreditNote ?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $header->TANGGAL,
      $req->keterangan ?? '',
      $req->urut,
      // No invoice & valas diambil dari item itu sendiri (di form Edit keduanya disabled), Nilai
      // Rp dihitung ulang dengan rumus yang sama seperti di browser (nilai x kurs).
      $item[0]->NoInv,
      $req->kodecustsupp,
      $this->angka($req->nilai),
      $item[0]->KodeVLS,
      $this->angka($req->kurs),
      round($this->angka($req->nilai) * $this->angka($req->kurs), 2),

    ]);

    return 1;

  }

}
