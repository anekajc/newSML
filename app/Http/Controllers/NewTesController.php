<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;





class NewTesController extends Controller


{

  public function index(Request $req) {
    $kodemenu = '02013';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu);
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $username = \Auth::user()->username;


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();





            $perkiraan = DB::connection("SML")->select("Select a.Perkiraan,b.Keterangan
from dbPosthutpiut a
 left outer join dbperkiraan b on b.perkiraan=a.perkiraan
where (a.Kode='KAS' or A.IsBeliJual=1)
 and A.perkiraan in (select perkiraan from dbaksesperkiraanBS where userID= :username )
 Order by a.Perkiraan" , ["username" => $username]);


    $tempPenerimaan = DB::connection('SML')->select("
    declare @Tahun int, @Bulan int, @Perkiraan varchar(20)

    select @Tahun= :tahun , @Bulan= :bulan , @Perkiraan= :perkiraan

    Select 	A.NoBukti+right('0000000000'+A.Perkiraan,10)+right('00000'+cast(A.Urut as varchar(5)),5) KeyNoBukti,
            A.Devisi, A.NoBukti, A.NOURUT, A.Tanggal, A.Penerima, A.Keterangan,
    	A.Debet, SUM(isnull(B.Kredit,0)) Kredit, A.Perkiraan, isnull(A.KodeVls,'IDR') KodeVls, isnull(A.Kurs,1) Kurs,
            isnull(A.DebetD,0) DebetD, SUM(isnull(B.KreditD,0)) KreditD,
    	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas,
            A.Debet-SUM(isnull(B.Kredit,0)) Saldo,
            isnull(A.DebetD,0)-SUM(isnull(B.KreditD,0)) SaldoD
    From    dbBon A
    Left Outer Join dbBon B on B.NoBukti=A.NoBukti and B.Perkiraan=A.Perkiraan and B.Kredit<>0
    where   A.Perkiraan=@Perkiraan
            and A.Debet<>0
    Group By A.Devisi, A.NoBukti, A.NOURUT, A.Tanggal, A.Penerima, A.Keterangan,
    	A.Debet, A.Perkiraan, A.KodeVls, A.Kurs, A.DebetD,
    	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas
    Having  SUM(isnull(B.Kredit,0))<A.Debet
    Order by A.Tanggal, A.NoBukti",[
  "tahun" =>$periode->tahun , "bulan" => $periode->bulan,
  "perkiraan" => $perkiraan[0]->Perkiraan
]);



    $tempOutstanding = DB::connection('SML')->select("declare @Tahun int, @Bulan int, @Perkiraan varchar(20)

select @Tahun= :tahun , @Bulan= :bulan, @Perkiraan= :perkiraan

Select 	A.NoBukti+right('0000000000'+A.Perkiraan,10)+right('00000'+cast(A.Urut as varchar(5)),5) KeyNoBukti,
        A.Devisi, A.NoBukti, A.NOURUT, A.Tanggal, A.Penerima, A.Keterangan,
	A.Debet, A.Kredit, A.Perkiraan, isnull(A.KodeVls,'IDR') KodeVls, isnull(A.Kurs,1) Kurs, isnull(A.DebetD,0) DebetD, isnull(A.KreditD,0) KreditD,
	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas,
        A.Debet-A.Kredit Saldo, isnull(A.DebetD,0)-isnull(A.KreditD,0) SaldoD,nobukti+perkiraan nobuktiB
From dbBon A
where A.Perkiraan=@Perkiraan and year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
--and A.Debet<>0
Order by A.NoBukti",[
  "tahun" =>$periode->tahun , "bulan" => $periode->bulan,
  "perkiraan" => $perkiraan[0]->Perkiraan
]);



    return view('bonsementara' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan" => $tempPenerimaan,
      "akses" => $akses,
      "perkiraan" => $perkiraan,
    ]);

  }


  public function loadAll (Request $req) {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempPenerimaan = DB::connection('SML')->select("
    declare @Tahun int, @Bulan int, @Perkiraan varchar(20)

    select @Tahun= :tahun , @Bulan= :bulan , @Perkiraan= :perkiraan

    Select 	A.NoBukti+right('0000000000'+A.Perkiraan,10)+right('00000'+cast(A.Urut as varchar(5)),5) KeyNoBukti,
            A.Devisi, A.NoBukti, A.NOURUT, A.Tanggal, A.Penerima, A.Keterangan,
    	A.Debet, SUM(isnull(B.Kredit,0)) Kredit, A.Perkiraan, isnull(A.KodeVls,'IDR') KodeVls, isnull(A.Kurs,1) Kurs,
            isnull(A.DebetD,0) DebetD, SUM(isnull(B.KreditD,0)) KreditD,
    	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas,
            A.Debet-SUM(isnull(B.Kredit,0)) Saldo,
            isnull(A.DebetD,0)-SUM(isnull(B.KreditD,0)) SaldoD
    From    dbBon A
    Left Outer Join dbBon B on B.NoBukti=A.NoBukti and B.Perkiraan=A.Perkiraan and B.Kredit<>0
    where   A.Perkiraan=@Perkiraan
            and A.Debet<>0
    Group By A.Devisi, A.NoBukti, A.NOURUT, A.Tanggal, A.Penerima, A.Keterangan,
    	A.Debet, A.Perkiraan, A.KodeVls, A.Kurs, A.DebetD,
    	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas
    Having  SUM(isnull(B.Kredit,0))<A.Debet
    Order by A.Tanggal, A.NoBukti",[
  "tahun" =>$periode->tahun , "bulan" => $periode->bulan,
  "perkiraan" => $req->perkiraan
]);



    $tempOutstanding = DB::connection('SML')->select("declare @Tahun int, @Bulan int, @Perkiraan varchar(20)

select @Tahun= :tahun , @Bulan= :bulan, @Perkiraan= :perkiraan

Select 	A.NoBukti+right('0000000000'+A.Perkiraan,10)+right('00000'+cast(A.Urut as varchar(5)),5) KeyNoBukti,
        A.Devisi, A.NoBukti, A.NOURUT, A.Tanggal, A.Penerima, A.Keterangan,
	A.Debet, A.Kredit, A.Perkiraan, isnull(A.KodeVls,'IDR') KodeVls, isnull(A.Kurs,1) Kurs, isnull(A.DebetD,0) DebetD, isnull(A.KreditD,0) KreditD,
	A.TglInput, A.UserID, A.Urut, A.BuktiKas, A.UrutKas,
        A.Debet-A.Kredit Saldo, isnull(A.DebetD,0)-isnull(A.KreditD,0) SaldoD,nobukti+perkiraan nobuktiB
From dbBon A
where A.Perkiraan=@Perkiraan and year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
--and A.Debet<>0
Order by A.NoBukti",[
  "tahun" =>$periode->tahun , "bulan" => $periode->bulan,
  "perkiraan" => $req->perkiraan
]);
  return [
    "tempOutstanding" => $tempOutstanding,
    "tempPenerimaan" => $tempPenerimaan
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
