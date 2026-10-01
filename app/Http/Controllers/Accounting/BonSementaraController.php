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





class BonSementaraController extends Controller


{
  // Nama href untuk DBHEADERTABLE. Dipatok (bukan dari $req->path()) supaya sama
  // persis dengan HREF / HREF_OUT dipakai di bonsementara.blade.php. Dua tabel di
  // halaman ini menyimpan susunan kolomnya masing-masing, jadi href-nya dua.
  // Pola id mengikuti penerimaandpp: #tabel selalu pasangan *_OUT/outtgl*,
  // #tabel2 selalu pasangan HREF utama/tgl* biasa.
  const HREF     = 'bonsementara';
  const HREF_OUT = 'bonsementaraoutstanding';

  // Kolom tabel #tabel2 ("Outstanding Bon", data $tempPenerimaan - bon yang belum
  // lunas dibayar kredit). Nama key HARUS sama persis dengan alias kolom di
  // queryPenerimaan() karena dipakai juga sebagai nama field data di JS.
  // tipe: 0 = varchar, 1 = float, 2 = date.
  private function kolomDefault () {
    return [
      'No Bon'     => 0,
      'Tanggal'    => 2,
      'Penerima'   => 0,
      'Keterangan' => 0,
      'Debet'      => 1,
      'Kredit'     => 1,
      'Saldo'      => 1,
    ];
  }

  // Kolom tabel #tabel ("Penambahan Bon", data $tempOutstanding - seluruh baris
  // dbBon pada periode terpilih). Aturannya sama dengan kolomDefault(): nama key =
  // alias kolom di queryOutstanding() = nama field di JS.
  private function kolomDefaultOutstanding () {
    return [
      'No Bon'     => 0,
      'Tanggal'    => 2,
      'Penerima'   => 0,
      'Keterangan' => 0,
      'Debet'      => 1,
      'Kredit'     => 1,
      'Saldo'      => 1,
    ];
  }

  // Rentang tanggal default = satu bulan penuh periode kerja user.
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  // Rentang tanggal tabel #tabel2 ("Outstanding Bon").
  private function rentangTanggal (Request $req, $periode) {
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
    return [$tglawal, $tglakhir];
  }

