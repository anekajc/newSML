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


class KoreksiStockController extends Controller
{

  public function index(Request $req) {
    $kodemenu = '06030';

    $akses = app('App\Http\Controllers\GlobalController')->getAkses1($kodemenu , $req->path());
    // $akses = DBFLMENU::where('USERID', \Auth::user()->username)-> where('L1', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0(6);


    $tempOutstanding = DB::connection("SML")->select("declare @Tahun int, @Bulan int

    Select * from vwMasterKoreksi
    where month(tanggal)= :bulan and year(tanggal)= :tahun and nobukti like '%KRS%' and isotorisasi1 = 0" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);

    $tempOutstanding2 = DB::connection("SML")->select("declare @Tahun int, @Bulan int

    Select * from vwMasterKoreksi
    where month(tanggal)= :bulan and year(tanggal)= :tahun and nobukti like '%KRS%' and isotorisasi1 = 1" , ["tahun" =>$periode->tahun , "bulan" => $periode->bulan ]);


    return view('gudang.koreksistock' , [
      "menul0" => $menul0,
      "periode" => $periode,
      "tempOutstanding" => $tempOutstanding,
      "tempOutstanding2" => $tempOutstanding2,
      "akses" => $akses,
      "listBarangAll" => []
    ]);

  }

  public function loadAll()
{
    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    // Belum Otorisasi
    $tempOutstanding = DB::connection("SML")->select("
        declare @Tahun int, @Bulan int
        Select * 
        from vwMasterKoreksi
        where month(tanggal) = :bulan 
          and year(tanggal) = :tahun 
          and nobukti like '%KRS%'
          and isotorisasi1 = 0
    ", [
        "tahun" => $periode->tahun,
        "bulan" => $periode->bulan
    ]);

    // Sudah Otorisasi
    $tempOutstanding2 = DB::connection("SML")->select("
        declare @Tahun int, @Bulan int
        Select * 
        from vwMasterKoreksi
        where month(tanggal) = :bulan 
          and year(tanggal) = :tahun 
          and nobukti like '%KRS%'
          and isotorisasi1 = 1
    ", [
        "tahun" => $periode->tahun,
        "bulan" => $periode->bulan
    ]);

    return [
        "tempOutstanding" => $tempOutstanding,
        "tempOutstanding2" => $tempOutstanding2
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


    public function spUpdateHeader (Request $req) {
      $res = DB::connection('SML')->update("update dbkoreksi set note = :keterangan where nobukti = :nobukti", ["keterangan" => $req->keterangan , "nobukti" => $req->nobukti]);
      return $res;

    }

public function listHeadGroup (Request $req) {

  return DB::connection("SML")->select("select KodeHDGrp,NamaHDGRP from  dbHdGroup");

}

public function listKategori (Request $req) {

  return DB::connection("SML")->select("select KodeSubGrp, NamaSubGrp from DBsubgroup where KodeHdGrp= :kode " , ["kode" => $req->kode]);

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

  public function getDetail (Request $req ) {



        $tempOutstanding = DB::connection("SML")->select("

declare @NoBukti varchar(20)

select  @NoBukti= :nobukti

Select A.IsOtorisasi1, A.nourut , A.Nobukti, A.Tanggal, A.note, A.ISCetak, Urut, b.kodebrg, C.namaBrg, A.KodeGdg, D.Nama NamaGDG,
       b.SaldoComp, b.QntOpname, b.Selisih,
       b.Qntdb, B.QntCr, b.Harga, (b.qntdb-b.qntcr)*b.harga as Total,
       (b.qntdb)*b.harga  HrgAdi, (b.qntcr)*b.harga HrgAdo,
       C.Sat1 Satuan,C.Sat2 Satuan2,b.Saldo2Comp, b.Qnt2Opname, b.Selisih2,
       b.Qnt2db, B.Qnt2Cr,Iscek,Iscek2,C.isi2,Isnull(C.Nfix,0) Nfix
From dbKoreksi A
     left outer join dbKoreksiDet B on b.nobukti=a.nobukti
     left outer join dbBarang C on c.kodebrg=b.kodebrg
     left outer join dbGudang D on d.kodegdg=A.kodegdg
where A.NoBukti=@NoBukti
order by B.Urut
        " , ["nobukti" => $req->nobukti]);
    return $tempOutstanding;
  }

  public function spOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbkoreksi set isOtorisasi1 = 1, maxol = 1 , OtoUser1= :username , TglOto1 = :tanggal where nobukti = :nobukti", ["username" => \Auth::user()->username , "tanggal" => $tanggal , "nobukti" => $req->nobukti]);
    
    $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'oto','KRS',$req->nobukti,'',0,'dbkoreksi');
    return $res;
  }
  public function spBatalOtorisasi (Request $req) {
    $tanggal = date('Y-m-d H:i:s');
    $res = DB::connection('SML')->update("update dbkoreksi set isOtorisasi1 = 0, maxol = -1 , OtoUser1= '' , TglOto1 = NULL  where nobukti = :nobukti", [ "nobukti" => $req->nobukti]);
     $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingData( 'btloto','KRS',$req->nobukti,$req->pket,0,'dbkoreksi');
    
    return $res;
  }


  public function listBarang (Request $req) {

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();


    // return ["gudang" => $req->gudang , "bulan" => $periode->bulan , "tahun" => $periode->tahun];


    $listData = DB::connection('SML')->select("
        Select A.KodeBrg, A.NamaBrg,A.Sat1,A.Sat2, 
              Isnull(b.Qnt,0) QntSaldo, 
              Isnull(b.Qnt2,0) Qnt2Saldo
        from dbBarang A
        left Outer Join (
            select a.KodeGdg,Kodebrg,
                  Sum(SaldoQnt)Qnt,
                  Sum(Saldo2Qnt)Qnt2 
            from DBStockBrg a
            Left Outer Join dbGudang b 
                On a.KodeGdg=b.KodeGdg 
            where a.Kodegdg= :gudang 
              and Bulan= :bulan 
              and Tahun= :tahun
            group by a.kodegdg,kodebrg
        ) b On b.kodebrg=a.KodeBrg
        where A.KodeGrp='BJ'
          and a.isAktif=1
          and (
                a.namabrg like '%".$req->search."%'
                or a.kodebrg like '%".$req->search."%'
              )
        order by A.KodeBrg
    ", [
        "gudang" => $req->gudang,
        "bulan"  => $periode->bulan,
        "tahun"  => $periode->tahun
    ]);
    return $listData;
  }

  public function spAdd (Request $req) {

    $username = \Auth::user()->username;
    $jmlrecord = $req->jmlrecord;

    $xurut=0;
//  return ["asd" => $nobukti] ;
     $purut = DB::connection('SML')->select('select * from DBKOREKSIDET where Nobukti = :nobukti', ['nobukti' => $req->nobukti]);
    if ($purut){

        if ($req->choice=='I' ){

        $purut = DB::connection('SML')->select('select max(urut)+1 xurut from DBKOREKSIDET where Nobukti = :nobukti', ['nobukti' => $req->nobukti]);
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
      $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingDataTrans( $req->choice,'KRS',$req->nobukti,'',$xurut,'DBKOREKSIDET');
      }


    if ($jmlrecord == 0 ) {
      $check = DB::connection('SML')->select('select * from DBKOREKSI where Nobukti = :nobukti',["nobukti" => $req->nobukti]);
        if ($check) {
          return 2;
      }
    }

    $check = DB::connection('SML')->select('select * from dbbarang where kodebrg = :kodebrg',["kodebrg" => $req->kodebrg]);
      if (!$check) {
        return 3;
    }

    DB::connection('SML')->statement('exec SP_KOREKSI ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
      $req->choice , // Choice = 'I',
      $req->nobukti,  // Nobukti = 'MGL/OPN/00015/0625',
      $req->nourut,// NoUrut = '00015',
      $req->tanggal,// Tanggal = getDate(),
      $req->keterangan ,// Note = '',
      $req->urut ,// Urut = 0,
      $req->kodebrg , // KodeBrg = 'JJ0198',
      $req->gudang, // KodeGdg = 'G01',
      $req->qntsaldo , // SaldoComp = --qtycomp,
      $req->qntopname, // QntOpname = 0,
      $req->selisih, // Selisih = 0,
      $req->qntdb, // QntDB  = 0 ,
      $req->qntcr, // QntCR = 0 ,
      $req->nosat, // NoSat = 1,
      $req->isi, // Isi = 1 ,
      $req->satuan ,// Satuan = 'PCS',
      $req->harga,// Harga = 0,
      '',// keterangan = '',
      $req->qntsaldo,// Saldo2Comp  = 0,
      $req->qntopname,// Qnt2Opname = 0 ,
      $req->selisih,// Selisih2  = 0,
      $req->qntdb,// Qnt2DB = 0,
      $req->qntcr,// Qnt2CR = 0,
      0,// isCek = 0,
      0,// IsCek2 = 0 ,
      'OPN' ,// Tipe = 'OPN',
      '',// NoPerintahOP = 'SPL/POP/00005/1015' ,
      0, 0// urutPerintahOP = 1
    // urutPerintahOP = 1
    ]);

        // $jmlrecord = 1;

      if ($req->choice !='D'){
       $tempX2 =  app('App\Http\Controllers\GlobalController')->LoggingDataTrans($req->choice,'KRS',$req->nobukti,'',$xurut ,'DBKOREKSIDET');
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


        return 1;
    }


}
