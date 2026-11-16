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

use App\Models\OutSOSPBWEB;
use App\Models\Vwpersiapanspb;

class PersiapanSpbController extends Controller
{





  public function index () {
    $kodemenu = '210307';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    //$outstanding = OutSPBWEB::all()->sortBy('URUT')->groupBy('NOSO');
    $outstanding = OutSOSPBWEB::all()->sortBy('URUT')->groupBy('NOSO');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    // Dinonaktifkan 2026-10-19: memuat seluruh view lalu menyaring periode di PHP (lambat).
    // $penerimaan = Vwpersiapanspb::all()->where('bulan',$periode->bulan )->where('tahun', $periode->tahun)->sortByDesc('tanggal')->groupBy('nobukti');
    // Pengganti: saring periode yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = Vwpersiapanspb::where('bulan', $periode->bulan)->where('tahun', $periode->tahun)->get()->sortByDesc('tanggal')->groupBy('nobukti');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }

    $expedisi = DB::connection("SML")->select('select * from dbcustsupp where jenis = :id', ['id' => 3]);

    return view('wms.persiapanspb' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "expedisi" => $expedisi,
      "akses" => $akses
    ]);
  }

  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select SPB from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->SPB,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }


  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = OutSOSPBWEB::all()->where('NOSO', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = OutSOSPBWEB::query()->whereRaw('NOSO = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailOutstandingKolli (Request $req) {

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = OutSOSPBWEB::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = OutSOSPBWEB::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {
    // return $req->input('NOBUKTI');
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = Vwpersiapanspb::all()->where('nobukti',$req->input('NOBUKTI'))->sortBy('Urut');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = Vwpersiapanspb::query()->whereRaw('nobukti = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('Urut');
    // $detailPenerimaan = OutSOSPBWEB::all()->where('NOBUKTI',$req->input('NOBUKTI'))->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getDetailPenerimaancetak (Request $req) {

    // $detailPenerimaan = Vwpersiapanspb::all()->where('nobukti',$req->input('NOBUKTI'))->where ->sortBy('Urut');

     $detailPenerimaan = DB::connection('SML')->select('
     select * from Vwpersiapanspb where NOBUKTI = :nobukti and qntcetak<>0 order by urut',
     ["nobukti" => $req->input('NOBUKTI')]
    );
 
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = Vwpersiapanspb::where('nobukti' , $req->input('norspb'))->select('kodebrg')->get()->toArray();

    $tempAddList = OutSOSPBWEB::select()->where('NOSO', $req->input('nosj'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();

    return $tempAddList;
  }

  public function addSuratJalan (Request $req) {


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');
    // $nosj = $req->input('nosj');
    $norspb = $req->input('norspb');
    $nooutso = $req->input('nooutso');
    $nopolkendaraan = $req->input('nopolkendaraan');
    $expedisi = $req->input('expedisi');
      // return [$data,$date,$nourut,$nosj,$norspb,$nooutso,$nopolkendaraan,$expedisi];

      // select * from dbSPB where NoBukti = 'SML/SJ/00002/0523'
      $check = DB::connection('SML')->select('select * from dbSPB where NOBUKTI = :nobukti',["nobukti" => $norspb]);
      if ($check) {
        return 2;
      }
      $checkSaldo = 0;
    DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);
    foreach ($data as $d) {
      // $values = [$username, $nosj,'2023','4','SPB',$d['inputQntTerima'],$d['URUT']];
      $values = [
          $username,
          $d['NOBUKTI'],
          $periode->tahun,
          $periode->bulan,
          'SPB',
          $d['inputQntTerima'],
          $d['URUT'],
          $d['NOSO'],
          $d['URUTSO']
      ];

      $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?',[$d['nosat'], $date, $d['kodegdg'],  $d['KODEBRG']]);
      // array_push($checkSaldo, ["input" => (float)$d['inputQntTerima']    , "saldoqnt" => (float)$check2[0]->SALDOQNT ]);
      if ($check2) {
        if ((int)$d['inputQntTerima'] > (int)$check2[0]->SALDOQNT ) {
          // return 'a';
          $checkSaldo = 1;
        }
      } else {
        // return 'b';
        $checkSaldo = 1;
      }


      //cek periode TANGGAL
      $check3 = DB::connection('SML')->select("select  case when month(:tgl) = (select bulan from DBPERIODE where user_id=:iduser1)
                                              and year(:tgl2)= (select tahun from DBPERIODE where user_id=:iduser2)

                                              then 0 else 1 End pp  ",
                                              ["tgl"=>$date,"iduser1"=>\Auth::user()->username,"tgl2"=>$date,"iduser2"=>\Auth::user()->username]);

        if (($check3[0]->pp ) ==1) {
          return  8;
        }


      DB::connection("SML")->statement('exec sp_RefreshTempOutpersiapanspb ?,?,?,?,?,?,?,?,?',$values);
    }
    // return $checkSaldo ;
    if ($checkSaldo) {
      // code...
      return 3;
    }

    $check4 = DB::connection('SML')->select('exec sp_checksospb ?',[$username]);
    // $check4 = [];
    if ($check4) {
      return 4;
    } 

    $tempValues = [$norspb, $nourut, $date, $username, 0,$nopolkendaraan,$expedisi];
    DB::connection('SML')->statement('exec sp_InsertOutpersiapanspb ?,?,?,?,?,?,?', $tempValues);

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

  public function updateCetakKolli (Request $req) {

    $res = DB::connection('SML')->statement('update	dbspb set cetakkolli = :jumlahPrint where nobukti = :nobukti',['jumlahPrint' => $req->jumlahPrint , 'nobukti' => $req->nobukti   ]);
    return 1;
  }

  public function spOtorisasi1 (Request $req) {
    $username = \Auth::user()->username;
    $nobukti =  $req->nobukti;
    $otorisasi = $req->otorisasi;

    if ($otorisasi == 0 ) {
      $username = '';
      $tanggal = null;
    }


    $update = DB::connection('SML')->update("update dbSPB set IsOtorisasi1 = :otorisasi , OtoUser1 = :username , TglOto1 = getDate() , MaxOL = 1 where nobukti = :nobukti", ['otorisasi' => $otorisasi, 'username' => $username,  'nobukti' => $nobukti ] );
    return $update;

  }


public function updateCetakspb (Request $req) {
    $nobukti =  $req->nobukti;
     $res = DB::connection('SML')->statement('update  dbspb set cetakke = Isnull(cetakKe,0) + 1 where nobukti = :nobukti',[ 'nobukti' => $nobukti ]);
  }


  public function loadAll () {
    $kodemenu = '210307';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = OutSOSPBWEB::all()->sortBy('URUT')->groupBy('NOSO');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    // Dinonaktifkan 2026-10-19: memuat seluruh view lalu menyaring periode di PHP (lambat).
    // $penerimaan = Vwpersiapanspb::all()->where('bulan',$periode->bulan )->where('tahun', $periode->tahun)->sortByDesc('tanggal')->groupBy('nobukti');
    // Pengganti: saring periode yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = Vwpersiapanspb::where('bulan', $periode->bulan)->where('tahun', $periode->tahun)->get()->sortByDesc('tanggal')->groupBy('nobukti');
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


// ALTER Procedure [dbo].[sp_SPBweb]
// @Choice varchar(1),
// @NOBUKTI varchar(30),
// @NoUrut varchar(7),
// @TANGGAL datetime,
// @NoSPP varchar(30),
// @KODECUSTSUPP varchar(15),
// @NoPolKend varchar(50),
// @Container varchar(50),
// @NoContainer varchar(50),
// @NoSeal varchar (500),
// @Catatan varchar(4000),
// @URUT int,
// @UrutSPP int,
// @KODEBRG varchar(25),
// @QNT numeric(18,3),
// @QNT2 numeric(18,3),
// @SAT_1 varchar(5),
// @SAT_2 varchar(5),
// @NoSat tinyint,
// @ISI numeric(18,2),
// @NetW numeric(18,2),
// @GrossW numeric(18,2),
// @IDUser varchar(30),
// @IsEmpty tinyint,
// @Namabrg varchar(200),
// @Flagmenu tinyint,
// @FlagTipe Tinyint,
// @ReturSupp Tinyint

    DB::connection('SML')->statement('exec sp_SPBweb ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;
  }








}