  /* Rentang tanggal tabel #tabel ("Penambahan Bon"). Aturannya sama persis dengan
     rentangTanggal() di atas dan dengan halaman penerimaandpp. Yang membedakan hanya
     nama parameternya (outtglawal/outtglakhir) karena tiap tab punya kotak sendiri. */
  private function rentangTanggalOutstanding (Request $req, $periode) {
    list($tglawal, $tglakhir) = $this->periodeRange($periode);

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('outtglawal')))  { $tglawal  = $req->input('outtglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('outtglakhir'))) { $tglakhir = $req->input('outtglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    return [$tglawal, $tglakhir];
  }

  /**
   * Susunan/tampil/desimal kolom milik user untuk salah satu tabel Bon Sementara.
   *
   * Bentuk hasilnya sengaja dibuat sama persis dengan
   * HeaderTableController@getHeaderTable dan dengan
   * PenerimaanDPPController@headerTable, supaya blade bisa memakai pola ReportTable
   * (geser & sembunyikan kolom) tanpa perlu menambah cabang baru di
   * HeaderTableController. Penyimpanannya tetap memakai tabel DBHEADERTABLE dan
   * route 'saveheadertable' yang sudah generik.
   */
  private function headerTable ($href, $kolomDefault, $reset = false) {
    $username = \Auth::user()->username;

    if ($reset) {
      DB::connection("SML")->update(
        "delete from DBHEADERTABLE where username = :username and href = :href",
        ["username" => $username, "href" => $href]
      );
    }

    $headertable = DB::connection("SML")->select(
      "select * from dbheadertable where href = :href and username = :username",
      ["username" => $username, "href" => $href]
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
      foreach ($kolomDefault as $key => $tipe) {
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

    // Desimal dititipkan sebagai JSON array di kolom `tipe` DBHEADERTABLE - pola yang
    // sama dengan HeaderTableController@desimalHeaderTable dan PenerimaanDPPController.
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

  /* Dipanggil tombol "Reset kolom" di bar kolom tersembunyi (ReportTable).
     Parameter `tabel` menentukan tabel mana yang di-reset: 'outstanding' untuk tabel
     Penambahan Bon (#tabel), selain itu tabel Outstanding Bon (#tabel2). */
  public function resetHeader (Request $req) {
    if ($req->input('tabel') === 'outstanding') {
      return $this->headerTable(self::HREF_OUT, $this->kolomDefaultOutstanding(), true);
    }
    return $this->headerTable(self::HREF, $this->kolomDefault(), true);
  }

  // Tabel #tabel2 ("Outstanding Bon") - bon yang Debet-nya belum lunas dibayar Kredit.
  // Kolom dialiaskan supaya namanya sama persis dengan kolomDefault().
  private function queryPenerimaan ($tglawal, $tglakhir, $perkiraan) {
    return DB::connection('SML')->select("
    declare @Perkiraan varchar(20), @TglAwal date, @TglAkhir date

    select @Perkiraan= :perkiraan, @TglAwal= :tglawal, @TglAkhir= :tglakhir

    Select 	A.NoBukti+right('0000000000'+A.Perkiraan,10)+right('00000'+cast(A.Urut as varchar(5)),5) KeyNoBukti,
            A.Devisi, A.NoBukti, A.NoBukti as [No Bon], A.NOURUT,
            Convert(varchar(10), A.Tanggal, 23) as [Tanggal],
            A.Penerima as [Penerima], A.Keterangan as [Keterangan],
    	A.Debet as [Debet], SUM(isnull(B.Kredit,0)) as [Kredit], A.Perkiraan, isnull(A.KodeVls,'IDR') KodeVls, isnull(A.Kurs,1) Kurs,
            isnull(A.DebetD,0) DebetD, SUM(isnull(B.KreditD,0)) KreditD,
    	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas,
            (A.Debet-SUM(isnull(B.Kredit,0))) as [Saldo],
            isnull(A.DebetD,0)-SUM(isnull(B.KreditD,0)) SaldoD
    From    dbBon A
    Left Outer Join dbBon B on B.NoBukti=A.NoBukti and B.Perkiraan=A.Perkiraan and B.Kredit<>0
    where   A.Perkiraan=@Perkiraan
            and A.Tanggal between @TglAwal and @TglAkhir
            and A.Debet<>0
    Group By A.Devisi, A.NoBukti, A.NOURUT, A.Tanggal, A.Penerima, A.Keterangan,
    	A.Debet, A.Perkiraan, A.KodeVls, A.Kurs, A.DebetD,
    	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas
    Having  SUM(isnull(B.Kredit,0))<A.Debet
    Order by A.Tanggal, A.NoBukti",[
      "perkiraan" => $perkiraan, "tglawal" => $tglawal, "tglakhir" => $tglakhir
    ]);
  }

  // Tabel #tabel ("Penambahan Bon") - seluruh baris dbBon (debet maupun kredit) pada
  // rentang tanggal terpilih. Kolom dialiaskan supaya namanya sama persis dengan
  // kolomDefaultOutstanding().
  private function queryOutstanding ($tglawal, $tglakhir, $perkiraan) {
    return DB::connection('SML')->select("
    declare @Perkiraan varchar(20), @TglAwal date, @TglAkhir date

    select @Perkiraan= :perkiraan, @TglAwal= :tglawal, @TglAkhir= :tglakhir

    Select 	A.NoBukti+right('0000000000'+A.Perkiraan,10)+right('00000'+cast(A.Urut as varchar(5)),5) KeyNoBukti,
            A.Devisi, A.NoBukti, A.NoBukti as [No Bon], A.NOURUT,
            Convert(varchar(10), A.Tanggal, 23) as [Tanggal],
            A.Penerima as [Penerima], A.Keterangan as [Keterangan],
	A.Debet as [Debet], A.Kredit as [Kredit], A.Perkiraan, isnull(A.KodeVls,'IDR') KodeVls, isnull(A.Kurs,1) Kurs, isnull(A.DebetD,0) DebetD, isnull(A.KreditD,0) KreditD,
	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas,
            -- Saldo = saldo berjalan per No Bukti: sisa bon setelah baris ini.
            -- Dihitung dari SELURUH baris bon (tidak dibatasi @TglAwal/@TglAkhir) supaya bon yang
            -- debet & kreditnya beda bulan tetap menampilkan sisa yang benar.
            -- Urutannya memakai A.Urut (urutan input dari spAdd), bukan A.Tanggal, karena tanggal
            -- kredit bisa lebih awal dari tanggal bon. Baris debet selalu Urut = 1.
            -- Memakai correlated subquery, bukan SUM() OVER(...), karena server masih SQL Server 2008 R2.
            -- C.Devisi disamakan agar subquery ini kena clustered PK (Devisi, NoBukti, Perkiraan, Urut).
            (select SUM(isnull(C.Debet,0)-isnull(C.Kredit,0))
               from dbBon C
              where C.Devisi = A.Devisi
                and C.NoBukti = A.NoBukti
                and C.Perkiraan = A.Perkiraan
                and C.Urut <= A.Urut) as [Saldo],
            (select SUM(isnull(C.DebetD,0)-isnull(C.KreditD,0))
               from dbBon C
              where C.Devisi = A.Devisi
                and C.NoBukti = A.NoBukti
                and C.Perkiraan = A.Perkiraan
                and C.Urut <= A.Urut) as SaldoD,
            A.NoBukti+A.Perkiraan nobuktiB
    From dbBon A
    where A.Perkiraan=@Perkiraan and A.Tanggal between @TglAwal and @TglAkhir
    Order by A.NoBukti, A.Urut",[
      "perkiraan" => $perkiraan, "tglawal" => $tglawal, "tglakhir" => $tglakhir
    ]);
  }

  public function index(Request $req) {
    $kodemenu = '02013';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());

    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $username = \Auth::user()->username;


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);

    $perkiraan = DB::connection("SML")->select("Select a.Perkiraan,b.Keterangan
from dbPosthutpiut a
 left outer join dbperkiraan b on b.perkiraan=a.perkiraan
where (a.Kode='KAS' or A.IsBeliJual=1)
 and A.perkiraan in (select perkiraan from dbaksesperkiraanBS where userID= :username )
 Order by a.Perkiraan" , ["username" => $username]);

    list($tglAwal, $tglAkhir) = $this->periodeRange($periode);
    list($outTglAwal, $outTglAkhir) = $this->rentangTanggalOutstanding($req, $periode);

    // Kedua tabel (Penambahan Bon & Outstanding Bon) digambar JS lewat loadAll(),
    // jadi tidak ada lagi data baris yang dikirim dari sini.
    return view('accounting.bonsementara' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "akses" => $akses,
      "perkiraan" => $perkiraan,
      "tglAwal" => $tglAwal,
      "tglAkhir" => $tglAkhir,
      "outTglAwal" => $outTglAwal,
      "outTglAkhir" => $outTglAkhir,
    ]);

  }


  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    list($tglawal, $tglakhir) = $this->rentangTanggal($req, $periode);
    $tempPenerimaan = $this->queryPenerimaan($tglawal, $tglakhir, $req->perkiraan);

    list($outAwal, $outAkhir) = $this->rentangTanggalOutstanding($req, $periode);
    $tempOutstanding = $this->queryOutstanding($outAwal, $outAkhir, $req->perkiraan);

    // Dua tabel, dua susunan kolom tersimpan (lihat headerTable()).
    $header    = $this->headerTable(self::HREF, $this->kolomDefault());
    $headerOut = $this->headerTable(self::HREF_OUT, $this->kolomDefaultOutstanding());

    return [
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan"  => $tempPenerimaan,

      "aliasordered"      => $header['aliasordered'],
      "headertableheader" => $header['headertableheader'],
      "headertablevalue"  => $header['headertablevalue'],
      "isnumeric"         => $header['isnumeric'],
      "isshown"           => $header['isshown'],
      "isparsed"          => $header['isparsed'],
      "desimal"           => $header['desimal'],

      // Konfigurasi kolom tabel Penambahan Bon (#tabel) - bentuknya sama, diawali "out".
      "outaliasordered"      => $headerOut['aliasordered'],
      "outheadertableheader" => $headerOut['headertableheader'],
      "outheadertablevalue"  => $headerOut['headertablevalue'],
      "outisnumeric"         => $headerOut['isnumeric'],
      "outisshown"           => $headerOut['isshown'],
      "outisparsed"          => $headerOut['isparsed'],
      "outdesimal"           => $headerOut['desimal'],
    ];
  }



  public function getDetailOutstanding (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // select * from DBBON where NoBukti = :NoBukti and Kredit > 0
    $check = DB::connection('SML')->select("select NoBukti, Devisi, perkiraan from dbbon where NoBukti = :nobukti  and perkiraan = :perkiraan and Kredit > 0" , [
      "nobukti" => $req->nobukti,
      // "bulan" => $periode->bulan,
      // "tahun" => $periode->tahun,
      "perkiraan" => $req->perkiraan
    ]);

        $tempOutstanding = DB::connection('SML')->select("declare @nobukti varchar(20), @Urut int, @Perkiraan varchar(20)

select @nobukti = :nobukti , @urut= :urut , @Perkiraan= :perkiraan

Select 	A.NoBukti+right('0000000000'+A.Perkiraan,10)+right('00000'+cast(A.Urut as varchar(5)),5) KeyNoBukti,
        A.Devisi, A.NoBukti, A.NOURUT, A.Tanggal, A.Penerima, A.Keterangan,
	A.Debet, A.Kredit, A.Perkiraan, isnull(A.KodeVls,'IDR') KodeVls, isnull(A.Kurs,1) Kurs, isnull(A.DebetD,0) DebetD, isnull(A.KreditD,0) KreditD,
	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas,
        A.Debet-A.Kredit Saldo, isnull(A.DebetD,0)-isnull(A.KreditD,0) SaldoD,nobukti+perkiraan nobuktiB
From dbBon A
where A.Perkiraan=@Perkiraan and Urut = @urut and NoBukti = @nobukti
--and A.Debet<>0
Order by A.NoBukti
",[
      "nobukti" => $req->nobukti , "urut" => $req->urut,
      "perkiraan" => $req->perkiraan
    ]);


    $sisa = DB::connection('SML')->select("select isnull(MAX(Debet) , 0) - isnull(sum(Kredit), 0) sisa
     from dbBon
    where NoBukti = :nobukti
     and Perkiraan= :perkiraan and Devisi='01'
",[
  "nobukti" => $req->nobukti ,
  "perkiraan" => $req->perkiraan
]);






  return
  ["check" => $check,
  "detail" =>$tempOutstanding,

  "sisa" => $sisa
];
  }


  public function getDetailPenerimaan (Request $req ) {





$tempHeader = DB::connection("SML")->select("


Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.NoDPP, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota, A.Penagih,
Round(Sum(A.DIBAYAR),0) TotDIBAYAR, Sum(A.LB) TotLB, Sum(A.KL) TotKL,
A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi , a.Debet
From vwTransTerimaDPP A
where A.pPLD=1 and a.nobukti = :nobukti
group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.Penagih,
A.NoDPP, A.NamaKota,
A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi , a.Debet
order by A.NOBUKTI

" , ["nobukti" => $req->nobukti]);

$tempDetail = DB::connection("SML")->select("
Select
A.NOBUKTI,A.NOURUT,A.TANGGAL,A.NoDPP,A.Debet ,A.KODECUSTSUPP,A.NAMACUSTSUPP
,A.Alamat,A.ALAMATKOTA,A.KOTA,A.NamaKota,A.Valas,A.Penagih,A.IsOtorisasi1,A.OtoUser1
,A.TglOto1,A.IsOtorisasi2,A.OtoUser2,A.TglOto2,A.IsOtorisasi3,A.OtoUser3,A.TglOto3
,A.IsOtorisasi4,A.OtoUser4,A.TglOto4,A.IsOtorisasi5,A.OtoUser5,A.TglOto5,A.NeedOtorisasi
,A.NoJurnal,A.NoUrutJurnal,A.TglJurnal,A.MaxOL,A.URUT,A.TipeKasBank,A.MyTipeKasBank
,A.KasBank,A.UrutDPP,A.NOFAKTUR,A.DIBAYAR,A.LB,A.KL,A.Kurs,A.perkiraan,A.Keterangan
,A.pPLD,A.KodeCustSuppD,A.NamaCustSuppD,A.TGLTITIP,A.Nott, P.Keterangan NamaPerkiraan , Q.Keterangan NamaKasBank
From vwTransTerimaDPP A
left outer join dbPerkiraan P on P.Perkiraan=A.Perkiraan
left outer join dbPerkiraan Q on Q.Perkiraan=A.KasBank
where	A.NoBukti= :nobukti
order by A.KasBank, A.NoFaktur, A.Perkiraan, A.Urut

" , ["nobukti" => $req->nobukti]);




    return ["header" => $tempHeader,
  "detail" => $tempDetail];
  }


  public function getNoBukti (Request $req) {
    // return 1;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $nobukti = DB::connection("SML")->select("Select top 1 NoBukti
    from dbBon
    where month(Tanggal)= :bulan and year(Tanggal)= :tahun
     and Perkiraan= :perkiraan and Devisi='01' and Debet<>0
    order by NoBukti desc" , [
      "bulan" => $periode->bulan,
      "tahun" => $periode->tahun,
      "perkiraan" => $req->perkiraan
    ]);

    return $nobukti;
  }









  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBTerimaDPP set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal , tglbatal = NULL, userbatal = '' where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update DBTerimaDPP set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL , tglbatal = :tanggal, userbatal = :username where nobukti = :nobukti", [ "nobukti" => $req->nobukti , "username" => \Auth::user()->username , "tanggal" => $tanggal ]);
    return $res;
  }

  public function spAdd (Request $req) {

    $username = \Auth::user()->username;

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->choice == 'I' && $req->tipeadd == 'nonkredit') {
      $check = DB::connection('SML')->select('select * from dbbon where Nobukti = :nobon and perkiraan = :perkiraan',["nobon" => $req->nobon , "perkiraan" => $req->perkiraan]);
        if ($check) {
          return 2;
      }
    }


    $urutx = $req->urut;

    if ($req->choice == 'I') {
      $uruty = DB::connection('SML')->select("Select top 1 isnull(urut , 0) + 1 urut
    from dbBon
    where  NoBukti = :nobon
     and Perkiraan= :perkiraan and Devisi='01'
     order by urut desc ",["nobon" => $req->nobon , "perkiraan" => $req->perkiraan]);


     if ($uruty) {
       $urutx = $uruty[0]->urut;
     } else {
       $urutx = 1;
     }

    }


      $values = [
    $req->choice , // @choice varchar(1),
    $req->devisi,// @Devisi varchar(10),
    $req->nobon,// @NoBukti varchar(20),
    $req->tanggal,// @Tanggal datetime,
    $req->penerima,// @Penerima varchar(40),
    $req->keterangan ? $req->keterangan : '',// @Keterangan varchar(40),
    $req->jumlah,// @Debet numeric(18,2),
    $req->kredit,// @Kredit numeric(18,2),
    $req->perkiraan,// @Perkiraan varchar(15),
    date('Y-m-d H:i:s'),// @TglInput datetime,
    $username,// @UserID varchar(10),
    $urutx,// @Urut tinyint,
    $req->valas,// @KodeVls varchar(10),
    $req->kurs,// @Kurs numeric(18,4),
    $req->debetd,// @DebetD numeric(18,2),
    $req->kreditd,// @KreditD numeric(18,2)
  ];



        DB::connection('SML')->statement('exec sp_bon ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);


      return 1;

  }

  public function spKoreksi (Request $req) {

    $username = \Auth::user()->username;
    $jmlrecord = $req->jmlrecord;

//     select * from dbdph where NoBukti like '%0525%'
//



      DB::connection('SML')->statement('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice,
      $req->nobukti,
      $req->nourut,
      $req->tanggal ,
      '',
      '',
      '',
      0,
      '',
      'DPP' ,
      '113400' ,
      0,
      $req->urut,
      '' ,
      $req->dibayar,
      $req->perkiraan,
      $req->kl,
      $req->lb,
      $username ,
      1 ,
      ''
    ]);


      return 1;
  }



}
