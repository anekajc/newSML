<?php

namespace App\Http\Controllers\Wms;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWOutBRGPemakaian;


use App\Models\VWPenyerahanOut;


class NewPenyerahanGudangController extends Controller
{




  public function index () {

    $kodemenu = '210402';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOutBRGPemakaian::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }
    //
    // $penerimaan = VWTRansferOut::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Dinonaktifkan 2026-10-22: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWPenyerahanOut::all()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWPenyerahanOut::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.newpenyerahangudang' , [
      "periode" => $periode,
      "outstandingArray" => $tempOutstanding,
      "penerimaanArray" => $tempPenerimaan,
      "akses" => $akses
    ]);
  }

  public function getDetailOutstanding (Request $req) {

    // Dinonaktifkan 2026-10-22: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailOutstanding = VWOutBRGPemakaian::all()->where('NOBUKTI', $req->input('NOBUKTI'))->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailOutstanding = VWOutBRGPemakaian::whereRaw('NOBUKTI = cast(? as varchar(50))', [(string) $req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempOutstanding = [];
    foreach ($detailOutstanding as $do) {
      // code...
      array_push($tempOutstanding,$do);
    }
    return $tempOutstanding;

  }

  public function getDetailPenerimaan (Request $req) {
    // Dinonaktifkan 2026-10-22: memuat seluruh view lalu menyaring di PHP (lambat).
    // $detailPenerimaan = VWPenyerahanOut::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('URUT');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $detailPenerimaan = VWPenyerahanOut::whereRaw('NOBUKTI = cast(? as varchar(50))', [(string) $req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function loadAll () {
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    $outstanding = VWOutBRGPemakaian::all()->sortBy('URUT')->groupBy('NOBUKTI');

    $tempOutstanding = [];
    foreach ($outstanding as $o) {
      // code...
      array_push($tempOutstanding, $o);
    }

    // Dinonaktifkan 2026-10-22: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = VWPenyerahanOut::all()->where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->sortBy('URUT')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = VWPenyerahanOut::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('URUT')->groupBy('NOBUKTI');
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

  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // $inisial = DB::connection("SML")->select('select PBL from DBNOMOR');
    $inisial = DB::connection("SML")->select('select pbg from DBNOMOR');
    $username = \Auth::user()->username;
    // return [$periode->bulan,$inisial[0]->PBL,$username];
    $values = [
        $inisial[0]->pbg,
        $periode->bulan,
        $periode->tahun,
        $username
    ];
    $noBukti = DB::connection('SML')->select('exec SP_IsiNobukti ?,?,?,?',$values);
    return $noBukti;
  }

  public function getKoreksiAddList (Request $req) {
    $tempPenerimaan = VWPenyerahanOut::where('NOBUKTI' , $req->input('nopbg'))->select('KODEBRG')->get()->toArray();

    $tempAddList = VWOutBRGPemakaian::select()->where('NOBUKTI', $req->input('noout'))->whereNotIn('KODEBRG', $tempPenerimaan)->get();

    return $tempAddList;
  }

  public function addPenyerahanGudang (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $date = $req->input('inputDate');
    $username = \Auth::user()->username;
    $data = $req->input('tempData');
    $nourut = $req->input('nourut');
    $noout = $req->input('noout');
    $nopbg = $req->input('nopbg');

    $checkSaldo = 0;
    // return [$data,$date,$username,$nourut,$noout,$nopbg];
    DB::connection('SML')->statement('delete	TempOutstandingPO where IDUser = :idUser',['idUser' => $username ]);
    $check = DB::connection('SML')->select('select * from DBPenyerahanBhndet where NOBUKTI = :nobukti',["nobukti" => $nopbg]);
    if ($check) {
      return 2;
    }

    // DB::connection('SML')->statement('delete	TempOutstanding where IDUser = :idUser',['idUser' => $username ]);

    // return 1;

    foreach ($data as $d) {


      $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?',[$d['NOSAT'], $date, $d['Kodegdg'],  $d['KODEBRG']]);
      // array_push($checkSaldo, ["input" => (float)$d['inputQntTerima']    , "saldoqnt" => (float)$check2[0]->SALDOQNT ]);
      if ($check2) {
        // Dinonaktifkan 2026-10-22: (int) memotong qty desimal (1,5 > 1,2 dianggap 1 > 1 = lolos).
        // if ((int)$d['inputQntTerima'] > (int)$check2[0]->SALDOQNT ) {
        if ((float)$d['inputQntTerima'] > (float)$check2[0]->SALDOQNT ) {
          // return 'a';
          $checkSaldo = 1;
        }
      } else {
        // return 'b';
        $checkSaldo = 1;
      }

      $values = [$username, $noout,$d['Tahun'],$d['Bulan'],$d['inputQntTerima'],$d['Urut']];
      DB::connection("SML")->statement('exec sp_RefreshTempPMKbhn ?,?,?,?,?,?',$values);
      if ($checkSaldo) {
        // code...
        return 3;
      }


    }
    $tempValues = [$nopbg, $nourut, $noout, $username, $date];
    DB::connection('SML')->statement('exec SP_InsertOutPmkBhn ?,?,?,?,?', $tempValues);
    // Ditambahkan 2026-10-22: SP menyalin lewat header DboutBRG. Kalau header dokumen Out tidak ada
    // (mis. SML/PLN/00007/0825), tidak ada yang tersimpan tapi dulu layar tetap "PBG telah ditambah".
    $tersimpan = DB::connection('SML')->select('select top 1 NOBUKTI from DBPenyerahanBhndet where NOBUKTI = :nobukti', ["nobukti" => $nopbg]);
    if (!$tersimpan) {
      return 4;
    }
    return 1;



  }

  public function spKoreksi (Request $req) {

      $checkSaldo = 0;
    // Ditambahkan 2026-10-22: hapus item (D) mengembalikan stok, jadi tidak perlu cek saldo.
    // Dulu kalau Sp_CekStockAkhir tidak mengembalikan baris, hapus ditolak (3) tanpa pesan yang benar.
    if ($req->input('choice') != 'D') {
    $check2 = DB::connection('SML')->select('exec Sp_CekStockAkhir ?,?,?,?',[$req->input('nosat'),   $req->input('inputDate'), $req->input('kodegdg'),  $req->input('kodebrg')]);
    if ($check2) {
      // Ditambahkan 2026-10-22: saat edit (U), saldo dari Sp_CekStockAkhir (s.d. tanggal dokumen) sudah
      // dikurangi qty lama item ini, jadi stok yang tersedia = saldo + qty lama. Dulu menaikkan qty
      // ditolak "Stok gudang tidak mencukupi" walau stok sebenarnya cukup.
      $qntLama = 0;
      if ($req->input('choice') == 'U') {
        $lama = VWPenyerahanOut::whereRaw('NOBUKTI = cast(? as varchar(50))', [(string) $req->input('nopbg')])
          ->where('URUT', $req->input('urut'))->first();
        if ($lama && $lama->KODEBRG == $req->input('kodebrg') && $lama->NOSAT == $req->input('nosat')) {
          $qntLama = (float) $lama->Qntx;
        }
      }
      // Dinonaktifkan 2026-10-22: (int) memotong qty desimal.
      // if ((int)$req['qntTerima'] > (int)$check2[0]->SALDOQNT ) {
      if ((float)$req['qntTerima'] > (float)$check2[0]->SALDOQNT + $qntLama ) {
        // return 'a';
        $checkSaldo = 1;
      }
    } else {
      // return 'b';
      $checkSaldo = 1;
    }
    }

    if ($checkSaldo)  {
      // code...
      return 3;
    }



    $values = [
      $req->input('choice'),
      $req->input('nopbg'),
      $req->input('nourut'),
      $req->input('inputDate'),
      $req->input('kodegdg'),
      $req->input('urut'),
      $req->input('kodebrg'),
      $req->input('qntTerima'),
      $req->input('nosat'),
      $req->input('sat'),
      $req->input('isi'),
      $req->input('nobppb'),
      $req->input('qntTerima2'),
      $req->input('urutspk'),
      $req->input('nosatspk'),
      $req->input('issample'),
      $req->input('isbarang'),
      $req->input('keterangan'),
      $req->input('kddep'),
      $req->input('nopr'),
      $req->input('urutpr'),
    ];

    // @Choice varchar(1),
    // @NoBukti varchar(50),
    // @NoUrut varchar(10)='',
    // @Tanggal datetime='',
    // @Kodegdg varchar(15)='',
    // @Urut int=0,
    // @KodeBrg varchar(25)='',
    // @Qnt numeric(18,2)=0,
    // @NoSat tinyint=0,
    // @Sat varchar(5)='',
    // @Isi numeric(18,2)=0,
    // @NoBppB Varchar(50)='',
    // @Qnt2 numeric(18,2)=0,
    // @UrutSPK int=0,
    // @NoSatSPK tinyint=0,
    // @IsSampel smallint=0,
    // @IsBrg bit=0,
    // @Keterangan varchar(100)='',
    // @KDDEP VARCHAR(25)='',
    // @NOPR VARCHAR(30)='',
    // @URUTPR INTEGER=0

    DB::connection('SML')->statement('exec sp_PmkBhn ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;



  }




}
