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





class PelunasanPiutangDPPController extends Controller


{
  // Nama href untuk DBHEADERTABLE - dipatok, bukan dari $req->path(), supaya sama
  // persis dengan PLD_HREF / OUT_HREF di pelunasanpiutangdpp.blade.php.
  const HREF     = 'pelunasanpiutangdpp';
  const HREF_OUT = 'pelunasanpiutangdppoutstanding';

  // Nama key HARUS sama persis dengan alias kolom di query karena dipakai juga
  // sebagai nama field data di JS. tipe: 0 = varchar, 1 = float, 2 = date.
  // Oto/OtoUser1/TglOto1 sengaja tidak dimasukkan - selalu ditambahkan lewat JS.
  private function kolomDefault () {
    return [
      'No Bukti'     => 0,
      'Tanggal'      => 2,
      'Kode Cust'    => 0,
      'Nama Cust'    => 0,
      'No DPP'       => 0,
      'Dibayar'      => 1,
      'Lebih Bayar'  => 1,
      'Kurang Bayar' => 1,
    ];
  }

  private function kolomDefaultOutstanding () {
    return [
      'No Bukti'   => 0,
      'Customer'   => 0,
      'Tanggal'    => 2,
      'Penagih'    => 0,
      'Jumlah$'    => 1,
      'JumlahRp'   => 1,
      'LB'         => 1,
      'Dibayar'    => 1,
      'Sisa'       => 1,
      'Keterangan' => 0,
    ];
  }

  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  private function rentangTanggal (Request $req, $periode) {
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }
    return [$tglawal, $tglakhir];
  }

  // Outstanding hanya pakai satu batas tanggal (sampai tanggal akhir) - tunggakan lama
  // tidak boleh hilang hanya karena ada batas bawah tanggal.
  private function tanggalAkhirOutstanding (Request $req, $periode) {
    list(, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('outtglakhir'))) { $tglakhir = $req->input('outtglakhir'); }
    return $tglakhir;
  }

  // Sama persis dengan PenerimaanDPPController@headerTable - susunan/tampil/desimal
  // kolom milik user, disimpan di DBHEADERTABLE lewat route generik 'saveheadertable'.
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

  public function resetHeader (Request $req) {
    if ($req->input('tabel') === 'outstanding') {
      return $this->headerTable(self::HREF_OUT, $this->kolomDefaultOutstanding(), true);
    }
    return $this->headerTable(self::HREF, $this->kolomDefault(), true);
  }

  private function queryOutstanding ($tglakhir) {
    return DB::connection('SML')->select("
select A.NoBukti+B.NOURUT KeyNOBUKTI,
	A.NOBUKTI,
	A.NOBUKTI                             as [No Bukti],
	B.NOURUT, A.TANGGAL,
	Convert(varchar(10), A.TANGGAL, 23)   as [Tanggal],
	A.Valas, '' Penagih, '' NoBukti,
	'' as [Penagih],
	C.namaCustSupp                        as [Customer],
	A.CustSuppL,
	case when A.Valas='IDR' then 0.00 else A.Debet+A.Kredit end JumlahD,
	case when A.Valas='IDR' then 0.00 else A.Debet+A.Kredit end as [Jumlah\$],
	(A.Debet+A.Kredit)*A.Kurs JumlahRp,
	(A.Debet+A.Kredit)*A.Kurs as [JumlahRp],
	A.urut urutTrans, A.debet, A.Keterangan as [Keterangan],
	a.CustSuppL, Round(d.DIBAYAR,0) Dibayar,
	Round(d.DIBAYAR,0) as [Dibayar],
	A.debet - (isnull(d.Dibayar,0)+isnull(D.LB,0))-ISNULL(E.DEBET,0) Sisa,
	A.debet - (isnull(d.Dibayar,0)+isnull(D.LB,0))-ISNULL(E.DEBET,0) as [Sisa],
	isnull(D.LB,0) LB,
	isnull(D.LB,0) as [LB]
from dbTransaksi A
LEFT OUTER JOIN DBTRANS B ON A.NoBukti=B.NoBukti
LEFT OUTER JOIN DBCUSTSUPP C ON A.CustSuppL=C.KODECUSTSUPP
LEFT OUTER JOIN (select UrutDPP,NODPP,sum(dibayar) Dibayar,sum(LB) LB from DBTerimaDPPDET group by UrutDPP,NODPP) D ON A.NObukti=D.NoDPP AND A.urut=D.UrutDPP
LEFT OUTER JOIN (SELECT NOTITIPAN,URUTTITIPAN,SUM(Debet) DEBET
                 FROM dbTransaksi GROUP BY NOTITIPAN,URUTTITIPAN) E ON A.NoBukti=E.NOTITIPAN AND A.Urut=E.URUTTITIPAN
where A.Lawan='113400' AND A.CustSuppL<>'' and A.TANGGAL <= :tglakhir
and    A.debet - (isnull(d.Dibayar,0)+isnull(D.LB,0))-ISNULL(E.DEBET,0) >0
order by A.TANGGAL desc, A.NOBUKTI desc
" , ["tglakhir" => $tglakhir]);
  }

  private function queryPelunasan ($tglawal, $tglakhir) {
    return DB::connection('SML')->select("
Select 	A.NoBukti,
	A.NoBukti                             as [No Bukti],
	A.NoUrut, A.Tanggal,
	Convert(varchar(10), A.Tanggal, 23)   as [Tanggal],
	A.NoDPP,
	A.NoDPP                               as [No DPP],
	A.KODECUSTSUPP                        as [Kode Cust],
	A.NamaCustSupp                        as [Nama Cust],
	A.NamaKota, A.Penagih,
	Round(Sum(A.DIBAYAR),0) TotDIBAYAR,
	Round(Sum(A.DIBAYAR),0) as [Dibayar],
	Sum(A.LB) TotLB,
	Sum(A.LB) as [Lebih Bayar],
	Sum(A.KL) TotKL,
	Sum(A.KL) as [Kurang Bayar],
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
From vwTransTerimaDPP A
where A.Tanggal between :tglawal and :tglakhir and A.pPLD=1
group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.Penagih,
	A.NoDPP, A.NamaKota,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
order by A.Tanggal desc, A.NoBukti desc" , ["tglawal" => $tglawal, "tglakhir" => $tglakhir]);
  }

  public function index(Request $req) {
    $kodemenu = '02031';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $username = \Auth::user()->username;

    $tempListPerkiraan = DB::connection("SML")->select("
    Select Perkiraan, Keterangan, Simbol, cast(IsPPN as tinyint) IsPPN from dbPerkiraan where Tipe=1
                    and Perkiraan in (select Perkiraan from dbPostHutPiut where Kode='SLS')
    and Perkiraan in (select Perkiraan from dbAksesPerkiraan where UserID= :username )
    ",[ "username" => $username ]);

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(5);

    list($pldTglAwal, $pldTglAkhir) = $this->periodeRange($periode);
    $outTglAkhir = $this->tanggalAkhirOutstanding($req, $periode);

    // Kedua tabel digambar JS lewat loadAll(), jadi tidak ada lagi baris data
    // yang dikirim dari sini.
    return view('accounting.pelunasanpiutangdpp' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "akses" => $akses,
      "tempListPerkiraan" => $tempListPerkiraan,
      "pldTglAwal" => $pldTglAwal,
      "pldTglAkhir" => $pldTglAkhir,
      "outTglAkhir" => $outTglAkhir,
    ]);

  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.SP_CETAKTERIMADPP ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    list($tglawal, $tglakhir) = $this->rentangTanggal($req, $periode);
    $tempPelunasan = $this->queryPelunasan($tglawal, $tglakhir);

    $outAkhir = $this->tanggalAkhirOutstanding($req, $periode);
    $tempOutstanding = $this->queryOutstanding($outAkhir);

    $header    = $this->headerTable(self::HREF, $this->kolomDefault());
    $headerOut = $this->headerTable(self::HREF_OUT, $this->kolomDefaultOutstanding());

    return [
      "tempOutstanding"   => $tempOutstanding,
      "tempPelunasan"     => $tempPelunasan,

      "aliasordered"      => $header['aliasordered'],
      "headertableheader" => $header['headertableheader'],
      "headertablevalue"  => $header['headertablevalue'],
      "isnumeric"         => $header['isnumeric'],
      "isshown"           => $header['isshown'],
      "isparsed"          => $header['isparsed'],
      "desimal"           => $header['desimal'],

      "outaliasordered"      => $headerOut['aliasordered'],
      "outheadertableheader" => $headerOut['headertableheader'],
      "outheadertablevalue"  => $headerOut['headertablevalue'],
      "outisnumeric"         => $headerOut['isnumeric'],
      "outisshown"           => $headerOut['isshown'],
      "outisparsed"          => $headerOut['isparsed'],
      "outdesimal"           => $headerOut['desimal'],
    ];
  }

  public function getListTerimaDPP (Request $req) {
    $username = \Auth::user()->username;
    $values = [
      $req->nodpp,
      $req->kodecust,
      '113400',
      $username
    ];


    $res = DB::connection('SML')->update('exec sp_RefreshTempLunasDPP ?,?,?,?',$values);


      $tempList = DB::connection("SML")->select("
      select * from dbTempTerimaDPP where IDUser= :username order by Urut" , ["username" => $username]);

      return $tempList;





  }

  public function getDetailOutstanding (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("declare @nobukti Varchar(30) , @Tahun int, @Bulan int, @Periode Varchar(30)

select @Tahun= :tahun , @Bulan= :bulan , @nobukti = :nobukti

select A.NoBukti+B.NOURUT KeyNOBUKTI,
	A.NOBUKTI, B.NOURUT, A.TANGGAL,
	A.Valas, ''Penagih,'' NoBukti ,C.namaCustSupp, A.CustSuppL,
         case when A.Valas='IDR' then 0.00 else A.Debet+A.Kredit end JumlahD, (A.Debet+A.Kredit)*A.Kurs JumlahRp ,
        A.urut urutTrans ,A.debet,A.Keterangan  ,a.CustSuppL ,Round(d.DIBAYAR,0) Dibayar , A.debet - (isnull(d.Dibayar,0)+isnull(D.LB,0))-ISNULL(E.DEBET,0) Sisa  ,
        isnull(D.LB,0) LB
from dbTransaksi A
LEFT OUTER JOIN DBTRANS B ON A.NoBukti=B.NoBukti
LEFT OUTER JOIN DBCUSTSUPP C ON A.CustSuppL=C.KODECUSTSUPP
LEFT OUTER JOIN (select UrutDPP,NODPP,sum(dibayar) Dibayar,sum(LB) LB from DBTerimaDPPDET group by UrutDPP,NODPP) D ON A.NObukti=D.NoDPP AND A.urut=D.UrutDPP
LEFT OUTER JOIN (SELECT NOTITIPAN,URUTTITIPAN,SUM(Debet) DEBET
                 FROM dbTransaksi GROUP BY NOTITIPAN,URUTTITIPAN) E ON A.NoBukti=E.NOTITIPAN AND A.Urut=E.URUTTITIPAN
where A.Lawan='113400' AND A.CustSuppL<>''  and A.TANGGAL>'03/28/2016' and a.nobukti = @nobukti
and    A.debet - (isnull(d.Dibayar,0)+isnull(D.LB,0))-ISNULL(E.DEBET,0) >0
" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan, "nobukti" => $req->nobukti ]);

  return $tempOutstanding;
  }


  public function getDetailPenerimaan (Request $req ) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    $X = DB::connection("SML")->select("declare @nobukti Varchar(30) , @Tahun int, @Bulan int, @Periode Varchar(30)

    select @Tahun= :tahun , @Bulan= :bulan , @nobukti = :nobukti

    select A.NoBukti+B.NOURUT KeyNOBUKTI,
    A.NOBUKTI, B.NOURUT, A.TANGGAL,
    A.Valas, ''Penagih,'' NoBukti ,C.namaCustSupp, A.CustSuppL,
         case when A.Valas='IDR' then 0.00 else A.Debet+A.Kredit end JumlahD, (A.Debet+A.Kredit)*A.Kurs JumlahRp ,
        A.urut urutTrans ,A.debet,A.Keterangan  ,a.CustSuppL ,Round(d.DIBAYAR,0) Dibayar , A.debet - (isnull(d.Dibayar,0)+isnull(D.LB,0))-ISNULL(E.DEBET,0) Sisa  ,
        isnull(D.LB,0) LB
    from dbTransaksi A
    LEFT OUTER JOIN DBTRANS B ON A.NoBukti=B.NoBukti
    LEFT OUTER JOIN DBCUSTSUPP C ON A.CustSuppL=C.KODECUSTSUPP
    LEFT OUTER JOIN (select UrutDPP,NODPP,sum(dibayar) Dibayar,sum(LB) LB from DBTerimaDPPDET group by UrutDPP,NODPP) D ON A.NObukti=D.NoDPP AND A.urut=D.UrutDPP
    LEFT OUTER JOIN (SELECT NOTITIPAN,URUTTITIPAN,SUM(Debet) DEBET
                 FROM dbTransaksi GROUP BY NOTITIPAN,URUTTITIPAN) E ON A.NoBukti=E.NOTITIPAN AND A.Urut=E.URUTTITIPAN
    where A.Lawan='113400' AND A.CustSuppL<>''  and A.TANGGAL>'03/28/2016' and a.nobukti = @nobukti

    " , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan, "nobukti" => $req->nodpp ]);



$tempHeader = DB::connection("SML")->select("


Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.NoDPP, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota, A.Penagih,
Round(Sum(A.DIBAYAR),0) TotDIBAYAR, Sum(A.LB) TotLB, Sum(A.KL) TotKL,
A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi , max(a.Debet) Debet
From vwTransTerimaDPP A
where A.pPLD=1 and a.nobukti = :nobukti
group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.Penagih,
A.NoDPP, A.NamaKota,
A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi


" , ["nobukti" => $req->nobukti]);

$tempDetail = DB::connection("SML")->select("
Select
A.NOBUKTI,A.NOURUT,A.TANGGAL,A.NoDPP,A.Debet ,A.KODECUSTSUPP,A.NAMACUSTSUPP
,A.Alamat,A.ALAMATKOTA,A.KOTA,A.NamaKota,A.Valas,A.Penagih,A.IsOtorisasi1,A.OtoUser1
,A.TglOto1,A.IsOtorisasi2,A.OtoUser2,A.TglOto2,A.IsOtorisasi3,A.OtoUser3,A.TglOto3
,A.IsOtorisasi4,A.OtoUser4,A.TglOto4,A.IsOtorisasi5,A.OtoUser5,A.TglOto5,A.NeedOtorisasi
,A.NoJurnal,A.NoUrutJurnal,A.TglJurnal,A.MaxOL,A.URUT,A.TipeKasBank,A.MyTipeKasBank
,A.KasBank,A.UrutDPP,A.NOFAKTUR,A.DIBAYAR,A.LB,A.KL,A.Kurs,A.perkiraan,A.Keterangan
,A.pPLD,A.KodeCustSuppD,A.NamaCustSuppD,A.TGLTITIP,A.Nodpp, P.Keterangan NamaPerkiraan , Q.Keterangan NamaKasBank
From vwTransTerimaDPP A
left outer join dbPerkiraan P on P.Perkiraan=A.Perkiraan
left outer join dbPerkiraan Q on Q.Perkiraan=A.KasBank
where	A.NoBukti= :nobukti
order by A.KasBank, A.NoFaktur, A.Perkiraan, A.Urut

" , ["nobukti" => $req->nobukti]);




    return ["X" => $X,
    "header" => $tempHeader,
  "detail" => $tempDetail];
  }


  public function getNoBukti (Request $req) {
    // return 1;
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $kode = $req->kode;
    $inisial = DB::connection("SML")->select('select ' . $kode . ' from DBNOMOR');

    $values = [
        $inisial[0]->$kode,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
        $req->simbol
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobuktiSimbol ?,?,?,?,?',$values);

    return $noBukti;
  }










  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $res = DB::connection('SML')->update("update DBTerimaDPP set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal , tglbatal = NULL, userbatal = '' where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);


    $values = [
               '',
               'DBTERIMADPP',
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
    $tanggal = date('Y-m-d H:i:s');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $res = DB::connection('SML')->update("update DBTerimaDPP set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL , tglbatal = :tanggal, userbatal = :username where nobukti = :nobukti", [ "nobukti" => $req->nobukti , "username" => \Auth::user()->username , "tanggal" => $tanggal ]);
    $values = [
               '',
               'DBTERIMADPP',
               $periode->bulan,
               $periode->tahun,
               $req->nobukti,
               0
          ];
          DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
          DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);



    return $res;
  }

  public function spAdd (Request $req) {

    $username = \Auth::user()->username;
    $listData = $req->tempData ?? [];
    $listDataKL = $req->tempDataKL ?? [] ;
    $listDataLB = $req->tempDataLB ?? [];

    // return [
    //   "listData"=> $listData,
    //   "listDataKL"=> $listDataKL,
    //   "listDataLB"=> $listDataLB,
    // ];

    $jmlrecord = $req->jmlrecord;

    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from DBTerimaDPP where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

      foreach ($listData as $d)  {


        DB::connection('SML')->statement('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal ,
          $d['NODPP'],
          $req->kodecustsupp,
          $d['Valas'],
          $d['Kurs'],
          '',
          'DPP' ,
          '113400' ,
          $req->urutTrans,
          0,
          $d['NOFAKTUR'] ,
          $d['DIBAYAR'] ,
          '113400',
          0 ,
          0,
          $username ,
          1 ,
          $d['KodeCustSupp']

        ]);

        $jmlrecord = 1;



      }

      foreach ($listDataKL as $d)  {


        DB::connection('SML')->statement('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal ,
          $d['NODPP'],
          $req->kodecustsupp,
          $d['Valas'],
          $d['Kurs'],
          '',
          'DPP' ,
          '113400' ,
          $req->urutTrans,
          0,
          $d['NOFAKTUR'] ,
          0 ,
          $d['inputPerkiraanKL'],
          $d['inputKL'] ,
          0,
          $username ,
          1 ,
          $d['KodeCustSupp']
        ]);

        $jmlrecord = 1;



      }

      foreach ($listDataLB as $d)  {


        DB::connection('SML')->statement('exec sp_TransTerimaDPP ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal ,
          $d['NODPP'],
          $req->kodecustsupp,
          $d['Valas'],
          $d['Kurs'],
          '',
          'DPP' ,
          '113400' ,
          $req->urutTrans,
          0,
          $d['NOFAKTUR'] ,
          0 ,
          $d['inputPerkiraanLB'],
          0 ,
          $d['inputLB'],
          $username ,
          1 ,
          $d['KodeCustSupp']

        ]);

        $jmlrecord = 1;



      }


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

  // Master customer untuk modal koreksi customer NO NAME di tab Outstanding.
  public function listCustomer () {
    return DB::connection('SML')->select("
      select KODECUSTSUPP, NAMACUSTSUPP, ALAMAT1
      from DBCUSTSUPP where JENIS = 1 and IsAktif = 1
      order by NAMACUSTSUPP");
  }

  // Koreksi customer satu baris outstanding (NoBukti+urut) di dbTransaksi. Tidak ada
  // stored procedure yang menyediakan ini - dbTransaksi.CustSuppL memang satu-satunya
  // tempat penyimpanan customer baris outstanding (lihat queryOutstanding()).
  public function spKoreksiCustomer (Request $req) {
    $kodemenu = '02031';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, 'pelunasanpiutangdpp');
    if (!$akses || !$akses->ISKOREKSI) {
      return 'No access';
    }

    $nobukti = (string) $req->input('nobukti');
    $urut    = $req->input('urut');
    $kode    = (string) $req->input('kodecustsupp');

    if ($kode === '' || $kode === 'CZ999') {
      return 'Customer tujuan tidak valid';
    }

    $cust = DB::connection('SML')->select(
      "select KODECUSTSUPP from DBCUSTSUPP where KODECUSTSUPP = :kode and JENIS = 1 and IsAktif = 1",
      ["kode" => $kode]
    );
    if (count($cust) === 0) {
      return 'Customer tujuan tidak ditemukan';
    }

    $baris = DB::connection('SML')->select(
      "select NoBukti from dbTransaksi where NoBukti = :nobukti and urut = :urut and Lawan = '113400'",
      ["nobukti" => $nobukti, "urut" => $urut]
    );
    if (count($baris) === 0) {
      return 'Baris outstanding tidak ditemukan';
    }

    $sudahDibayar = DB::connection('SML')->select(
      "select UrutDPP from DBTerimaDPPDET where NODPP = :nobukti and UrutDPP = :urut",
      ["nobukti" => $nobukti, "urut" => $urut]
    );
    if (count($sudahDibayar) > 0) {
      return 'Baris ini sudah ada pelunasan, tidak bisa dikoreksi customernya';
    }

    DB::connection('SML')->update(
      "update dbTransaksi set CustSuppL = :kode where NoBukti = :nobukti and urut = :urut and Lawan = '113400'",
      ["kode" => $kode, "nobukti" => $nobukti, "urut" => $urut]
    );

    return 1;
  }

}
