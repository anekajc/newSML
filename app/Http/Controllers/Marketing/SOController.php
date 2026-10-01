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
use App\Models\VwPPL;

// use App\Http\Controllers\NewMenuController;

class SOController extends Controller
{

  public function index(Request $req) {

    $kodemenu = '04101';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu , $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    // $username = \Auth::user()->username;

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(4);

    // $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 0)->where('pAgen', 1)->groupBy('NoBukti');
    $tempOutstanding = DB::connection("SML")->select("

              select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
    Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
    Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
    Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
    Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
    Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
	Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
              a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen

  from dbso a
Left Outer join dbSODet B on B.NoBukti=a.NoBukti
LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
left outer join [user] Mx on a.boffice=Mx.keynik
where
month(a.TANGGAL) = :bulan and year(a.TANGGAL) = :tahun " , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun] );

    $collection1 = collect($tempOutstanding)->groupBy('NOBUKTI');
    $tempOutstanding1 = [];
    foreach ($collection1 as $p) {
      // code...
      array_push($tempOutstanding1, $p);
    }

    $tempOutstanding2 = DB::connection("SML")->select("select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
    Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
    Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
    Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
    Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
    Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,

    Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock

    from dbso a
    Left Outer join dbSODet B on B.NoBukti=a.NoBukti
    LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
    Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
    Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
    Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
left outer join [user] Mx on a.boffice=Mx.keynik where MONTH(a.TANGGAL) = :bulan and YEAR(a.TANGGAL) = :tahun and A.Tipebayar = 0 and isnull(A.unblock, 0) = 0 and
Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) = 0" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

$listsattax = DB::connection("SML")->select("select * from dbsattax" );




$collection2 = collect($tempOutstanding2)->groupBy('NOBUKTI');
$tempOutstanding3 = [];
foreach ($collection2 as $p) {
  // code...
  array_push($tempOutstanding3, $p);
}

$tempOutstanding4 = DB::connection("SML")->select("select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
    Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
    Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
    Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
    Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
    Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,

    Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,
              case when A.tglunblock is null then '' else CONVERT(VARCHAR, a.tglunblock, 103) end tglunblock


    from dbso a
    Left Outer join dbSODet B on B.NoBukti=a.NoBukti
    LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
    Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
    Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
    Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
left outer join [user] Mx on a.boffice=Mx.keynik where MONTH(a.TANGGAL) = :bulan and YEAR(a.TANGGAL) = :tahun and (isnull(A.unblock, 0) = 1 or A.Tipebayar = 1) and
Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) = 0" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);



    $tempOutstanding6 = DB::connection("SML")->select("
    declare @Tahun int, @Bulan int,@pAgen Bit,@pJasa bit

    Select 	c.PPN,B.NOBUKTI,A.TANGGAL,B.KODEBRG,A.KODECUST,C.NAMACUSTSUPP,B.NAMABRG,
    /*(case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt2,0)when B.NOSAT=3 then Isnull(B.Qnt2,0) End)*/  B.QNT,
    D.QntSO,
    (case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt2,0)when B.NOSAT=3 then Isnull(B.Qnt2,0) End) -
    isnull(D.QntSO,0) Sisa,
    B.HARGA,a.NAMAPIC,
    FRANCO,A.DELIVERY,A.VALIDITAS,B.ID,B.Nosat


    From /*DBSPL..*/DBPENAWARANSODet B
    Left Outer Join /*DBSPL..*/DBPENAWARANSO A on A.id =B.IDMASTER
    left outer join /*DBSPL..*/DBCUSTSUPP C on A.KODECUST=C.KODECUSTSUPP
    Left outer join (select NOtawar,Uruttawar,
    SUM(case when NOSAT=1 then Isnull(Qnt,0) when NOSAT=2 then Isnull(Qnt2,0)when NOSAT=3 then Isnull(Qnt2,0) End) QntSO  ,
                        SUM(CASE WHEN NOSAT=1 THEN QNT ELSE QNT2 * ISI END) qNTSOX
    from /*DBSPL..*/DBSODET
    group by NOtawar,Uruttawar ) D on B.NOBUKTI=D.NOtawar and B.Urut=D.Uruttawar
    where b.QNT * B.ISI - isnull (D.qNTSOX,0)>0
    and B.kodebrg<>'-'  and isnull(B.IsVerf,0)=1 /* and isnull(A.isotorisasi1,0)=1  */
    and A.tanggal >= '05/01/2026'
    order by B.NoBukti
    " );




$collection3 = collect($tempOutstanding4)->groupBy('NOBUKTI');
$tempOutstanding5 = [];
foreach ($collection3 as $p) {
  // code...
  array_push($tempOutstanding5, $p);
}

$collection4 = collect($tempOutstanding6)->groupBy('NOBUKTI');
$tempOutstanding7 = [];
foreach ($collection4 as $p) {
  // code...
  array_push($tempOutstanding7, $p);
}


    return view('marketing.so' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      // "tempOutstanding" => $tempOutstanding,
      "tempOutstanding1" => $tempOutstanding1,
      // "tempOutstanding2" => $tempOutstanding2,
      "tempOutstanding3" => $tempOutstanding3,
      "tempOutstanding5" => $tempOutstanding5,
      "tempOutstanding7" => $tempOutstanding7,
      "level" => $akses->OL,
      "listBarangAll" => [] ,
      "listSattax" => $listsattax,
      "akses" => $akses
    ]);

  }

