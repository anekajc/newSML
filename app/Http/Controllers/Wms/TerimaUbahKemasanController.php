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
use App\Models\VWOutPRUKI;
use App\Models\VWUbahKemasanIN;



class TerimaUbahKemasanController extends Controller
{




  public function index () {
    $kodemenu = '210205';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    //$outstanding = VWOutPRUKI::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NoBeli');
    $outstanding = VWOutPRUKI::all()->sortBy('URUT')->groupBy('NoBeli');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }
    //
    // $penerimaan = VWTRansferOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWUbahKemasanIN::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWUbahKemasanIN::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.terimaubahkemasan' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }


  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select KMBJ from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->KMBJ,
        $periode->bulan,
        $periode->tahun,
        $username,
        $req->get('nopr')
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?,?',$values);
    return $noBukti;
  }

  // Ditambah 2026-10-20: KMBJ yang sudah dibuat untuk PR ubah kemasan ini (VWOutPRUKI hanya
  // menampilkan PR yang sudah punya KMBJ). SP_InsertOutUKI selalu menambah baris terima ke KMBJ
  // milik PR (TempOutstandingPO.NOBATCH, diisi sp_RefreshOutTempUKI dengan "top 1" yang sama).
  // Dulu SP_IsiNobukti mengembalikan nomor ini (cabang KMBJ + @NoPRKMBJ); di DBSMLBARU cabang
  // itu dikomentari sehingga keluar nomor baru dan simpan gagal "duplicate key IX_dbUBAHKEMASAN".
  private function kmbjMilikPR ($nopr) {
    $kmbj = DB::connection('SML')->select('select top 1 nobukti from DBUBAHKEMASANDET where noprubah = cast(? as varchar(30))', [$nopr]);
    if (!$kmbj) {
      return null;
    }
    $header = DB::connection('SML')->select('select NOBUKTI, NOURUT, TANGGAL, month(TANGGAL) BULAN, year(TANGGAL) TAHUN from DBUBAHKEMASAN where NOBUKTI = cast(? as varchar(30))', [$kmbj[0]->nobukti]);
    if (!$header) {
      return null;
    }
    $header[0]->TERKUNCI = count(DB::connection('SML')->select('select 1 from dblockperiode where TAHUN = ? and BULAN = ?', [$header[0]->TAHUN, $header[0]->BULAN])) > 0;
    return $header[0];
  }

  // Ditambah 2026-10-20: nomor bukti untuk form Terima Ubah Kemasan. Bila PR sudah punya KMBJ,
  // pakai nomor itu (perilaku lama); bila belum, sama seperti getnobuktiubahkemasan (SP_IsiNobukti).
  public function getNoBuktiTerima (Request $req) {
    $kmbj = $this->kmbjMilikPR($req->get('nopr'));
    if ($kmbj) {
      return [[
        'Nobukti' => $kmbj->NOBUKTI,
        'Nourut' => $kmbj->NOURUT,
        'Terkunci' => $kmbj->TERKUNCI,
        'Tanggal' => $kmbj->TANGGAL
      ]];
    }
    return $this->getNoBukti($req);
  }

  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = VWOutPRUKI::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = VWOutPRUKI::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = VWUbahKemasanIN::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = VWUbahKemasanIN::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
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

    // Ditambah 2026-10-20: baris terima selalu masuk ke KMBJ milik PR (lihat kmbjMilikPR). Pakai
    // nomor itu supaya SP_InsertOutUKI tidak membuat header kedua (duplicate key). Bila KMBJ itu
    // berada di periode terkunci, tolak (return 3) - stok bulan yang sudah ditutup tidak boleh berubah.
    $kmbj = $this->kmbjMilikPR($noout);
    if ($kmbj) {
      if ($kmbj->TERKUNCI) {
        return 3;
      }
      $nossp = $kmbj->NOBUKTI;
      $nourut = $kmbj->NOURUT;
    }

    DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser',['idUser' => $username ]);




    foreach ($data as $d) {
      $values = [$username, $noout,$d['Tahun'],$d['Bulan'],$d['inputQntTerima'],$d['Urut']];
      DB::connection("SML")->statement('exec sp_RefreshOutTempUKI ?,?,?,?,?,?',$values);
      // return $values;
    }
    // return 1;


    $tempValues = [$nossp, $nourut, $noout, $username, $date];
    DB::connection('SML')->statement('exec SP_InsertOutUKI ?,?,?,?,?', $tempValues);
    return 1;



  }

  public function loadAll () {

    $kodemenu = '210205';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    //$outstanding = VWOutPRUKI::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NoBeli');
    $outstanding = VWOutPRUKI::all()->sortBy('URUT')->groupBy('NoBeli');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWUbahKemasanIN::all()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWUbahKemasanIN::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
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

    $tanggal = date("Y-m-d");
    // return $req;
    if ($req->input('choice') == "D") {
      $checkSaldo = 0;

      $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhirLok ?,?,?,?',[$req->input('nosat'), $tanggal,$req->input('kodelokasi'),  $req->input('kodebrg') ]);
      // Dinonaktifkan 2026-10-20: sisa debug - hapus item tidak pernah dijalankan (langsung kembali).
      // return $check2;
      if ($check2) {
        // Dinonaktifkan 2026-10-20: (int) memotong desimal (0.5 dianggap 0) -> pakai (float).
        // if (((int)$check2[0]->SALDOQNT - (int)$req->input('qntDB')) < 0 ) {
        if (((float)$check2[0]->SALDOQNT - (float)$req->input('qntDB')) < 0 ) {
          // return 'a';
          $checkSaldo = 1;
        }
      } else {
      // return 'b';
      $checkSaldo = 1;
      }


      if ($checkSaldo) {
      // code...
      return 3;
      }

    }


  $values = [
    $req->input('choice'),
    $req->input('nossp'),
    $req->input('note'),
    $req->input('urut'),
    $req->input('kodebrg'),
    $req->input('gdgasal'),
    $req->input('satuan'),
    $req->input('nosat'),
    $req->input('isi'),
    $req->input('qntTerima'),
    $req->input('qntCR'),
  ];

  DB::connection('SML')->statement('exec SP_UBAHKEMASANWEB ?,?,?,?,?,?,?,?,?,?,?' ,$values);
  return 1;

  }





}
