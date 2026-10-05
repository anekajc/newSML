<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewAksesMenu;
use App\Models\DBFLMENU;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\vwOUtPOWMS;
use App\Models\VWtampilbeli;
use App\Models\vwBrowsOutBeli;
use App\Models\vwMasterBeli;
use App\Models\NEWDBBELI;

class InvoicePembelianController extends Controller 
{


  public function index (Request $req) {
    $kodemenu = '030401';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path);
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

   $tempoutstanding = DB::connection("SML")->select("
   declare @Tahun int, @Bulan int, @Periode Varchar(30)
   select @Tahun=:tahun, @Bulan=:bulan
   select top 20 A.NoBukti, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
   	A.NoPO, A.KodeGdg,A.PPN
   	,CASE WHEN  A.PPN=0 THEN 'NONE'
   	      WHEN A.PPN=1 THEN 'EXCLUDE'
   	      WHEN A.PPN=2 THEN 'INCLUDE' END KETPPN,isNULL(C.NuangMuka,0)-ISNULL(b.DPP,0) sISANUM ,
                 A.TNDPP,A.TNNET,A.TNPPN,A.TSUBTOTAL
   from vwBrowsOutBeli A
   left outer join DBUMJUAL b on a.NOPO=b.NOSO
   left outer join DBBELI C ON A.NOBUKTI=C.NOBUKTI
   where ( (YEAR(A.TANGGAL)<=@Tahun and MONTH(A.TANGGAL)<=@Bulan))
           and A.FlagTipe<>9
        and a.TNDPP is not null  and year(C.tanggal)>2017

    and Isnull(pCLoseInv,0)=0


   group by A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
   	A.NoPO, A.KodeGdg,A.PPN,b.DPP,C.NuangMuka,b.DPP  ,A.TNDPP,A.TNNET,A.TNPPN,A.TSUBTOTAL
   order by A.NOBUKTI


   " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);


    $tampilinvoicebeli = DB::connection("SML")->select("
    declare @Tahun int, @Bulan int

select @Tahun=:tahun , @Bulan=:bulan

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota,
	A.NoPO,
	Sum(A.NDPPRp) TotDPPRp, Sum(A.NPPNRp) TotPPNRp, Sum(A.NNETRp) TotNetRp,
	A.IDUser,
	A.IsOtorisasi1, A.OtoUser1,A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
    A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
    A.NoInvoice
    , case when YEAR(A.TglInvoice)=1899 then '' else CAST(DATEPART(DD,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglInvoice) AS VARCHAR(4)) End TglInvoice
    , A.NoFakturPajak
    ,case when YEAR(A.TglFakturPajak)=1899 then '' else CAST(DATEPART(DD,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglFakturPajak) AS VARCHAR(4)) End TglFakturPajak
    , A.NoBuktiPotong
    ,case when YEAR(A.TglBuktiPotong)=1899 then '' else CAST(DATEPART(DD,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglBuktiPotong) AS VARCHAR(4)) End TglBuktiPotong,
    A.BlnMasaPajak, A.ThnMasaPajak
From vwTransInvoice A
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
and A.FlagTipe<>9 and A.IsOtorisasi1 = 0


group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp,
	A.NoPO, A.NamaKota,
	A.IDUser,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
    A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
    A.NoInvoice, A.TglInvoice, A.NoFakturPajak, A.TglFakturPajak, A.NoBuktiPotong, A.TglBuktiPotong,
    A.BlnMasaPajak, A.ThnMasaPajak
order by A.NOBUKTI
" , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);

    $tampilinvoicebeliOto = DB::connection("SML")->select("
    declare @Tahun int, @Bulan int

select @Tahun=:tahun , @Bulan=:bulan

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota,
	A.NoPO,
	Sum(A.NDPPRp) TotDPPRp, Sum(A.NPPNRp) TotPPNRp, Sum(A.NNETRp) TotNetRp,
	A.IDUser,
	A.IsOtorisasi1, A.OtoUser1,A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
    A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
    A.NoInvoice
    , case when YEAR(A.TglInvoice)=1899 then '' else CAST(DATEPART(DD,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglInvoice) AS VARCHAR(4)) End TglInvoice
    , A.NoFakturPajak
    ,case when YEAR(A.TglFakturPajak)=1899 then '' else CAST(DATEPART(DD,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglFakturPajak) AS VARCHAR(4)) End TglFakturPajak
    , A.NoBuktiPotong
    ,case when YEAR(A.TglBuktiPotong)=1899 then '' else CAST(DATEPART(DD,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglBuktiPotong) AS VARCHAR(4)) End TglBuktiPotong,
    A.BlnMasaPajak, A.ThnMasaPajak
From vwTransInvoice A
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
and A.FlagTipe<>9 and A.IsOtorisasi1 = 1


group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp,
	A.NoPO, A.NamaKota,
	A.IDUser,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
    A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
    A.NoInvoice, A.TglInvoice, A.NoFakturPajak, A.TglFakturPajak, A.NoBuktiPotong, A.TglBuktiPotong,
    A.BlnMasaPajak, A.ThnMasaPajak
order by A.NOBUKTI
" , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);

    // $gudang = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);

    return view('purchasing.invoicepembelian' , [
      // "title" => "Home",
      // "menu" => $menu,
      "tempoutstanding" =>$tempoutstanding,
      // "ptempoutstanding" =>$ptempoutstanding,
      "tampilinvoicebeli" =>$tampilinvoicebeli,
      "tampilinvoicebeliOto" =>$tampilinvoicebeliOto,
      "periode" => $periode,
      "menul0" => $menul0,
      // "po" => $po,
      // "poGroup" => $poGroup,
      // "tempPo" => $tempPo,
      // "tempPembelian" => $tempPembelian,
      // "tesMax" => $tesMax
      "gudang" => [],
      // "pembelian" => $pembelian,
      // "tempPembelian1" => $tempPembelian1,
      "akses" => $akses
    ]);


  }



  public function getBeliDet (Request $req) {
    // $periode = NewPeriode::where('user_id' , \Auth::id())->first();
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $pembelian = VWtampilbeli::all()->where('NoBukti', $req->input('NoBukti'))->sortBy('NoBukti')->groupBy('NoBukti');
    $tempoutstanding = [];
    foreach ($pembelian as $p) {
      array_push($tempoutstanding, $p);
    }
    return $tempoutstanding;
  }

  public function getAkses () {
    $kodemenu = '03007';
    // $akses = NewAksesMenu::where('USERID', \Auth::id())-> where('L1', $kodemenu)->first();
      $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    return $akses;
  }

  public function getAllPO () {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

       $tampilinvoicebeli = DB::connection("SML")->select("
       declare @Tahun int, @Bulan int

   select @Tahun=:tahun , @Bulan=:bulan

   Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota,
   	A.NoPO,
   	Sum(A.NDPPRp) TotDPPRp, Sum(A.NPPNRp) TotPPNRp, Sum(A.NNETRp) TotNetRp,
   	A.IDUser,
   	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
   	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
   	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
       A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
       A.NoInvoice
       , case when YEAR(A.TglInvoice)=1899 then '' else CAST(DATEPART(DD,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglInvoice) AS VARCHAR(4)) End TglInvoice
       , A.NoFakturPajak
       ,case when YEAR(A.TglFakturPajak)=1899 then '' else CAST(DATEPART(DD,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglFakturPajak) AS VARCHAR(4)) End TglFakturPajak
       , A.NoBuktiPotong
       ,case when YEAR(A.TglBuktiPotong)=1899 then '' else CAST(DATEPART(DD,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglBuktiPotong) AS VARCHAR(4)) End TglBuktiPotong,
       A.BlnMasaPajak, A.ThnMasaPajak
   From vwTransInvoice A
   where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
   and A.FlagTipe<>9 and A.IsOtorisasi1 = 0


   group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp,
   	A.NoPO, A.NamaKota, 
   	A.IDUser,
   	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
   	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
   	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
       A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
       A.NoInvoice, A.TglInvoice, A.NoFakturPajak, A.TglFakturPajak, A.NoBuktiPotong, A.TglBuktiPotong,
       A.BlnMasaPajak, A.ThnMasaPajak
   order by A.NOBUKTI


       " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);

  $tampilinvoicebeliOto = DB::connection("SML")->select("
       declare @Tahun int, @Bulan int

   select @Tahun=:tahun , @Bulan=:bulan

   Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota,
   	A.NoPO,
   	Sum(A.NDPPRp) TotDPPRp, Sum(A.NPPNRp) TotPPNRp, Sum(A.NNETRp) TotNetRp,
   	A.IDUser,
   	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
   	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
   	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
       A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
       A.NoInvoice
       , case when YEAR(A.TglInvoice)=1899 then '' else CAST(DATEPART(DD,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglInvoice) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglInvoice) AS VARCHAR(4)) End TglInvoice
       , A.NoFakturPajak
       ,case when YEAR(A.TglFakturPajak)=1899 then '' else CAST(DATEPART(DD,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglFakturPajak) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglFakturPajak) AS VARCHAR(4)) End TglFakturPajak
       , A.NoBuktiPotong
       ,case when YEAR(A.TglBuktiPotong)=1899 then '' else CAST(DATEPART(DD,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(MM,A.TglBuktiPotong) AS VARCHAR(2))+'/'+CAST(DATEPART(YYYY,A.TglBuktiPotong) AS VARCHAR(4)) End TglBuktiPotong,
       A.BlnMasaPajak, A.ThnMasaPajak
   From vwTransInvoice A
   where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
   and A.FlagTipe<>9 and A.IsOtorisasi1 = 1


   group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp,
   	A.NoPO, A.NamaKota, 
   	A.IDUser,
   	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
   	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
   	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
       A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
       A.NoInvoice, A.TglInvoice, A.NoFakturPajak, A.TglFakturPajak, A.NoBuktiPotong, A.TglBuktiPotong,
       A.BlnMasaPajak, A.ThnMasaPajak
   order by A.NOBUKTI


       " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);

    return [
      "tampilinvoicebeli" => $tampilinvoicebeli,
      "tampilinvoicebeliOto" => $tampilinvoicebeliOto
    ];
  }

  public function getAllPembelian () {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // $pembelian = vwMasterBeli::all()->where('pJasa',0)->where('IsOtorisasi1', 1)->where('BULAN',$periode->bulan )->where('TAHUN', $periode->tahun)->sortBy('NoBukti')->groupBy('NoBukti');
    // $tempPembelian1 = [];
    // foreach ($pembelian as $p) {
    //   array_push($tempPembelian1, $p);
    // }
    //
    // return $tempPembelian1;


       $tempoutstanding = DB::connection("SML")->select("
       declare @Tahun int, @Bulan int, @Periode Varchar(30)
       select @Tahun=:tahun, @Bulan=:bulan
       select top 20 A.NoBukti, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
       	A.NoPO, A.KodeGdg,A.PPN
       	,CASE WHEN  A.PPN=0 THEN 'NONE'
       	      WHEN A.PPN=1 THEN 'EXCLUDE'
       	      WHEN A.PPN=2 THEN 'INCLUDE' END KETPPN,isNULL(C.NuangMuka,0)-ISNULL(b.DPP,0) sISANUM ,
                     A.TNDPP,A.TNNET,A.TNPPN,A.TSUBTOTAL
       from vwBrowsOutBeli A
       left outer join DBUMJUAL b on a.NOPO=b.NOSO
       left outer join DBBELI C ON A.NOBUKTI=C.NOBUKTI
       where ( (YEAR(A.TANGGAL)<=@Tahun and MONTH(A.TANGGAL)<=@Bulan))
               and A.FlagTipe<>9
            and a.TNDPP is not null  and year(C.tanggal)>2017

        and Isnull(pCLoseInv,0)=0


       group by A.NOBUKTI, A.NoUrut, A.TANGGAL, A.KODECUSTSUPP, A.NAMACUSTSUPP,
       	A.NoPO, A.KodeGdg,A.PPN,b.DPP,C.NuangMuka,b.DPP  ,A.TNDPP,A.TNNET,A.TNPPN,A.TSUBTOTAL
       order by A.NOBUKTI


       " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan]);


    return $tempoutstanding;

  }



  public function spOtorisasi1 (Request $req) {
      $username = \Auth::user()->username;
      $nobukti =  $req->nobukti;
      $otorisasi = $req->otorisasi;

      if ($otorisasi == 0 ) {
        $username = '';
        $tanggal = null;
      }


      $update = DB::connection('SML')->update("update dbinvoice set IsOtorisasi1 = :otorisasi , OtoUser1 = :username , TglOto1 = getDate() , MaxOL = 1 where nobukti = :nobukti", ['otorisasi' => $otorisasi, 'username' => $username,  'nobukti' => $nobukti ] );
      return $update;

    }


    public function spUnOtorisasi1 (Request $req) {
        $username = \Auth::user()->username;
        $nobukti =  $req->nobukti;
        $otorisasi = $req->otorisasi;

        if ($otorisasi == 0 ) {
          $username = '';
          $tanggal = null;
        }


        $update = DB::connection('SML')->update("update dbinvoice set IsOtorisasi1 = 0 , OtoUser1 = '' , TglOto1 = NULL, MaxOL = -1 where nobukti = :nobukti", [  'nobukti' => $nobukti ] );
        return $update;

      }

    public function deleteinvoice (Request $req) {
      
        $nobukti =  $req->nobukti;
       
        $update = DB::connection('SML')->update("
        
        declare @NoBukti varchar(30)
        set @NoBukti= :nobukti
        
        
        delete dbinvoicedet where nobukti = @NoBukti
        
        
         if not exists( select NoBukti from dbinvoicedet where NoBukti=@NoBukti)
        begin
        delete dbinvoice where NoBukti=@NoBukti
      end
        
        ", [  'nobukti' => $nobukti ] );
        return $update;

      }  



      public function spedit (Request $req) {
        $username = \Auth::user()->username;

          $nobukti =  $req->input('nobukti');//1
          $tanggal=  $req->input('tanggal');  //1
          $tglfpajak =  $req->input('tglfpajak');
          $tglbpotong =  $req->input('tglbpotong');
          $tglinvoice = $req->input('tglinvoice');
          $nopajak = $req->input('nopajak');
          $nobuktipotong=  $req->input('nobuktipotong');
          $noinvoice = $req->input('noinvoice');





          $update = DB::connection('SML')->update("update dbinvoice set tanggal =:tanggal ,
          Tglfakturpajak =:tglfpajak, nofakturpajak =:nopajak,nobuktipotong=:nobuktipotong,
          tglbuktipotong=:tglbuktipotong,noinvoice=:noinvoice,tglinvoice=:tglinvoice where nobukti = :nobukti",
        ['tanggal'=> $tanggal,'tglfpajak'=>$tglfpajak,'nopajak'=>$nopajak,'nobuktipotong'=>$nobuktipotong,
          'tglbuktipotong'=>$tglbpotong,'noinvoice'=>$noinvoice,'tglinvoice'=>$tglinvoice, 'nobukti' => $nobukti ] );
          return $update;

        }


public function getDetailPembelian (Request $req) {
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

  $tampilinvoicebeli = DB::connection("SML")->select("
  declare @Tahun int, @Bulan int

select @Tahun=:tahun , @Bulan=:bulan

Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp, A.NamaKota,
A.NoPO,
A.NDPPRp TotDPPRp, A.NPPNRp TotPPNRp, A.NNETRp TotNetRp,
A.IDUser,
A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi,
  A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
  A.NoInvoice
  ,  A.TglInvoice
  , A.NoFakturPajak
  ,A.TglFakturPajak
  , A.NoBuktiPotong
  ,A.TglBuktiPotong,
  A.BlnMasaPajak, A.ThnMasaPajak,a.myppn, A.NoBeli,A.tglbeli,A.TglPO,
  A.KodeGdg,A.kodebrg KODEBARANG,A.namabrg NAMABARANG,A.QNT,A.SATUAN,A.HARGA,A.NNET
From vwTransInvoice A
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan
and A.FlagTipe<>9
and a.noBukti =:nobukti

/*
group by A.NoBukti, A.NoUrut, A.Tanggal, A.KODECUSTSUPP, A.NamaCustSupp,
A.NoPO, A.NamaKota,
A.IDUser,
A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.NeedOtorisasi, 
  A.ISBATAL, A.USERBATAL, A.TglBatal, A.TipePPn,
  A.NoInvoice, A.TglInvoice, A.NoFakturPajak, A.TglFakturPajak, A.NoBuktiPotong, A.TglBuktiPotong,
  A.BlnMasaPajak, A.ThnMasaPajak,a.myppn*/
order by A.NOBUKTI


  " , [ "tahun" =>$periode->tahun , "bulan" => $periode->bulan, "nobukti" => $req->NoBukti]);

  return $tampilinvoicebeli;
}


public function getDetailPO (Request $req) {

$tempBeli = DB::connection("SML")->select("

select distinct 0 isterima,A.NoBukti, A.Tanggal, A.NoPO, S.Tanggal TglPO,a.NAMACUSTSUPP,case when a.PPN= 0 then 'NONE' when a.PPN=1 then 'EXCLUDE' when a.PPN=2 then 'INCLUDE' end MyPPN
from vwBrowsOutBeli A
left outer join dbPO S on S.NoBukti=A.NOPO
where  A.NoPO = :nobukti
and isnull(A.QntSisa,0)>0
order by A.Tanggal, A.NoBukti" ,["nobukti"=>  $req->input('NoBukti')] );

   return $tempBeli;
}

public function spBeliGudang (Request $req) {
$username = \Auth::user()->username;
$tempData = $req->input('tempData');
foreach ($tempData as $d) {
      $values = [
    $req->input('reqNobukti'), //1
    $req->input('reqNourut'),  //1
    $req->input('reqTanggal'),  //1
    $req->input('reqNopo'),   //1
    $req->input('reqTglpajak'),
    $req->input('reqTglbpotong'),
    $req->input('reqTglinvoice'),
    $req->input('reqNopajak'),
    $req->input('reqNobpotong'),
    $req->input('reqNoinvoice'),
    $username,
    $d['NoBukti']


  ];

  DB::connection('SML')->statement('exec SP_INVB ?,?,?,?,?,?,?,?,?,?,?,?', $values);


    }
  
  
   1;


}



public function getNoBukti (Request $req) {
 
  $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
  $inisial = DB::connection("SML")->select('select BPL from DBNOMOR');
  $username = \Auth::user()->username;
  $values = [
      $inisial[0]->BPL,
      $periode->bulan,
      $periode->tahun,
      $username
  ];
  $resnobukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    // $nobuktib =  $resnobukti[0]->Nobukti;
    // $nourutb =  $resnobukti[0]->Nourut;

    return $resnobukti;


}

public function addDBBeli (Request $req) {
  // try {

        $data = $req->input('data');
        $suratJalan = $req->input('suratJalan');
        $noKend = $req->input('noKend');
        $noPO = $req->input('noPO');
        // $date = date("Y-m-d H:i:s");
        $date = $req->input('inputTanggal');
        $gudang = $req->input('gudang');
        $noBukti = $req->input('noBukti');
        $noUrut = $req->input('noUrut');
        $username = \Auth::user()->username;
        $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


        $check = DB::connection('SML')->select('select * from dbBeli where NOBUKTI = :nobukti',["nobukti" => $noBukti]);
        if ($check) {
           2;
        }
        // delete	TempOutstandingPO where IDUser=@IDUser
        DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser',['idUser' => $username ]);
        foreach ($data as $d) {
          $values = [$username,$noPO,$periode->tahun,$periode->bulan, 0,$d['inputQntTerima'], 1, $d['Urut']];
          DB::connection("SML")->statement('exec sp_RefreshTempOutstandingPOweb ?,?,?,?,?,?,?,?',$values);

        }

          $tempValues = [$noBukti,$noUrut,$date,$gudang, $noPO, $suratJalan, $noKend, 0, 0, $username];

        DB::connection('SML')->statement('exec sp_InsertOutstandingPO ?,?,?,?,?,?,?,?,?,?', $tempValues);
         1;

}




}
