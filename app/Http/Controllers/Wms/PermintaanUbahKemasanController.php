<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWMASTERPRUBAHKEMASANWEB;


class PermintaanUbahKemasanController extends Controller
{




  public function index () {
    // kalo nosat 1 qnt , qnt2 langsung dr input
    //kalo nosat 2/3 , qnt dikali isi qnt 2 dr input
    $kodemenu = '210312';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWMASTERPRUBAHKEMASANWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWMASTERPRUBAHKEMASANWEB::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.permintaanubahkemasan' , [
      "periode" => $periode,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }

  public function listBarang (Request $req) {
    $listBarang = DB::connection('SML')->select("select KODEBRG,NAMABRG,SAT1,SAT2,SAT3, ISI1,ISI2,ISI3 from DBBARANG where ISAKTIF = 1");
    return $listBarang;
  }

  public function listGudang (Request $req) {
    $listLokasi = DB::connection('SML')->select("select KODEGDG, NAMA from DBGUDANG");
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
    $inisial1 = 'PRU';
    $query = 'select '.$inisial1.' from DBNOMOR';
    $inisial = DB::connection("SML")->select($query);
    $username = \Auth::user()->username;
    $values = [
        $inisial[0]->$inisial1,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function detailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWMASTERPRUBAHKEMASANWEB::all()->where('NOBUKTI' ,$req->input('nobukti'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWMASTERPRUBAHKEMASANWEB::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('nobukti')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function detailPenerimaanPrint (Request $req) {
    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWMASTERPRUBAHKEMASANWEB::all()->where('NOBUKTI' ,$req->input('nobukti'))->where('pDebet', 0)->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWMASTERPRUBAHKEMASANWEB::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('nobukti')])->whereRaw('(pDebet = 0 or pDebet is null)')->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }



  public function loadAll () {
    $kodemenu = '210312';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();


    // Dinonaktifkan 2026-10-21: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWMASTERPRUBAHKEMASANWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWMASTERPRUBAHKEMASANWEB::query()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
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
    $tanggal = $req->input('tanggal');
    $note = $req->input('note');
    $urut = $req->input('urut');
    $kodebarang = $req->input('kodebrg');
    $kodegdg= $req->input('kodegdg');
    $SAT = $req->input('SAT');
    $ISI = $req->input('ISI');
    $nosat = $req->input('nosat');
    $qntDB = $req->input('qntDB');
    $qntCR = $req->input('qntCR');
    $harga = $req->input('harga');
    $nourut = $req->input('nourut');
    $flagtipe = $req->input('flagtipe');
    $biaya = $req->input('biaya');
    $hpp = $req->input('hpp');
    $jmlRecord = $req->input('jmlRecord');
    $qnt = $req->input('qnt');
    $pDebet = $req->input('pDebet');
    $tanggalInput = date("Y-m-d");
    $namabrg = $req->input('namabrg');



    if ($jmlRecord == 0 && $choice == 'I') {
      // code...
      $check = DB::connection('SML')->select('select * from dboutbrgdet where NOBUKTI = :nobukti',["nobukti" => $nobukti]);
      if ($check) {
        return 2;
      }
    }

    if ($choice != 'I') {
      $check2 = DB::connection('SML')->select('select * from dboutbrgdet2 where NoBeli = :nobukti',["nobukti" => $nobukti]);
      if ($check2) {
        return 3;
      }
    }

    // Ditambahkan 2026-11-19: PRU yang dibuat dari menu SO (KETERANGAN diawali 'SO:') --
    // baris Qty DB (barang assembly dari item SO) dikunci: tidak bisa ditambah/diubah/dihapus
    // di sini, sehingga dokumennya juga tidak bisa dihapus dari WMS (hapus lewat menu SO).
    // Komponen (Qty CR) tetap boleh diubah. 4 = ditolak karena dikunci SO.
    if ($choice == 'I' && (int) $jmlRecord == 0 && strncmp(ltrim((string) $note), 'SO:', 3) === 0) {
      return 4; // awalan 'SO:' khusus penanda PRU dari SO
    }
    $header = DB::connection('SML')->selectOne('select KETERANGAN from DBOutBrg where NOBUKTI = cast(? as varchar(30))', [$nobukti]);
    if ($header && strncmp((string) $header->KETERANGAN, 'SO:', 3) === 0) {
      if ($choice != 'D' && (int) $pDebet === 1) {
        return 4;
      }
      if ($choice != 'I') {
        $lama = DB::connection('SML')->selectOne('select isnull(pDebet,0) pDebet from DBOutBrgDET where NOBUKTI = cast(? as varchar(30)) and URUT = ?', [$nobukti, (int) $urut]);
        if ($lama && (int) $lama->pDebet === 1) {
          return 4;
        }
      }
    }



      $values = [
        $choice,
        $nobukti ,
        $tanggal ,
        $note ,
        $urut ,
        $kodebarang ,
        $kodegdg,
        $SAT ,
        $nosat ,
        $ISI,
        $qnt ,
        $harga ,
        $nourut ,
        $flagtipe ,
        $biaya ,
        $hpp,
        $pDebet,
        $username,
        'PRU',
        $tanggalInput,
        $namabrg
      ];

      DB::connection("SML")->statement('exec Sp_OutBrgUKWEB ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?',$values);

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