  public function loadSOFilter (Request $req) {
    $tempOutstanding1 = [];
    $queryTipeBayar = '';

    if ($req->tipebayar != 4) {
      $queryTipeBayar = ' and a.TIPEBAYAR = ' . $req->tipebayar;
    }
    // return $queryTipeBayar;
    // return ["tglawal" => $req->tglawal, "tglakhir" =>$req->tglakhir , $queryTipeBayar , $req->tipefilter, $req->needoto];

    if ($req->tipefilter == 1) {
      $tempOutstanding = DB::connection("SML")->select("



      declare @bulan Date,@tahun Date

      select
    @bulan = :tglawal , @tahun = :tglakhir

              select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
    Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
    Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
    Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
    Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
    Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
              a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen


  from dbso a
  Left Outer join dbSODet B on B.NoBukti=a.NoBukti
  LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
  Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
  Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
  Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
  left outer join [user] Mx on a.boffice=Mx.keynik
  where
  a.TANGGAL between @bulan and @tahun " . $queryTipeBayar , ["tglawal" => $req->tglawal, "tglakhir" =>$req->tglakhir ] );
  $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();


} else if ($req->tipefilter == 2) {
  // return 'asdadasa';
  $tempOutstanding = DB::connection("SML")->select("

  declare @bulan Date,@tahun Date

  select
  @bulan = :tglawal , @tahun = :tglakhir

          select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
  Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
  Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
  Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
  Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
  Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                   Case when A.IsOtorisasi2=1 then 1 else 0 end+
                    Case when A.IsOtorisasi3=1 then 1 else 0 end+
                    Case when A.IsOtorisasi4=1 then 1 else 0 end+
                    Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
               else 1
          end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
          a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen


  from dbso a
  Left Outer join dbSODet B on B.NoBukti=a.NoBukti
  LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
  Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
  Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
  Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
  left outer join [user] Mx on a.boffice=Mx.keynik
  where
  a.TANGGAL between @bulan and @tahun and
Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                   Case when A.IsOtorisasi2=1 then 1 else 0 end+
                    Case when A.IsOtorisasi3=1 then 1 else 0 end+
                    Case when A.IsOtorisasi4=1 then 1 else 0 end+
                    Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
               else 1
          end As Bit) = :needoto" , ["tglawal" => $req->tglawal , "tglakhir" =>$req->tglakhir , "needoto" => $req->needoto] );
          $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();


} else if ($req->tipefilter == 3) {
      $tempOutstanding = DB::connection("SML")->select("



      declare @bulan Date,@tahun Date

      select
    @bulan = :tglawal , @tahun = :tglakhir

              select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
    Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
    Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
    Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
    Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
    Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
              a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen


              from dbso a
               Left Outer join dbSODet B on B.NoBukti=a.NoBukti
               LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
               Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
               Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
               Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
               left outer join [user] Mx on a.boffice=Mx.keynik
               left outer join (select NoSo,UrutSo,SUM(QNT) Qntspb
            					from dbSPBDet
            					group by NoSo,UrutSo)
            					 Z on b.nobukti=z.NoSo and b.urut=z.UrutSo
  where a.TANGGAL between @bulan and @tahun
  and case when b.QNT = ISNULL(z.Qntspb,0) then 'F'
         when b.QNT - ISNULL(z.Qntspb,0) > 0 and ISNULL(z.Qntspb,0)<>0   then 'S'
         when  ISNULL(z.Qntspb,0)=0 then 'B'
          end  = :ketproses " . $queryTipeBayar , ["tglawal" => $req->tglawal , "tglakhir" =>$req->tglakhir , "ketproses" => $req->ketproses] );
              $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();


    } else if ($req->tipefilter == 4) {
      $tempOutstanding = DB::connection("SML")->select("



      declare @bulan Date,@tahun Date

      select
    @bulan = :tglawal , @tahun = :tglakhir

              select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
    Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
    Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
    Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
    Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
    Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
              a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen


              from dbso a
               Left Outer join dbSODet B on B.NoBukti=a.NoBukti
               LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
               Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
               Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
               Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
               left outer join [user] Mx on a.boffice=Mx.keynik
               left outer join (select NoSo,UrutSo,SUM(QNT) Qntspb
            					from dbSPBDet
            					group by NoSo,UrutSo)
            					 Z on b.nobukti=z.NoSo and b.urut=z.UrutSo
  where a.TANGGAL between @bulan and @tahun
 and case when ISNULL(B.QNTBATAL,0)>0 then 1 else 0 end = :ketclose " . $queryTipeBayar , ["tglawal" => $req->tglawal , "tglakhir" =>$req->tglakhir , "ketclose" => $req->ketclose] );
              $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();



    }



    return $tempOutstanding1;

   //   if ($req->filterso == 0) {
   //     $tempOutstanding = DB::connection("SML")->select("
   //
   //
   //
   //     declare @bulan Date,@tahun Date
   //
   //     select
   //   @bulan = :tglawal , @tahun = :tglakhir
   //
   //             select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
   //   Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
   //   Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
   //   Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
   //   Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
   //   Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
   // Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
   //                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
   //                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
   //                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
   //                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
   //                  else 1
   //             end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
   //             a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen
   //
   //
   // from dbso a
   // Left Outer join dbSODet B on B.NoBukti=a.NoBukti
   // LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
   // Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
   // Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
   // Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
   // left outer join [user] Mx on a.boffice=Mx.keynik
   // where
   // a.TANGGAL between @bulan and @tahun " , ["tglawal" => $req->tglawal, "tglakhir" =>$req->tglakhir ] );
   // $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();
   //
   //             return $tempOutstanding1;
   //
   //
   //   } else {
   //     $tempOutstanding = DB::connection("SML")->select("
   //
   //
   //
   //     declare @bulan Date,@tahun Date
   //
   //     select
   //   @bulan = :tglawal , @tahun = :tglakhir
   //
   //             select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
   //   Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
   //   Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
   //   Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
   //   Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
   //   Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
   // Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
   //                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
   //                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
   //                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
   //                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
   //                  else 1
   //             end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
   //             a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen
   //
   //
   // from dbso a
   // Left Outer join dbSODet B on B.NoBukti=a.NoBukti
   // LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
   // Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
   // Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
   // Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
   // left outer join [user] Mx on a.boffice=Mx.keynik
   // where
   // a.TANGGAL between @bulan and @tahun and case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end = :cbdneedopen and Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
   //                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
   //                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
   //                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
   //                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
   //                  else 1
   //             end As Bit) = :needoto" , ["tglawal" => $req->tglawal , "tglakhir" =>$req->tglakhir , "cbdneedopen" => $req->cbdneedopen , "needoto" => $req->needoto] );
   //             $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();
   //
   //             return $tempOutstanding1;
   //
   //   }


  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.Sp_CetakSO1 ?",
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

    $tempOutstanding1 = [];
    $queryTipeBayar = '';
    if ($req->tipebayar != 4) {
      $queryTipeBayar = ' and a.TIPEBAYAR = ' . $req->tipebayar;
    }

    if ($req->tipefilter == 1) {


      $tempOutstanding = DB::connection("SML")->select("
      declare @bulan Date,@tahun Date

            select
          @bulan = :tglawal , @tahun = :tglakhir

                    select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
          Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
          Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
          Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
          Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
          Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                             Case when A.IsOtorisasi2=1 then 1 else 0 end+
                              Case when A.IsOtorisasi3=1 then 1 else 0 end+
                              Case when A.IsOtorisasi4=1 then 1 else 0 end+
                              Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                         else 1
                    end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
                    a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen


        from dbso a
        Left Outer join dbSODet B on B.NoBukti=a.NoBukti
        LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
        Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
        Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
        Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
        left outer join [user] Mx on a.boffice=Mx.keynik
        where
        a.TANGGAL between @bulan and @tahun " . $queryTipeBayar , ["tglawal" => $req->tglawal, "tglakhir" =>$req->tglakhir ] );
  $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();


    } else if ($req->tipefilter == 2) {

      $tempOutstanding = DB::connection("SML")->select("



      declare @bulan Date, @tahun Date

      select
    @bulan = :tglawal , @tahun = :tglakhir

              select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
    Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
    Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
    Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
    Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
    Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
              a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen


  from dbso a
  Left Outer join dbSODet B on B.NoBukti=a.NoBukti
  LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
  Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
  Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
  Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
  left outer join [user] Mx on a.boffice=Mx.keynik
  where
   Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) = :needoto" , ["tglawal" => $req->tglawal , "tglakhir" =>$req->tglakhir , "needoto" => $req->needoto] );
              $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();


    } else if ($req->tipefilter == 3) {
      $tempOutstanding = DB::connection("SML")->select("



      declare @bulan Date,@tahun Date

      select
    @bulan = :tglawal , @tahun = :tglakhir

              select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
    Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
    Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
    Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
    Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
    Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
              a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen


  from dbso a
  Left Outer join dbSODet B on B.NoBukti=a.NoBukti
  LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
  Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
  Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
  Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
  left outer join [user] Mx on a.boffice=Mx.keynik
  where a.TANGGAL between @bulan and @tahun
  and case when b.QNT = ISNULL(z.Qntspb,0) then 'F'
         when b.QNT - ISNULL(z.Qntspb,0) > 0 and ISNULL(z.Qntspb,0)<>0   then 'S'
         when  ISNULL(z.Qntspb,0)=0 then 'B'
          end  = :ketproses " . $queryTipeBayar , ["tglawal" => $req->tglawal , "tglakhir" =>$req->tglakhir , "ketproses" => $req->ketproses] );
              $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();


    } else if ($req->tipefilter == 4) {
      $tempOutstanding = DB::connection("SML")->select("



      declare @bulan Date,@tahun Date

      select
    @bulan = :tglawal , @tahun = :tglakhir

              select a.* , m.NAMACUSTSUPP NAMACUSTSUPP , m1.Nama NAMASALES , m3.Nama NAMAPIC , mx. username  NAMABOFFICE,
    Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
    Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
    Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotDPP,
    Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotPPn,
    Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotNet,I.TotSubTotal,
  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi, A.nopesanan,A.unblock,A.userunblock,A.tglunblock,
              a.tipebayar,case when  a.TIPEBAYAR = 0 and isnull(A.unblock, 0) = 0 then 1 else 0 end cbdneedopen


  from dbso a
  Left Outer join dbSODet B on B.NoBukti=a.NoBukti
  LEFT outer join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
  Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
  Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
  Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
  left outer join [user] Mx on a.boffice=Mx.keynik
  where a.TANGGAL between @bulan and @tahun
 and case when ISNULL(B.QNTBATAL,0)>0 then 1 else 0 end = :ketclose " . $queryTipeBayar , ["tglawal" => $req->tglawal , "tglakhir" =>$req->tglakhir , "ketclose" => $req->ketclose] );
              $tempOutstanding1 = collect($tempOutstanding)->groupBy('NOBUKTI')->values();



    }

    // #tabel2 ("Sudah Otorisasi"-ish plain tab) and #tabel_oto ("Open CBD" plain tab) on
    // page1 used to only get this data once, server-rendered at page load (see index()
    // above, whose query this is copied from verbatim) -- loadAll() never refreshed them.
    // Restored here so the front-end's AJAX refresh (soloadall) can keep both in sync with
    // the rest of page1 too, matching purchaseOrder.blade.php's own loadAll() refreshing
    // its "Purchase Order" tab via loadTabelPO(). Periode-scoped like index(), not
    // tglawal/tglakhir-filtered like tempOutstanding1 above -- neither tab is wired to the
    // date-range filter modal in the UI.
    $tempOutstanding2 = DB::connection("SML")->select("
        select a.* ,Isnull(M6.PoCustomer, A.NoPesanan) as NoPesanan, m.NAMACUSTSUPP , m1.Nama NAMASALES ,
               m3.Nama NAMAPIC , mx.username NAMABOFFICE,

        Case when A.Kodevls='IDR' then B.SubTotalRp else B.SubTotal end Total,
        Case when A.Kodevls='IDR' then I.TotDiskonRp else I.TotDiskon end Diskon,
        Case when A.Kodevls='IDR' then I.TotDPPRp else I.TotDPP end TotDPP,
        Case when A.Kodevls='IDR' then I.TotPPnRp else I.TotPPn end TotPPn,
        Case when A.Kodevls='IDR' then I.TotNetRp else I.TotNet end TotNet,
        I.TotSubTotal,

        Cast(
            Case
                when
                    Case when A.IsOtorisasi1=1 then 1 else 0 end +
                    Case when A.IsOtorisasi2=1 then 1 else 0 end +
                    Case when A.IsOtorisasi3=1 then 1 else 0 end +
                    Case when A.IsOtorisasi4=1 then 1 else 0 end +
                    Case when A.IsOtorisasi5=1 then 1 else 0 end = A.MaxOL
                then 0 else 1
            end
        As Bit) NeedOtorisasi,

        A.nopesanan, A.unblock, A.userunblock,
 case when A.tglunblock is null then '' else CONVERT(VARCHAR, a.tglunblock, 103) end tglunblock


        from dbso a
        left join dbSODet B on B.NoBukti=a.NoBukti
        left join dbPOCustsupp M6 on A.IDPOCUst = M6.ID

        left join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
        left join vwRpDetSO I on I.NoBukti=A.NoBukti
        left join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
        left join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
        left join [user] Mx on a.boffice=Mx.keynik

        where MONTH(a.TANGGAL) = :bulan
        and YEAR(a.TANGGAL) = :tahun
        and A.Tipebayar = 0
        and ISNULL(A.unblock,0) = 0
        and Cast(
            Case
                when
                    Case when A.IsOtorisasi1=1 then 1 else 0 end +
                    Case when A.IsOtorisasi2=1 then 1 else 0 end +
                    Case when A.IsOtorisasi3=1 then 1 else 0 end +
                    Case when A.IsOtorisasi4=1 then 1 else 0 end +
                    Case when A.IsOtorisasi5=1 then 1 else 0 end = A.MaxOL
                then 0 else 1
            end
        As Bit) = 0
    ", ["bulan" => $periode->bulan, "tahun" => $periode->tahun]);

    $tempOutstanding3 = collect($tempOutstanding2)->groupBy('NOBUKTI')->values();


    $tempOutstanding4 = DB::connection("SML")->select("
        select a.* ,Isnull(M6.PoCustomer, A.NoPesanan) as NoPesanan, m.NAMACUSTSUPP , m1.Nama NAMASALES ,
               m3.Nama NAMAPIC , mx.username NAMABOFFICE,

        Case when A.Kodevls='IDR' then B.SubTotalRp else B.SubTotal end Total,
        Case when A.Kodevls='IDR' then I.TotDiskonRp else I.TotDiskon end Diskon,
        Case when A.Kodevls='IDR' then I.TotDPPRp else I.TotDPP end TotDPP,
        Case when A.Kodevls='IDR' then I.TotPPnRp else I.TotPPn end TotPPn,
        Case when A.Kodevls='IDR' then I.TotNetRp else I.TotNet end TotNet,
        I.TotSubTotal,

        Cast(
            Case
                when
                    Case when A.IsOtorisasi1=1 then 1 else 0 end +
                    Case when A.IsOtorisasi2=1 then 1 else 0 end +
                    Case when A.IsOtorisasi3=1 then 1 else 0 end +
                    Case when A.IsOtorisasi4=1 then 1 else 0 end +
                    Case when A.IsOtorisasi5=1 then 1 else 0 end = A.MaxOL
                then 0 else 1
            end
        As Bit) NeedOtorisasi,

        A.nopesanan, A.unblock, A.userunblock, A.tglunblock

        from dbso a
        left join dbSODet B on B.NoBukti=a.NoBukti
        left join dbPOCustsupp M6 on A.IDPOCUst = M6.ID

        left join DBCUSTSUPP M on M.KODECUSTSUPP = a.kodecust
        left join vwRpDetSO I on I.NoBukti=A.NoBukti
        left join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
        left join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
        left join [user] Mx on a.boffice=Mx.keynik

        where MONTH(a.TANGGAL) = :bulan
        and YEAR(a.TANGGAL) = :tahun
        and (ISNULL(A.unblock,0) = 1 OR A.Tipebayar = 1)
        and Cast(
            Case
                when
                    Case when A.IsOtorisasi1=1 then 1 else 0 end +
                    Case when A.IsOtorisasi2=1 then 1 else 0 end +
                    Case when A.IsOtorisasi3=1 then 1 else 0 end +
                    Case when A.IsOtorisasi4=1 then 1 else 0 end +
                    Case when A.IsOtorisasi5=1 then 1 else 0 end = A.MaxOL
                then 0 else 1
            end
        As Bit) = 0
    ", ["bulan" => $periode->bulan, "tahun" => $periode->tahun]);

    $tempOutstanding5 = collect($tempOutstanding4)->groupBy('NOBUKTI')->values();

    $tempOutstanding6 = DB::connection("SML")->select("
    declare @Tahun int, @Bulan int,@pAgen Bit,@pJasa bit



    Select 	c.PPN,B.NOBUKTI,A.TANGGAL,B.KODEBRG,A.KODECUST,C.NAMACUSTSUPP,B.NAMABRG,
    /*(case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt2,0)when B.NOSAT=3 then Isnull(B.Qnt2,0) End)*/  B.QNT,
    D.QntSO,
    (case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt2,0)when B.NOSAT=3 then Isnull(B.Qnt2,0) End) -
    isnull(D.QntSO,0) Sisa,
    B.HARGA,a.NAMAPIC,
    FRANCO,A.DELIVERY,A.VALIDITAS,B.ID,B.Nosat


    From /*DBSPL..*/DBPENAWARANSODet B
    Left Outer Join /*DBSPL..*/DBPENAWARANSO A on A.id =B.IDMASTER
    left outer join /*DBSPL..*/DBCUSTSUPP C on A.KODECUST=C.KODECUSTSUPP
    Left outer join (select NOtawar,Uruttawar,
    SUM(case when NOSAT=1 then Isnull(Qnt,0) when NOSAT=2 then Isnull(Qnt2,0)when NOSAT=3 then Isnull(Qnt2,0) End) QntSO  ,
                        SUM(CASE WHEN NOSAT=1 THEN QNT ELSE QNT2 * ISI END) qNTSOX
    from /*DBSPL..*/DBSODET
    group by NOtawar,Uruttawar ) D on B.NOBUKTI=D.NOtawar and B.Urut=D.Uruttawar
    where b.QNT * B.ISI - isnull (D.qNTSOX,0)>0
    and B.kodebrg<>'-'  and isnull(B.IsVerf,0)=1 /* and isnull(A.isotorisasi1,0)=1  */
    and A.tanggal >= '05/01/2026'
    order by B.NoBukti
    " );
    $tempOutstanding7 = collect($tempOutstanding6)->groupBy('NOBUKTI')->values();

    return [
        // "tempOutstanding" => $tempOutstanding,
        "tempOutstanding1" => $tempOutstanding1,
        // "tempOutstanding2" => $tempOutstanding2,
        "tempOutstanding3" => $tempOutstanding3,
        "tempOutstanding5" => $tempOutstanding5,

        "tempOutstanding7" => $tempOutstanding7
    ];
}


public function listNoPoTambahSO (Request $req) {

  $listData = DB::connection('SML')->select("

select a.* , b.namacustsupp
    from dbpocustsupp a
    left outer join dbcustsupp b on a.kodecustsupp = b.kodecustsupp
     left outer join DBSO c on a.POCustomer=c.NoPesanan and a.KodeCustSupp=c.KODECUST
  where    isnull(IsClose,0)=0  and isnull(A.pGanti,0)=0 and c.NoPesanan is  null
  /*isnull(a.id, 0) not in(select isnull(idpocust, 0) from dbso)*/



 and a.KodeCustSupp = :kodecustsupp",
  ["kodecustsupp" => $req->kodecustsupp]);
  return $listData;
}

public function getDetailTambahSOAll (Request $req) {
  $nobukti = $req->nobukti;


  $username = \Auth::user()->username;


  $list = DB::connection('SML')->select("
  declare
  @IDUser varchar(30),
  @NoBukti varchar(30),
  @Tahun int,
  @Bulan int,
  @Trans Varchar(5),
  @TGL dateTime=null

  Select @IDUser= :username ,@NoBukti= '' ,@Tahun=2026,@Bulan=5,@Trans='SO'


     select	@IDUser iduser, cast(0 as bit) IsTerima, A.NoBukti, A.NoBukti+'-'+right('00000000'+cast(min(A.Urut) as varchar(8)),8) KeyNoBukti,

     A.URUT,
     A.KodeBrg, Convert(Varchar(100),br.NAMABRG) NamaBrg, sum(isnull(A.Qnt,0)) Qnt, A.NoSat,
    case when A.NOSAT=1 then Br.SAT1 when A.nosat=2 then Br.SAT2 when A.NOSAT=3 then Br.SAT3 end Satuan,
    case when A.NOSAT=1 then 1  when A.nosat=2 then Br.ISI2 when A.Nosat=3 then Br.ISI3 end Isi,
     sum(A.QntOut) QntSisa, 0 CollyTerima, sum(A.QntOut) QntTerima,
    @Trans Trans,isnull(a.KodeGdg,'') kodegdg,m2.KODECUST,m1.NAMACUSTSUPP , m2.CATATAN

    from (

      select A.NoBukti, B.Urut, B.KODEBRG, B.NOSAT,
      case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt,0) when B.NOSAT=3 then Isnull(B.Qnt,0) End Qnt,
      case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt,0) when B.NOSAT=3 then Isnull(B.Qnt,0) End QntOut,
      A.KodeGdg
      from DBPENAWARANSO A, DBPENAWARANSODET B
      where A.NOBUKTI=B.NOBUKTI AND b.KODEBRG<>'-'



      union all

      select B.Notawar,B.Uruttawar URUT, B.KODEBRG, B.NOSAT, 0.00 Qnt,
          case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt2,0)when B.NOSAT=3 then Isnull(B.Qnt2,0) End QntOut
      ,
      A.KODeGDG
      from dbso A, DBSODET B, DBPENAWARANSODET C ,DBPENAWARANSO D
      where A.NOBUKTI=B.Nobukti  AND B.NOTAWAR=C.NOBUKTI AND B.URUTTAWAR=C.URUT AND C.NOBUKTI=D.NOBUKTI --AND B.NOtawar=@NoBukti

    ) A
    left outer join DBBARANG Br on Br.KODEBRG=A.KODEBRG
    left outer join DBPENAWARANSO m2 on a.NOBUKTI = m2.NOBUKTI
    left outer join DBCUSTSUPP m1 on m2.KODECUST=m1.KODECUSTSUPP
    where m1.KODECUSTSUPP = :kodecustsupp
    group by A.NOBUKTI,  A.KODEBRG, br.NAMABRG, A.NOSAT, Br.SAT1, Br.SAT2, Br.ISI1, Br.ISI2,
    Br.ISI3,br.SAT3,isnull(A.KodeGdg,''),a.urut,m2.KODECUST,m1.NAMACUSTSUPP, m2.CATATAN
    HAVING sum(A.QntOut)<>0

  order by URUT
  "  , ["username" => $username , "kodecustsupp" => $req->kodecust] ) ;



  return $list;
}


public function getDetailTambahSO (Request $req) {
  $nobukti = $req->nobukti;
  // return "furuaaabb";

  $username = \Auth::user()->username;


  $list = DB::connection('SML')->select("
  declare
  @IDUser varchar(30),
  @NoBukti varchar(30),
  @Tahun int,
  @Bulan int,
  @Trans Varchar(5),
  @TGL dateTime=null

  Select @IDUser= :username ,@NoBukti= :nobukti ,@Tahun=2026,@Bulan=5,@Trans='SO'


    select	@IDUser iduser, cast(0 as bit) IsTerima, A.NoBukti, A.NoBukti+'-'+right('00000000'+cast(min(A.Urut) as varchar(8)),8) KeyNoBukti,

     A.URUT,
     A.KodeBrg, Convert(Varchar(100),br.NAMABRG) NamaBrg, sum(isnull(A.Qnt,0)) Qnt, A.NoSat,
    case when A.NOSAT=1 then Br.SAT1 when A.nosat=2 then Br.SAT2 when A.NOSAT=3 then Br.SAT3 end Satuan,
    case when A.NOSAT=1 then 1  when A.nosat=2 then Br.ISI2 when A.Nosat=3 then Br.ISI3 end Isi,
     sum(A.QntOut) QntSisa, 0 CollyTerima, sum(A.QntOut) QntTerima,
    @Trans Trans,isnull(KodeGdg,'') kodegdg,a.KODECUST,m1.NAMACUSTSUPP

    from (

      select A.NoBukti, B.Urut, B.KODEBRG, B.NOSAT,
      case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt,0) when B.NOSAT=3 then Isnull(B.Qnt,0) End Qnt,
      case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt,0) when B.NOSAT=3 then Isnull(B.Qnt,0) End QntOut,
      A.KodeGdg,A.KODECUST
      from DBPENAWARANSO A, DBPENAWARANSODET B
      where A.NOBUKTI=B.NOBUKTI AND b.KODEBRG<>'-'
      AND A.NOBUKTI= @NoBukti


      union all

      select B.Notawar,B.Uruttawar URUT, B.KODEBRG, B.NOSAT, 0.00 Qnt,
          case when B.NOSAT=1 then Isnull(B.Qnt,0) when B.NOSAT=2 then Isnull(B.Qnt2,0)when B.NOSAT=3 then Isnull(B.Qnt2,0) End QntOut
      ,
      A.KODeGDG,A.KODECUST
      from dbso A, DBSODET B, DBPENAWARANSODET C ,DBPENAWARANSO D
      where A.NOBUKTI=B.Nobukti  AND B.NOTAWAR=C.NOBUKTI AND B.URUTTAWAR=C.URUT AND C.NOBUKTI=D.NOBUKTI AND B.NOtawar=@NoBukti

    ) A
    left outer join DBBARANG Br on Br.KODEBRG=A.KODEBRG
    left outer join DBCUSTSUPP m1 on a.KODECUST=m1.KODECUSTSUPP
    group by A.NOBUKTI,  A.KODEBRG, br.NAMABRG, A.NOSAT, Br.SAT1, Br.SAT2, Br.ISI1, Br.ISI2,
    Br.ISI3,br.SAT3,isnull(A.KodeGdg,''),a.urut,A.KODECUST,m1.NAMACUSTSUPP
    HAVING sum(A.QntOut)<>0

  order by URUT
  "  , ["username" => $username , "nobukti" => $req->nobukti] ) ;



  return $list;
}


public function spAddTambahSOAll (Request $req) {

  // $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
$date = $req->input('tanggal');
  $username = \Auth::user()->username;
  $data = $req->input('tempData');
    $values = [];

    // return $data;
  // if ($req->ppn == 1) {
  //   $inisial = DB::connection("SML")->select('select SO from DBNOMOR');
  //
  //   $values = [
  //       $inisial[0]->SO,
  //       $periode->bulan,
  //       $periode->tahun,
  //       'tes',
  //       // $periode
  //       // $periode
  //   ];
  //
  // } else {
  // // return 'asd';
  // $values = [
  //       'SON',
  //       $periode->bulan,
  //       $periode->tahun,
  //       'tes2',
  //       // $periode
  //       // $periode
  //   ];
  // }
  //
  // $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
  // return ["asd" => $noBukti[0]->Nobukti];
    // return [$data,$date,$nourut,$nosj,$norspb,$nooutso,$nopolkendaraan,$expedisi];
  // DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);
  foreach ($data as $d) {
    // return 4;

    // $values = [$username, $nosj,'2023','4','SPB',$d['inputQntTerima'],$d['URUT']];
    $values1 = [
$req->nobukti,//           @Nobukti varchar(25),
$req->nourut,// @Nourut varchar(10),
$d['NoBukti'],// @Nob varchar(25),
$username,// @IdUser varchar(20),
1,// @MaxOL integer,
$req->tanggal,// @Tanggal DateTime,
$req->nopo ? $req->nopo : '',// @POcust varchar(50)='',
$req->kodecust,// @kodeCust varchar(50)='',
$req->ppn ? $req->ppn : 0,// @TipePPN smallint,
$req->idpo ? $req->idpo : 0,// @IDPOCUST Int=0
$d['URUT']
    ];

    DB::connection("SML")->statement('exec SP_InsertPSOweb ?,?,?,?,?,?,?,?,?,?,?',$values1);
  }
   DB::connection('SML')->update('exec Sp_UpdateSO ?', [$req->nobukti]);


return 1;
}

public function spAddTambahSO (Request $req) {

  // $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
$date = $req->input('tanggal');
  $username = \Auth::user()->username;
  $data = $req->input('tempData');
    $values = [];
  if ($req->ppn == 1) {
    $inisial = DB::connection("SML")->select('select SO from DBNOMOR');

    $values = [
        $inisial[0]->SO,
        $periode->bulan,
        $periode->tahun,
        'tes',
        // $periode
        // $periode
    ];

  } else {
  // return 'asd';
  $values = [
        'SON',
        $periode->bulan,
        $periode->tahun,
        'tes2',
        // $periode
        // $periode
    ];
  }

  $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
  // return ["asd" => $noBukti[0]->Nobukti];
    // return [$data,$date,$nourut,$nosj,$norspb,$nooutso,$nopolkendaraan,$expedisi];
  // DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);
  foreach ($data as $d) {
    // return 4;

    // $values = [$username, $nosj,'2023','4','SPB',$d['inputQntTerima'],$d['URUT']];
    $values1 = [
$noBukti[0]->Nobukti,//           @Nobukti varchar(25),
$noBukti[0]->Nourut,// @Nourut varchar(10),
$d['NoBukti'],// @Nob varchar(25),
$username,// @IdUser varchar(20),
1,// @MaxOL integer,
$req->tanggal,// @Tanggal DateTime,
$req->nopo ? $req->nopo : '',// @POcust varchar(50)='',
$req->kodecust,// @kodeCust varchar(50)='',
$req->ppn ? $req->ppn : 0,// @TipePPN smallint,
$req->idpo ? $req->idpo : 0,// @IDPOCUST Int=0
$d['URUT']
    ];

    DB::connection("SML")->statement('exec SP_InsertPSOweb ?,?,?,?,?,?,?,?,?,?,?',$values1);
  }
   DB::connection('SML')->update('exec Sp_UpdateSO ?', [$noBukti[0]->Nobukti]);


return $noBukti[0]->Nobukti;
}

  public function cekOtorisasi (Request $req) {


    $res = DB::connection('SML')->select("select isOtorisasi1 from dbso where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function onChangeHeader (Request $req) {
    $query = 'update dbso set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }


    public function cekHargaOto (Request $req) {

      // return 1;
      // $listData = $req->tempData ? $req->tempData : [] ;
      // $tempArray = [];
      //
      // foreach ($listData as $d)  {
        // $xso = '';
        // if ($d['NOSO'] != '-') {
        //   $xso = $d['NOSO'];
        // }
        // return ["noso" => $d['NOSO'], "kodebrg" => $d['KodeBrg'] ,"nopo" => $d['NoBukti']];
      $x = DB::connection('SML')->select("declare @noSO varchaR(30)--,@KODEBRG VARCHAR(30)
select @noSO= :noso




SELECT XTABLE.kodebrg ,

XTABLE.XBeli + (XTABLE.XBeli * 0.1 )  hrgminso,
XTABLE.XBeli + (XTABLE.XBeli * 1.00 ) hrgmaxso,
XTABLE.xhrgpo
, case when XTABLE.xhrgpo < XTABLE.XBeli + (XTABLE.XBeli * 0.1 ) then 'Margin Kurang dari Harga Minimal'
  when XTABLE.xhrgpo >= XTABLE.XBeli + (XTABLE.XBeli * 1.00 ) then 'Margin Lebih dari 100%'
else
'lanjut' End Ket
 FROM (


 select AA.kodebrg,AA.Harga - AA.DiscRp1 Hrg,B.tanggal,AA.nosat,
 AA.harga * AA.Kurs harga,
 (AA.harga * AA.Kurs ) - (AA.DiscTot * AA.kurs) -
(case when AA.PPN=2 then  (AA.harga * AA.Kurs ) * 0.1 else 0 end )XHrgPO,

 (select top 1

 case when aa.NOSAT=1 then
      case when A.NOSAT=1 then A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
            when A.NOSAT=2 then (A.HRGNETTO /C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO/C.ISI2) * 0.10 else 0 end))
            when A.NOSAT=3 THEN (A.HRGNETTO /C.ISI3) - ((case when a.PPN=2 then (A.HRGNETTO/C.ISI3) * 0.10 else 0 end)) END * A.KURS

     when aa.NOSAt=2 then
       CASE WHEN A.NOSAT=2 THEN A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
            WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI2) * 0.10 else 0 end))
            WHEN A.NOSAT=3 THEN (((A.HRGNETTO / C.ISI3)*C.ISI2)- ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI3)*C.ISI2) * 0.10 else 0 end))) END * A.KURS

     when aa.NOSAt=3 then
       CASE WHEN A.NOSAT=3 THEN A.HRGNETTO - ((case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end))
            WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI3)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI3) * 0.10 else 0 end))
            WHEN A.NOSAT=2 THEN (((A.HRGNETTO/ C.ISI2)*c.ISI3) - ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI2)*c.ISI3) * 0.10 else 0 end))) eND * A.KURS
