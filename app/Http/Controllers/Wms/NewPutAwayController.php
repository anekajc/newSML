<?php

namespace App\Http\Controllers\Wms;



use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewMenu;
use App\Models\NewPeriode;
use App\Models\NewUsers;
use Illuminate\Support\Facades\DB;
use App\Models\VWOUTPENERIMAANBRGWEB;
use App\Models\vwShowAlokasiBrg;


class NewPutAwayController extends Controller
{




  public function index () {

    $kodemenu = '210202';
    $akses = NewMenu::join('DBFLMENUWEB', 'DBFLMENUWEB.L1', '=', 'DBMENUWEB.KODEMENU')->where('DBFLMENUWEB.USERID', \Auth::user()->username)->where('DBMENUWEB.KODEMENU', $kodemenu)->first();
    if(!$akses || !$akses->HASACCESS) {
       return redirect('/home');
    }


    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();


    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = vwShowAlokasiBrg::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('urut')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = vwShowAlokasiBrg::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('urut')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }


    return view('wms.newputaway' , [
      "periode" => $periode,
      "penerimaanArray" => $tempPenerimaan
    ]);
  }

  public function loadAll () {
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
    // $penerimaan = vwShowAlokasiBrg::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->sortBy('urut')->groupBy('NOBUKTI');
    // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
    $penerimaan = vwShowAlokasiBrg::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->get()->sortBy('urut')->groupBy('NOBUKTI');
    $tempPenerimaan = [];
    foreach ($penerimaan as $p) {
      // code...
      array_push($tempPenerimaan, $p);
    }

    return [
      "penerimaanArray" => $tempPenerimaan
    ];
  }

  public function spAdd (Request $req) {

    $nobukti = $req->input('nobukti');
    $nobarang = $req->input('nobarang');
    $barcodelokasi = $req->input('barcodelokasi');
    $urut = $req->input('urut');
    $qntShow = $req->input('qntShow');

    // $urut = 0;
    // 'SML/OPN/00002/0123'
    $values = [
      $nobukti, $nobarang, $barcodelokasi, $urut, $qntShow
    ];

      DB::connection("SML")->statement('exec Sp_TransAlokasiBrg ?,?,?,?,?',$values);
      return 1;

  }

  public function spCheckAdd (Request $req) {

    $nobukti = $req->input('nobukti');
    $nobarang = $req->input('nobarang');
    $urut = $req->input('urut');    
    // $barcodelokasi = $req->input('barcodelokasi');
    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();

    // $urut = 0;
    // 'SML/OPN/00002/0123'
    // $values = [
    //   $nobukti, $nobarang, $barcodelokasi
    // ];
    // $gudang = DB::connection("SML")->select('select * from DBGUDANG where KODEGDG <> :id', ['id' => 'GTC']);

      // $check = DB::connection("SML")->select('select * from dbBeli where nobukti = :nobukti AND month(tanggal) = :bulan AND year(tanggal) = :tahun',['nobukti' => $nobukti, 'bulan'=> $periode->bulan, 'tahun'=>$periode->tahun]);
      // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
      // $check = VWOUTPENERIMAANBRGWEB::all()->where('Bulan',$periode->bulan )->where('Tahun', $periode->tahun)->where('NoBukti1',$nobukti )->where('Kodebrg', $nobarang)->where('Urut', $urut);
      // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
      $check = VWOUTPENERIMAANBRGWEB::where('Bulan', $periode->bulan)->where('Tahun', $periode->tahun)->whereRaw('NoBukti1 = cast(? as varchar(50))', [$nobukti])->whereRaw('Kodebrg = cast(? as varchar(50))', [$nobarang])->where('Urut', $urut)->get();
      $tempCheck = [];
      foreach ($check as $p) {
        // code...
        array_push($tempCheck, $p);
      }

      $kodegdg='';
      if ($tempCheck &&  $tempCheck[0]){
      $kodegdg = $tempCheck[0]->Kodegdg;
      }

      if (str_contains($nobukti, 'TLO')) {

        $barcodeLoc = DB::connection('SML')->select( 'select kodelokasi from dbtransferdet where NOBUKTI = :nobukti' , ["nobukti" => $nobukti]);
        $lokasi =  DB::connection('SML')->select('select A.BarcodeLoc,A.qntMax,isnull(b.SALDOQNT,0) StockLokasi,A.qntMax - isnull(B.SALDOQNT,0) SisaKapasitas
  from dbBarangLokasiSIG A
  left outer join DBSTOCKBRGSIG B on A.KodeBarang=B.KODEBRG and A.BarcodeLoc=B.BARCODE AND B.BULAN= :bulan AND B.TAHUN= :tahun
  where A.KodeBarang= :nobarang and BarcodeLoc =:barcodeloc', ["nobarang" => $nobarang, "bulan" => $periode->bulan , "tahun" => $periode->tahun, "barcodeloc" => $barcodeLoc[0]->kodelokasi]);
      } else {
        $lokasi =  DB::connection('SML')->select('select A.BarcodeLoc,A.qntMax,isnull(b.SALDOQNT,0) StockLokasi,A.qntMax - isnull(B.SALDOQNT,0) SisaKapasitas
  from dbBarangLokasiSIG A
  left outer join DBSTOCKBRGSIG B on A.KodeBarang=B.KODEBRG and A.BarcodeLoc=B.BARCODE AND B.BULAN= :bulan AND B.TAHUN= :tahun
  left outer join dbLokasiSIG C on a.barcodeloc=C.barcodeloc
  where A.KodeBarang= :nobarang and C.kodegdg= :kodegdg', ["nobarang" => $nobarang, "bulan" => $periode->bulan , "tahun" => $periode->tahun, "kodegdg" => $kodegdg]);
      }



      return ["check" => $tempCheck ,"lokasi" => $lokasi];

  }



  public function spKoreksi (Request $req) {
    $values = [
      $req->input('choice'),
      $req->input('nobukti'),
      $req->input('urut'),
      $req->input('kodeBrg'),
      $req->input('inputQnt')
    ];

    DB::connection('SML')->statement('exec Sp_updAlokasibrgdet ?,?,?,?,?', $values);
    return 1;
  }


    public function getDetailPenerimaan (Request $req) {
      // Dinonaktifkan 2026-10-20: memuat seluruh view lalu menyaring di PHP (lambat).
      // $detailPenerimaan = vwShowAlokasiBrg::all()->where('NOBUKTI',$req->input('NOBUKTI') )->sortBy('URUT');
      // Pengganti: saringan yang sama langsung di query database (hasil sama, jauh lebih cepat).
      $detailPenerimaan = vwShowAlokasiBrg::whereRaw('NOBUKTI = cast(? as varchar(50))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
      $tempPenerimaan = [];
      foreach ($detailPenerimaan as $p) {
        // code...
        array_push($tempPenerimaan, $p);
      }
      return $tempPenerimaan;
    }

}
