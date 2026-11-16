<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWRSPBWEB;
use App\Models\VwOutSPBRSPB;
use App\Models\VwOutSPBRSPBLIMIT;

class NewReturSuratJalanController extends Controller
{




  public function index () {
    $kodemenu = '210204';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VwOutSPBRSPBLIMIT::all()->sortBy('URUT')->groupBy(['NOBUKTI', 'KodeGdg']);
    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      foreach ($o as $m) {
        // code...
        array_push($tempOutstanding, $m);
      }
    }

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWRSPBWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWRSPBWEB::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.newretursuratjalan' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }

  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select RSPB from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->RSPB,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }


  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = VwOutSPBRSPB::all()->where('NOBUKTI', $req->input('NOBUKTI'))->where('KodeGdg', $req->input('kodegdg'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = VwOutSPBRSPB::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->whereRaw('KodeGdg = cast(? as varchar(50))', [$req->input('kodegdg')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = VWRSPBWEB::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = VWRSPBWEB::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = VWRSPBWEB::where('NOBUKTI' , $req->input('norspb'))->select('KODEBRG')->get()->toArray();

    $tempAddList = VwOutSPBRSPB::select()->where('NOBUKTI', $req->input('nosj'))->where('KodeGdg', $req->input('kodegdg'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();

    return $tempAddList;
  }

  public function addSuratJalan (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');
    $nosj = $req->input('nosj');
    $norspb = $req->input('norspb');
    // return [$data,$date,$nourut,$nosj,$norspb];


    DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);

    $check = DB::connection('SML')->select('select * from DBRSPBDet where NOBUKTI = :nobukti',["nobukti" => $norspb]);
    if ($check) {
      return 2;
    }


    foreach ($data as $d) {
      $values = [$username, $nosj,$d['Tahun'],$d['Bulan'],'RSPB',$d['inputQntTerima'],$d['URUT']];
      DB::connection("SML")->statement('exec sp_RefreshTempOutstandingRSPB ?,?,?,?,?,?,?',$values);
    }
    $tempValues = [$norspb, $nourut, $date, $username, 0];
    DB::connection('SML')->statement('exec sp_InsertOutstandingSpbweb ?,?,?,?,?', $tempValues);
    return 1;
    //   create Procedure [dbo].[sp_InsertOutstandingSpbweb]
    // --declare
    // @NoBukti varchar(20),
    // @NoUrut varchar(10),
    // @Tanggal datetime,
    // @Iduser Varchar(20),
    // @JmlRecord integer    === 0



    // create   Procedure [dbo].[sp_RefreshTempOutstandingRSPB]
    // --Declare
    // @IDUser varchar(30),
    // @NoBukti varchar(20),
    // @Tahun int,
    // @Bulan int,
    // @Trans Varchar(5),  'RSPB'
    // @Qnt numeric(18,2)=0,
    // @urut integer=0

  }

  public function loadAll () {
    // Dinonaktifkan 2026-10-20: '014' = kode menu Retur Jual (salin-tempel), padahal menu ini Retur Srt Jln
    // (index() memakai '016'). Akibatnya hak tambah/koreksi/cetak berganti setelah simpan.
    // $kodemenu = '210203';
    $kodemenu = '210204';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VwOutSPBRSPBLIMIT::all()->sortBy('URUT')->groupBy(['NOBUKTI', 'KodeGdg']);

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      foreach ($o as $m) {
        // code...
        array_push($tempOutstanding, $m);
      }
    }

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWRSPBWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWRSPBWEB::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }

    return [
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ];
  }

  public function spKoreksi (Request $req) {
    $values = [
      $req->input('choice'),
      $req->input('norspb'),
      $req->input('nourut'),
      $req->input('inputDate'),
      $req->input('nosj'),
      $req->input('kodecustsupp'),
      $req->input('nopolkend'),
      $req->input('container'),
      $req->input('nocontainer'),
      $req->input('noseal'),
      $req->input('catatan'),
      $req->input('urut'),
      $req->input('urutsj'),
      $req->input('kodebrg'),
      $req->input('qntTerima'),
      $req->input('qntTerima2'),
      $req->input('sat_1'),
      $req->input('sat_2'),
      $req->input('nosat'),
      $req->input('isi'),
      $req->input('netw'),
      $req->input('grossw'),
      \Auth::user()->username,
      $req->input('isempty'),
      $req->input('namabrg'),
      $req->input('flagmenu'),
      $req->input('flagtipe'),
      $req->input('retursupp')
    ];

  //   @Choice varchar(1),
  // @NOBUKTI varchar(30),
  // @NoUrut varchar(7),
  // @TANGGAL datetime,
  // @NoSPP varchar(30),
  // @KODECUSTSUPP varchar(15),
  // @NoPolKend varchar(50),
  // @Container varchar(50),
  // @NoContainer varchar(50),
  // @NoSeal varchar (500), // 10
  // @Catatan varchar(4000),
  // @URUT int,
  // @UrutSPP int,
  // @KODEBRG varchar(25),
  // @QNT numeric(18,3),
  // @QNT2 numeric(18,3),
  // @SAT_1 varchar(5),
  // @SAT_2 varchar(5),
  // @NoSat tinyint,
  // @ISI numeric(18,2), // 20
  // @NetW numeric(18,2),
  // @GrossW numeric(18,2),
  // @IDUser varchar(30),
  // @IsEmpty tinyint,
  // @Namabrg varchar(200),
  // @Flagmenu tinyint,
  // @FlagTipe Tinyint,
  // @ReturSupp Tinyint // 28

    DB::connection('SML')->statement('exec sp_RSPB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;
  }








}
