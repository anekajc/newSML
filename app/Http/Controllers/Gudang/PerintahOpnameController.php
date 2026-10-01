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





class PerintahOpnameController extends Controller


{

  public function index(Request $req) {
    $kodemenu = '06027';

    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);


    $tempOutstanding = DB::connection("SML")->select("declare @Tahun int, @Bulan int

select @Tahun= :tahun , @Bulan=:bulan

select b.NAMAHDGRP,c.NamaSubGrp,d.NAMAMERK,a.*
,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
from DBPerintahOp A
left outer join DBHDGROUP b on b.KODEHDGRP=a.KodeHdGrp
left outer join DBSubGroup c on c.KodeSubGrp=a.KodeSubGrp  and C.KodeHDGrp=C.KodeGrp
left outer join DBMERK d on d.KODEMERK=a.KodeMerk
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan and A.IsOtorisasi1 = 0
order by A.NoBukti

" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);
        // $tempOutstanding = [];
        // foreach ($outstanding as $p) {
        //   // code...
        //   array_push($tempOutstanding, $p);
        // }
        //

        $collection1 = collect($tempOutstanding)->groupBy('NoBukti');
        $tempOutstanding1 = [];
        foreach ($collection1 as $p) {
          // code...
          array_push($tempOutstanding1, $p);
        }
    
    $tempPenerimaan = DB::connection("SML")->select("
        declare @Tahun int, @Bulan int
        select @Tahun= :tahun , @Bulan=:bulan

        select b.NAMAHDGRP,c.NamaSubGrp,d.NAMAMERK,a.*,
            Cast(Case when 
                (Case when A.IsOtorisasi1=1 then 1 else 0 end+
                 Case when A.IsOtorisasi2=1 then 1 else 0 end+
                 Case when A.IsOtorisasi3=1 then 1 else 0 end+
                 Case when A.IsOtorisasi4=1 then 1 else 0 end+
                 Case when A.IsOtorisasi5=1 then 1 else 0 end) = A.MaxOL 
                 then 0 else 1 end As Bit) NeedOtorisasi
        from DBPerintahOp A
        left outer join DBHDGROUP b on b.KODEHDGRP=a.KodeHdGrp
        left outer join DBSubGroup c on c.KodeSubGrp=a.KodeSubGrp and c.KodeHDGrp=c.KodeGrp
        left outer join DBMERK d on d.KODEMERK=a.KodeMerk
        where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan and A.IsOtorisasi1 = 1
        order by A.NoBukti
    ", ["tahun" =>$periode->tahun , "bulan" => $periode->bulan]);

    $collection2 = collect($tempPenerimaan)->groupBy('NoBukti');
    $tempPenerimaan1 = [];
    foreach ($collection2 as $p) {
        array_push($tempPenerimaan1, $p);
    }

    return view('gudang.perintahopname' , [
      "menul0"            => $menul0,
      "periode"           => $periode,
      "tempOutstanding"   => $tempOutstanding1,   
      "tempPenerimaan"  => $tempPenerimaan1,  
      "akses"             => $akses
    ]);

  }

  public function loadAll () {


    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $tempOutstanding = DB::connection("SML")->select("declare @Tahun int, @Bulan int

select @Tahun= :tahun , @Bulan=:bulan

select b.NAMAHDGRP,c.NamaSubGrp,d.NAMAMERK,a.*
,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi
from DBPerintahOp A
left outer join DBHDGROUP b on b.KODEHDGRP=a.KodeHdGrp
left outer join DBSubGroup c on c.KodeSubGrp=a.KodeSubGrp  and C.KodeHDGrp=C.KodeGrp
left outer join DBMERK d on d.KODEMERK=a.KodeMerk
where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan and A.IsOtorisasi1 = 0
order by A.NoBukti

" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);
        // $tempOutstanding = [];
        // foreach ($outstanding as $p) {
        //   // code...
        //   array_push($tempOutstanding, $p);
        // }
        //

        $collection1 = collect($tempOutstanding)->groupBy('NoBukti');
        $tempOutstanding1 = [];
        foreach ($collection1 as $p) {
          // code...
          array_push($tempOutstanding1, $p);
        }

        $tempPenerimaan = DB::connection("SML")->select("
        declare @Tahun int, @Bulan int
        select @Tahun= :tahun , @Bulan=:bulan

        select b.NAMAHDGRP,c.NamaSubGrp,d.NAMAMERK,a.*,
            Cast(Case when 
                (Case when A.IsOtorisasi1=1 then 1 else 0 end+
                 Case when A.IsOtorisasi2=1 then 1 else 0 end+
                 Case when A.IsOtorisasi3=1 then 1 else 0 end+
                 Case when A.IsOtorisasi4=1 then 1 else 0 end+
                 Case when A.IsOtorisasi5=1 then 1 else 0 end) = A.MaxOL 
                 then 0 else 1 end As Bit) NeedOtorisasi
        from DBPerintahOp A
        left outer join DBHDGROUP b on b.KODEHDGRP=a.KodeHdGrp
        left outer join DBSubGroup c on c.KodeSubGrp=a.KodeSubGrp and c.KodeHDGrp=c.KodeGrp
        left outer join DBMERK d on d.KODEMERK=a.KodeMerk
        where year(A.Tanggal)=@Tahun and month(A.Tanggal)=@Bulan and A.IsOtorisasi1 = 1
        order by A.NoBukti
    ", ["tahun" =>$periode->tahun , "bulan" => $periode->bulan]);

    $collection2 = collect($tempPenerimaan)->groupBy('NoBukti');
    $tempPenerimaan1 = [];
    foreach ($collection2 as $p) {
        array_push($tempPenerimaan1, $p);
    }

    return [
        "tempOutstanding" => $tempOutstanding1,
        "tempPenerimaan"  => $tempPenerimaan1 
    ];
  }

  public function getListPengajuan (Request $req) {
    $username = \Auth::user()->username;
    $values = [
      $req->tglawal,
      $req->tglakhir,
      $req->valas,
      $req->tipe,
      $username,
      2
    ];

    $res = DB::connection('SML')->update('exec sp_CariHutangJT ?,?,?,?,?,?',$values);

    if ($req->tipelist == 0 ) {
      $tempOutstanding = DB::connection("SML")->select("
      Select * from dbTempHUTPIUTJt
      where IDUSER = :username" , ["username" => $username]);

      return $tempOutstanding;

    } else {
      $tempOutstanding = DB::connection("SML")->select("
      Select * from dbTempHUTPIUTJt
      where IDUSER = :username and kodecustsupp = :kodecustsupp" , ["username" => $username, "kodecustsupp" => $req->kodecustsupp]);

      return $tempOutstanding;
    }



  }

  public function listGudang (Request $req) {

    return DB::connection("SML")->select("select KodeGdg, Nama from dbGudang where isaktif=1 order by KodeGdg");

  }

public function listHeadGroup (Request $req) {

  return DB::connection("SML")->select("select KodeHDGrp,NamaHDGRP from  dbHdGroup");

}

public function listKategori (Request $req) {

  return DB::connection("SML")->select("select KodeSubGrp, NamaSubGrp from DBsubgroup where KodeHdGrp= :kode " , ["kode" => $req->kode]);

}

public function getDetailCetak(Request $req)
  {
      $noBukti = $req->input('NOBUKTI');

      $cetak = DB::connection("SML")->select(
          "EXEC dbo.CetakPerintahOPname ?",
          [$noBukti]
      );

      $tempCetak1 = [];
      foreach ($cetak as $p) {
          array_push($tempCetak1, $p);
      }

      return $tempCetak1;
  }

public function listSubKategori (Request $req) {

  return DB::connection("SML")->select("select Urut,Keterangan
from DBSubGroupJnsTambah
where HDgroup= :kode
and KodeSubGrp= :kode1
and kodegrp='BJ'" , ["kode" => $req->kode , "kode1" => $req->kode1]);

}

  public function listMerk (Request $req) {

    return DB::connection("SML")->select("select KODEMERK, NAMAMERK from DBMERK");

  }

  public function listBarang (Request $req) {
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $values = [
      0,
      0,
      $req->gudang,
      $req->kodehdgrp,
      $req->kodekategori,
      $req->kodemerk,
      $req->nobukti,
      $periode->bulan,
      $periode->tahun,
      $req->tanggal
    ];

    $res = DB::connection('SML')->update('exec Sp_RefreshTempPerintahOpname ?,?,?,?,?,?,?,?,?,?',$values);

    return DB::connection("SML")->select("select * from TempPerintahOpname");

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









  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbperintahop set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','PROP',$req->nobukti,'',0,'dbperintahop');
    
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbperintahop set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','PROP',$req->nobukti,$req->pket,0,'dbperintahop');
    return $res;
  }


  public function spAdd (Request $req) {

    $username = \Auth::user()->username;
    $listData = $req->tempData;
    $jmlrecord = $req->jmlrecord;
    $xurut=0;
//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from dbperintahopDET where Nobukti = :nobukti', ['nobukti' => $req->choice]);
    if ($purut){

        if ($req->choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from dbperintahopDET where Nobukti = :nobukti', ['nobukti' => $req->choice]);
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


    if ($req->choice =='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PRP',$nobukti,'',$xurut,'dbperintahopDET');
      }



    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from DBDPH where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

      foreach ($listData as $d)  {

        DB::connection('SML')->statement('exec sp_PerintahOpname ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
          $req->choice,
          $req->nobukti,
          $req->nourut,
          $req->tanggal,
          $req->gudang,
          $req->kodehdgrp,
          $req->kodekategori,
          $req->kodemerk,
          0,
          $d['KodeBrg'],
          $d['NamaBrg'],
          $d['Satuan'],
          0,
          $req->keterangan ? $req->keterangan : '',
          $req->kodesubkategori,
          $req->tanggalpelaksanaan ? $req->tanggalpelaksanaan : '' ,
          $req->tanggalcutoff ? $req->tanggalcutoff : '',
        ]);

        // $jmlrecord = 1;



      }
    if ($req->choice !='D'){
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PROP',$nobukti,'',$xurut,'dbperintahopDET');
      }
      return 1;

  }

    public function spKoreksi (Request $req) {
      $username = \Auth::user()->username;
      DB::connection('SML')->statement('exec sp_PerintahOpname ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
        $req->choice,
        $req->nobukti,
        $req->nourut,
        $req->tanggal,
        $req->gudang,
        $req->kodehdgrp,
        $req->kodekategori,
        $req->kodemerk,
        $req->urut,
        $req->kodebrg,
        $req->namabrg,
        $req->satuan,
        0,
        $req->keterangan ? $req->keterangan : '',
        $req->kodesubkategori,
        $req->tanggalpelaksanaan ? $req->tanggalpelaksanaan : '' ,
        $req->tanggalcutoff ? $req->tanggalcutoff : '',
      ]);

      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( $req->choice,'PROP',$nobukti,'',$xurut,'dbperintahopDET');
        return 1;
    }


}
