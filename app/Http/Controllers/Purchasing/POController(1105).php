<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\VwPPL;
use Illuminate\Auth;


// use App\Http\Controllers\NewMenuController;

class POController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '04101';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // $users = DB::connection("SML")->select('select * from new_users');
    // $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    // $outstanding = VwPPL::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('IsJasa', 0)->where('pAgen', 1)->groupBy('NoBukti');
    $tempOutstanding1 = DB::connection("SML")->select("
      DECLARE @Tahun int, @Bulan int
      SELECT @Tahun=2018, @Bulan=78

      SET NOCOUNT ON
      SELECT  A.NoBukti+' '+right('00000000'+cast(A.urut as varchar(8)),8) KeyUrut,
      A.*
      from DBO.vwOutPPL A WITH(NOLOCK)
      where A.SisaPPL>0
      and A.pjasa=0
      order by A.Tanggal, A.NoBukti, A.Urut
    ");

    $tempOutstanding3 = DB::connection("SML")->select("declare @Tahun int, @Bulan int  ,@pJasa Bit

select @Tahun= :tahun, @Bulan= :bulan


Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO,NOPOCUST ,A.tglKirim,A.MaxOL
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan
and  /*Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=1  */    Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)= 1  AND TotTotalRp>=200000000
and B.pJasa= 0


UNION ALL


Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO,NOPOCUST ,A.tglKirim,A.MaxOL
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan
and  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=1       AND TotTotalRp < 200000000
and B.pJasa= 0

order by NoBukti" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

  $tempOutstanding5 = DB::connection("SML")->select("declare @Tahun int, @Bulan int,@pJasa Bit

select @Tahun= :tahun, @Bulan= :bulan,@pJasa= 0

Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO, CASE WHEN ISNULL(NOPOCUST,'')='' THEN
			(SELECT TOP 1 NOPESANAN FROM DBSO WHERE NOBUKTI=a.NOSO )
       ELSE NOPOCUST END NOPOCUST,A.TglKirim
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan   and
 /*Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0*/  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) =0    AND TotTotalRp>=200000000
and b.pjasa=@pJasa


UNION ALL


Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO, CASE WHEN ISNULL(NOPOCUST,'')='' THEN
			(SELECT TOP 1 NOPESANAN FROM DBSO WHERE NOBUKTI=a.NOSO )
       ELSE NOPOCUST END NOPOCUST,A.TglKirim
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan   and
 Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0     AND TotTotalRp < 200000000
and b.pjasa=@pJasa


order by NoBukti" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

    return view('purchasing.purchaseOrder' , [
      "menul0" => $menul0,
      "periode" => $periode,
      // "users"=> $users,
      // "tempOutstanding" => $tempOutstanding,
      "tempOutstanding1" => $tempOutstanding1,
      // "tempOutstanding2" => $tempOutstanding2,
      "tempOutstanding3" => $tempOutstanding3,
      // "tempOutstanding3" => $tempOutstanding3,
      "tempOutstanding5" => $tempOutstanding5,

      "level" => $akses->OL,
      "listBarangAll" => [] ,
      "akses" => $akses
    ]);

}

  public function loadAll () {

    $periode = NewPeriode::where('user_id' , \Auth::User()->username)->first();
    //
    $tempOutstanding = DB::connection("SML")->select("
      DECLARE @Tahun int, @Bulan int
      SELECT @Tahun=2018, @Bulan=78

      SET NOCOUNT ON
      SELECT  A.NoBukti+' '+right('00000000'+cast(A.urut as varchar(8)),8) KeyUrut,
      A.*
      from DBO.vwOutPPL A WITH(NOLOCK)
      where A.SisaPPL>0
      and A.pjasa=0
      order by A.Tanggal, A.NoBukti, A.Urut
    ");

    $collection1 = collect($tempOutstanding)->groupBy('NOBUKTI');
    $tempOutstanding1 = [];
    foreach ($collection1 as $p) {
      array_push($tempOutstanding1, $p);
    }

    $tempOutstanding2 = DB::connection("SML")->select("declare @Tahun int, @Bulan int  ,@pJasa Bit

select @Tahun= :tahun, @Bulan= :bulan

Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO,NOPOCUST ,A.tglKirim,A.MaxOL
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan
and  /*Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=1  */    ISNULL (A.IsOtorisasi2,0)=0  AND TotTotalRp>=200000000
and B.pJasa= 0

UNION ALL

Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO,NOPOCUST ,A.tglKirim,A.MaxOL
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan
and  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=1       AND TotTotalRp < 200000000
and B.pJasa= 0

order by NoBukti" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

$collection2 = collect($tempOutstanding2)->groupBy('NOBUKTI');
$tempOutstanding3 = [];
foreach ($collection2 as $p) {
  // code...
  array_push($tempOutstanding3, $p);
}

  $tempOutstanding4 = DB::connection("SML")->select("declare @Tahun int, @Bulan int,@pJasa Bit

select @Tahun= :tahun, @Bulan= :bulan,@pJasa= 0

Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO, CASE WHEN ISNULL(NOPOCUST,'')='' THEN
			(SELECT TOP 1 NOPESANAN FROM DBSO WHERE NOBUKTI=a.NOSO )
       ELSE NOPOCUST END NOPOCUST,A.TglKirim
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan   and
 /*Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0*/  ISNULL (A.IsOtorisasi2,0)=1    AND TotTotalRp>=200000000
and b.pjasa=@pJasa


UNION ALL


Select  a.isAut,a.NoBukti, a.Tanggal,a.KodeSupp, b.NamaCustSupp, b.Handling, b.FakturSupp,
        b.TotSubTotal, b.TotDiskon, b.TotTotal, TotDPP, b.TotPPN, TotNet,
        TotSubTotalRp, TotDiskonRp, TotTotalRp, TotDPPRp, TotPPNRp, TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1,
       A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
       A.IsOtorisasi3, A.OtoUser3, A.TglOto3,
       A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
       A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.IsOtorisasi2
       ,Isnull(A.IsBatal,0) Isbatal,A.UserBatal,A.TglBatal,
       A.FlagTipe,NOSO, CASE WHEN ISNULL(NOPOCUST,'')='' THEN
			(SELECT TOP 1 NOPESANAN FROM DBSO WHERE NOBUKTI=a.NOSO )
       ELSE NOPOCUST END NOPOCUST,A.TglKirim
From dbPO a Left Outer Join vwMasterPO b on a.NoBukti=b.NoBukti
where year(a.Tanggal)=@Tahun and month(a.Tanggal)=@Bulan   and
 Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit)=0     AND TotTotalRp < 200000000
and b.pjasa=@pJasa


order by NoBukti" , ["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

$collection3 = collect($tempOutstanding4)->groupBy('NOBUKTI');
$tempOutstanding5 = [];
foreach ($collection3 as $p) {
  // code...
  array_push($tempOutstanding5, $p);
}

    return [
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding1" => $tempOutstanding1,
      "tempOutstanding2" => $tempOutstanding2,
      "tempOutstanding3" => $tempOutstanding3,
      "tempOutstanding4" => $tempOutstanding4,
      "tempOutstanding5" => $tempOutstanding5
    ];
}

  public function cekOtorisasi (Request $req) {
    $res = DB::connection('SML')->select("select isOtorisasi1 from dbpo where nobukti = :nobukti", ["nobukti" => $req->nobukti ]);
    return $res;
  }

  public function onChangeHeader (Request $req) {
    $query = 'update dbpo set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
    return $res;

  }


  public function cekHargaOto (Request $req) {

    // return 1;
    $listData = $req->tempData ? $req->tempData : [] ;
    $tempArray = [];

    foreach ($listData as $d)  {
      $xso = '';
      if ($d['NOSO'] != '-') {
        $xso = $d['NOSO'];
      }
      // return ["noso" => $d['NOSO'], "kodebrg" => $d['KodeBrg'] ,"nopo" => $d['NoBukti']];
    $x = DB::connection('SML')->select("declare @noSO varchaR(30),@KODEBRG VARCHAR(30)
    select @noSO= :noso ,@KODEBRG= :kodebrg

    SELECT XTABLE.kodebrg ,

    XTABLE.hrgmin hrgminso,XTABLE.HRGMIN + (XTABLE.HRGMIN * 1.00 ) hrgmaxso,
    XTABLE.xhrgpo
    , case when XTABLE.xhrgpo > XTABLE.HRGMIN then 'harga lebih besar dari pada harga min SO'

    	when XTABLE.xhrgpo >= (XTABLE.HRGMIN * 1.00 ) then 'harga lebih besar dari pada harga max SO (100%)'
    else
    'lanjut' End Ket
     FROM (

    select
    AA.kodebrg,AA.PPN * AA.KUrs  PPN ,AA.HrgNetto Hrg,B.tanggal,AA.nosat,
    (AA.harga * AA.Kurs )harga,
    (AA.harga * AA.Kurs ) - (AA.DiscTot * AA.kurs) -
    (case when AA.PPN=2 then  (AA.harga * AA.Kurs ) * 0.1 else 0 end )XHrgPO,
    AA.DiscTot * AA.kurs DiscTot,B.NOSO ,
    (
    select top 1
    case when AA.NOSAT=1 then
          case when A.NOSAT=1 then A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
                when A.NOSAT=2 then (A.HRGNETTO /C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO/C.ISI2) * 0.10 else 0 end))
                when A.NOSAT=3 THEN (A.HRGNETTO /C.ISI3) - ((case when a.PPN=2 then (A.HRGNETTO/C.ISI3) * 0.10 else 0 end)) END * A.KURS
          when AA.NOSAT=2 then
           CASE WHEN A.NOSAT=2 THEN A.HRGNETTO - (case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end)
                WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI2)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI2) * 0.10 else 0 end))
                WHEN A.NOSAT=3 THEN (((A.HRGNETTO / C.ISI3)*C.ISI2)- ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI3)*C.ISI2) * 0.10 else 0 end))) END * A.KURS

          when AA.NOSAT=3 then
           CASE WHEN A.NOSAT=3 THEN A.HRGNETTO - ((case when a.PPN=2 then A.HRGNETTO * 0.10 else 0 end))
                WHEN A.NOSAT=1 THEN (A.HRGNETTO * C.ISI3)- ((case when a.PPN=2 then (A.HRGNETTO * C.ISI3) * 0.10 else 0 end))
                WHEN A.NOSAT=2 THEN (((A.HRGNETTO/ C.ISI2)*c.ISI3) - ((case when a.PPN=2 then ((A.HRGNETTO / C.ISI2)*c.ISI3) * 0.10 else 0 end))) eND * A.KURS

    END
    from DBSODET A
    Left OUter join DBSO b on A.nobukti=B.NOBUKTI
    Left Outer join DBBARANG C on A.KODEBRG=C.KODEBRG
    Where A.NOBUKTI=@noSO and A.KODEBRG=@Kodebrg  --and A.kodegdg<>'G06'
    order by B.TANGGAL Desc
    ) HRGMIN

    from DbPODET AA
    left outer join dbPo B on AA.nobukti=B.nobukti
    where AA.nobukti = :nopo and isnull(B.noso,'') not in ('','-')
    ) XTABLE where XTABLE.KODEBRG=@KODEBRG
    ", ["noso" => $xso, "kodebrg" => $d['KodeBrg'] ,"nopo" => $d['NoBukti'] ]);
    if ($x) {

      array_push($tempArray, $x);
    }

    }
    return $tempArray;
  }





  public function updateOtorisasi (Request $req) {
    $username = \Auth::user()->username;
     $maxOL = DB::connection('SML')->select("select * from dbmenu where href ='purchaseorder'");
    $cekOto = DB::connection('SML')->select("
select b.*,c.KodeJab,isnull(d.PlafonOtoPO , 0) PlafonOtoPO,
       case when B.IsOtorisasi1=1 then 1
	 when B.IsOtorisasi2=1 then 2
	 when B.IsOtorisasi3=1 then 3
	 when B.IsOtorisasi4=1 then 4
	 when B.IsOtorisasi5=1 then 5 end leveloto,e.nnet
       from dbmenu a
    join dbflmenu b on a.kodemenu = b.l1
    join DBFLPASS c on b.USERID=c.USERID
    join DBJABATAN d on c.KodeJab=d.KODEJAB
    left outer join (select nobukti,SUM(nnetrp) nnet from DBPODET
					 where NOBUKTI= :nobukti
					 group by nobukti
					 ) E on 1=1
    where a.href ='purchaseorder' and b.USERID= :username ", ["nobukti" => $req->nobukti , "username" => $username]);
    $tanggal = date('Y-m-d H:i:s');
    if ( $cekOto[0]->nnet > $cekOto[0]->PlafonOtoPO ) {
      if($cekOto[0]->leveloto == 1) {

        $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 1, maxol = :maxol , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username ,"maxol" => $maxOL[0]->OL , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
         $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PO',$req->nobukti,'',0,'DBPO');

      } else {
        $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 1, maxol = :maxol, OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username ,"maxol" => $maxOL[0]->OL , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
        $res = DB::connection('SML')->update("update dbpo set isOtorisasi2 = 1, maxol = :maxol , OtoUser2= :username , TglOto2 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username ,"maxol" => $maxOL[0]->OL , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
         $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PO',$req->nobukti,'',0,'DBPO');
         $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto2','PO',$req->nobukti,'',0,'DBPO');
      }

    } else {
      $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 1, maxol = :maxol , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "maxol" => $maxOL[0]->OL , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
      $res = DB::connection('SML')->update("update dbpo set isOtorisasi2 = 1, maxol = :maxol , OtoUser2= :username , TglOto2 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username ,"maxol" => $maxOL[0]->OL , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
       $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PO',$req->nobukti,'',0,'DBPO');
       $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto2','PO',$req->nobukti,'',0,'DBPO');
    }



return $res;
  }

  public function updateBatalOtorisasi (Request $req) {
    $username = \Auth::user()->username;
    $maxOL = DB::connection('SML')->select("select * from dbmenu where href = 'purchaseorder'");

    $cekOto = DB::connection('SML')->select("
select b.*,c.KodeJab,isnull(d.PlafonOtoPO , 0) PlafonOtoPO,
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
    where a.href ='purchaseorder' and b.USERID= :username ", ["nobukti" => $req->nobukti , "username" => $username]);
    $tanggal = date('Y-m-d H:i:s');
    if ( $cekOto[0]->nnet > $cekOto[0]->PlafonOtoPO ) {
      if($cekOto[0]->leveloto == 1) {

        $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", ["nobukti" => $req->nobukti]);
        $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PO',$req->nobukti,'',0,'DBPO');

      } else {
        $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", ["nobukti" => $req->nobukti]);
        $res = DB::connection('SML')->update("update dbpo set isOtorisasi2 = 0, maxol = -1 , OtoUser2= '' , TglOto2 = NULL where nobukti = :nobukti", ["nobukti" => $req->nobukti]);

         $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PO',$req->nobukti,'',0,'DBPO');
         $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto2','PO',$req->nobukti,'',0,'DBPO');


      }

    } else {
      $res = DB::connection('SML')->update("update dbpo set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL , isbatal = 1, Userbatal = :username , tglbatal = getDate() where nobukti = :nobukti", ["username" => $username, "nobukti" => $req->nobukti]);
      $res = DB::connection('SML')->update("update dbpo set isOtorisasi2 = 0, maxol = -1 , OtoUser2= '' , TglOto2 = NULL , isbatal = 1, Userbatal = :username , tglbatal = getDate() where nobukti = :nobukti", ["username" => $username, "nobukti" => $req->nobukti]);

       $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PO',$req->nobukti,'',0,'DBPO');
       $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto2','PO',$req->nobukti,'',0,'DBPO');


    }



  // $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto2','PO',$req->nobukti,'',0,'DBPO');
    return $res;
  }

  public function onChangeHeaderSP (Request $req) {
    $query = 'update dbso set ' . $req->field . ' = :value where nobukti = :nobukti';
    $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);

    $res2 = DB::connection('SML')->select('exec Sp_UpdateSO ?', [$req->bukti]);

    return $res;

  }

  public function spUpdatePO (Request $req) {
    $res = DB::connection('SML')->update('exec Sp_UpdatePO ?', [$req->nobukti]);

    return $res;
  }

  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $inisial = DB::connection("SML")->select('select PO from DBNOMOR');

    $values = [
        $inisial[0]->PO,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
        // $periode
        // $periode
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function listPelanggan (Request $req) {

    $listData = DB::connection('SML')->select("select Y.KodeCustSupp, Y.NamaCustSupp, Y.Alamat1 Alamat,
                       Z.namaKota,Y.PPN,Y.HARI,Y.PPN,Y.Kota ,Y.NPPH23  ,Y.NPPH22 NPPH21,Y.HARIHUTPIUT
                       from  DBCUSTSUPP Y
                       Left Outer Join Dbkota Z on Y.kota=Z.KodeKota
                       where isnull(Y.JENIS,0)=0
                     and Y.IsAktif=1
                       order by Y.KODECUSTSUPP");
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

    public function loadOutstandingPPL (Request $req) {

    $listData = DB::connection('SML')->select("declare @Tahun int, @Bulan int

              select @Tahun=2018, @Bulan=78

              SET NOCOUNT ON
              select  A.NoBukti+' '+right('00000000'+cast(A.urut as varchar(8)),8) KeyUrut,
              A.*
              from DBO.vwOutPPL A WITH(NOLOCK)
              where A.SisaPPL>0
              and A.pjasa=0
              order by A.Tanggal, A.NoBukti, A.Urut");
    return $listData;
  }

  public function listGudang (Request $req) {

    $listData = DB::connection('SML')->select("select KODEGDG, NAMA, Alamat from DBGUDANG");
    return $listData;
  }

  public function listPIC (Request $req) {

    $listData = DB::connection('SML')->select("select kodepic, nama from DBPICCUSTSUPP where KODECUSTSUPP =:kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listPWO (Request $req) {

    $listData = DB::connection('SML')->select("SELECT A.no_bukti,a.tanggal,a.supplier,d.NAMACUSTSUPP,
                        b.kode,c.NAMABRG,F.QNT qty,F.nmsat satuan
                        ,F.NOSAT NOsat ,B.harga
                        from penawaran_po A
                        left outer join detail_penawaran_po_barang B on A.id=
                        B.penawaran_id

                        left outer join DBBARANG c on b.kode=c.KODEBRG
                        left outer join DBCUSTSUPP d on A.supplier=d.KODECUSTSUPP
                        left outer join DBREFPRDET E on B.id_rfq=E.ID
                        left outer join DBPENAWARANSODET F on E.NOBUKTI=F.NoRPR
                        and E.URUT=F.UrutRPR
                        left outer join DBSOdet G on F.NOBUKTI=G.NOtawar and
                        f.URUT=G.urutTawar

                        where G.NOBUKTI= :noSo" , ["noSo" => $req->noSo]);
    return $listData;
  }

  public function listBarangFOC (Request $req)
  {
    $listData = DB::connection('SML')->select("select a.Kodebrg, a.NamaBrg,A.partNumber,B.NamaMerk
                                                from Dbbarang a
                                                Left Outer join dbmerk B on A.kodemerk=b.KodeMerk
                                                where a.isaktif=1");
    return $listData;
  }

  public function listBarangNonFOC1 (Request $req)
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeBrg, a.NamaBrg,a.PartNumber,a.NAMAMERK, a.Sat, a.NoSat, a.Isi, a.Qnt, a.QntPO, a.SisaPPL, a.NoBukti, a.Urut,a.tolerate,A.NosoCust
                                                from vwOutPPL a
                                                where Isjasa= 0 and NoBukti = :nobukti and a.sisaPPL > = 0
                                                order by a.KodeBrg, a.NoSat, a.NoBukti", ["nobukti" => $req->noBukti]);
    return $listData;
  }

    public function listBarangNonFOC1AllSO (Request $req)
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeBrg, a.NamaBrg,a.PartNumber,a.NAMAMERK, a.Sat, a.NoSat, a.Isi, a.Qnt, a.QntPO, a.SisaPPL, a.NoBukti, a.Urut,a.tolerate,A.NosoCust
                                                from vwOutPPL a
                                                where Isjasa= 0 and a.sisaPPL > = 0
                                                order by a.KodeBrg, a.NoSat, a.NoBukti");
    return $listData;
  }

  public function listBarangNonFOC2 (Request $req)
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeBrg, B.NamaBrg, a.Qnt,a.Qnt2, a.SATUAN Sat,A.Qnt-ISnull(C.Qnt,0) SisaPPL,
                        A.Qnt2- Case When a.NoSAT=2 Then ISnull(C.Qnt2,0) When a.NoSAT=3 Then ISnull(C.Qnt2,0) else ISnull(C.Qnt2,0)*a.ISI end  Sisa2PPL,
                        a.NoSat, a.Isi, a.NoBukti, a.Urut,0 Tolerate
                        , B.PartNumber
                         from DBSODET a
                        Left Outer Join Dbbarang B on A.kodebrg=B.Kodebrg
                        left Outer Join (select NoPPL,UrutPPL,Sum(case when nosat=1 then Qnt else Qnt*ISI End) - Sum(case when nosat=1 then QntBatal else QntBatal*ISI End) Qnt
                        ,Sum(case when Nosat=2 then Qnt
                        when NOSAT=3 then Qnt
                        when NOSAT=1 then Qnt/ISI  End )-
                        Sum(case when Nosat=2 then QntBatal
                        when NOSAT=3 then QntBatal
                        when NOSAT=1 then QntBatal/ISI  End ) Qnt2 from dbPOdet group by NoPPL,UrutPPL)
                        C on A.nobukti=C.noppl and A.urut=C.urutPPL
                         where  isnull(B.Isjasa,0)=0 and  nobukti= :noSo and IsCetakKitir=1
                        And A.Qnt-ISnull(C.Qnt,0)>0
                        ", ["noSo" => $req->noSo]);
    return $listData;
  }

  public function listNoSo (Request $req) {

    $listData = DB::connection('SML')->select("SELECT A.NOBUKTI,A1.Tanggal, A1.NoPesanan
                                                from DBSODET A
                                                Left Outer join DBSO A1 ON A.NOBUKTI=A1.NOBUKTI
                                                Left Outer Join DBBarang B on  A.KOdebrg=B.Kodebrg
                                                where A.iscetakkitir=1 and
                                                Cast(Case when Case when A1.IsOtorisasi1=1 then 1 else 0 end+
                                                Case when A1.IsOtorisasi2=1 then 1 else 0 end+
                                                Case when A1.IsOtorisasi3=1 then 1 else 0 end+
                                                Case when A1.IsOtorisasi4=1 then 1 else 0 end+
                                                Case when A1.IsOtorisasi5=1 then 1 else 0 end=A1.MaxOL then 0
                                                else 1
                                                end As Bit)=0  and A.Nobukti in (
                                                  select A.NOBUKTI
                                                  from DBSODET a
                                                  left Outer Join (select NoPPL,UrutPPL,Sum(Qnt)- Sum(QntBatal) Qnt from dbPOdet group by NoPPL,UrutPPL )
                                                  C on A.nobukti=C.noppl and A.urut=C.urutPPL
                                                  where   IsCetakKitir=1 And A.Qnt-ISnull(C.Qnt,0)>0)


                                                and CASE WHEN A1.TIPEBAYAR=0 AND  ISNULL(a1.unblock,0)=1 THEN 1
                                                WHEN A1.TIPEBAYAR=0 AND  ISNULL(a1.unblock,0)=0 THEN 0
                                                WHEN a1.TIPEBAYAR=1 THEN 1 END =1





                                                Group By A.NOBUKTI,A1.Tanggal, A1.NoPesanan
                                                order by A.NOBUKTI,A1.Tanggal");
    return $listData;
  }

  public function listLokasiPenerima (Request $req)
  {
    $listData = DB::connection('SML')->select("SELECT a.KodeCustsupp, a.NamaCustSupp NamaCust, A.Alamat, A.Telpon, A.Kota
                            from vwBrowsExpedisi A
                            where a.isaktif=1
                            Order by a.kodecustsupp");
    return $listData;
  }

  public function listBackOffice (Request $req) {

    $listData = DB::connection('SML')->select("select keynik, fullname from [user] order by keynik" );
    return $listData;
  }

  public function listBarang (Request $req) {
    // $harga = DB::connection('SML')->select("select * from dbHARGAJUAL where KODEBRG = :kodebarang" , ['kodebarang' => $req->kodebarang]);
//     select b.NAMAMERK ,  a.* from dbbarang a
// join DBMERK b on a.KodeMerk = b.KODEMERK
//  where a.KODEGRP = 'BJ' and a.pAgen = 1



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
    $listData = DB::connection('SML')->select("select hari,harihutpiut from dbcustsupp where KODECUSTSUPP = :kodepelanggan", ["kodepelanggan" => $req->kodepelanggan]);
    return $listData;
  }

  public function getSatuanBarang (Request $req) {
    return DB::connection('SML')->select("select SAT1, SAT2,SAT3 , ISI1,ISI2,ISI3 from dbbarang where kodebrg = :kodebarang", ["kodebarang" => $req->kodebarang]);

  }

  public function spAdd (Request $req) {
    $choice = $req->Choice;
    $jmlrecord = $req->Jmlrecord;
    $nobukti = $req->NoBukti;
     $xurut=0;


//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from dbpodet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from dbpodet where Nobukti = :nobukti', ['nobukti' => $nobukti]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else {
            // return 'mmm';
            $xurut = $req->Urut;
        }

    }else{
        // return 'ttt';
        $xurut=1;
    }
    // return ["asd" => $xurut] ;

    if ($choice =='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($choice,'PO',$nobukti,'',$xurut,'dbpodet');
      }





    if ($choice == "I" && $jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from dbpo where NOBUKTI = :nobukti',["nobukti" => $nobukti]);
        if ($check) {
          return 2;
      }
    }

      $values = [
        $choice, //Choice
        $nobukti, //NoBukti
        $req->NoUrut, //NoUrut
        $req->Tanggal, //Tanggal
        $req->TglJatuhTempo, //TglJatuhTempo
        $req->KodeSupp, //KodeSupp
        0, //Handling
        $req->KodeExp, //KodeExp
        $req->Keterangan, //Keterangan
        '', // FakturSupp
        $req->KodeVls, //KodeVls
        $req->Kurs, //Kurs
        $req->PPn, //PPn
        $req->TipeBayar, //TipeBayar
        $req->Hari, //Hari
        0, //TipeDisc
        0,//Disc
        0, //DiscRp
        $req->Urut, //Urut
        $req->KodeBrg, // KodeBrg
        $req->Qnt, //Qnt
        $req->NoSat, //NoSat
        $req->Satuan, //Satuan
        $req->Isi, //Isi
        $req->Harga, //Harga
        $req->DiscP, //DiscP
        $req->DiscTot, //DiscTot
        $req->NoPPL, //NoPPL
        0,  //IsClose
        0,  //IsCloseD
        '', //Catatan
        0, //IsExp
        0, //Tolerate
        $req->UrutPPL, //UrutPPL
        $req->Kodegdg, //Kodegdg
        $req->Discpdet2, //Discpdet2
        $req->Discpdet3, //Discpdet3
        0, //Discpdet4
        0, //Discpdet5
        1, //FlagTipe
        $req->NamaBrg, //Namabrg
        0, //IsJasa
        0, //pFirst
        $req->pFOC, //pFOC
        $req->Noso, //Noso
        $req->Jmlrecord, //Jmlrecord
        $req->NOPOCUST, //NOPOCUST
        \Auth::User()->username, //IdUser
        0,  //pJasa
        0,  //NPPH23
        '', //PERKIRAAN
        '', //SatX
        '', //COST
        '', //SUBCOST
        $req->TglKirim, //Tglkirim
        0, //PPH21
        $req->NOPNw, //NOPNw
        $req->UrutPNW, //UrutPNW
        $req->HrgAwal, //UrutPNW
        $req->KeteranganBarang //UrutPNW

      ];
      DB::connection('SML')->statement('exec sp_PO ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
      DB::connection('SML')->update('exec Sp_UpdatePO ?', [$nobukti]);

	if ($choice !='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData($choice,'PO',$nobukti,'',$xurut,'dbpodet');
      }
      return 1;
  }

  public function spCekHarga (Request $req) {
        $harga = DB::connection('SML')->select("Declare @Kodebrg varchar(15)
                                                Set @Kodebrg=:kodebarang
                                                select top 4 b.NOBUKTI,b.TANGGAL,a.KODEBRG,c.NAMABRG,
                                                a.SATUAN,a.QNT,b.KODEVLS,b.KURS,A.HARGA,b.DISCRP,A.NDPP,
                                                ROW_NUMBER() over(PARTITION By A.kodebrg Order by A.kodebrg) as LineNum
                                                ,A.DISCP,A.HrgNetto,A.DiscTot,D.NamaCustSupp
                                                from DBBELIDET A
                                                left outer join DBBELI b on a.NOBUKTI=b.NOBUKTI
                                                left outer join DBBARANG c on a.KODEBRG=c.KODEBRG
                                                Left Outer join dbcustsupp D on B.kodesupp=D.KodeCustSupp
                                                where A.KODEBRG=@Kodebrg
                                                order by b.TANGGAL desc" ,["kodebarang" => $req->kodebarang]);

      return $harga;
  }

  public function cekPoDet (Request $req) {
        $cekPoDet = DB::connection('SML')->select("SELECT * FROM DBPODET WHERE NOBUKTI = 'MGL/PO/00001/0625'");

      return $cekPoDet;
  }

   public function cekSatuanBarang (Request $req) {
        $cekSatuanBarang = DB::connection('SML')->select("select SAT1, ISI1, SAT2, ISI2, SAT3, ISI3 from DBBARANG where KODEBRG = :KodeBrg", ["KodeBrg"=>$req->KodeBrg]);

      return $cekSatuanBarang;
  }

  public function detailBarangAll (Request $req) {
    $barang = DB::connection('SML')->select(" select a.Kodebrg, a.NamaBrg,I.NamaSubGrp,A.PartNumber,J.NAMAMERK,a.ISI1, a.ISI2, a.ISI3,
                                              A.Sat1,A.Sat2 ,A.Sat3,A.pPPN,Isnull(A.QntMin,0) QntMin ,a.Hrg1_1 , a.Hrg2_1, a.Hrg3_1
                                              from DBbarang a
                                              left OUter JOin DbSubgroup I on A.KodeSubGRp=I.KodeSUbgrp and A.KodeHdGrp=i.KodeHDGrp
                                              Left Outer join DbMerk J on A.KodeMerk=J.KodeMerk
                                              where a.isaktif=1 and A.KodeGrp in ('BJ','JS')
                                              and A.KodeBrg = :kodebrg
                                              and isnull(A.Isaktif,0)=1
                                              order by a.Kodebrg ASC" ,
                                              ["kodebrg" => $req->kodebrg] );

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
                                            order by a.TANGGAL desc  ",
                                            ["kodebarang" => $req->kodebrg , "nosat" => $req->nosat]);

    return ["barang" => $barang , "harga" => $harga];
  }

  public function getDetail (Request $req) {
    $nobukti = $req->nobukti;

    $list = DB::connection('SML')->select("DECLARE @NoBukti varchar(30)

select 	@NoBukti= :nobukti
Select 	b.nosat,E.ISI1,E.ISI2,E.ISI3,E.SAT1,E.SAT2,E.SAT3,A.NoBukti, A.NoUrut, A.Tanggal, A.TglJatuhTempo, A.KodeSupp, C.NamaCustSupp, C.Alamat1, C.Alamat2, C.Kota,
        C.Alamat1+Char(13)+C.Alamat2+Char(13)+C.kota Alamat,
	A.Handling, A.Keterangan, A.FakturSupp,IsExp,
	A.KodeVls, D.NamaVls, A.Kurs, A.PPN, A.TipeBayar, A.Hari, A.Disc, B.KeteranganBarang, B.Hrgawal,
	B.Urut, B.KodeBrg, case when B.NAMABRG='' then E.NAMABRG else B.NamaBrg End NamaBrg, B.Satuan, B.Qnt, B.Nosat, B.Isi,
        B.Harga, B.DISCP, B.DISCTOT, B.NoPPL, B.UrutPPL, A.IsClose,B.IsClose IsCloseD,
        case when A.Kurs=1 then 0.0 else B.SubTotal end TotalUSD,a.KodeExp,F.namaCustSupp NamaExp,
	round(B.SubTotal*A.Kurs,2) TotalIDR, round(B.NDPP*A.Kurs,2) NDPP,
        round(B.NPPN*A.Kurs,2) NPPN,isnull(B.Tolerate,0) Tolerate,
	B.BYAngkut Beban,
	round(B.SubTotal*A.Kurs,2) + B.BYAngkut Total,
        H.TotDiskon, H.TotDPP, H.TotPPN, H.TotNet,
        A.Kodegdg, I.Nama NamaGDG, I.Alamat ALamatGdg,
        x.TotalX,dbo.terbilang(x.totalx) Terbilang,B.Discp2,B.Discp3,B.Discp4,B.Discp5,
        A.PPN PPNTrans,isnull(B.Tolerate,0) ToleratePO,Isnull(B.Isjasa,0) Isjasa,
        B.Discp DiscP1,x1.NosoCust,Isnull(B.pFoc,0) PFOC,A.NOSO,M1.Nopesanan,A.npph23,A.perkiraan,m2.Keterangan Nmperkiraan,B.SatX
        ,B.Cost,b.subcost, Cs.NamaCost, SCs.NamaSubCost,A.TglKirim ,H.TOtSubtotalRP ,A.NPPH21,'' NoPNW,0 UrutPNW,A.FlagTipe,
        Stk.SaldoQnt,stk.SaldoRP
        From dbPO A
Left Outer join dbPODet B on B.NoBukti=a.NoBukti
Left Outer Join dbCustSupp C on c.KodeCustSupp=a.KodeSupp
Left Outer join dbValas D on D.KodeVls=A.KodeVls
Left Outer join dbBarang E on E.KodeBrg=B.KodeBrg
Left Outer join vwBrowsExpedisi F On F.KodeCustSupp=A.KodeExp
Left Outer Join vwMasterPO H on H.NoBukti=A.NoBukti
Left Outer Join (select KodeBrg,NoBukti,Min(Tanggal)Tanggal from dbKirimDet group by KodeBrg,NoBukti) J On J.KodeBrg=B.KodeBrg and J.NoBukti=B.NoBukti
left outer join (select A.Nobukti, sum(round(B.SubTotal*A.Kurs,2) + B.BYAngkut)  totalx from Dbpo A
                        left outer join dbPODet B on A.nobukti = B.nobukti group By A.NObukti) X on A.nobukti = x.nobukti
Left Outer join dbgudang I on I.Kodegdg=A.kodegdg
Left Outer Join DBPPLDet X1 on B.Noppl=X1.Nobukti and B.UrutPPL=X1.Urut
Left Outer join DbSo M1 on A.Noso=M1.nobukti
Left Outer join dbperkiraan M2 on A.perkiraan=M2.Perkiraan
left outer join dbCost Cs on Cs.KodeCost=B.Cost
left outer join vwSubCost SCs on SCs.KodeCost=b.Cost and SCs.KodeSubCost=b.SubCost
left outer join (select kodebrg,BULAN,TAHUN,SUM(isNULL(SALDOQNT,0)) SaldoQnt,SUM(isnull(SALDORP,0)) SaldoRP
				 from  DBSTOCKBRG
				 where KODEGDG<>'GTC'
				 group By KODEBRG,BULAN,TAHUN) Stk on B.KODEBRG=stk.KODEBRG and stk.BULAN=MONTH(A.TANGGAL) and Stk.TAHUN=YEAR(a.TANGGAL)
where	A.NoBukti=@NoBukti
order by B.Urut", ["nobukti" => $nobukti]);

    return [
      "list" => $list
    ];
  }

  public function spCetak (Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC Sp_CetakPO ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }


 public function CheckHargaAdd(Request $req) {
  $noso = $req->input('Noso');
  $kodebrg = $req->input('KodeBrg');
  $harga = $req->input('xharga');
  $nosat = $req->input('NoSat');
  $flagharga='';

// return ['controller =============',$noso,$kodebrg,$harga,$nosat];
  $checkharga= DB::connection('SML')->select("
    declare @noSO varchaR(30),@KODEBRG VARCHAR(30),@harga numeric(18,2),@nosat int
select @noSO=:noso ,@KODEBRG=:kodebrg,@harga =:harga ,@nosat=:nosat

select @harga hargapo, XTABLE.hrgmin hrgminso,
XTABLE.HRGMIN + (XTABLE.HRGMIN * 1.00 ) hrgmaxso,
 case when @harga > XTABLE.HRGMIN then 'harga beli lebih besar dari pada harga so'
  when @harga >= XTABLE.HRGMIN + (XTABLE.HRGMIN * 1.00 ) then 'Margin  Lebih dari 100%'
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

END HRGMIN
from DBSODET A
Left OUter join DBSO b on A.nobukti=B.NOBUKTI
Left Outer join DBBARANG C on A.KODEBRG=C.KODEBRG
Where A.NOBUKTI=@noSO and A.KODEBRG=@Kodebrg
--and A.kodegdg<>'G06'
order by B.TANGGAL Desc
) xtable

    ", ["noso"=>$noso,"kodebrg"=>$kodebrg,"harga"=>$harga,"nosat"=>$nosat] );

    //  if ($checkharga->isEmpty()){
    if(empty($checkharga)){
      $flagharga = 'lanjut';

     } else {
        // if ($checkharga[0]->ket == 'Margin  Lebih dari 100%') {
        //     $flagharga = 'hargamax';
        // } else if ($checkharga[0]->ket == 'harga lebih kecil dari pada harga min SO') {
        //     $flagharga = 'hargamin';
        // }
         $flagharga = $checkharga[0]->ket;
     }

     return [$flagharga];
   return $flagharga;

  }






}