End
from DBBELIDET A
Left OUter join DBBELI b on A.nobukti=B.NOBUKTI
Left Outer join DBBARANG C on A.KODEBRG=C.KODEBRG
Where B.TANGGAL <=B.TANGGAL and A.KODEBRG=AA.KODEBRG  and A.kodegdg<>'G06'
order by B.TANGGAL Desc
   ) XBeli


 from DbSODET AA
left outer join dbSo B on AA.NOBUKTI=B.nobukti
where AA.nobukti = @noSO and AA.iscetakkitir=0
  ) XTABLE

      ", ["noso" => $req->nobukti ]);
      // if ($x) {
      //
      //   array_push($tempArray, $x);
      // }
      //
      // }
      return $x;


    }



  public function updateOtorisasi (Request $req) {
    $username = \Auth::user()->username;
     $maxOL = DB::connection('SML')->select("select * from dbmenu where href ='so'");
    $cekOto = DB::connection('SML')->select("
select b.*,c.KodeJab,isnull(d.PlafonOtoSO , 0) PlafonOtoPO,
       case when B.IsOtorisasi1=1 then 1
   when B.IsOtorisasi2=1 then 2
   when B.IsOtorisasi3=1 then 3
   when B.IsOtorisasi4=1 then 4
   when B.IsOtorisasi5=1 then 5 end leveloto,e.nnet,e.pblacklist
       from dbmenu a
    join dbflmenu b on a.kodemenu = b.l1
    join DBFLPASS c on b.USERID=c.USERID
    join DBJABATAN d on c.KodeJab=d.KODEJAB
    left outer join (select a.nobukti,SUM(a.nnet) nnet,isnull(c.pblacklist,0) pblacklist
           from DBSODET A
           left outer join dbSo b on a.noBukti=b.noBukti
           left outer join DBCustSUpp c on b.kodecust=c.kodecustsupp
           where a.NOBUKTI= :nobukti
           group by a.nobukti,pblacklist
           ) E on 1=1
    where a.href ='so' and b.USERID= :username ", ["nobukti" => $req->nobukti , "username" => $username]);
    $tanggal = date('Y-m-d H:i:s');
  // return ["cekoto" => $cekOto ,
// "blacklist" => $cekOto[0]->pblacklist, "nnet" => (int)$cekOto[0]->nnet , "plafon" => (int)$cekOto[0]->PlafonOtoPO ];
     if($cekOto[0]->pblacklist == 1) {

        return 9;
     }


    if ( (int)$cekOto[0]->nnet > (int)$cekOto[0]->PlafonOtoPO ) {
      if($cekOto[0]->leveloto == 1) {

        return 2;
      } else {
        $res = DB::connection('SML')->update("update dbso set isOtorisasi1 = 1, maxol = :maxol, OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username ,"maxol" => $maxOL[0]->OL , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
         $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','SO',$req->nobukti,'',0,'DBSO');
      }

    } else {
      $res = DB::connection('SML')->update("update dbso set isOtorisasi1 = 1, maxol = :maxol , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "maxol" => $maxOL[0]->OL , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
       $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','SO',$req->nobukti,'',0,'DBSO');
    }



return 1;

  }

  public function updateCBD (Request $req) {

    $username = \Auth::user()->username;

    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbso set unblock = 1 , userunblock= :username , tglunblock = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    return 1;
  }

  public function updateBatalOtorisasi (Request $req) {
    $username = \Auth::user()->username;
    $maxOL = DB::connection('SML')->select("select * from dbmenu where href = 'purchaseorder'");

    $cekOto = DB::connection('SML')->select("
    select b.*,c.KodeJab,isnull(d.PlafonOtoSO , 0) PlafonOtoPO,
           case when B.IsOtorisasi1=1 then 1
    	 when B.IsOtorisasi2=1 then 2
    	 when B.IsOtorisasi3=1 then 3
    	 when B.IsOtorisasi4=1 then 4
    	 when B.IsOtorisasi5=1 then 5 end leveloto,e.nnet
           from dbmenu a
        join dbflmenu b on a.kodemenu = b.l1
        join DBFLPASS c on b.USERID=c.USERID
        join DBJABATAN d on c.KodeJab=d.KODEJAB
        left outer join (select nobukti,SUM(nnet) nnet from DBPODET
    					 where NOBUKTI= :nobukti
    					 group by nobukti
    					 ) E on 1=1
        where a.href ='purchaseorder' and b.USERID= :username", ["nobukti" => $req->nobukti , "username" => $username]);
    $tanggal = date('Y-m-d H:i:s');
    if ( $cekOto[0]->nnet > $cekOto[0]->PlafonOtoPO ) {
      if($cekOto[0]->leveloto == 1) {

        return 0;
      } else {
        $res = DB::connection('SML')->update("update dbso set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", ["nobukti" => $req->nobukti]);

         $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','SO',$req->nobukti,'',0,'DBSO');

      }

    } else {
      $res = DB::connection('SML')->update("update dbso set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);

       $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','SO',$req->nobukti,'',0,'DBSO');


    }



  // $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto2','PO',$req->nobukti,'',0,'DBPO');
    return 1;
  }


  //   public function updateBatalOtorisasi (Request $req) {
  //   $tanggal = date('Y-m-d H:i:s');
  //   $res = DB::connection('SML')->update("update dbso set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
  //
  //   $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','SO',$req->nobukti,$req->pket,0,'DBSO');
  //
  //   return $res;
  // }

  public function onChangeHeaderSP (Request $req) {
    $query = 'update dbso set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);

    $res2 = DB::connection('SML')->select('exec Sp_UpdateSO ?', [$req->bukti]);

    return $res;

  }

  public function spUpdateSO (Request $req) {
    $res = DB::connection('SML')->update('exec Sp_UpdateSO ?', [$req->nobukti]);



    return $res;
  }



  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    if ($req->ppn == 1) {
      $inisial = DB::connection("SML")->select('select SO from DBNOMOR');

      $values = [
          $inisial[0]->SO,
          $periode->bulan,
          $periode->tahun,
          $username,
          // $periode
          // $periode
      ];

    } else {
      $values = [
          'SON',
          $periode->bulan,
          $periode->tahun,
          $username,
          // $periode
          // $periode
      ];
    }

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listPelanggan (Request $req) {

    $listData = DB::connection('SML')->select("select
    A.kodecustsupp,
    A.namacustsupp,
    A.alamat1,
    A.PPN,
    A.HARI,
    A.KodeSls,
    A.pBlacklist,
    B.Nama as NamaSales,
    A.BOffice,
    C.Nama as NamaBackOffice
from DBCUSTSUPP A
left join vwboffice B on A.KodeSls = B.boffice
left join vwboffice C on A.BOffice = C.boffice
where A.JENIS = 1 and A.IsAktif = 1");
    return $listData;
  }

  public function listRefPR (Request $req) {
    // return $req->kodecustsupp;
    $listData = DB::connection('SML')->select("

select A.nobukti,a.tanggal,a.refPR from (
       select A.NOBUKTI,C.TANGGAL,C.REFPR,a.KODEBRG,
       case when NOSAT=1 then sum(a.QNT)-ISNULL(b.QNT,0)  -ISNULL(X.QNTRSAMPLE,0) -ISNULL(Z.QNTBEBAN,0)
            when NOSAT=2 then sum(a.QNT2)-ISNULL(b.QNT2,0) -ISNULL(X.QNT2RSAMPLE,0) -ISNULL(Z.QNT2BEBAN,0)
           when NOSAT=3 then sum(a.QNT2)-ISNULL(b.QNT2,0) -ISNULL(X.QNT2RSAMPLE,0) -ISNULL(Z.QNT2BEBAN,0)  end Sisa
        from DBPRSAMPLEDET A
         left outer join DBPRSAMPLE C on A.NOBUKTI= c.NOBUKTI
     left outer join DBCustSUpp CX on C.KodeCUstSUpp=Cx.KodecustSUPP
        Left OUTER JOIN (select refpr,kodebrg,
                                         sum(qnt- isnull(Qntbatal,0)) Qnt,sum(QNT2- isnull(Qntbatal,0)) Qnt2
                                         from DBSOdet group by refpr,kodebrg) B ON C.REFPR=B.REFPR  and a.KODEBRG=b.KODEBRG

     LEFT OUTER JOIN (SELECT A.KODEBRG,SUM(A.QNT) QNTRSAMPLE,SUM(A.QNT2) QNT2RSAMPLE ,B.NOPRSAMPLE
    							    FROM DBRSERAHSAMPLEDET A
    								LEFT OUTER JOIN DBSERAHSAMPLEDET B ON A.NOSERAHSAMPLE=B.NOBUKTI AND A.URUTSERAHSAMPLE=B.URUT
    								GROUP BY A.KODEBRG,B.NOPRSAMPLE
    								)X ON A.NOBUKTI=X.NOPRSAMPLE AND A.KODEBRG=X.KODEBRG
     LEFT OUTER JOIN (SELECT A.NORSERAHSAMPLE,A.KODEBRG,SUM(A.QNT) QNTBEBAN,SUM(A.QNT2) QNT2BEBAN ,B.NOPRSAMPLE
    							FROM DBBEBANSAMPLEDET A
    							LEFT OUTER JOIN DBSERAHSAMPLEDET B ON A.NOrSERAHSAMPLE=B.NOBUKTI AND A.URUTrSERAHSAMPLE=B.URUT
    							GROUP BY A.NORSERAHSAMPLE,A.KODEBRG,B.NOPRSAMPLE
    							) Z  ON A.NOBUKTI=Z.NOPRSAMPLE AND A.KODEBRG=Z.KODEBRG
        where
         c.RefPR<>'-'   and     Cx.Agent=(select Agent from DBCUSTSUPP where KODECUSTSUPP= :kodecustsupp )
      group by  A.NOBUKTI,C.TANGGAL,C.REFPR,a.KODEBRG,b.Qnt,A.NOSAT,b.Qnt2,ISNULL(X.QNT2RSAMPLE,0),ISNULL(X.QNTRSAMPLE,0),ISNULL(Z.QNTBEBAN,0),ISNULL(Z.QNT2BEBAN,0)
     having case when NOSAT=1 then sum(a.QNT)-ISNULL(b.QNT,0) -ISNULL(X.QNTRSAMPLE,0) -ISNULL(Z.QNTBEBAN,0)
                   when NOSAT=2 then sum(a.QNT2)-ISNULL(b.QNT2,0) -ISNULL(X.QNT2RSAMPLE,0) -ISNULL(Z.QNT2BEBAN,0)
                   when NOSAT=3 then sum(a.QNT2)-ISNULL(b.QNT2,0) -ISNULL(X.QNT2RSAMPLE,0) -ISNULL(Z.QNT2BEBAN,0)  end>0.00
       )A     group by A.nobukti,a.tanggal,a.refPR
      ",
       ["kodecustsupp" => $req->kodecustsupp]);
          return $listData;
  }

  public function listNoPo (Request $req) {

    $listData = DB::connection('SML')->select("



select a.* , b.namacustsupp
    from dbpocustsupp a
    left outer join dbcustsupp b on a.kodecustsupp = b.kodecustsupp
     left outer join DBSO c on a.POCustomer=c.NoPesanan and a.KodeCustSupp=c.KODECUST
  where    isnull(IsClose,0)=0  and isnull(A.pGanti,0)=0 and c.NoPesanan is  null
  /*isnull(a.id, 0) not in(select isnull(idpocust, 0) from dbso)*/



 and a.KodeCustSupp = :kodecustsupp",
  ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listNoPenyerahan (Request $req) {

    $listData = DB::connection('SML')->select("
    select a.NOBUKTI,c.NAMABRG,sum(a.Qnt) - ISNULL(B.QntSample,0) SisaSample ,a.GDGTUJUAN
                          from DBSERAHSAMPLEDET a
                          left outer join  (select NOserah,kodebrg,SUM(Qnt- (isnull(Qntbatal,0) * isi)) QntSample
                                                            from DBSODET
                                                            group by NOserah,kodebrg
                                                            ) b on a.NOBUKTI=b.NOserah and a.kodebrg=b.kodebrg
                          Left Outer join DBBARANG c on a.KODEBRG=c.KODEBRG
                         Left Outer JOin DBSERAHSAMPLE D on A.nobukti= +
                         D.Nobukti
                          left outer join DBCUSTSUPP E on D.KODECUSTSUPP=E.KODECUSTSUPP
                          where  E.Agent=(select Agent from DBCUSTSUPP where KODECUSTSUPP=:kodecustsupp)
                         and A.NoPRSample= :refpr
                          group by  a.NOBUKTI,c.NAMABRG,b.QntSample ,A.GDGTUJUAN  having  +
                        sum(a.Qnt) - ISNULL(B.QntSample,0)>0
                         order by a.NOBUKTI,c.NAMABRG
",
  ["kodecustsupp" => $req->kodecustsupp,"refpr" => $req->refpr]);
    return $listData;
  }

  public function listBarangRefPR (Request $req ) {

    // noreferensi (RefPR) sekarang opsional -- kalau user cuma isi No Penyerahan
    // tanpa RefPR, tetap dicari berdasarkan No Penyerahan saja (RefPR check
    // dilewati). Pakai positional ? (bukan :noreferensi/:nopenyerahan) karena
    // noreferensi dipakai 2x di query ini dan sqlsrv/ODBC driver tidak
    // mendukung reuse named placeholder yang sama lebih dari sekali.
    $noreferensi = $req->noreferensi ?: '';
    $listData = DB::connection('SML')->select("
select SS.NOBUKTI,C.TANGGAL,C.REFPR,a.KODEBRG,D.namaBrg,A.SAT_1  ,a.SAT_2  ,
   case when A.NOSAT=1 then SUM(ISNULL(SS.QNT,0))-ISNULL(b.QNT,0)
        when A.NOSAT=2 then SUM(ISNULL(SS.QNT2,0))-ISNULL(b.QNT2,0)
        when A.NOSAT=3 then SUM(ISNULL(SS.QNT2,0))-ISNULL(b.QNT2,0) end Sisa
 ,D.pPPN,Isnull(D.QntMin,0) QntMin,D.isi2
 , sum(ss.QNT)-ISNULL(b.QNT,0)  -ISNULL(Z.QNTBEBAN,0) Sisa1
 , (sum(ss.QNT)-ISNULL(b.QNT,0)-ISNULL(Z.QNTBEBAN,0))/A.ISI Sisa2  ,h.namamerk
    from DBPRSAMPLEDET A
     left outer join DBPRSAMPLE C on A.NOBUKTI=c.NOBUKTI
 Left OUTER JOIN (select A.noprsample,A.urutprsample, nobukti,
 sum(A.qnt)- ISNULL(QNTRSAMPLE,0) Qnt,sum(A.QNT2) - ISNULL(QNT2RSAMPLE,0) Qnt2
 from dbserahsampledet A
 LEFT OUTER JOIN (SELECT A.KODEBRG,SUM(A.QNT) QNTRSAMPLE,SUM(A.QNT2) QNT2RSAMPLE ,A.NoSerahsample
							  FROM DBRSERAHSAMPLEDET A
							  GROUP BY A.KODEBRG,A.NoSerahsample
								)X ON A.NOBUKTI=X.NoSerahsample AND A.KODEBRG=X.KODEBRG
 where nobukti = ?
  group by noprsample,urutprsample,nobukti,ISNULL(QNTRSAMPLE,0),ISNULL(QNT2RSAMPLE,0)
 ) SS ON A.NOBUKTI=SS.NOPRSAMPLE AND A.URUT=SS.URUTPRSAMPLE

    Left OUTER JOIN (select noserah,kodebrg,
                                     sum(qnt- isnull(Qntbatal,0)) Qnt,sum(QNT2- isnull(Qntbatal,0)) Qnt2
                                     from DBSOdet group by noserah,kodebrg) B ON ss.nobukti=B.noserah AND A.KODEBRG=B.KODEBRG
 LEFT OUTER JOIN (SELECT A.KODEBRG,SUM(A.QNT) QNTRSAMPLE,SUM(A.QNT2) QNT2RSAMPLE ,B.NOPRSAMPLE
							    FROM DBRSERAHSAMPLEDET A
								LEFT OUTER JOIN DBSERAHSAMPLEDET B ON A.NOSERAHSAMPLE=B.NOBUKTI AND A.URUTSERAHSAMPLE=B.URUT
								GROUP BY A.KODEBRG,B.NOPRSAMPLE
								)X ON A.NOBUKTI=X.NOPRSAMPLE AND A.KODEBRG=X.KODEBRG
 LEFT OUTER JOIN (SELECT A.NORSERAHSAMPLE,A.KODEBRG,SUM(A.QNT) QNTBEBAN,SUM(A.QNT2) QNT2BEBAN ,B.NOPRSAMPLE
							FROM DBBEBANSAMPLEDET A
							LEFT OUTER JOIN DBSERAHSAMPLEDET B ON A.NOrSERAHSAMPLE=B.NOBUKTI AND A.URUTrSERAHSAMPLE=B.URUT
							GROUP BY A.NORSERAHSAMPLE,A.KODEBRG,B.NOPRSAMPLE
							) Z  ON A.NOBUKTI=Z.NOPRSAMPLE AND A.KODEBRG=Z.KODEBRG
 left outer join dbbarang D on A.kodebrg=D.kodebrg
 left outer join dbmerk h on D.kodemerk = h.kodemerk
    where
     c.RefPR<>'-' and (? = '' or c.RefPR= ?) and isnull(D.Isaktif,0)=1

 and ss.nobukti is not null
 group by   A.NOBUKTI,C.TANGGAL,C.REFPR,a.KODEBRG,b.Qnt,A.NOSAT,b.Qnt2,
 Z.QNTBEBAN ,Z.QNT2BEBAN,a.SAT_1,a.SAT_2,a.ISI,Ss.Nobukti,D.namaBrg,D.pPPN,Isnull(D.QntMin,0),D.isi2,h.namamerk
  having sum(SS.QNT)-ISNULL(b.QNT,0)  -ISNULL(Z.QNTBEBAN,0)>0
",
  [$req->nopenyerahan, $noreferensi, $noreferensi]);

    return $listData;



  }

  public function listSales (Request $req) {

    $listData = DB::connection('SML')->select("SELECT keynik, nama FROM dbkaryawan where IsSales = 1");
    return $listData;
  }

  public function listValas (Request $req) {

    $listData = DB::connection('SML')->select("SELECT kodevls, namavls, kurs FROM dbvalas");
    return $listData;
  }

  public function listAlamatKirim (Request $req) {

    $listData = DB::connection('SML')->select(" select nomor , nama , alamat from DBALAMATCUST where KODECUSTSUPP =:kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listPIC (Request $req) {

    $listData = DB::connection('SML')->select("select kodepic, nama from DBPICCUSTSUPP where KODECUSTSUPP =:kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listLokasiPenerima (Request $req) {

    $listData = DB::connection('SML')->select("select kodekebun, nama from DBKEBUNCUSTSUPP where KODECUSTSUPP =:kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listBackOffice (Request $req) {

    $listData = DB::connection('SML')->select("select keynik, fullname from [user] order by keynik" );
    return $listData;
  }

  public function listBarang (Request $req) {


    $listData = DB::connection('SML')->select("select a.Kodebrg, a.NamaBrg,I.NamaSubGrp,A.PartNumber,J.NAMAMERK,a.ISI1, a.ISI2, a.ISI3,
                    A.Sat1,A.Sat2 ,A.Sat3,A.pPPN,Isnull(A.QntMin,0) QntMin ,a.Hrg1_1 , a.Hrg2_1, a.Hrg3_1
                    from DBbarang a
                    left OUter JOin DbSubgroup I on A.KodeSubGRp=I.KodeSUbgrp and A.KodeHdGrp=i.KodeHDGrp
                    Left Outer join DbMerk J on A.KodeMerk=J.KodeMerk
                    where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
                     and (A.KodeBrg like '%" . $req->input('search') . "%') or (a.NamaBrg like '%" . $req->input('search') . "%')
                    and isnull(A.Isaktif,0)=1
                    order by a.Kodebrg ASC");
    return $listData;
  }

  public function cekKreditHari (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
//     select b.NAMAMERK ,  a.* from dbbarang a
// join DBMERK b on a.KodeMerk = b.KODEMERK
//  where a.KODEGRP = 'BJ' and a.pAgen = 1
    $listData = DB::connection('SML')->select("select hari from dbcustsupp where KODECUSTSUPP = :kodepelanggan", ["kodepelanggan" => $req->kodepelanggan]);
    return $listData;
  }

  public function getSatuanBarang (Request $req) {
    return DB::connection('SML')->select("select SAT1, SAT2,SAT3 , ISI1,ISI2,ISI3 from dbbarang where kodebrg = :kodebarang", ["kodebarang" => $req->kodebarang]);

  }

  public function spAdd (Request $req) {
    $nobukti = $req->nobukti;
    $username = \Auth::user()->username;
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $choice = $req->choice;

    if ($choice=='I') {
      $cariurut = DB::connection('SML')->select('select max(urut)  urut from dbsodet where Nobukti = :nobukti',["nobukti" => $nobukti]);
       if(empty($cariurut)){
       $murut = 0;


       if ($req->tipeppn == 1) {
        $inisial = DB::connection("SML")->select('select SO from DBNOMOR');
          $values = [
          $inisial[0]->SO,
          $periode->bulan,
          $periode->tahun,
          $username,
          // $periode
          // $periode
          ];

        } else {
        $values = [
          'SON',
          $periode->bulan,
          $periode->tahun,
          $username,
          // $periode
          // $periode
          ];
        }

        $cnoBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
        $nobukti =  $cnoBukti[0]->Nobukti;



       }else{
         $murut = $cariurut[0]->urut + 1;

       }
    }else {
      $murut = $req->urut;
    }


    $jmlrecord = $req->jmlrecord;

    if ($choice == "I" && $jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from dbso where Nobukti = :nobukti',["nobukti" => $nobukti]);
        if ($check) {
          return 2;
      }
      $check = DB::connection('SML')->select('select * from dbbarang where kodebrg = :kodebrg',["kodebrg" => $req->kodebarang]);
        if (!$check) {
          return 3;
      }
    }

    // $check = DB::connection('SML')->select('select * from dbbarang where kodebrg = :kodebrg',["kodebrg" => $req->kodebarang]);
    //   if (!$check) {
    //     return 3;
    // }



      // code...
    //   return [$choice,
    //   $nobukti,$req->urut,
    //   $req->discDet,
    //   $req->discrpDet,
    // $req->qnt1,
    // $req->qty,
    // $req->nosat,
    // $req->isi,
    // $req->harga,
    // ];
     $values = [
        $choice,
        $nobukti,
        $req->nourut,
        $req->tanggal,
        $req->tanggaljatuhtempo,
        $req->kodepelanggan,
        '',
        $req->kodesales,
        $req->kodealamatkirim,
        $req->alamatkirim, // 10
        '',
        $req->valas,
        $req->kurs,
        $req->tipeppn,
        $req->pembayaran,
        $req->hari,
        $req->tipediskon,
        $req->disc,
        $req->discrp,
        $req->catatan, // 20
        $req->urut,
        0,
        $req->kodebarang,
        $req->qnt1,
        $req->qty,
        $req->nosat,
        $req->isi,
        $req->harga,
        $req->discDet,
        $req->discrpDet, // 30
        '', // 30
        '',
        0,
        '',
        '',
        0,
        \Auth::User()->username,
        $req->tanggal,
        $req->satuan,
        $req->nopo, // 40
        $req->tanggalkirim, //40
        null,
        0,
        0,
        0,
        0,
        $req->tipeppn,
        '',
        '',
        '', // 50
        0, //50
        $req->tambahkepo,
        $req->kodepic,
        $req->namaproduk,
        $req->kodelokasipenerima,
        $req->satuanproduk,
        $req->jmlrecord,
        $req->refpr, // !
        $req->pppn, // !
        $req->dp, // 60
        $req->booking, //60
        $req->nopenyerahan,
        $req->urgent,
        $req->draftpo,
        $req->kodebackoffice,
        $req->tanggalpo,
        $req->idpocust,
        $req->kodesattax,
        $req->lokcounter,
        $req->piccounter,
        $req->tglkirimdet, // 70
        $req->statuspengiriman,
      ];
      // DB::connection()->enableQueryLog();
      DB::connection('SML')->statement('exec sp_SO ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
      // return $queries = DB::getQueryLog();
      DB::connection('SML')->update('exec Sp_UpdateSO ?', [$nobukti]);
    return 1;
  }

  public function spCekHarga (Request $req) {
        $harga = DB::connection('SML')->select("declare @kodebrg varchar(50),@nosat tinyint
      select @kodebrg= :kodebarang ,@nosat= :nosat
      select top 1 B.KODEBRG,b.NOSAT,b.HARGA,c.ISI2,c.ISI3, a.TANGGAL, b.SATUAN,
      case when @nosat=1 then              +
                       case when NOSAT=1 then HARGA when NOSAT=2 then HARGA/c.ISI2 when NOSAT=3 then HARGA/ISI3 end
               when @nosat=2 then
                       case when NOSAT=2 then HARGA when NOSAT=1 then HARGA*c.ISI2 when NOSAT=3 then (HARGA/ISI3)*c.ISI2 end
               when @nosat=3 then
                       case when NOSAT=3 then HARGA when NOSAT=1 then HARGA*c.ISI3 when NOSAT=2 then (HARGA/ISI2)*c.ISI3 end
      End Xharga
      from DBBELI a
      left outer join DBBELIDET b on a.NOBUKTI=b.NOBUKTI
      left outer join DBBARANG c on b.KODEBRG=c.KODEBRG
      where b.KODEBRG=@kodebrg
      order by a.TANGGAL desc" ,["kodebarang" => $req->kodebarang , "nosat" => $req->nosat]);

      // HARGA JUAL
    $hargaJual = DB::connection('SML')->select("
    declare @kodecustsupp varchar(30),
            @kodekebun varchar(100),
            @kodebrg varchar(20)

    select @kodecustsupp = :kodecustsupp,
           @kodekebun = :kodekebun,
           @kodebrg = :kodebarang

    select TOP 5 A.tanggal,B.qnt2,B.satuan ,b.harga,b.discrp1,b.disctot
    from DBSO A
    LEFT OUTER JOIN DBSODET B ON A.NOBUKTI=B.NOBUKTI
    where a.KODECUST=@kodecustsupp
    and a.KODEKEBUN=@kodekebun
    and b.KODEBRG=@kodebrg
    ORDER BY A.TANGGAL DESC
    ",[
        "kodecustsupp" => $req->kodecustsupp,
        "kodekebun" => $req->kodekebun,
        "kodebarang" => $req->kodebarang
    ]);

    // HARGA BELI
    $hargaBeli = DB::connection('SML')->select("
    declare @kodebrg varchar(20)

    select @kodebrg = :kodebarang

    select top 5 A.tanggal,B.qntterima,B.satuan ,b.harga,b.disctot,b.ndiskon
    from DBBELI a
    left outer join DBBELIDET b on a.NOBUKTI=b.NOBUKTI
    where b.KODEBRG=@kodebrg
    and KodeGdg not in ('GSML','GMPL','GSML3')
    order by a.TANGGAL desc
    ",[
        "kodebarang" => $req->kodebarang
    ]);


    return response()->json([
        "harga" => $harga,
        "harga_jual" => $hargaJual,
        "harga_beli" => $hargaBeli
    ]);

      // return $harga;
  }

  public function detailBarangAll (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
//     select b.NAMAMERK ,  a.* from dbbarang a
// join DBMERK b on a.KodeMerk = b.KODEMERK
//  where a.KODEGRP = 'BJ' and a.pAgen = 1
    $barang = DB::connection('SML')->select(" select a.Kodebrg, a.NamaBrg,I.NamaSubGrp,A.PartNumber,J.NAMAMERK,a.ISI1, a.ISI2, a.ISI3,
                    A.Sat1,A.Sat2 ,A.Sat3,A.pPPN,Isnull(A.QntMin,0) QntMin ,a.Hrg1_1 , a.Hrg2_1, a.Hrg3_1
                    from DBbarang a
                    left OUter JOin DbSubgroup I on A.KodeSubGRp=I.KodeSUbgrp and A.KodeHdGrp=i.KodeHDGrp
                    Left Outer join DbMerk J on A.KodeMerk=J.KodeMerk
                    where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
                     and A.KodeBrg = :kodebrg
                    and isnull(A.Isaktif,0)=1
                    order by a.Kodebrg ASC" , ["kodebrg" => $req->kodebrg] );
    $harga = DB::connection('SML')->select("declare @kodebrg varchar(50),@nosat tinyint
        select @kodebrg= :kodebarang ,@nosat= :nosat
        select top 1 B.KODEBRG,b.NOSAT,b.HARGA,c.ISI2,c.ISI3, a.TANGGAL, b.SATUAN,
        case when @nosat=1 then              +
                         case when NOSAT=1 then HARGA when NOSAT=2 then HARGA/c.ISI2 when NOSAT=3 then HARGA/ISI3 end
                 when @nosat=2 then
                         case when NOSAT=2 then HARGA when NOSAT=1 then HARGA*c.ISI2 when NOSAT=3 then (HARGA/ISI3)*c.ISI2 end
                 when @nosat=3 then
                         case when NOSAT=3 then HARGA when NOSAT=1 then HARGA*c.ISI3 when NOSAT=2 then (HARGA/ISI2)*c.ISI3 end
        End Xharga
        from DBBELI a
        left outer join DBBELIDET b on a.NOBUKTI=b.NOBUKTI
        left outer join DBBARANG c on b.KODEBRG=c.KODEBRG
        where b.KODEBRG=@kodebrg
        order by a.TANGGAL desc  ",["kodebarang" => $req->kodebrg , "nosat" => $req->nosat]);




    return ["barang" => $barang , "harga" => $harga];
  }


  public function getDetail (Request $req) {
    $nobukti = $req->nobukti;
    // return "furuaaabb";


    $header = DB::connection('SML')->select("
    declare @NoBukti varchar(20)

    select 	@NoBukti= :nobukti

    Select 	A.idpocust , B.SP xSP,A.NoBukti, A.NOURUT, A.Tanggal, A.TglJatuhTempo, A.KodeCUST,  M2.NAMACUSTSUPP NamaCust,
            A.NoAlamatKirim, J.Alamat AlamatKirim,
    	A.Handling, A.NOSPB, Cast(A.KodeSls as varchar(200)) kodesls,  M1.Nama NamaSls, A.KodeGdg, A.Keterangan, A.KodeVls, D.NamaVls, A.Kurs, A.PPN,
    	A.TipeBayar, A.Hari, A.TipeDisc, A.Disc, A.DiscRp, A.Catatan,
    	B.Urut, B.UrutSPB, B.KodeBrg,
    h.NAMABRG  NamaBrg,B.namabrg namabrgalias,
       IsCetakKitir IsPO,
            Case when b.NOSAT=1 then B.qnt else b.Qnt2 end Qnt, B.NoSat, B.Isi,
            Case when b.NOSAT=1 then H.Sat1 when b.NOSAT=2 then  H.Sat2 when b.nosat=3 then H.sat3 end Satuan,
            B.Qnt2, H.Sat2 SatuanRoll, B.Qnt3, B.Harga,
            B.DiscP1, B.DiscRp1, B.DiscTot,
            B.SubTotal TotalUSD, B.SubTotalRp TotalIDR, B.NDPPRp NDPP,
            B.NPPNRp NPPN, B.BYAngkut Beban,
            I.TotSubTotal, I.TotDiskon, I.TotTotal, I.TotDPP, I.TotPPN, I.TotNet,
            I.TotSubTotalRp, I.TotDiskonRp, I.TotTotalRp, I.TotDPPRp, I.TotPPNRp, I.TotNetRp,
            A.KodeExp, M.NamaExp, A.InsGdg, A.InsBrg, C.IsPPN, A.IsLengkap,
            Isnull(M6.PoCustomer,A.NoPesanan) NoPesanan, A.TglKirim, A.MasaBerlaku,
            Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
            Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
            Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotalDPP,
            Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotalPPn,
            Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotalNetto,
            B.Discp2,Discp3,Discp4,Discp5,
            ltrim(M2.ALAMAT1+case when ltrim(m2.ALAMAT2)<>'' then char(13)+M2.ALAMAT2 else '' end+
    	case when ltrim(isnull(M2.KOTA,''))<>'' then char(13)+isnull(M2.KOTA,'')+' '+M2.KodePos else '' end) ALAMAT,
            NoResi,NoPolKend,Sopir,JumlahTagihan ,M2.plafon,A.kodePF,M3.Nama NamaPF,/*case when ISNULL(b.notawar,'')='' then*/ B.namaBRg pNamaBRG/*else '' End*/ ,
            A.KODEKEBUN,M4.NAMA NAMAKEBUN, b.sATx
            ,case when ISnull(B.iscetakKitir,0)=0 then 'Tidak' else 'Ya' End KetPO
            ,ZC.NNET NNETOUT ,B.SP
            ,B.RefPR ,B.PPNBRG,A.DP,B.sattax ,A.lokCounter  ,A.PicCounter, case when year(B.dettglkirim)<2000 then null else B.dettglkirim end DetTglKirim,
            B.Pbooking  ,B.NOserah ,B.pUrgent,A.PPO,A.Boffice,Mx.nama NamaBoFFice,a.TglPO ,B.kodegdg KOdegdgdet,M5.Nama NamaGDGdet ,Isnull(A.TipePPN,0) TipePPN ,I.SubtotalCLose, A.isotorisasi1,b.noserah
    From dbSO A
    Left Outer join dbSODet B on B.NoBukti=a.NoBukti
    Left Outer Join vwBrowsCustomer C on c.Kodecust=a.KodeCust and c.Sales=A.KODESLS
    Left Outer join dbValas D on D.KodeVls=A.KodeVls
    left outer join DBSALESCUSTOMER F on F.KeyNIK=A.KODESLS and F.KodeCustSupp=A.KODECUST
    Left Outer Join vwSatuanBrg H on H.KodeBrg=B.KodeBrg --and H.NoSat=B.NoSat
    Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
    left outer join vwAlamatCust J on J.KODECUSTSUPP=A.KodeCust and J.Nomor=A.NoAlamatKirim
    left outer join dbExpedisi M on M.KodeExp=A.KodeExp
    Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
    Left OUter Join DBCUSTSUPP M2 on A.KODECUST=M2.KODECUSTSUPP  left outer join VwBoFfice Mx on a.boffice=Mx.Boffice
    Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
    lEFT OUTER JOIN DBKEBUNCUSTSUPP M4 ON A.KODEKEBUN=M4.KODEKEBUN  and A.kodecust=M4.KodeCustSupp
    Left Outer join Dbgudang M5 on B.kodegdg=M5.kodegdg
    left outer join dbPOCustsupp M6 on A.IDPOCUst=m6.ID
    Left outer join ( select D.KODECUST,
                    Sum(
                    ((case when A.ppn=(1) OR A.ppn=(0) then case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*
                    (A.HARGA-A.DISCTOT)-((case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0) when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT))*A.DISC)/(100)
                    when A.ppn=(2) then (case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0) when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT)-
                    ((case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT))*A.DISC)/(100))/(1.1)
                    else (0) end+case when A.ppn=(1) then case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0) when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*
                    (A.HARGA-A.DISCTOT)-((case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT))
                    *A.DISC)/(100) when A.ppn=(2) then (case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT)-
                    ((case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT))*A.DISC)/(100))/(1.1)
                    else (0) end*(0.1))*A.Kurs)) NNet
                    from DBSODET A
                    left outer join dbSPBDet b on A.NOBUKTI=b.NoSo and A.Urut=b.UrutSo
                    left outer join (select NoSPB,UrutSPB,SUM(QNT) Qnt1,SUM(QNT2) Qnt2
                    from dbInvoicePLDet group by NoSPB,UrutSPB) c on B.NoBukti=C.NoSPB and B.Urut=c.UrutSPB
                    Left Outer Join DBSO D ON A.NOBUKTI=D.NOBUKTI
                    where A.QNT - Isnull(C.Qnt1,0)>0
                    and A.NOBUKTI not in (@NObukti)
                    group By D.KODECUST
                    ) ZC on A.KODECUST=ZC.KODECUST
    where A.NoBukti=@NoBukti
    order by B.Urut
    " , ["nobukti" => $nobukti]) ;

    $list = DB::connection('SML')->select("
    declare @NoBukti varchar(20)

    select 	@NoBukti= :nobukti


    Select m8.ISI1 , m8.ISI2 , m8.ISI3 , m8.SAT1 , m8.SAT2 , m8.SAT3 ,b.sattax NAMATAX, isnull(J2.NAMAMERK,'') as namamerk,b.SP xsp,	A.NoBukti, A.NOURUT, A.Tanggal, A.TglJatuhTempo, A.KodeCUST,  M2.NAMACUSTSUPP NamaCust,
            A.NoAlamatKirim, J.Alamat AlamatKirim,
    	A.Handling, A.NOSPB, Cast(A.KodeSls as varchar(200)) kodesls,  M1.Nama NamaSls, A.KodeGdg, A.Keterangan, A.KodeVls, D.NamaVls, A.Kurs, A.PPN,
    	A.TipeBayar, A.Hari, A.TipeDisc, A.Disc, A.DiscRp, A.Catatan,
    	B.Urut, B.UrutSPB, B.KodeBrg, H.NamaBrg, IsCetakKitir IsPO,
            Case when b.NOSAT=1 then B.qnt else b.Qnt2 end Qnt, B.NoSat, B.Isi,
            Case when b.NOSAT=1 then H.Sat1 when b.NOSAT=2 then  H.Sat2 when b.nosat=3 then H.sat3 end Satuan,
            B.Qnt2, H.Sat2 SatuanRoll, B.Qnt3, B.Harga,
            B.DiscP1, B.DiscRp1, B.DiscTot,
            B.SubTotal TotalUSD, B.SubTotalRp TotalIDR, B.NDPPRp NDPP,
            B.NPPNRp NPPN, B.BYAngkut Beban,
            I.TotSubTotal, I.TotDiskon, I.TotTotal, I.TotDPP, I.TotPPN, I.TotNet,
            I.TotSubTotalRp, I.TotDiskonRp, I.TotTotalRp, I.TotDPPRp, I.TotPPNRp, I.TotNetRp,
            A.KodeExp, M.NamaExp, A.InsGdg, A.InsBrg, C.IsPPN, A.IsLengkap,
            Isnull(M6.PoCustomer,A.NoPesanan) NoPesanan, A.TglKirim, A.MasaBerlaku,
            Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
            Case when A.Kodevls='IDR' then I.TotDiskonRp  else I.TotDiskon end Diskon,
            Case when A.Kodevls='IDR' then I.TotDPPRp  else I.TotDPP end TotalDPP,
            Case when A.Kodevls='IDR' then I.TotPPnRp  else I.TotPPn end TotalPPn,
            Case when A.Kodevls='IDR' then I.TotNetRp  else I.TotNet end TotalNetto,
            B.Discp2,Discp3,Discp4,Discp5,
            ltrim(M2.ALAMAT1+case when ltrim(m2.ALAMAT2)<>'' then char(13)+M2.ALAMAT2 else '' end+
    	case when ltrim(isnull(M2.KOTA,''))<>'' then char(13)+isnull(M2.KOTA,'')+' '+M2.KodePos else '' end) ALAMAT,
            NoResi,NoPolKend,Sopir,JumlahTagihan ,M2.plafon,A.kodePF,M3.Nama NamaPF,/*case when ISNULL(b.notawar,'')='' then*/ B.namaBRg pNamaBRG/*else '' End*/ ,
            A.KODEKEBUN,M4.NAMA NAMAKEBUN, b.sATx
            ,case when ISnull(B.iscetakKitir,0)=0 then 'Tidak' else 'Ya' End KetPO
            ,ZC.NNET NNETOUT ,B.SP
            ,B.RefPR ,B.PPNBRG,A.DP,B.sattax ,A.lokCounter  ,A.PicCounter, case when year(B.dettglkirim)<2000 then null else B.dettglkirim end DetTglKirim,
            B.Pbooking  ,B.NOserah ,B.pUrgent,A.PPO,A.Boffice,Mx.nama NamaBoFFice,a.TglPO ,B.kodegdg KOdegdgdet,M5.Nama NamaGDGdet ,Isnull(A.TipePPN,0) TipePPN ,I.SubtotalCLose, A.isotorisasi1 ,isnull(b.namabrg,'') namabrgalias,b.noserah,isnull(outbrg.nobeli,'') nopl
    From dbSO A
    Left Outer join dbSODet B on B.NoBukti=a.NoBukti
    Left join DBBARANG m8 on b.KODEBRG = m8.KODEBRG
    Left join DbMerk J2 on m8.KodeMerk = J2.KodeMerk
    Left Outer Join vwBrowsCustomer C on c.Kodecust=a.KodeCust and c.Sales=A.KODESLS
    Left Outer join dbValas D on D.KodeVls=A.KodeVls
    left outer join DBSALESCUSTOMER F on F.KeyNIK=A.KODESLS and F.KodeCustSupp=A.KODECUST
    Left Outer Join vwSatuanBrg H on H.KodeBrg=B.KodeBrg --and H.NoSat=B.NoSat
    Left Outer Join vwRpDetSO I on I.NoBukti=A.NoBukti
    left outer join vwAlamatCust J on J.KODECUSTSUPP=A.KodeCust and J.Nomor=A.NoAlamatKirim
    left outer join dbExpedisi M on M.KodeExp=A.KodeExp
    Left Outer Join dbKaryawan M1 on A.KODESLS=M1.KeyNIK
    Left OUter Join DBCUSTSUPP M2 on A.KODECUST=M2.KODECUSTSUPP  left outer join VwBoFfice Mx on a.boffice=Mx.Boffice
    Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
    lEFT OUTER JOIN DBKEBUNCUSTSUPP M4 ON A.KODEKEBUN=M4.KODEKEBUN  and A.kodecust=M4.KodeCustSupp
    Left Outer join Dbgudang M5 on B.kodegdg=M5.kodegdg
    left outer join dbPOCustsupp M6 on A.IDPOCUst=m6.ID
    left outer join DBSATTAX M7 on b.sattax = m7.KODETAX

  --  left outer join DBBARANG m81 on b.KODEBRG = m81.KODEBRG
    Left outer join ( select D.KODECUST,
                    Sum(
                    ((case when A.ppn=(1) OR A.ppn=(0) then case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*
                    (A.HARGA-A.DISCTOT)-((case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0) when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT))*A.DISC)/(100)
                    when A.ppn=(2) then (case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0) when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT)-
                    ((case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT))*A.DISC)/(100))/(1.1)
                    else (0) end+case when A.ppn=(1) then case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0) when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*
                    (A.HARGA-A.DISCTOT)-((case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT))
                    *A.DISC)/(100) when A.ppn=(2) then (case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT)-
                    ((case when A.NOSAT=(1) then A.Qnt- Isnull(C.Qnt1,0) when A.NOSAT=(2) then A.Qnt2- Isnull(C.Qnt2,0)when A.NOSAT=(3) then A.Qnt2- Isnull(C.Qnt2,0) else (0) end*(A.HARGA-A.DISCTOT))*A.DISC)/(100))/(1.1)
                    else (0) end*(0.1))*A.Kurs)) NNet
                    from DBSODET A
                    left outer join dbSPBDet b on A.NOBUKTI=b.NoSo and A.Urut=b.UrutSo
                    left outer join (select NoSPB,UrutSPB,SUM(QNT) Qnt1,SUM(QNT2) Qnt2
                    from dbInvoicePLDet group by NoSPB,UrutSPB) c on B.NoBukti=C.NoSPB and B.Urut=c.UrutSPB
                    Left Outer Join DBSO D ON A.NOBUKTI=D.NOBUKTI
                    where A.QNT - Isnull(C.Qnt1,0)>0
                    and A.NOBUKTI not in (@NObukti)
                    group By D.KODECUST
                    ) ZC on A.KODECUST=ZC.KODECUST
    left outer join (select nobeli,urutbeli from dboutbrgdet group by nobeli,urutbeli) outbrg on outbrg.nobeli=b.nobukti and outbrg.urutbeli=b.urut
    where A.NoBukti=@NoBukti
    order by B.Urut
