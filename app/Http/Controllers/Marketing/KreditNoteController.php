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
        select a.* , b.NAMACUSTSUPP from dbkreditnote a join DBCUSTSUPP b on a.KodeSupp = b.KODECUSTSUPP
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
        $tempOutstanding = DB::connection("SML")->select("Select  c.KodeSupp,d.NamaCustSupp,d.Alamat1,Right(a.noBukti,4)NoUrut,a.Urut,a.NoBukti,convert(varchar(10), c.tanggal, 23) tanggal,Keterangan,A.NoInv, b.Saldo, b.SaldoD,
        a.Nilai,
        Case when a.kodevls='IDR' then 0.00 else a.Nilai end NilaiValas,
        a.kodeVls, a.Kurs, a.NilaiRp , c.IsOtorisasi1
From  dbKreditNoteDet a
Left Outer Join dbKreditNote c On c.NoBukti=a.NoBukti
Left Outer Join (select a.NoFaktur,Sum(Saldo) Saldo, Sum(SaldoD) SaldoD
                 from dbo.vwHutPiut a
                 where a.Tipe='PT'
                   and a.NoFaktur in (select k.NoInv from dbKreditNoteDet k where k.NoBukti = cast(:nobukti2 as varchar(100)))
                 Group by a.NoFaktur) b On a.NoInv=B.Nofaktur
Left Outer Join dbCustSupp d On d.KodeCustSupp=c.KodeSupp
where a.NOBUKTI = cast(:nobukti as varchar(100))
order by a.urut" , ["nobukti" => $req->nobukti, "nobukti2" => $req->nobukti]);
    return $tempOutstanding;
  }

  private function headerKN ($nobukti) {
    $header = DB::connection('SML')->select("select NOBUKTI, convert(varchar(10), TANGGAL, 23) TANGGAL, isnull(IsOtorisasi1,0) IsOtorisasi1 from DBKREDITNOTE where NOBUKTI = cast(? as varchar(100))", [(string) $nobukti]);
    return $header ? $header[0] : null;
  }

  // Saldo invoice (Rp & valas) dan akun piutangnya dari DBHUTPIUT. Akun dipakai untuk perkHP
  // (lawan jurnal KN); kalau tidak ketemu pakai akun piutang posting (DBPOSTHUTPIUT PT urut 2).
  private function infoInvoice ($nofaktur) {
    $saldo = DB::connection('SML')->select("select isnull(sum(Saldo),0) Saldo, isnull(sum(SaldoD),0) SaldoD from DBHUTPIUT where NoFaktur = cast(? as varchar(30)) and Tipe = 'PT'", [(string) $nofaktur]);
    $perk = DB::connection('SML')->select("select top 1 Perkiraan from DBHUTPIUT where NoFaktur = cast(? as varchar(30)) and Tipe = 'PT' and isnull(NOINVOICE,'') <> 'KN' and isnull(Perkiraan,'') <> '' order by Tanggal, Urut", [(string) $nofaktur]);
    if (!$perk) {
      $perk = DB::connection('SML')->select("select top 1 Perkiraan from DBPOSTHUTPIUT where Kode = 'PT' and Urut = 2");
    }
    return (object) [
      "Saldo" => (float) $saldo[0]->Saldo,
      "SaldoD" => (float) $saldo[0]->SaldoD,
      "Perkiraan" => $perk ? $perk[0]->Perkiraan : '',
    ];
  }

  // Nilai KN harus > 0 dan tidak melebihi saldo invoice (IDR: Saldo, valas: SaldoD). Keputusan
  // user 14 Okt 2026; programajcSML hanya menolak nilai minus.
  private function cekNilai ($noinv, $kodevls, $nilai, $kurs, $info) {
    if (!((float) $nilai > 0)) {
      return "Nilai KN invoice " . $noinv . " harus lebih dari 0";
    }
    if ((float) $kurs < 0) {
      return "Kurs invoice " . $noinv . " tidak boleh kurang dari 0";
    }
    $saldo = strtoupper(trim((string) $kodevls)) === 'IDR' || trim((string) $kodevls) === '' ? $info->Saldo : $info->SaldoD;
    if ((float) $nilai > $saldo + 0.005) {
      return "Nilai KN invoice " . $noinv . " melebihi saldo invoice (" . number_format($saldo, 2, '.', ',') . ")";
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
    $listData = DB::connection('SML')->select("declare @awal Varchar(15)
    Select @Awal= cast(:kodecustsupp as varchar(15))
    select Cast(1 as Bit) Pilih,Convert(Numeric(18,2),0) Nilai, isnull(v.Valas,'IDR') KodeVls, isnull(v.Kurs,1) Kurs, s.NoFaktur, s.Tanggal, s.JatuhTempo,
           s.Saldo, s.SaldoD
    from (select a.NoFaktur, Min(a.Tanggal) Tanggal, Min(a.JatuhTempo) JatuhTempo, SUM(a.Saldo) Saldo, SUM(a.SaldoD) SaldoD,
                 nullif(max(case when isnull(a.Valas,'') not in ('','IDR') then a.Valas else '' end),'') ValasAsing
          from DBHUTPIUT a
          where a.KodeCustSupp=@Awal and a.Tipe='PT'
            and not exists (select 1 from dbKreditNoteDet k where k.NoInv=a.NoFaktur)
          Group by a.NoFaktur) s
    outer apply (select top 1 h.Valas, h.Kurs from DBHUTPIUT h
                 where h.NoFaktur=s.NoFaktur and h.KodeCustSupp=@Awal and h.Tipe='PT'
                   and isnull(h.Valas,'IDR')=isnull(s.ValasAsing,'IDR')
                 order by h.Tanggal, h.Urut) v
    where case when s.ValasAsing is null then s.Saldo else s.SaldoD end >= 1
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
      $infoInv = [];
      foreach ($tempData as $d) {
        $noinv = (string) ($d['NoFaktur'] ?? '');
        // Satu invoice hanya boleh di satu KN (daftar invoice sudah menyaring, tapi bisa
        // keduluan user lain saat form terbuka).
        $dipakai = DB::connection('SML')->select("select top 1 NoBukti from dbKreditNoteDet where NoInv = cast(? as varchar(30))", [$noinv]);
        if ($dipakai) {
          return ["status" => 0, "pesan" => "Invoice " . $noinv . " sudah dipakai di KN " . $dipakai[0]->NoBukti];
        }
        $infoInv[$noinv] = $this->infoInvoice($noinv);
        $salah = $this->cekNilai($noinv, $d['KodeVls'] ?? '', $this->angka($d['inputNilai'] ?? 0), $this->angka($d['inputKurs'] ?? 0), $infoInv[$noinv]);
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

      foreach ($tempData as $d)  {
        // code...
        DB::connection('SML')->statement('exec Sp_KreditNote ?,?,?,?,?,?,?,?,?,?,?,?', [
          "I",
          $req->nobukti,
          $tanggal,
          $d['Keterangan'] ?? '',
          0,
          $d['NoFaktur'],
          $req->kodecustsupp,
          $this->angka($d['inputNilai'] ?? 0),
          $d['KodeVls'],
          $this->angka($d['inputKurs'] ?? 0),
          $this->angka($d['inputNilaiRp'] ?? 0),
          $req->nourut


        ]);

        // Sp_KreditNote tidak mengisi perkHP dan kegagalan insert item tidak sampai ke PHP
        // (dulu hanya header yang tersimpan). Item dipastikan ada, lalu perkHP diisi akun
        // piutang invoice (lawan jurnal KN).
        $urut = DB::connection('SML')->select("select max(URUT) urut from dbKreditNoteDET where NOBUKTI = cast(? as varchar(100)) and NoInv = cast(? as varchar(30))", [(string) $req->nobukti, (string) $d['NoFaktur']]);
        if (!$urut || $urut[0]->urut === null) {
          return ["status" => 0, "pesan" => "Invoice " . $d['NoFaktur'] . " gagal disimpan, silahkan coba lagi"];
        }
        DB::connection('SML')->update("update dbKreditNoteDET set perkHP = ? where NOBUKTI = cast(? as varchar(100)) and URUT = ?", [$infoInv[(string) $d['NoFaktur']]->Perkiraan, (string) $req->nobukti, (int) $urut[0]->urut]);
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
    $item = DB::connection('SML')->select("select NoInv, KodeVLS from dbKreditNoteDET where NOBUKTI = cast(? as varchar(100)) and URUT = ?", [(string) $req->nobukti, (int) $req->urut]);
    if (!$item) {
      return ["status" => 0, "pesan" => "Item KN tidak ditemukan, mungkin sudah dihapus"];
    }
    if ($req->choice == 'U') {
      $salah = $this->cekNilai($item[0]->NoInv, $item[0]->KodeVLS, $this->angka($req->nilai), $this->angka($req->kurs), $this->infoInvoice($item[0]->NoInv));
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
