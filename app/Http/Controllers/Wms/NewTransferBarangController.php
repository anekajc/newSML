<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWTRansferOut;

use App\Models\VWOutBRGTRansfer;


class NewTransferBarangController extends Controller
{




  public function index () {

    $kodemenu = '210308';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOutBRGTRansfer::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    //
    // $penerimaan = VWTRansferOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWTRansferOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWTRansferOut::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.newtransferbarang' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }

  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select TRS from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->TRS,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    return
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function loadAll () {
    $kodemenu = '210308';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOutBRGTRansfer::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWTRansferOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWTRansferOut::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
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

  public function getDetailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = VWTRansferOut::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = VWTRansferOut::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = VWOutBRGTRansfer::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = VWOutBRGTRansfer::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = VWTRansferOut::where('NOBUKTI' , $req->input('notrf'))->select('KODEBRG')->get()->toArray();

    $tempAddList = VWOutBRGTRansfer::select()->where('NOBUKTI', $req->input('noout'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();

    return $tempAddList;
  }

  public function addTransferBarang (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');
    $noout = $req->input('noout');
    $notrf = $req->input('notrf');


    // return [$data,$date,$username,$nourut,$noout,$notrf];

    // select * from DBTRANSFER where NOBUKTI ='SML/TRF/00004/0523'
    $check = DB::connection('SML')->select('select * from DBTRANSFER where NOBUKTI = :nobukti',["nobukti" => $notrf]);
    if ($check) {
      return 2;
    }
    DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser',['idUser' => $username ]);


    // DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);


    foreach ($data as $d) {
      $values = [$username, $noout,$d['Tahun'],$d['Bulan'],$d['inputQntTerima'],$d['Urut']];
      DB::connection("SML")->statement('exec sp_RefreshTempOutBRG ?,?,?,?,?,?',$values);
    }
    $tempValues = [$notrf, $nourut, $noout, $username, $date];
    DB::connection('SML')->statement('exec SP_InsertOutBrgTRansfer ?,?,?,?,?', $tempValues);
    // Ditambah 2026-10-21: SP_InsertOutBrgTRansfer membatalkan sendiri (rollback) bila detail gagal
    // ditulis (GDGASAL/GDGTUJUAN NULL karena TFL tidak punya Permintaan Transfer), tanpa error ke PHP.
    // Dulu tetap dijawab 1 (sukses) padahal tidak ada yang tersimpan.
    $tersimpan = DB::connection('SML')->select('select top 1 1 ada from DBTRANSFERDET where NOBUKTI = :nobukti', ["nobukti" => $notrf]);
    if (!$tersimpan) {
      return 5;
    }
    return 1;




  }


  public function spKoreksi (Request $req) {
    $values = [
      $req->input('choice'),
      $req->input('notrf'),
      $req->input('nourut'),
      $req->input('inputDate'),
      $req->input('note'),
      $req->input('urut'),
      $req->input('kodebrg'),
      $req->input('gdgasal'),
      $req->input('gdgtujuan'),
      $req->input('sat_1'),
      $req->input('sat_2'),
      $req->input('qntTerima'),
      $req->input('qntTerima2'),
      $req->input('nosat'),
      $req->input('isi'),
      \Auth::user()->username,
      $req->input('nopenyerahan'),
      $req->input('noout'),
      $req->input('urutout')
    ];

    DB::connection('SML')->statement('exec SP_TRANSFERWEB1 ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;
  }




}