", ["nobukti" => $nobukti]);


    return [
      "header" => $header ,
      "list" => $list
    ];
  }


public function SOCheckHargaAdd(Request $req) {

if ( $req->input('choice')=='I') {

  $tanggal = $req->input('Tanggal');
  $kodebrg = $req->input('KodeBrg');
  $harga = $req->input('xharga');
  $nosat = $req->input('NoSat');

} else {
  $tanggal = $req->input('tanggal');
  $kodebrg = $req->input('kodebarang');
  $harga = $req->input('xharga');
  $nosat = $req->input('nosat');


}



  $flagharga='';

  // return [$tanggal,$kodebrg,$harga,$nosat];

  $checkharga= DB::connection('SML')->select("
   declare @KODEBRG VARCHAR(30),@harga numeric(18,2),@nosat int,@tanggal date
select @KODEBRG=:kodebrg ,@harga =:harga ,@nosat=:nosat, @tanggal = :tanggal

select
@harga hargaso, XTABLE.hrgmin hargaakhir,
XTABLE.hrgmin + (XTABLE.hrgmin * 0.1 )  hrgminbeli,
XTABLE.hrgmin + (XTABLE.hrgmin * 1.00 ) hrgmaxbeli,
 case when @harga < XTABLE.hrgmin + (XTABLE.hrgmin * 0.1 )  then 'harga lebih kecil dari pada harga min SO'
  when @harga >= XTABLE.hrgmin + (XTABLE.hrgmin * 1.00 ) then 'Margin  Lebih dari 100%'
else
'lanjut' End ket
from (
select top 1
 case when @nosat=1 then
      case when A.NOSAT=1 then A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
            when A.NOSAT=2 then (A.HRGNETTO /C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO/C.ISI2) * 0.10 else 0 end))
            when A.NOSAT=3 THEN (A.HRGNETTO /C.ISI3) - ((case when a.PPN=2 then (A.HRGNETTO/C.ISI3) * 0.10 else 0 end)) END * A.KURS

     when @nosat=2 then
       CASE WHEN A.NOSAT=2 THEN A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
            WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI2) * 0.10 else 0 end))
            WHEN A.NOSAT=3 THEN (((A.HRGNETTO / C.ISI3)*C.ISI2)- ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI3)*C.ISI2) * 0.10 else 0 end))) END * A.KURS

     when @nosat=3 then
       CASE WHEN A.NOSAT=3 THEN A.HRGNETTO - ((case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end))
            WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI3)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI3) * 0.10 else 0 end))
            WHEN A.NOSAT=2 THEN (((A.HRGNETTO/ C.ISI2)*c.ISI3) - ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI2)*c.ISI3) * 0.10 else 0 end))) eND * A.KURS
