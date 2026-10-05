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

class InvoiceReturBeliController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '030503';
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    // $tulisan = $req->get('tulisan');
    // $menumenus = $req->get('menul0');


    // $users = DB::connection("SML")->select('select * from new_users');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    // $listData = DB::connection('SML')->select('SELECT * FROM DBMERK');


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);


$tempOutstanding = DB::connection("SML")->select("
declare @Tahun int, @Bulan int

select @Tahun=:tahun, @Bulan=:bulan

Select  A.NoBukti, a.Tanggal, A.KodeSupp, A.NamaCustSupp, A.NoBeli, A.Handling, A.FakturSupp,
        A.TotSubTotal, A.TotDiskon, A.TotTotal, A.TotDPP, A.TotPPN, A.TotNet,
        A.TotSubTotalRp, A.TotDiskonRp, A.TotTotalRp, A.TotDPPRp, A.TotPPNRp, A.TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.MAXOL,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
From vwMasterRBeli A
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan   and isnull(A.isbatal,0)=0 and IsOtorisasi1 = 0
order by A.NoBukti

",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

$tempOutstanding2 = DB::connection("SML")->select("
declare @Tahun int, @Bulan int

select @Tahun=:tahun, @Bulan=:bulan

Select  A.NoBukti, a.Tanggal, A.KodeSupp, A.NamaCustSupp, A.NoBeli, A.Handling, A.FakturSupp,
        A.TotSubTotal, A.TotDiskon, A.TotTotal, A.TotDPP, A.TotPPN, A.TotNet,
        A.TotSubTotalRp, A.TotDiskonRp, A.TotTotalRp, A.TotDPPRp, A.TotPPNRp, A.TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.MAXOL,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
From vwMasterRBeli A
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan and isnull(A.isbatal,0)=0 and IsOtorisasi1 = 1
order by A.NoBukti

",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);


    return view('purchasing.invoicereturbeli' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "akses" => $akses
    ]);

  }

  public function loadAll () {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

  $tempOutstanding = DB::connection("SML")->select("
declare @Tahun int, @Bulan int

select @Tahun=:tahun, @Bulan=:bulan

Select  A.NoBukti, a.Tanggal, A.KodeSupp, A.NamaCustSupp, A.NoBeli, A.Handling, A.FakturSupp,
        A.TotSubTotal, A.TotDiskon, A.TotTotal, A.TotDPP, A.TotPPN, A.TotNet,
        A.TotSubTotalRp, A.TotDiskonRp, A.TotTotalRp, A.TotDPPRp, A.TotPPNRp, A.TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.MAXOL,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
From vwMasterRBeli A
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan   and isnull(A.isbatal,0)=0 and IsOtorisasi1 = 0
order by A.NoBukti



",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);

$tempOutstanding2 = DB::connection("SML")->select("
declare @Tahun int, @Bulan int

select @Tahun=:tahun, @Bulan=:bulan

Select  A.NoBukti, a.Tanggal, A.KodeSupp, A.NamaCustSupp, A.NoBeli, A.Handling, A.FakturSupp,
        A.TotSubTotal, A.TotDiskon, A.TotTotal, A.TotDPP, A.TotPPN, A.TotNet,
        A.TotSubTotalRp, A.TotDiskonRp, A.TotTotalRp, A.TotDPPRp, A.TotPPNRp, A.TotNetRp,
        A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2,
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.MAXOL,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
From vwMasterRBeli A
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan   and isnull(A.isbatal,0)=0 and IsOtorisasi1 = 1
order by A.NoBukti



",["bulan" => $periode->bulan , "tahun" =>$periode->tahun]);




    return ["tempOutstanding" => $tempOutstanding,"tempOutstanding2" => $tempOutstanding2];
  }

public function onChangeHeader (Request $req) {
  $query = 'update dbrbeli set ' . $req->field . ' = :value where nobukti = :nobukti';
  $query2 = 'update dbrbelidet set ' . $req->field . ' = :value where nobukti = :nobukti';
  
  $res = DB::connection('SML')->update($query, ["value" => $req->value , "nobukti" => $req->nobukti]);
  $res2 = DB::connection('SML')->update($query2, ["value" => $req->value , "nobukti" => $req->nobukti]);
  
  return response()->json([
    'res' => $res,
    'res2' => $res2
  ]);
}


  public function getDetail (Request $req ) {



        $tempOutstanding = DB::connection("SML")->select("
        declare @NoBukti varchar(30)

  select 	@NoBukti= :nobukti

  Select 	A.NoBukti, A.NoUrut, A.Tanggal, A.TglJatuhTempo, A.KodeSupp, C.NamaCustSupp, C.Alamat1, C.Alamat2, C.Kota,
          C.Alamat1+Char(13)+C.Alamat2+Char(13)+C.kota Alamat, B.NoPBL NoBeli,
  	B.KodeGdg, F.Nama NamaGdg, A.Handling, A.KodeExp, G.NamaExp, A.Keterangan, A.FakturSupp,
  	A.KodeVls, D.NamaVls, A.Kurs, A.PPN, A.TipeBayar, A.Hari, A.TipeDisc, A.Disc, A.DiscRp,
  	B.Urut, B.KodeBrg,case when B.nopbl='-' then E.namaBrg Else E.namaBrg End NamaBrg
          , case when b.nosat=1 then E.Sat1 when b.nosat=2 then e.sat2 when b.nosat=3 then e.sat3 end Satuan,
          B.NoSat, B.Isi,
          B.Qnt, B.Harga,B.qnt1,B.qnt2,E.sat1,E.sat2,
  	B.DiscP, B.DiscTot,b.NDPP,b.NPPN,b.NNET,
  	B.UrutPBL,
  	B.BYANGKUT Beban, b.NoPBL,
          H.TotDiskon, H.TotDPP, H.TotPPN, H.TotNet,
          Case when A.Kodevls='IDR' then B.SubTotalRp  else B.SubTotal end Total,
          Case when A.Kodevls='IDR' then H.TotDiskonRp  else H.TotDiskon end Diskon,
          Case when A.Kodevls='IDR' then H.TotDPPRp  else H.TotDPP end TotalDPP,
          Case when A.Kodevls='IDR' then H.TotPPnRp  else H.TotPPn end TotalPPn,
          Case when A.Kodevls='IDR' then H.TotNetRp  else H.TotNet end TotalNetto ,
          B.Discp2,B.Discp3,B.Discp4,B.Discp5,ISnull(A.FlagTipe,0) FlagTipe,Isnull(B.IsJasa,0) ISJasa,B.nilaiPPN,
          case when a.ppn=0 then 'NONE' when a.ppn=1 then 'EXCLUDE' WHEN A.PPN=2 THEN 'INCLUDE' END MyPPN
          ,CASE WHEN A.TipeBayar=0 THEN 'TUNAI' WHEN A.TIPEBAYAR=1 THEN 'KREDIT' END MYBAYAR
  From dbRBeli A
  Left Outer join dbRBeliDet B on B.NoBukti=A.NoBukti
  Left Outer Join dbCustSupp C on C.KodeCustSupp=A.KodeSupp
  Left Outer join dbValas D on D.KodeVls=A.KodeVls
  Left Outer join dbBarang E on E.KodeBrg=B.KodeBrg
  Left Outer Join dbGudang F on F.KodeGdg=B.KodeGdg
  Left Outer Join dbExpedisi G on G.KodeExp=A.KodeExp
  Left Outer Join vwMasterRBeli H on H.NoBukti=A.NoBukti
  Left Outer Join vwRpDetBeli I on I.NoBukti=A.NoBukti
  left outer join DBBELIDET J on J.NOBUKTI = B.NOPBL and J.URUT = B.URUTPBL
  where	A.NoBukti=@NoBukti
  order by B.Urut
" , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }


  public function getNoBukti (Request $req) {

    $username = \Auth::user()->username;
    $periode = DB::connection("SML")->select('select TOP 1 * from DBPERIODE where user_id = :username ' , ["username" => $username]);
    $inisial = DB::connection("SML")->select('select PRJ from DBNOMOR');


    $values = [
        $inisial[0]->PRJ,
        $periode[0]->bulan,
        $periode[0]->tahun,
        $username,
    ];

    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);

    return $noBukti;
  }


  public function listCustomer (Request $req) {

    $listData = DB::connection('SML')->select("select * from vwBrowsCustSupp where IsCustomer=1");
    return $listData;
  }

  public function listNoInvoice (Request $req) {

    $listData = DB::connection('SML')->select("select distinct A.NOBUKTI, A.TANGGAL, A.NoSO,A.KODEGDG,A.NAMAGDG , a.ppn, a.flagtipe
                from vwBrowsOutInvoicePL A
                where A.KodeCustSupp= :kodecustsupp" , ["kodecustsupp" => $req->kodecustsupp]);
    return $listData;
  }

  public function listBarang (Request $req) {

    $listData = DB::connection('SML')->select("select A.Urut Urut, A.KodeBrg, A.NamaBrg, A.NoSat, A.Satuan,
                case when A.NOSAT=1 then 1 else A.ISI2 end Isi, A.Isi1, A.Isi2,
                A.QntSisa, A.Qnt1Sisa, A.Qnt2Sisa, A.NFix,a.SATX , b.NAMABRG NamaBrgx, a.SAT1 , a.SAT2

                from vwBrowsOutPLPR A
                left join dbbarang b on a.KODEBRG = b.KODEBRG
                where A.NoBukti= :noinvoice

                order by A.Urut" , ["noinvoice" => $req->noinvoice]);
    return $listData;
  }

  public function listNoBeli (Request $req) {

    $listData = DB::connection('SML')->select(" select A.NOBUKTI,A.urut
                       from DBBELIDET A
                       Left Outer join dbPRRJualdet b on a.NOBUKTI=b.nobeli and a.URUT=b.urutbeli
                      left outer join DBPO C on a.NoPO=c.NOBUKTI
                       where A.kodebrg= :kodebrg and B.NoBukti is null
                      and C.Noso= :noso
                       group by A.NOBUKTI,A.Urut" , ["kodebrg" => $req->kodebrg , "noso" => $req->noso]);
    return $listData;
  }

  public function getLPBDetail (Request $req) {

    $listData = DB::connection('SML')->select(" select * from DBBELIdet where nobukti = :nobukti" , ["nobukti" => $req->nobukti]);
    return $listData;
  }

  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $res = DB::connection('SML')->update("update dbrbeli set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    
      $values = [
      '',
      'dbrbeli',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      1
    ];

    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
	 $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','RPB',$req->nobukti,'',0,'DBRBELI');

    return $res;
  }

  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $res = DB::connection('SML')->update("update dbrbeli set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
	$tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','RPB',$req->nobukti,$req->pket,0,'DBRBELI');
    $values = [
      '',
      'dbrbeli',
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
    $choice = $req->choice;
    $jmlrecord = $req->jmlrecord;
    $nobukti = $req->nobukti;
    $urut = $req->urut;
    $harga = $req->harga;
    $DISCP = $req->DISCP;
    $DiscP2 = $req->DiscP2;
    $DiscP3 = $req->DiscP3;
    $xurut=0;

//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBRBELIDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
    if ($purut){

        if ($choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBRBELIDET where Nobukti = :nobukti', ['nobukti' => $nobukti]);
            // return 'uuu';
        $xurut= $purut[0]->xurut;
        }else { 
            // return 'mmm';
            $xurut = $req->urut;
        }
        
    }else{
        // return 'ttt';
        $xurut=1; 
    }
    // return ["asd" => $xurut] ;



    if ($choice == "I" && $jmlrecord == 0) {
      $check = DB::connection('SML')->select('select * from dbrbeli where Nobukti = :nobukti',["nobukti" => $nobukti]);
        if ($check) {
          return 2;
      }
    }

    $res = DB::connection('SML')->update("update DBRbelidet set harga=:harga, DISCP = :DISCP, DiscP2 = :DiscP2, DiscP3 = :DiscP3 where nobukti = :nobukti and urut=:urut", [ "nobukti" => $req->nobukti,"urut" => $req->urut,"harga" => $req->harga,"DISCP" => $req->DISCP,"DiscP2" => $req->DiscP2,"DiscP3" => $req->DiscP3]);
	$tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $choice,'RPB',$nobukti,'',$xurut,'DBRBELIDET');

    //
    // $values = [
    //   $choice,
    //   $nobukti,
    //   $req->nourut,
    //   $req->tanggal,
    //   $req->noinvoice,
    //   $req->kodecustsupp,
    //   '', // nopolkend
    //   '',
    //   '',
    //   '', // noseal 10
    //   $req->catatan,
    //   $req->urut,
    //   $req->urutinvoice,
    //   $req->kodebrg,
    //   $req->qnt,
    //   $req->qnt1,
    //   $req->qnt2,
    //   $req->sat1,
    //   $req->nosat,
    //   $req->isi, // 20
    //   0, // netw
    //   0,
    //   \Auth::User()->username,
    //   $req->jmlrecord,
    //   $req->namabrg, // 25
    //   '', // sopir
    //   0,
    //   $req->kodegdg,
    //   $req->flagtipe,
    //   $req->ppn, // 30
    //   $req->noso,
    //   '', // nobatch
    //   $req->retursupp,
    //   $req->sat2,
    //   '', //satx
    //   0, // pmin 50
    //   $req->ketdet,
    //   $req->nobeli,
    //   $req->urutbeli
    //
    // ];
    //
    // DB::connection('SML')->statement('exec sp_TransPRRJUAL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);





    return 1;

  }



    public function spCetak (Request $req)
      {
          $noBukti = $req->input('NOBUKTI');

          $cetak = DB::connection("SML")->select(
              "EXEC SP_CETAKRBELI ?",
              [$noBukti]
          );

          $tempCetak1 = [];
          foreach ($cetak as $p) {
              array_push($tempCetak1, $p);
          }

          return $tempCetak1;
      }



}
