<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\NewMenu;
use App\Model\NewAksesMenu;
use App\Model\NewPeriode;
use App\Model\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Model\VWRSPBWEB;
use App\Model\VwOutSPBRSPB;
use App\Model\VwOutSPBRSPBLIMIT;

use App\Model\OutSOSPBWEB;
use App\Model\Vwpersiapanspb;

class PersiapanSpbController extends Controller
{




  public function index () {

    $periode = NewPeriode::where('user_id' , \Auth::id())->first();
    $menul0 = app('App\Http\Controllers\NewMenuController')->getMenuL0();

    $outstanding = OutSOSPBWEB::all()->sortBy('NOBUKTI')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    $penerimaan = Vwpersiapanspb::all()->where('bulan',$periode->bulan )->where('tahun', $periode->tahun)->sortBy('urut')->groupBy('nobukti');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }

    $expedisi = DB::connection("SML")->select('select * from dbcustsupp where jenis = :id', ['id' => 3]);

    return view('persiapanspb' , [
      "periode" => $periode,
      "menul0" => $menul0,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "expedisi" => $expedisi
    ]);
  }

  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::id())->first();
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

    $detailOutstanding = OutSOSPBWEB::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {
    // return $req->input('NOBUKTI');
    $detailPenerimaan = Vwpersiapanspb::all()->where('nobukti',$req->input('NOBUKTI'))->sortBy('Urut');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = Vwpersiapanspb::where('nobukti' , $req->input('norspb'))->select('kodebrg')->get()->toArray();

    $tempAddList = OutSOSPBWEB::select()->where('nobukti', $req->input('nosj'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();

    return $tempAddList;
  }

  public function addSuratJalan (Request $req) {

    $periode = NewPeriode::where('user_id' , \Auth::id())->first();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');
    $nosj = $req->input('nosj');
    $norspb = $req->input('norspb');
    $nooutso = $req->input('nooutso');
    $nopolkendaraan = $req->input('nopolkendaraan');
    $expedisi = $req->input('expedisi');
      // return [$data,$date,$nourut,$nosj,$norspb,$nooutso,$nopolkendaraan,$expedisi];
    DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);
    foreach ($data as $d) {
      // $values = [$username, $nosj,'2023','4','SPB',$d['inputQntTerima'],$d['URUT']];
      $values = [
          $username,
          $nosj,
          $periode->tahun,
          $periode->bulan,
          'SPB',
          $d['inputQntTerima'],
          $d['URUT']
      ];

      DB::connection("SML")->statement('exec sp_RefreshTempOutpersiapanspb ?,?,?,?,?,?,?',$values);
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

  public function loadAll () {
    $periode = NewPeriode::where('user_id' , \Auth::id())->first();

    $outstanding = OutSOSPBWEB::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    $penerimaan = Vwpersiapanspb::all()->where('bulan',$periode->bulan )->where('tahun', $periode->tahun)->sortBy('urut')->groupBy('nobukti');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }

    return [
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan
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

    DB::connection('SML')->statement('exec sp_SPBweb ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;
  }








}
