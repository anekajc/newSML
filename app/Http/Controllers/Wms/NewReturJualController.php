<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\vwOUtPOWMS;
use App\Models\VWtampilbeli;
use App\Models\NEWDBBELI;
use App\Models\VWOUtPrRJUal;
use App\Models\VwSpbRjual;



// use App\Http\Controllers\NewMenuController;

class NewReturJualController extends Controller
{


  public function index () {
    // VwSpbRjual
    $kodemenu = '210203';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOUtPrRJUal::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $returGudang = VwSpbRjual::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $returGudang = VwSpbRjual::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempReturGudang = [];
    foreach ($returGudang as $o) {
      // code...
      array_push($tempReturGudang, $o);
    }
    $gudang = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);

    return view('wms.newreturjual' , [
      "periode" => $periode,
      "gudang" => $gudang,
      "outstandingArray" => $tempOutstanding,
      "returGudangArray" => $tempReturGudang,
      "akses" => $akses
    ]);


  }

  public function loadAll () {
    $kodemenu = '210203';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOUtPrRJUal::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $returGudang = VwSpbRjual::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $returGudang = VwSpbRjual::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempReturGudang = [];
    foreach ($returGudang as $o) {
      // code...
      array_push($tempReturGudang, $o);
    }

    return [
      "outstandingArray" => $tempOutstanding,
      "returGudangArray" => $tempReturGudang,
      "akses" => $akses
    ];
  }

  public function getDetailReturGudang (Request $req) {
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailReturGudang = VwSpbRjual::all()->where('NOBUKTI', $req->input('NOBUKTI'))->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailReturGudang = VwSpbRjual::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT');
    $tempReturGudang = [];
    foreach ($detailReturGudang as $dr) {
      // code...
      array_push($tempReturGudang,$dr);
    }
    return $tempReturGudang;
  }

  public function spKoreksi (Request $req) {
    // return $req->input('noprj');
    $values = [
      $req->input('choice'),
      $req->input('nospr'),
      $req->input('nourut'),
      $req->input('tanggal'),
      $req->input('noprj'),
      $req->input('kodecustsupp'),
      $req->input('nopolkend'),
      $req->input('container'),
      $req->input('nocontainer'),
      $req->input('noseal'),
      $req->input('catatan'),
      $req->input('urut'),
      $req->input('urutrpj'),
      $req->input('kodebrg'),
      $req->input('inputQty'),
      $req->input('inputQty1'),
      $req->input('inputQty2'),
      $req->input('sat_1'),
      $req->input('nosat'),
      $req->input('isi'),
      $req->input('netw'),
      $req->input('grossw'),
      \Auth::user()->username,
      $req->input('isempty'),
      $req->input('namabarang'),
      $req->input('sopir'),
      $req->input('flagmenu'),
      $req->input('kodegdg'),
      $req->input('flagtipe'),
      $req->input('tipePPN'),
      $req->input('noSO'),
      $req->input('noBatch'),
      $req->input('flagKembali'),
      $req->input('qntReject'),
      $req->input('qntReject1'),
      $req->input('qntReject2'),
      $req->input('sat_2')
    ];

    DB::connection('SML')->statement('exec sp_TransSPBRJUAL ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', $values);
    return 1;


// @Choice varchar(1),
// @NOBUKTI varchar(30),
// @NoUrut varchar(7),
// @TANGGAL datetime,
// @NoRPJ varchar(30), 5
// @KODECUSTSUPP varchar(15),
// @NoPolKend varchar(50), ""
// @Container varchar(50), ""
// @NoContainer varchar(50), ""
// @NoSeal varchar (500), "" 10
// @Catatan varchar(4000), ""
// @URUT int, urutspr ? urut dari edit, dari add 0
// @UrutRPJ int, ururpj
// @KODEBRG varchar(25),
// @QNT numeric(18,2), 15
// @QNT1 numeric(18,2),
// @QNT2 numeric(18,2),
// @SAT_1 varchar(5),
// @NoSat tinyint,
// @ISI numeric(18,2),
// @NetW numeric(18,2), 0
// @GrossW numeric(18,2), 0
// @IDUser varchar(30),
// @IsEmpty tinyint, 0
// @Namabrg varchar(200), 25
// @Sopir varchar(100), ""
// @Flagmenu tinyint, 1
// @kodegdg varchar(15),
// @FlagTipe tinyint, 1
// @TipePPN tinyint, 0 30
// @NOSO varchar(30), '0'
// @NoBatch varchar(50), '0'
// @FlagKembali Tinyint, 0
// @QntReject Numeric(18,2), 0
// @Qnt1Reject Numeric(18,2), 0
// @Qnt2Reject Numeric(18,2), 0
// @Sat_2 varchar(10)='' 37

}

  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = VWOUtPrRJUal::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = VWOUtPrRJUal::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getKoreksiAddList (Request $req) {
    $tempJual = VwSpbRjual::where('NOBUKTI' , $req->input('nospr'))->select('KODEBRG')->get()->toArray();

    $tempAddList = VWOUtPrRJUal::select()->where('NOBUKTI', $req->input('noprj'))->whereNotIn('KODEBRG', $tempJual)->get();

    return $tempAddList;
  }



  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select SPR from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->SPR,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }



  public function addReturJual (Request $req) {


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');
    $noprj = $req->input('noprj');
    $nospr = $req->input('nospr');

    // return [$date, $username, $nourut, $nospr, $noprj];

    $check = DB::connection('SML')->select('select * from dbSPBRJual where NOBUKTI = :nobukti',["nobukti" => $nospr]);
    if ($check) {
      return 2;
    }

    DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);
    foreach ($data as $d) {
      $values = [$username,$noprj,'',$d['URUT'],$d['inputQntTerima'],true];
      DB::connection("SML")->statement('exec sp_ReTempRJualGdgWEb ?,?,?,?,?,?',$values);

    }


    $tempValues = [$nospr, $nourut,$noprj, $username, 1, $date, ''];
    DB::connection('SML')->statement('exec SP_InsertPRRSPB ?,?,?,?,?,?,?', $tempValues);
    return 1;
  }

}
