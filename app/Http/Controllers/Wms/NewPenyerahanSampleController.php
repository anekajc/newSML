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


class NewPenyerahanSampleController extends Controller
{





  public function index () {

    $kodemenu = '210309';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOutSerahSample::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }
    //
    // $penerimaan = VWTRansferOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWSerahSampleOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWSerahSampleOut::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.newpenyerahansample' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }


  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select ssp from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->ssp,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = VWOutSerahSample::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = VWOutSerahSample::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = VWSerahSampleOut::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = VWSerahSampleOut::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = VWSerahSampleOut::where('NOBUKTI' , $req->input('nossp'))->select('KODEBRG')->get()->toArray();

    $tempAddList = VWOutSerahSample::select()->where('NOBUKTI', $req->input('noout'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();

    return $tempAddList;
  }

  public function spAdd (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');
    $noout = $req->input('noout');
    $nossp = $req->input('nossp');


    // return [$data,$date,$username,$nourut,$noout,$nopbg];
    // select * from DBSERAHSAMPLE where NOBUKTI = 'SML/SSP/00007/0523'
    $check = DB::connection('SML')->select('select * from DBSERAHSAMPLE where NOBUKTI = :nobukti',["nobukti" => $nossp]);
    if ($check) {
      return 2;
    }

    $checkSaldo = 0;
    DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser',['idUser' => $username ]);


    // DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);

    // return 1;

    // sp_RefreshOutTempSerahSample
    // @IDUser varchar(30),
    // @NoBukti varchar(20),
    // @Tahun int,
    // @Bulan int,
    // @Quantity numeric(18,2),
    // @urut int


    foreach ($data as $d) {

      $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?',[$d['NOSAT'], $date, $d['Kodegdg'],  $d['KODEBRG']]);
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



      $values = [$username, $noout,$d['Tahun'],$d['Bulan'],$d['inputQntTerima'],$d['Urut']];
      DB::connection("SML")->statement('exec sp_RefreshOutTempSerahSample ?,?,?,?,?,?',$values);

      if ($checkSaldo) {
        // code...
        return 3;
      }



    }

    // SP_InsertOutSerahSample
    // @Nobukti varchar(25),
    // @Nourut varchar(10),
    // @Nob varchar(25),
    // @IdUser varchar(20),
    // @Tanggal DateTime

    $tempValues = [$nossp, $nourut, $noout, $username, $date];
    DB::connection('SML')->statement('exec SP_InsertOutSerahSample ?,?,?,?,?', $tempValues);
    return 1;



  }

  public function loadAll () {
    $kodemenu = '210309';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOutSerahSample::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWSerahSampleOut::all()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWSerahSampleOut::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
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

    $choice = $req->input('choice');
    if ($choice == 'U' || $choice == 'D') {

      $check = DB::connection('SML')->select('select * from dbsodet where noserah = :nobukti and kodebrg = :kodebrg',["nobukti" => $req->input('nossp') , "kodebrg" => $req->input('kodebrg') ]);
      if ($check) {
        return 2;
      }

    }


  $values = [
    $req->input('choice'),
    $req->input('nossp'),
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
    $req->input('kodecustsupp'),
    $req->input('kodesls'),
    $req->input('pbonus'),
    $req->input('maxol'),
    $req->input('nopr'),
    $req->input('urutpr'),
    $req->input('pkonsi'),
    $req->input('nooutbrg'),
    $req->input('urutoutbrg'),
  ];

  DB::connection('SML')->statement('exec SP_SerahSampleWEB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
  return 1;

  }





}
