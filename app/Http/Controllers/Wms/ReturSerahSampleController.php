<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWRSERAHSAMPLEWEB;
use App\Models\VwOutSSPRSSP;

class ReturSerahSampleController extends Controller
{




  public function index () {
    // Dinonaktifkan 2026-10-20: '016' = kode menu Retur Srt Jln (salin-tempel). Retur Serah Sample di
    // new_menu = '019'. Hak user 016 sudah disalin ke 019:
    // docs/update-db-DBSMLBARU/2026-10-20_akses_returserahsample_019.sql (jalankan saat deploy).
    // $kodemenu = '210204';
    $kodemenu = '210207';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VwOutSSPRSSP::all()->sortBy('URUT')->groupBy(['NOBUKTI', 'KodeGdg']);
    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      foreach ($o as $m) {
        // code...
        array_push($tempOutstanding, $m);
      }
    }

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWRSERAHSAMPLEWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWRSERAHSAMPLEWEB::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.returserahsample' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }

  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select RSS from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->RSS,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }


  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = VwOutSSPRSSP::all()->where('NOBUKTI', $req->input('NOBUKTI'))->where('KodeGdg', $req->input('kodegdg'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = VwOutSSPRSSP::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->whereRaw('KodeGdg = cast(? as varchar(50))', [$req->input('kodegdg')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = VWRSERAHSAMPLEWEB::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = VWRSERAHSAMPLEWEB::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = VWRSERAHSAMPLEWEB::where('NOBUKTI' , $req->input('norspb'))->select('KODEBRG')->get()->toArray();

    $tempAddList = VwOutSSPRSSP::select()->where('NOBUKTI', $req->input('nosj'))->where('KodeGdg', $req->input('kodegdg'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();

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

    $check = DB::connection('SML')->select('select * from DBRSERAHSAMPLEDET where NOBUKTI = :nobukti',["nobukti" => $norspb]);
    if ($check) {
      return 2;
    }


    foreach ($data as $d) {
      $values = [$username, $nosj,$d['Tahun'],$d['Bulan'],'RSS',$d['inputQntTerima'],$d['URUT']];
      DB::connection("SML")->statement('exec sp_RefreshTempOutstandingRSS ?,?,?,?,?,?,?',$values);
    }
    $tempValues = [$norspb, $nourut, $date, $username, 0];
    DB::connection('SML')->statement('exec sp_InsertOutstandingRSS ?,?,?,?,?', $tempValues);
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
    // Dinonaktifkan 2026-10-20: '014' = kode menu Retur Jual (salin-tempel) - hak tambah/koreksi/cetak
    // berganti setelah simpan. Disamakan dengan index(): '019'.
    // $kodemenu = '210203';
    $kodemenu = '210207';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VwOutSSPRSSP::all()->sortBy('URUT')->groupBy(['NOBUKTI', 'KodeGdg']);

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      foreach ($o as $m) {
        // code...
        array_push($tempOutstanding, $m);
      }
    }

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWRSERAHSAMPLEWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWRSERAHSAMPLEWEB::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
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
      $req->input('note'),
      $req->input('urut'),
      $req->input('kodebrg'),
      $req->input('kodegdg'),
      $req->input('gdgtujuan'),
      $req->input('sat_1'),
      $req->input('sat_2'),
      $req->input('qntTerima'),
      $req->input('qntTerima2'),
      $req->input('nosat'),
      $req->input('isi'),
      \Auth::user()->username,
      $req->input('kodecustsupp'),
      $req->input('kodeslsl'),
      $req->input('pbonus'),
      $req->input('maxol'),
      $req->input('noserahsample'),
      $req->input('urutserahsample'),
      $req->input('pkomisi'),
      $req->input('gdgtujuan')

    ];



    DB::connection('SML')->statement('exec SP_RSerahSample ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;
  }








}
