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





class OpnameBarangController extends Controller


{

  public function index(Request $req) {
    $kodemenu = '06028';
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);


    $tempOutstanding = DB::connection("SML")->select("select c.NAMAHDGRP,'' NamaSubGrp,'' NAMAMERK,A.NoBukti, A.Tanggal, A.KodeGdg, B.KodeHdGrp, B.KodeSubGrp, B.KodeMerk
      ,B.NoBukti NoPerintahOP
      from DBBAOP A
      left outer join DBPerintahOP B on B.NoBukti = A.NoPerintahOP
      left outer join DBHDGROUP c on c.KODEHDGRP=B.KodeHdGrp
      where A.IsOtorisasi1 = 1
      and A.nobukti in ( select A.Nobukti
                      from DBBAOP A
      		LEFT OUTER JOIN DBBAOPDET B ON A.NOBUKTI=B.NOBUKTI
      		LEFT OUTER JOIN DBKOREKSIDET C ON B.NoBukti=C.NOBAP AND B.Urut=C.URUTBAP
      		where  C.NOBUKTI IS NULL)
      " );


    $tempPenerimaan = DB::connection("SML")->select("
    Select * from vwMasterKoreksi where month(tanggal)= :bulan and year(tanggal)= :tahun and nobukti like '%OPN%' and IsOtorisasi1 = 0 Order by nobukti
    " , [ "bulan" => $periode->bulan,"tahun" =>$periode->tahun  ]);

    $tempPenerimaan1 = DB::connection("SML")->select("
    Select * from vwMasterKoreksi where month(tanggal)= :bulan and year(tanggal)= :tahun and nobukti like '%OPN%' and IsOtorisasi1 = 1 Order by nobukti
    " , [ "bulan" => $periode->bulan,"tahun" =>$periode->tahun  ]);


    return view('gudang.opnamebarang' , [
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


        $tempOutstanding = DB::connection("SML")->select("select c.NAMAHDGRP,'' NamaSubGrp,'' NAMAMERK,A.NoBukti, A.Tanggal, A.KodeGdg, B.KodeHdGrp, B.KodeSubGrp, B.KodeMerk
    ,B.NoBukti NoPerintahOP
    from DBBAOP A
    left outer join DBPerintahOP B on B.NoBukti = A.NoPerintahOP
    left outer join DBHDGROUP c on c.KODEHDGRP=B.KodeHdGrp
    where A.IsOtorisasi1 = 1
    and A.nobukti in ( select A.Nobukti
                    from DBBAOP A
    		LEFT OUTER JOIN DBBAOPDET B ON A.NOBUKTI=B.NOBUKTI
    		LEFT OUTER JOIN DBKOREKSIDET C ON B.NoBukti=C.NOBAP AND B.Urut=C.URUTBAP
    		where  C.NOBUKTI IS NULL)
    " );


      $tempPenerimaan = DB::connection("SML")->select("
      Select * from vwMasterKoreksi where month(tanggal)= :bulan and year(tanggal)= :tahun and nobukti like '%OPN%' and IsOtorisasi1 = 0 Order by nobukti
      " , [ "bulan" => $periode->bulan,"tahun" =>$periode->tahun  ]);

      $tempPenerimaan1 = DB::connection("SML")->select("
      Select * from vwMasterKoreksi where month(tanggal)= :bulan and year(tanggal)= :tahun and nobukti like '%OPN%' and IsOtorisasi1 = 1 Order by nobukti
      " , [ "bulan" => $periode->bulan,"tahun" =>$periode->tahun  ]);

    return [
      "tempOutstanding" => $tempOutstanding,
      "tempPenerimaan" => $tempPenerimaan,
      "tempPenerimaan1" => $tempPenerimaan1
    ];
  }


  public function listAdd (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();
    $username = \Auth::user()->username;
    $values = [
      $username,
      $req->nobukti,
      $periode->tahun,
      $periode->bulan,
      'OPN',
      date('Y-m-d')
    ];

    $res = DB::connection('SML')->update('exec sp_TempOutBAOP ?,?,?,?,?,?',$values);

    return DB::connection("SML")->select("select * from TempOutstanding
          where IDUSER= :username and trans = 'OPN'
          order by urut
      " , ["username" => $username]);

  }

  public function getDetailKoreksi (Request $req) {

    $tempOutstanding = DB::connection("SML")->select("

    declare @NoBukti varchar(20)

select  @NoBukti= :nobukti

Select A.nonbap, A.Nobukti, A.Tanggal, A.note, A.ISCetak, Urut, b.kodebrg, C.namaBrg, A.KodeGdg, D.Nama NamaGDG,
       b.SaldoComp, b.QntOpname, b.Selisih,
       b.Qntdb, B.QntCr, b.Harga, (b.qntdb-b.qntcr)*b.harga as Total,
       (b.qntdb)*b.harga  HrgAdi, (b.qntcr)*b.harga HrgAdo,
       C.Sat1 Satuan,C.Sat2 Satuan2,b.Saldo2Comp, b.Qnt2Opname, b.Selisih2,
       b.Qnt2db, B.Qnt2Cr,Iscek,Iscek2,C.isi2,
       E.NoBukti NoPerintahOP, E.KodeGdg, E.KodeHdGrp, E.KodeSubGrp, E.KodeMerk,A.Nourut
From dbKoreksi A
     left outer join dbKoreksiDet B on b.nobukti=a.nobukti
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

  public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakOpnameBahan ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }


  public function listPROpname (Request $req) {
    $list = DB::connection("SML")->select("

    select  A.NoBukti, A.Tanggal, A.KodeGdg, A.KodeHdGrp, A.KodeSubGrp, A.KodeMerk from DBPerintahOP A
 left outer join DBKOREKSI B on B.NoPerintahOP = A.NoBukti
 where B.NoPerintahOP is null
 and YEAR(A.tanggal) > 2022
 order by Tanggal, NoBukti

    " );


    return $list;


  }

  public function spAddPROpname (Request $req) {

      $check = DB::connection('SML')->select('select * from DBKOREKSI where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }

      $list = DB::connection("SML")->select("
      select A.Urut, A.KodeBrg, A.NamaBrg, A.Satuan,dbo.SaldoQntDJ(A.KodeBrg,B.Kodegdg, GETDATE()) QtyComp
      from DBPerintahOPDet A
      Left OUter join dbPerintahOP B on A.Nobukti=B.Nobukti
      where A.NoBukti = :nobukti
      " , ["nobukti" => $req->nopropname] );



      if (!$list){
        return 3;
      }

      // $list = get_object_vars($list);


// Choice = 'I',
// Nobukti = 'MGL/OPN/00015/0625',
// NoUrut = '00015',
// Tanggal = getDate(),
// Note = '',
// Urut = 0,
// KodeBrg = 'JJ0198',
// KodeGdg = 'G01',
// SaldoComp = --qtycomp,
// QntOpname = 0,
// Selisih = 0,
// QntDB  = 0 ,
// QntCR = 0 ,
// NoSat = 1,
// Isi = 1 ,
// Satuan = 'PCS',
// Harga = 0,
// keterangan = '',
// Saldo2Comp  = 0,
// Qnt2Opname = 0 ,
// Selisih2  = 0,
// Qnt2DB = 0,
// Qnt2CR = 0,
// isCek = 0,
// IsCek2 = 0 ,
// Tipe = 'OPN',
// NoPerintahOP = 'SPL/POP/00005/1015' ,
// urutPerintahOP = 1

      foreach ($list as $d)  {
        // return [
        //   'I' , // Choice = 'I',
        //   $req->nobukti,  // Nobukti = 'MGL/OPN/00015/0625',
        //   $req->nourut,// NoUrut = '00015',
        //   $req->tanggal,// Tanggal = getDate(),
        //   '' ,// Note = '',
        //   0 ,// Urut = 0,
        //   $d->KodeBrg , // KodeBrg = 'JJ0198',
        //   $req->kodegdg, // KodeGdg = 'G01',
        //   $d->QtyComp ? $d->QtyComp : 0 , // SaldoComp = --qtycomp,
        //   0, // QntOpname = 0,
        //   0, // Selisih = 0,
        //   0, // QntDB  = 0 ,
        //   0, // QntCR = 0 ,
        //   1, // NoSat = 1,
        //   1, // Isi = 1 ,
        //   $d->Satuan ,// Satuan = 'PCS',
        //   0,// Harga = 0,
        //   '',// keterangan = '',
        //   0,// Saldo2Comp  = 0,
        //   0,// Qnt2Opname = 0 ,
        //   0,// Selisih2  = 0,
        //   0,// Qnt2DB = 0,
        //   0,// Qnt2CR = 0,
        //   0,// isCek = 0,
        //   0,// IsCek2 = 0 ,
        //   'OPN' ,// Tipe = 'OPN',
        //   $req->nopropname,// NoPerintahOP = 'SPL/POP/00005/1015' ,
        //   $d->Urut// urutPerintahOP = 1
        // ];
        DB::connection('SML')->statement('exec SP_KOREKSI ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          'I' , // Choice = 'I',
          $req->nobukti,  // Nobukti = 'MGL/OPN/00015/0625',
          $req->nourut,// NoUrut = '00015',
          $req->tanggal,// Tanggal = getDate(),
          '' ,// Note = '',
          0 ,// Urut = 0,
          $d->KodeBrg , // KodeBrg = 'JJ0198',
          $req->kodegdg, // KodeGdg = 'G01',
          $d->QtyComp ? $d->QtyComp : 0 , // SaldoComp = --qtycomp,
          0, // QntOpname = 0,
          0, // Selisih = 0,
          0, // QntDB  = 0 ,
          0, // QntCR = 0 ,
          1, // NoSat = 1,
          1, // Isi = 1 ,
          $d->Satuan ,// Satuan = 'PCS',
          0,// Harga = 0,
          '',// keterangan = '',
          0,// Saldo2Comp  = 0,
          0,// Qnt2Opname = 0 ,
          0,// Selisih2  = 0,
          0,// Qnt2DB = 0,
          0,// Qnt2CR = 0,
          0,// isCek = 0,
          0,// IsCek2 = 0 ,
          'OPN' ,// Tipe = 'OPN',
          $req->nopropname,// NoPerintahOP = 'SPL/POP/00005/1015' ,
          $d->Urut, 1// urutPerintahOP = 1
        // urutPerintahOP = 1
        ]);



         $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('U','OPN',$req->nobukti,'',$d->Urut,'DBKOREKSIDET');


      }


      return 1;
  }







  public function spUpdateHeader (Request $req) {
    $res = DB::connection('SML')->update("update dbkoreksi set note = :keterangan where nobukti = :nobukti", ["keterangan" => $req->keterangan , "nobukti" => $req->nobukti]);
    return $res;

  }

  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbkoreksi set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    
 $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','OPN',$req->nobukti,'',0,'DBKOREKSI');
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbkoreksi set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','OPN',$req->nobukti,$req->pket,0,'DBKOREKSI');
    
    return $res;
  }

  public function spKoreksi (Request $req) {

    $listData = $req->tempData;

    foreach ($listData as $d)  {

       // update TempOutstanding set IsTerima = 1 ,QntTerima = :qntterima where NOBUKTI = :NOBUKTI and URUT = :urut

      DB::connection('SML')->update('update DBKOREKSIDET set HARGA = :harga where nobukti = :nobukti and URUT = :urut',
      [
        "harga" => $d['qntedit'],
        "nobukti" => $req->nobukti,
        "urut" => $d['Urut']
      ]);

      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingDataTrans('U','OPN',$req->nobukti,'',$d['Urut'],'DBKOREKSIDET');


      // $jmlrecord = 1;



    }




    return 1;


  }


  public function spKoreksiNonBap (Request $req) {

    $listData = $req->tempData;

    foreach ($listData as $d)  {
      
      DB::connection('SML')->update('update DBKOREKSIDET
      set qntopname = :qntopname ,
      qnt2opname = :qnt2opname,
      selisih = :selisih ,
      selisih2 = :selisih2 ,
      qntdb = :qntdb,
      qnt2db = :qnt2db , qntcr = :qntcr , qnt2cr = :qnt2cr , HARGA = :harga where nobukti = :nobukti and URUT = :urut',
      [
        "qntopname" => $d["inputqntopname"],
        "qnt2opname" => $d["inputqntopname"],
        "selisih" => $d["inputselisih"],
        "selisih2" => $d["inputselisih"],
        "qntdb" => $d["inputqntdb"],
        "qnt2db" => $d["inputqntdb"],
        "qnt2cr" => $d["inputqntcr"],
        "qntcr" => $d["inputqntcr"],
        "harga" => $d['qntedit'],
        "nobukti" => $d['Nobukti'],
        "urut" => $d['Urut']
      ]);


    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingDataTrans('U','OPN',$d['Nobukti'],'',$d['Urut'],'DBKOREKSIDET');

    }




    return 1;


  }


  public function spAdd (Request $req) {

    $username = \Auth::user()->username;
    $listData = $req->tempData;

    $jmlrecord = $req->jmlrecord;

    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from DBKOREKSI where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

    DB::connection('SML')->update('update TempOutstanding set IsTerima = 0  where NOBUKTI = :nobukti' , ["nobukti" => $req->nob]);


      foreach ($listData as $d)  {

         // update TempOutstanding set IsTerima = 1 ,QntTerima = :qntterima where NOBUKTI = :NOBUKTI and URUT = :urut

        DB::connection('SML')->update('update TempOutstanding set IsTerima = 1  where NOBUKTI = :nobukti and URUT = :urut',
        [
          "nobukti" => $d['NOBUKTI'],
          "urut" => $d['URUT']
        ]);




      }


      DB::connection('SML')->statement('exec SP_INSERTBAPOP ?,?,?,?', [
        $req->nobukti,
        $req->nourut,
        $req->nob,
        $req->tanggal
      ]);



      return 1;

  }

  public function spDelete (Request $req) {


 $res = DB::connection('SML')->update("


declare @NoBukti varchar(50) , @urut int


select @nobukti = :nobukti , @urut = :urut
 delete DBKOREKSIDET where nobukti=@nobukti and  urut=@urut
if not exists( select nobukti from DBKOREKSIDET where nobukti=@nobukti)
begin
delete DBKOREKSI where nobukti=@nobukti
end", [ "nobukti" => $req->nobukti , "urut" => $req->urut]);
$tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData('D','OPN',$req->nobukti,'',$req->urut,'DBKOREKSIDET');
return 1;

  }



}
