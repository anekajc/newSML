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





class CetakPengajuanDphController extends Controller


{

  public function index(Request $req) {
    $kodemenu = '020303';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
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









    $tempOutstanding = DB::connection("SML")->select("


    select A.nobukti,A.tanggal,c.kodecustsupp,D.namacustsupp,B.nobukti nott,B.usercetak,B.TANGGAL tglcetak,
    a.valas,E.kl,e.dibayar
    from dbDPH A
    left outer join DBNOMORDPH B on A.NoBukti=b.NOINVOICE
    left outer join (select nobukti,kodecustsupp from DBDPHDET group by NoBukti,KODECUSTSUPP) c on a.NoBukti=c.NoBukti
    left outer join DBCUSTSUPP d on c.KODECUSTSUPP=d.KODECUSTSUPP
    Left Outer Join (select NoBukti,Sum(Dibayar)dibayar,Sum(KL) KL,KODECUSTSUPP from dbDPHdet Group By NoBukti,KODECUSTSUPP
    )E on a.NoBukti=E.NoBukti
    WHERE
    B.NOBUKTI IS NULL  and A.nobukti like '%DPH%'
    order by a.NoBukti
    " , []);



        $collection1 = collect($tempOutstanding)->groupBy('nobukti');
        $tempOutstanding1 = [];
        foreach ($collection1 as $p) {
          // code...
          array_push($tempOutstanding1, $p);
        }




        $tempPenerimaan = DB::connection("SML")->select("declare @Tahun int, @Bulan int

    select @Tahun= :tahun , @Bulan= :bulan

    Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.NoDPP, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota, A.Penagih,
      Round(Sum(A.DIBAYAR),0) TotDIBAYAR, Sum(A.LB) TotLB, Sum(A.KL) TotKL,
      A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
    From vwTransTerimaDPP A
    where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan  and A.pPLD=1
    group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.Penagih,
      A.NoDPP, A.NamaKota,
      A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
    order by A.NOBUKTI" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);



            $collection2 = collect($tempPenerimaan)->groupBy('NoBukti');
            $tempPenerimaan1 = [];
            foreach ($collection2 as $p) {
              // code...
              array_push($tempPenerimaan1, $p);
            }

    return view('accounting.cetakpengajuandph' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding1,
      "tempPenerimaan" => $tempPenerimaan1,
      "akses" => $akses,
      "tempListPerkiraan" => $tempListPerkiraan,
    ]);

  }



  public function loadAll () {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("

    select A.nobukti,A.tanggal,c.kodecustsupp,D.namacustsupp,B.nobukti nott,B.usercetak,B.TANGGAL tglcetak,
    a.valas,E.kl,e.dibayar
    from dbDPH A
    left outer join DBNOMORDPH B on A.NoBukti=b.NOINVOICE
    left outer join (select nobukti,kodecustsupp from DBDPHDET group by NoBukti,KODECUSTSUPP) c on a.NoBukti=c.NoBukti
    left outer join DBCUSTSUPP d on c.KODECUSTSUPP=d.KODECUSTSUPP
    Left Outer Join (select NoBukti,Sum(Dibayar)dibayar,Sum(KL) KL,KODECUSTSUPP from dbDPHdet Group By NoBukti,KODECUSTSUPP
    )E on a.NoBukti=E.NoBukti
    WHERE
    B.NOBUKTI IS NULL  and A.nobukti like '%DPH%'
    order by a.NoBukti
    " , []);


        $collection1 = collect($tempOutstanding)->groupBy('nobukti');
        $tempOutstanding1 = [];
        foreach ($collection1 as $p) {
          // code...
          array_push($tempOutstanding1, $p);
        }



        $tempPenerimaan = DB::connection("SML")->select("declare @Tahun int, @Bulan int

    select @Tahun= :tahun , @Bulan= :bulan

    Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.NoDPP, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota, A.Penagih,
      Round(Sum(A.DIBAYAR),0) TotDIBAYAR, Sum(A.LB) TotLB, Sum(A.KL) TotKL,
      A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
    From vwTransTerimaDPP A
    where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan  and A.pPLD=1
    group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.Penagih,
      A.NoDPP, A.NamaKota,
      A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
      A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
      A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi
    order by A.NOBUKTI" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);



            $collection2 = collect($tempPenerimaan)->groupBy('NoBukti');
            $tempPenerimaan1 = [];
            foreach ($collection2 as $p) {
              // code...
              array_push($tempPenerimaan1, $p);
            }
    return [
    "tempOutstanding" => $tempOutstanding1,
    "tempPenerimaan" => $tempPenerimaan1];
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

     $res = DB::connection('SML')->update("     Delete dbTempTTDPH
     Insert Into dbTempTTDPH
     Select 1,A.NoBukti,b.KODECUSTSUPP,C.nAMACUSTSUPP,A.VALAS,B.DIBAYAR,B.KL from dbDPH A
     LEFT OUTER JOIN (SELECT NOBUKTI,KODECUSTSUPP,SUM(DIBAYAR) DIBAYAR,SUM(KL) kl
						FROM DBDPHDET GROUP BY NoBukti,KODECUSTSUPP) B ON A.NoBukti=B.NoBukti
     LEFT OUTER JOIN DBCUSTSUPP C ON B.KODECUSTSUPP =C.KODECUSTSUPP
     where
      A.Nobukti Not in(select NoInvoice from DBNOMORDPH) and A.Tipe='DPH'",
     []);



    $tempOutstanding = DB::connection("SML")->select("
    Select pilih,noinvoice,kodecustsupp,namacustsupp,valas,dibayar,kl from dbTempTTDPH

    " , []);

  return $tempOutstanding;
  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakTTDPH ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
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
,A.pPLD,A.KodeCustSuppD,A.NamaCustSuppD,A.TGLTITIP,A.Nott, P.Keterangan NamaPerkiraan , Q.Keterangan NamaKasBank
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

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?,?',$values);

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



}
