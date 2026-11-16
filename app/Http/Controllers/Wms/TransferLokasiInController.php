<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWSerahSampleOut;


use App\Models\VWOutSerahSample;
use App\Models\VWOutPRTLO;
use App\Models\VWTranLokasiOut;


class TransferLokasiInController extends Controller
{




  public function index () {
    $kodemenu = '210206';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOutPRTLO::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }
    //
    // $penerimaan = VWTRansferOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWTranLokasiOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWTranLokasiOut::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.transferlokasiin' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }


  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select TLO from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->TLO,
        $periode->bulan,
        $periode->tahun,
        $username,
        // $req->get('nopr')
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = VWOutPRTLO::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = VWOutPRTLO::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = VWTranLokasiOut::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = VWTranLokasiOut::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  // public function getKoreksiAddList (Request $req) {
  //   $tempPenerimaan = VWSerahSampleOut::where('NOBUKTI' , $req->input('nossp'))->select('KODEBRG')->get()->toArray();
  //
  //   $tempAddList = VWOutSerahSample::select()->where('NOBUKTI', $req->input('noout'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();
  //
  //   return $tempAddList;
  // }

  public function spAdd (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');
    $noout = $req->input('noout');
    $nossp = $req->input('nossp');


    DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser',['idUser' => $username ]);

    // if ($jmlRecord == 0 && $choice == 'I') {
      // code...
      $check = DB::connection('SML')->select('select * from DBTRANSFERDET where NOBUKTI = :nobukti',["nobukti" => $nossp]);
      if ($check) {
        return 2;
      }
    // }


    foreach ($data as $d) {
      $values = [$username, $noout,$d['Tahun'],$d['Bulan'],$d['inputQntTerima'],$d['Urut']];
      DB::connection("SML")->statement('exec sp_RefreshOutTempTFLIN ?,?,?,?,?,?',$values);
      // return $values;
    }
    // return 1;


    $tempValues = [$nossp, $nourut, $noout, $username, $date, $req->input('pTerima')];
    // return $tempValues;
    DB::connection('SML')->statement('exec SP_InsertOutTFLIN ?,?,?,?,?,?', $tempValues);

    return 1;



  }

  public function loadAll () {
    $kodemenu = '210206';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOutPRTLO::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWTranLokasiOut::all()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWTranLokasiOut::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
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


    // ALTER Procedure [dbo].[SP_TRANSFERWEB1]
    // @Choice Varchar(1),
    // @Nobukti Varchar(20),
    // @NoUrut varchar(10),
    // @Tanggal Datetime,
    // @Note varchar(200),
    // @urut int,
    // @Kodebrg varchar(25),
    // @GdgAsal varchar(15),
    // @GdgTujuan varchar(15),
    // @Sat_1 varchar(5), --
    // @Sat_2 varchar(5), --
    // @Qnt numeric(18,2), --
    // @QNt2 numeric(18,2), --
    // @NoSat tinyint,
    // @Isi numeric(18,2)=0, --
    // @IDUSER varchar(15),
    // @NoPenyerahan varchar(100), --
    // @NoOutBrg varchar(30), --
    // @UrutOutBrg int, --
    // @QntX Numeric(18,2)=0, -- qnt terima tfl out
    // @pTerima bit = 0

  $values = [
    $req->input('choice'),
    $req->input('nossp'),
    $req->input('nourut'),
    $req->input('tanggal'),
    $req->input('note'),
    $req->input('urut'),
    $req->input('kodebrg'),
    $req->input('gdgasal'),
    $req->input('gdgtujuan'),
    $req->input('sat_1'),
    $req->input('sat_2'),
    0,
    0,
    $req->input('nosat'),
    $req->input('isi'),
    \Auth::user()->username,
    '',
    '',
    0,
    $req->input('qntTerima'),
    $req->input('pTerima'),
  ];

  DB::connection('SML')->statement('exec SP_TRANSFERWEB1 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
  return 1;

  }





}
