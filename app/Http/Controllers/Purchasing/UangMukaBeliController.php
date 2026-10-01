<?php

namespace App\Http\Controllers\Purchasing;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;





class UangMukaBeliController extends Controller


{

  // Rentang tanggal default = satu bulan penuh periode kerja user (sama seperti Purchase Order).
  private function periodeRange ($periode) {
    $stamp = mktime(0, 0, 0, (int) $periode->bulan, 1, (int) $periode->tahun);
    return [ date('Y-m-01', $stamp), date('Y-m-t', $stamp) ];
  }

  public function index(Request $req) {
    $kodemenu = '030208';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses($kodemenu, $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(3);

    list($umbTglAwal, $umbTglAkhir) = $this->periodeRange($periode);

    // Baris tabel digambar JS lewat loadAll() (lihat uangmukabeli.blade.php), jadi
    // index() tidak lagi perlu menyiapkan tempOutstanding/tempPenerimaan.
    return view('purchasing.uangmukabeli' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "umbTglAwal" => $umbTglAwal,
      "umbTglAkhir" => $umbTglAkhir,
      "akses" => $akses
    ]);

  }

  public function loadAll (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    list($tglawal, $tglakhir) = $this->periodeRange($periode);
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglawal')))  { $tglawal  = $req->input('tglawal'); }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $req->input('tglakhir'))) { $tglakhir = $req->input('tglakhir'); }
    if ($tglawal > $tglakhir) { $tglakhir = $tglawal; }

    // Konfigurasi kolom (susunan/lebar/desimal tersimpan per user) - sama seperti
    // PembelianPermintaanAgenController@loadAll.
    $reqHeader = new Request(['href' => 'uangmukabeli']);
    $header = app('App\Http\Controllers\HeaderTableController')->getHeaderTable($reqHeader);

    // Satu tabel gabungan (belum maupun sudah diotorisasi) - penyaringan otorisasi
    // dikerjakan di browser lewat modal Filter, sama seperti Purchase Order/PR Agen.
    // Kolom dialiaskan supaya sama persis dengan alias di
    // HeaderTableController@getHeaderTable cabang 'uangmukabeli'.
    $tempData = DB::connection("SML")->select("declare @tglawal date, @tglakhir date

select @tglawal= :tglawal, @tglakhir= :tglakhir

select A.NOBUKTI,
       A.NOBUKTI as [No Bukti],
       Convert(varchar(10), A.TANGGAL, 23) as [Tanggal],
       A.NOSO as [No PO],
       C.NamaCustSupp as [Supplier],
       A.VALAS as [Valas],
       A.DPP as [DPP],
       Round(A.PPN,0) as [PPN],
       A.PERSEN as [Persen],
       Convert(varchar(10), A.tglEst, 23) as [Tgl Est],
       case when Isnull(A.Bayar,0)=0 then '-' when Isnull(A.Bayar,0)=1 then 'Tunai' when Isnull(A.Bayar,0)=2 then 'Transfer' end as [Bayar],
       A.IsOtorisasi1, A.OtoUser1, A.TglOto1
from DBUMJUAL A
Left Outer Join DBPO B on A.Noso=B.Nobukti
Left Outer join DBCustSupp C on B.KodeSupp=C.KOdeCustsupp
where A.TANGGAL between @tglawal and @tglakhir
and Isnull(A.pBeli,0)=1
order by A.TANGGAL desc, A.NOBUKTI desc

" , [ "tglawal" => $tglawal , "tglakhir" => $tglakhir  ]);

    return [
      "listData1"         => $tempData,
      "aliasordered"      => $header['aliasordered'],
      "headertableheader" => $header['headertableheader'],
      "isnumeric"         => $header['isnumeric'],
      "headertablevalue"  => $header['headertablevalue'],
      "isparsed"          => $header['isparsed'],
      "isshown"           => $header['isshown'],
      "desimal"           => $header['desimal'],
    ];
  }



  public function getDetailKoreksi (Request $req) {

    $tempOutstanding = DB::connection("SML")->select("


  declare @nobukti varchar(25)

  select @NObukti= :nobukti

  select a.IsOtorisasi1 ,A.NOBUKTI,A.NOURUT,A.TANGGAL,A.NOSO,A.VALAS,A.KURS,A.DPP,A.PPN,A.PERSEN, b.namacustsupp , b.kodesupp,(select
top 1 nilaippn from dbnilaippn where GETDATE() between tglawal and
tglakhir) NilaiPPN,
  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                         Case when A.IsOtorisasi2=1 then 1 else 0 end+
                         Case when A.IsOtorisasi3=1 then 1 else 0 end+
                         Case when A.IsOtorisasi4=1 then 1 else 0 end+
                         Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                    else 1
               end As Bit) NeedOtorisasi,B.DPP DPPSO,B.PPN SOPPN ,B.nnet SUBTOTALSO,
               A.Valas,A.Kurs,B.TipePPN
  ,A.SUBTOTAL ,B.nnet ,B.NamaCustSupp,A.TglEst ,Isnull(A.Bayar,0) Bayar ,b.tipebayar
  from DBUMJUAL A
  left outer join (select A.nobukti,B.PPN TipePPN,sum(Ndpp) DPP, Sum(NPPN) PPN,Sum(Nnet) Nnet,b.kodesupp,C.NamaCustSupp,b.tipebayar from dbPodet a
                   Left Outer Join dbPO b on A.NoBukti=B.NoBukti
                   Left Outer join DbCustSupp C on B.KodeSUPP=C.KodeCustSupp
                   group by A.nobukti,B.PPN,C.NamaCustSupp,b.tipebayar,b.kodesupp) B on A.noSO= B.nobukti
  where A.nobukti=@NObukti
    " , ["nobukti" => $req->nobukti]);
return $tempOutstanding;

  }

  public function getDetail (Request $req ) {



        $data = DB::connection("SML")->select("

        declare @nobukti varchar(25)

select @NObukti= :nobukti

select A.NOBUKTI,(select
top 1 nilaippn from dbnilaippn where GETDATE() between tglawal and
tglakhir) NilaiPPN,A.NOURUT,A.TANGGAL,A.NOSO,A.VALAS,A.KURS,A.DPP,A.PPN,A.PERSEN, b.KodeSupp, B.NamaCustSupp,
Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
               Case when A.IsOtorisasi2=1 then 1 else 0 end+
               Case when A.IsOtorisasi3=1 then 1 else 0 end+
               Case when A.IsOtorisasi4=1 then 1 else 0 end+
               Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
          else 1
     end As Bit) NeedOtorisasi,B.DPP DPPSO,B.PPN SOPPN ,B.nnet SUBTOTALSO,
     A.Valas,A.Kurs,B.TipePPN
,A.SUBTOTAL ,B.nnet ,B.NamaCustSupp,A.TglEst ,Isnull(A.Bayar,0) Bayar ,b.tipebayar
from DBUMJUAL A
left outer join (select A.nobukti,B.PPN TipePPN,sum(Ndpp) DPP, Sum(NPPN) PPN,Sum(Nnet) Nnet,C.NamaCustSupp,b.tipebayar, b.KODESUPP from dbPodet a
         Left Outer Join dbPO b on A.NoBukti=B.NoBukti
         Left Outer join DbCustSupp C on B.KodeSUPP=C.KodeCustSupp
         group by A.nobukti,B.PPN,C.NamaCustSupp,b.tipebayar ,b.KodeSupp) B on A.noSO= B.nobukti
where A.nobukti=@NObukti

        " , ["nobukti" => $req->nobukti]);
    return $data;
  }








  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $res = DB::connection('SML')->update("update dbumjual set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('oto','UMB',$req->nobukti,'',0,'DBUMJUAL');
    $values = [
      '',
      'DBUMJUAL',
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

    $res = DB::connection('SML')->update("update dbumjual set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','UMB',$req->nobukti,$req->pket,0,'DBUMJUAL');
    $values = [
      '',
      'DBUMJUAL',
      $periode->bulan,
      $periode->tahun,
      $req->nobukti,
      0
    ];
    DB::connection('SML')->statement('exec sp_ProsesPostingHutPiut ?,?,?,?,?,?', $values);
    DB::connection('SML')->statement('exec sp_ProsesPostingJurnalOto ?,?,?,?,?,?', $values);
   
    return $res;
  }

  public function spKoreksi (Request $req) {

    $listData = $req->tempData;

    foreach ($listData as $d)  {

       // update TempOutstanding set IsTerima = 1 ,QntTerima = :qntterima where NOBUKTI = :NOBUKTI and URUT = :urut

      DB::connection('SML')->update('update DBBAOPDET set QntOpname = :qntedit where NoBukti = :nobukti and Urut = :urut',
      [
        "qntedit" => $d['qntedit'],
        "nobukti" => $req->nobukti,
        "urut" => $d['Urut']
      ]);



      // $jmlrecord = 1;



    }


    DB::connection('SML')->update('update DBBAOPDET set Selisih= case when QntOpname-SaldoComp<0 then -1*(QntOpname-SaldoComp)
 else QntOpname-SaldoComp end,
QntDB=CASE WHEN QntOpname-SaldoComp>=0 THEN QntOpname-SaldoComp ELSE 0 END ,
QNTCR=CASE WHEN QntOpname-SaldoComp>=0  THEN 0 ELSE (QntOpname-SaldoComp)*-1  END
 WHERE NOBUKTI= :nobukti
', [
      "nobukti" => $req->nobukti
    ]);



    return 1;


  }

  public function listPO (Request $req) {


    return DB::connection('SML')->select("select  A.NoBukti,A.KodeSupp,C.NamaCustSupp ,b.DPP,b.PPN,b.Nnet,
    A.Flagtipe TipePPN,A.kODEVLS Valas,A.Kurs,A.PPN pPPN,(select
    top 1 nilaippn from dbnilaippn where GETDATE() between tglawal and
    tglakhir) NilaiPPN,a.tipebayar,CASE WHEN A.TIPEBAYAR=0 THEN
    'TUNAI' ELSE 'KREDIT' END XTIPEBAYAR
    from DBPO A
    Left Outer Join (select nobukti,sum(Ndpp) DPP, Sum(NPPN) PPN,Sum(nnet) nnet,nilaippn from dbPodet group by nobukti,nilaippn) B
                     on A.nobukti=B.nobukti
                     Left Outer Join DbCustSupp C on A.KodeSUPP=C.KodeCustSupp
                     Left OUter Join (select NOso,SUM(DPP) TOtDPPUM
    		      	from DBUMJUAL Group By NOSO ) X on A.NOBUKTI=x.NOSO
                left outer join DBBELI M on A.NOBUKTI=M.NoPOHd
                           where  Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                                             Case when A.IsOtorisasi2=1 then 1 else 0 end+
                                             Case when A.IsOtorisasi3=1 then 1 else 0 end+
                                             Case when A.IsOtorisasi4=1 then 1 else 0 end+
                                             Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                                        else 1
                                      end As Bit)=0    and Year(A.tanggal)>2025

                           and B.DPP - ISnull(x.TOtDPPUM,0)>0
                         
                           ");



  }

  public function spAdd (Request $req) {

      $choice = $req->choice;
      $jmlrecord = $req->jmlrecord;
      $nobukti = $req->nobukti;
      $xurut=0;

   if ($choice=='D'){  
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'UMB',$nobukti,'',$xurut,'DBUMJUAL');
  //return [ $req->choice,'UMB',$nobukti,'',$xurut,'DBUMJUAL'];
  }
  
      if ($choice == "I" && $jmlrecord == 0) {
        $check = DB::connection('SML')->select('select * from dbumjual where Nobukti = :nobukti',["nobukti" => $nobukti]);
          if ($check) {
            return 2;
        }
      }

      $checkqucary = DB::connection('SML')->select("Select Nobukti,ISnull(OutUM,0) OutUM,ISnull(TOTDPPUM,0) TOTDPPUM,round(ISnull(DPP,0),1) DPP from VwOUMJ where Nobukti= :nopo",["nopo" => $req->noso]);

      if ($choice == "I") {
        $checkbatal = DB::connection('SML')->select("select * from DBPODET where NOBUKTI = :nopo",["nopo" => $req->noso]);
        if ((float)$checkbatal[0]->QntBatal > 0) {
          return 4;
        }

      //   if ((float)$req->dppx > (float)$checkqucary[0]->OutUM) {

      //     return [
      //         'dppx' => (float)$req->dppx,
      //         'OutUM' => (float)$checkqucary[0]->OutUM,
      //         'TOTDPPUM' => (float)$checkqucary[0]->TOTDPPUM,
      //         'DPP' => (float)$checkqucary[0]->DPP
      //     ];
      // }

        if ((float)$req->dppx > (float)$checkqucary[0]->OutUM) {
  // return ['input : ',(float)$req->dppx, 'out vw : ' ,  (float)$checkqucary[0]->OutUM];

          return 3;
        }
        $checkporp = DB::connection('SML')->select("select * from vwoutporp where NOBUKTI = :nopo",["nopo" => $req->noso]);
        if(!$checkporp) {
          return 3;
        }

        if ((float)$checkporp[0]->OUTPORP - (float)$req->subtotal < 0) {
         return 3;
  //return [(float)$checkporp[0]->OUTPORP ,'ccccc' ,(float)$req->subtotal];
        }
      } else if ($choice == "U") {
        if (floor($checkqucary[0]->TOTDPPUM) -  floor($req->dppdetail) + floor($req->dppx) > floor($checkqucary[0]->DPP)) {
         return 3;
  // return [floor($checkqucary[0]->TOTDPPUM),  floor($req->dppdetail) ,  floor($req->dppx) ,floor($checkqucary[0]->DPP) ];
        }
      }

        // code...
        $values = [
          $choice,
          $nobukti,
          $req->nourut,
          $req->noso,
          $req->dppx,
          $req->ppnx,
          $req->presentase,
          $req->tanggal,
          $req->valas,
          $req->kurs,
          $req->subtotal,
          $req->maxol,
          $req->pbeli,
          $jmlrecord,
          $req->flagtipe,
          $req->tglest,
          $req->bayar

        ];
        DB::connection('SML')->statement('exec sp_umj ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
      if ($choice !='D'){ 
        $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'UMB',$nobukti,'',$xurut,'DBUMJUAL');
      }
      return 1;
    }


  public function spDelete (Request $req) {


 $res = DB::connection('SML')->update("


declare @NoBukti varchar(50) , @urut int


select @nobukti = :nobukti , @urut = :urut
 delete DBBAOPDET where nobukti=@nobukti and  urut=@urut
if not exists( select nobukti from DBBAOPDET where nobukti=@nobukti)
begin
delete DBBAOP where nobukti=@nobukti
end", [ "nobukti" => $req->nobukti , "urut" => $req->urut]);

return 1;

  }

public function spCetak (Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC CetakUangMUka ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

}