End hrgmin
from DBBELIDET A
Left OUter join DBBELI b on A.nobukti=B.NOBUKTI
Left Outer join DBBARANG C on A.KODEBRG=C.KODEBRG
Where B.TANGGAL <=@tanggal and A.KODEBRG=@KODEBRG  and A.kodegdg<>'G06'
order by B.TANGGAL Desc
) xtable




    ", ["tanggal"=>$tanggal,"kodebrg"=>$kodebrg,"harga"=>$harga,"nosat"=>$nosat] );

    //  if ($checkharga->isEmpty()){
    if(empty($checkharga)){
      $flagharga = 'lanjut';

     } else {

         $flagharga = $checkharga[0]->ket;
     }

   return $flagharga;

  }

  public function loadHeader(Request $req) {
    $header = DB::connection('SML')->select('select * from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode', [
      'user' => \Auth::User()->username,
      'href' => $req->href,
      'mode' => $req->mode,
    ]);

    return $header;
  }

  public function simpanHeader(Request $req) {
    DB::connection('SML')->update('delete from DBSIMPANHEADER where username = :user and href = :href and reportmode = :mode', [
      'user' => \Auth::User()->username,
      'href' => $req->href,
      'mode' => $req->mode,
    ]);

    DB::connection('SML')->insert('insert into DBSIMPANHEADER (username, href, reportmode, header, issubtotal, isgrandtotal) values (:user, :href, :mode, :header, :issubtotal, :isgrandtotal)', [
      'user' => \Auth::User()->username,
      'href' => $req->href,
      'mode' => $req->mode,
      'header' => $req->header,
      'issubtotal' => $req->issubtotal,
      'isgrandtotal' => $req->isgrandtotal,
    ]);

    return 1;
  }

}
