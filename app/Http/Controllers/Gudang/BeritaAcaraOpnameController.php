<?php

namespace App\Http\Controllers\Gudang;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\DBFLMENU;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;





class BeritaAcaraOpnameController extends Controller


{

  public function index(Request $req) {
    $kodemenu = '060274';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);


    $tempOutstanding = DB::connection("SML")->select("declare @Tahun int, @Bulan int

    select c.NAMAHDGRP,'' NamaSubGrp,'' NAMAMERK,A.NoBukti, A.Tanggal, A.KodeGdg, A.KodeHdGrp, A.KodeSubGrp, A.KodeMerk,
    	B.NoPerintahOP
    from DBPerintahOP A
    left outer join DBBAOP B on B.NoPerintahOP = A.NoBukti
    left outer join DBHDGROUP c on c.KODEHDGRP=a.KodeHdGrp
    where A.IsOtorisasi1 = 1
    and A.nobukti in ( select A.Nobukti
                    from DBPERINTAHOP A
    		LEFT OUTER JOIN DBPERINTAHOPDET B ON A.NOBUKTI=B.NOBUKTI
    		LEFT OUTER JOIN DBBAOPDET C ON B.NoBukti=C.NOPROPNAME AND B.Urut=C.UrutPROpname
    		where  C.NOBUKTI IS NULL)
    and Month(A.tanggal)= :bulan and Year(A.tanggal)= :tahun
    and A.tanggal>'10/31/2017'" , [ "bulan" => $periode->bulan , "tahun" =>$periode->tahun  ]);
        // $tempOutstanding = [];
        // foreach ($outstanding as $p) {
        //   // code...
        //   array_push($tempOutstanding, $p);
        // }
        //

        // $collection1 = collect($tempOutstanding)->groupBy('NoBukti');
        // $tempOutstanding1 = [];
        // foreach ($collection1 as $p) {
        //   // code...
        //   array_push($tempOutstanding1, $p);
        // }

        $tempPenerimaan = DB::connection("SML")->select("
        Select * from vwMasterBAOP where month(tanggal)= :bulan and year(tanggal)= :tahun AND IsOtorisasi1 = 0 Order by nobukti" , [ "bulan" => $periode->bulan,"tahun" =>$periode->tahun  ]);

        $tempPenerimaan1 = DB::connection("SML")->select("
        Select * from vwMasterBAOP where month(tanggal)= :bulan and year(tanggal)= :tahun AND IsOtorisasi1 = 1 Order by nobukti" , [ "bulan" => $periode->bulan,"tahun" =>$periode->tahun  ]);


    return view('gudang.beritaacaraopname' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan" => $tempPenerimaan,
      "tempPenerimaan1" => $tempPenerimaan1,
      "akses" => $akses
    ]);

  }

  public function loadAll () {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("declare @Tahun int, @Bulan int

    select c.NAMAHDGRP,'' NamaSubGrp,'' NAMAMERK,A.NoBukti, A.Tanggal, A.KodeGdg, A.KodeHdGrp, A.KodeSubGrp, A.KodeMerk,
      B.NoPerintahOP
    from DBPerintahOP A
    left outer join DBBAOP B on B.NoPerintahOP = A.NoBukti
    left outer join DBHDGROUP c on c.KODEHDGRP=a.KodeHdGrp
    where A.IsOtorisasi1 = 1
    and A.nobukti in ( select A.Nobukti
                    from DBPERINTAHOP A
        LEFT OUTER JOIN DBPERINTAHOPDET B ON A.NOBUKTI=B.NOBUKTI
        LEFT OUTER JOIN DBBAOPDET C ON B.NoBukti=C.NOPROPNAME AND B.Urut=C.UrutPROpname
        where  C.NOBUKTI IS NULL)
    and Month(A.tanggal)= :bulan and Year(A.tanggal)= :tahun
    and A.tanggal>'10/31/2017'

" , [ "bulan" => $periode->bulan , "tahun" =>$periode->tahun  ]);
        // $tempOutstanding = [];
        // foreach ($outstanding as $p) {
        //   // code...
        //   array_push($tempOutstanding, $p);
        // }
        //

        // $collection1 = collect($tempOutstanding)->groupBy('NoBukti');
        // $tempOutstanding1 = [];
        // foreach ($collection1 as $p) {
        //   // code...
        //   array_push($tempOutstanding1, $p);
        // }

        $tempPenerimaan = DB::connection("SML")->select("
        Select * from vwMasterBAOP where month(tanggal)= :bulan and year(tanggal)= :tahun and IsOtorisasi1 = 0 Order by nobukti

        " , [ "bulan" => $periode->bulan,"tahun" =>$periode->tahun  ]);

        $tempPenerimaan1 = DB::connection("SML")->select("
        Select * from vwMasterBAOP where month(tanggal)= :bulan and year(tanggal)= :tahun and IsOtorisasi1 = 1 Order by nobukti

        " , [ "bulan" => $periode->bulan,"tahun" =>$periode->tahun  ]);

    return [
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan" => $tempPenerimaan,
      "tempPenerimaan1" => $tempPenerimaan1
    ];
  }

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakBAOP ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

  public function listAdd (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $username = \Auth::user()->username;
    $values = [
      $username,
      $req->nobukti,
      $periode->tahun,
      $periode->bulan,
      'BAP',
      date('Y-m-d')
    ];

    $res = DB::connection('SML')->update('exec sp_TempOutPROP ?,?,?,?,?,?',$values);

    return DB::connection("SML")->select("select * from TempOutstanding
          where IDUSER= :username and trans = 'BAP'
          order by urut
      " , ["username" => $username]);

  }

  public function getDetailKoreksi (Request $req) {

    $tempOutstanding = DB::connection("SML")->select("

    declare @NoBukti varchar(20)

    select  @NoBukti= :nobukti

    Select A.IsOtorisasi1,A.Nobukti, A.Tanggal, A.note, A.ISCetak, Urut, b.kodebrg, C.namaBrg, A.KodeGdg, D.Nama NamaGDG,
           b.SaldoComp, b.QntOpname, b.Selisih,
           b.Qntdb, B.QntCr, b.Harga, (b.qntdb-b.qntcr)*b.harga as Total,
           (b.qntdb)*b.harga  HrgAdi, (b.qntcr)*b.harga HrgAdo,
           C.Sat1 Satuan,C.Sat2 Satuan2,b.Saldo2Comp, b.Qnt2Opname, b.Selisih2,
           b.Qnt2db, B.Qnt2Cr,Iscek,Iscek2,C.isi2,
           E.NoBukti NoPerintahOP, E.KodeGdg, E.KodeHdGrp, E.KodeSubGrp, E.KodeMerk,A.Nourut ,B.Qnt
    From dbbaop A
         left outer join dbbaopDET B on b.nobukti=a.nobukti
         left outer join dbBarang C on c.kodebrg=b.kodebrg
         left outer join dbGudang D on d.kodegdg=A.kodegdg
         left outer join DBPerintahOP E on E.NoBukti = A.NoPerintahOP
    where A.NoBukti=@NoBukti
    order by B.Urut
    " , ["nobukti" => $req->nobukti]);
return $tempOutstanding;

  }

  public function getDetail (Request $req ) {



        $tempOutstanding = DB::connection("SML")->select("

        declare @NoBukti varchar(50)

        select 	@NoBukti= :nobukti

        select  A.*, B.Urut, B.KodeBrg, D.NamaBrg, B.Satuan, B.QtyComp
        from DBPerintahOp A
        left outer join DBPerintahOpDet B on B.NoBukti = A.NoBukti
        Left Outer join dbBarang D on D.KodeBrg=B.Kodebrg
        where	A.NoBukti=@NoBukti
        order by B.Urut

        " , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }







  public function spUpdateHeader (Request $req) {
    $res = DB::connection('SML')->update("update dbbaop set note = :keterangan where nobukti = :nobukti", ["keterangan" => $req->keterangan , "nobukti" => $req->nobukti]);
    return $res;

  }

  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbbaop set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','BAOP',$req->nobukti,'',0,'dbbaop');
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbbaop set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','BAOP',$req->nobukti,$req->pket,0,'dbbaop');
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

       $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('U','BAOP',$req->nobukti,'',$d['Urut'],'DBBAOPDET');

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


  public function spAdd (Request $req) {

    $username = \Auth::user()->username;
    $listData = $req->tempData;

    $jmlrecord = $req->jmlrecord;

    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from dbbaop where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

      foreach ($listData as $d)  {

         // update TempOutstanding set IsTerima = 1 ,QntTerima = :qntterima where NOBUKTI = :NOBUKTI and URUT = :urut

        DB::connection('SML')->update('update TempOutstanding set IsTerima = 1 ,QntTerima = :qntterima where NOBUKTI = :nobukti and URUT = :urut',
        [
          "qntterima" => $d['inputQntTerima'],
          "nobukti" => $d['NOBUKTI'],
          "urut" => $d['URUT']
        ]);



        // $jmlrecord = 1;



      }


      DB::connection('SML')->statement('exec SP_InsertPRBAOP ?,?,?,?,?,?', [
        $req->nobukti,
        $req->nourut,
        $req->nob,
        $username,
        -1,
        $req->tanggal
      ]);



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

 $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('D','BAOP',$req->nobukti,'',$req->urut,'DBBAOPDET');
return 1;

  }



}
