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
use App\Models\OutSOPACKINGWEB;
use App\Models\Vwpersiapanspb;
use App\Models\vwpersiapanPACKING;

class PackingController extends Controller
{






  public function index () {
    $kodemenu = '210306';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = OutSOPACKINGWEB::all()->sortBy('URUT')->groupBy('NOSO');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }


    // Dinonaktifkan 2026-10-19: memuat seluruh view lalu menyaring periode di PHP (lambat).
    // $penerimaan = vwpersiapanPACKING::all()->where('bulan',$periode->bulan )->where('tahun', $periode->tahun)->sortBy('urut')->groupBy('nobukti');
    // Pengganti: saring periode yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = vwpersiapanPACKING::where('bulan', $periode->bulan)->where('tahun', $periode->tahun)->get()->sortBy('urut')->groupBy('nobukti');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }

    $expedisi = DB::connection("SML")->select('select * from dbcustsupp where jenis = :id', ['id' => 3]);

    return view('wms.packing' , [
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
    // $inisial = DB::connection("SML")->select('select SPB from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        'PCK',
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }


  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = OutSOPACKINGWEB::all()->where('NOSO', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = OutSOPACKINGWEB::query()->whereRaw('NOSO = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }


public function checkAdd (Request $req) {

    $check = DB::connection('SML')->select('exec sp_checkPLQC ?,? ',[ $req->input('NOBUKTI') ,$req->input('gdgpl') ]);
    if ($check) {
      return 0;
    } else {
      return 1;
    }




  }

  public function getDetailOutstandingKolli (Request $req) {

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = OutSOPACKINGWEB::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = OutSOPACKINGWEB::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
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
    // $detailPenerimaan = vwpersiapanPACKING::all()->where('nobukti',$req->input('NOBUKTI'))->sortBy('Urut');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = vwpersiapanPACKING::query()->whereRaw('nobukti = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('Urut');
    // $detailPenerimaan = OutSOPACKINGWEB::all()->where('NOBUKTI',$req->input('NOBUKTI'))->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = vwpersiapanPACKING::where('nobukti' , $req->input('norspb'))->select('kodebrg')->get()->toArray();

    $tempAddList = OutSOPACKINGWEB::select()->where('NOSO', $req->input('nosj'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();

    return $tempAddList;
  }

  public function addPacking (Request $req) {


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');

    $jumlahkolli = $req->input('jumlahkolli');
    // $nosj = $req->input('nosj');
    $norspb = $req->input('norspb');
    $nooutso = $req->input('nooutso');
    $nopolkendaraan = $req->input('nopolkendaraan');
    $expedisi = $req->input('expedisi');
    $choice = "I";
      // return [$data,$date,$nourut,$nosj,$norspb,$nooutso,$nopolkendaraan,$expedisi];

      // select * from dbSPB where NoBukti = 'SML/SJ/00002/0523'
      $check = DB::connection('SML')->select('select * from dbpacking where NOBUKTI = :nobukti',["nobukti" => $norspb]);
      if ($check) {
        return 2;
      }

      $check3 = DB::connection('SML')->select("select  case when month(:tgl) = (select bulan from DBPERIODE where user_id=:iduser1)
                                              and year(:tgl2)= (select tahun from DBPERIODE where user_id=:iduser2)

                                              then 0 else 1 End pp  ",
                                              ["tgl"=>$date,"iduser1"=>\Auth::user()->username,"tgl2"=>$date,"iduser2"=>\Auth::user()->username]);

        if (($check3[0]->pp ) ==1) {
          return  8;
        }


        $flagCheck4 = 0;
        foreach ($data as $d) {
          // code...
          $check4 = DB::connection('SML')->select('select * from dbpackingdet where nopicking = :nobukti and urutpicking = :urut',["nobukti" => $d['NOBUKTI'] , "urut" => $d['URUT']]);
          if ($check4) {
            $flagCheck4 = 1;
          }

        }

        if($flagCheck4 == 1) {
          return 4;
        }
      // $checkSaldo = 0;
    // DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);
    foreach ($data as $d) {
      // code...
      $values = [
          $choice,
          $norspb,
          $nourut,
          $date,
          $d['NOSO'],
          $jumlahkolli,
          $username,
          0,
          $d['NOBUKTI'],
          $d['URUT'],
          $d['Qnt']
      ];


      DB::connection("SML")->statement('exec sp_packing ?,?,?,?,?,?,?,?,?,?,?',$values);


    }


    return 1;

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

  public function loadAll () {
    $kodemenu = '210306';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = OutSOPACKINGWEB::all()->sortBy('URUT')->groupBy('NOSO');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    // Dinonaktifkan 2026-10-19: memuat seluruh view lalu menyaring periode di PHP (lambat).
    // $penerimaan = vwpersiapanPACKING::all()->where('bulan',$periode->bulan )->where('tahun', $periode->tahun)->sortBy('Urut')->groupBy('nobukti');
    // Pengganti: saring periode yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = vwpersiapanPACKING::where('bulan', $periode->bulan)->where('tahun', $periode->tahun)->get()->sortBy('Urut')->groupBy('nobukti');
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






  // choice
  // nobukti
  // nourut
  // tanggal
  // noso
  // jumlahkolli
  // iduser
  // urut
  // nopicking
  // urutpicking
  // qnt


  public function spKoreksi (Request $req) {

    // choice
    // nobukti
    // nourut
    // tanggal
    // noso
    // jumlahkolli
    // iduser
    // urut
    // nopicking
    // urutpicking
    // qnt

    if ($req->input('choice') == 'D') {

      $check = DB::connection('SML')->select('select * from dbspbdet where Nopacking = :nobukti and Urutpacking = :urut',["nobukti" => $req->input('nobukti') , "urut" => $req->input('urut')]);
      if ($check) {
        return 2;
      }
    }

    $username = \Auth::user()->username;

    $values = [
      $req->input('choice'),
      $req->input('nobukti'),
      $req->input('nourut'),
      $req->input('inputDate'),
      $req->input('noso'),
      $req->input('jumlahkolli'),
      $username ,
      $req->input('urut'),
      $req->input('nopicking'),
      $req->input('urutpicking'),
      $req->input('qnt'),

    ];

      DB::connection('SML')->statement('exec sp_packing ?,?,?,?,?,?,?,?,?,?,?' ,$values);
      return 1;

  }

  // public function spKoreksi (Request $req) {
  //   $values = [
  //     $req->input('choice'),
  //     $req->input('norspb'),
  //     $req->input('nourut'),
  //     $req->input('inputDate'),
  //     $req->input('nosj'),
  //     $req->input('kodecustsupp'),
  //     $req->input('nopolkend'),
  //     $req->input('container'),
  //     $req->input('nocontainer'),
  //     $req->input('noseal'),
  //     $req->input('catatan'),
  //     $req->input('urut'),
  //     $req->input('urutsj'),
  //     $req->input('kodebrg'),
  //     $req->input('qntTerima'),
  //     $req->input('qntTerima2'),
  //     $req->input('sat_1'),
  //     $req->input('sat_2'),
  //     $req->input('nosat'),
  //     $req->input('isi'),
  //     $req->input('netw'),
  //     $req->input('grossw'),
  //     \Auth::user()->username,
  //     $req->input('isempty'),
  //     $req->input('namabrg'),
  //     $req->input('flagmenu'),
  //     $req->input('flagtipe'),
  //     $req->input('retursupp')
  //   ];
  //
  //
  //
  //   DB::connection('SML')->statement('exec sp_SPBweb ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
  //   return 1;
  // }








}
