<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\vwShowOutBrgTFLokasi;


class TransferLokasiController extends Controller
{




  public function index () {

    $kodemenu = '210404';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // Dinonaktifkan 2026-10-22: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = vwShowOutBrgTFLokasi::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = vwShowOutBrgTFLokasi::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }



    return view('wms.transferlokasi' , [
      "periode" => $periode,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }

  public function listBarang (Request $req) {
    $listBarang = DB::connection('SML')->select("select KODEBRG,NAMABRG,SAT1,SAT2,SAT3 from DBBARANG where ISAKTIF = 1");
    return $listBarang;
  }

  public function listLokasi (Request $req) {
    $listLokasi = DB::connection('SML')->select("select a.* , b.KodeGdg from dbBarangLokasiSIG a join dblokasisig b on a.BarcodeLoc = b.BarcodeLoc where KodeBarang = :kodebarang ", ["kodebarang" => $req->get('kodebarang')]);
    return $listLokasi;
  }

  // public function getNoBukti (Request $req) {
  //   $kodeInisial = "TFL";
  //
  //   $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
  //   $inisial = DB::connection("SML")->select('select ? from DBNOMOR',[$kodeInisial] );
  //   $username = \Auth::user()->username;
  //   // $tesInisial = $inisial[0];
  //   $values = [
  //       $inisial[0]->TFL,
  //       $periode->bulan,
  //       $periode->tahun,
  //       $username
  //   ];
  //   $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
  //   return $noBukti;
  // }

  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $inisial = 'TFL';
    $query = 'select '.$inisial.' from DBNOMOR';
    $inisial = DB::connection("SML")->select($query);
    $username = \Auth::user()->username;
    $values = [
        $inisial[0]->TFL,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function detailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-22: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = vwShowOutBrgTFLokasi::all()->where('NOBUKTI' ,$req->input('nobukti'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = vwShowOutBrgTFLokasi::whereRaw('NOBUKTI = cast(? as varchar(50))', [(string) $req->input('nobukti')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }



  public function loadAll () {
    $kodemenu = '210404';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();


    // Dinonaktifkan 2026-10-22: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = vwShowOutBrgTFLokasi::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = vwShowOutBrgTFLokasi::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return [
      "penerimaan" => $tempPenerimaan,
      "akses" => $akses
    ];
  }
  //
  // public function detailPenerimaan (Request $req) {
  //   $penerimaan = vwShowOutBrgNonSO::all()->where('NOBUKTI' ,$req->input('nobukti'))->sortBy('URUT');
  //   $tempPenerimaan = [];
  //   foreach ($penerimaan as $p) {
  //     // code...
  //     array_push($tempPenerimaan, $p);
  //   }
  //   return $tempPenerimaan;
  // }
  //
  // public function getNoBukti (Request $req) {
  //
  //   $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
  //   $inisial = DB::connection("SML")->select('select OBN from DBNOMOR');
  //   $username = \Auth::user()->username;
  //   $values = [
  //       $inisial[0]->OBN,
  //       $periode->bulan,
  //       $periode->tahun,
  //       $username
  //   ];
  //   $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
  //   return $noBukti;
  // }
  //


  public function spAdd (Request $req) {


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $username = \Auth::user()->username;
    $choice = $req->input('choice');
    $nobukti = $req->input('nobukti');
    $nourut = $req->input('nourut');
    $tanggal = $req->input('tanggal');
    $kodebarang = $req->input('kodebrg');
    $qntTerima = $req->input('qntTerima');
    $nosat = $req->input('nosat');
    $keterangan = $req->input('keterangan');
    $lokasiasal = $req->input('lokasiasal');
    $lokasitujuan = $req->input('lokasitujuan');
    $urut = $req->input('urut');
    $jmlRecord = $req->input('jmlRecord');

    if ($jmlRecord == 0 && $choice == 'I') {
      // code...
      $check = DB::connection('SML')->select('select * from DBOutBrg where NOBUKTI = :nobukti',["nobukti" => $nobukti]);
      if ($check) {
        return 2;
      }
    }



      $values = [
        $choice,
        $nobukti,
        $nourut,
        $tanggal,
        $urut,
        $kodebarang,
        $qntTerima,
        $nosat,
        $keterangan,
        $lokasiasal,
        $username,
        $lokasitujuan,
        $jmlRecord
      ];

      DB::connection("SML")->statement('exec SP_PLTFLokasiOut ?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

      return 1;


  }


  // public function spDelete (Request $req) {
  //
  //   $choice = $req->input('choice');
  //   $nobukti = $req->input('nobukti');
  //   $urut = $req->input('urut');
  //
  //
  //     $values = [
  //       $choice,
  //       $nobukti,
  //       '',
  //       '',
  //       '',
  //       '',
  //       '',
  //       '',
  //       $urut,
  //       '',
  //       '',
  //       0,
  //       0,
  //       '',
  //       0,
  //       '',
  //       0,
  //       '',
  //       '',
  //       ''
  //     ];
  //     DB::connection("SML")->statement('exec Sp_OutBrg ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);
  //
  //     return 1;
  //
  //
  // }




}
