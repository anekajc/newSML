<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewPeriode;
use Illuminate\Support\Facades\DB;
use App\Models\VWUbahKemasanOut;

// Dipindah dari ajcwms (UbahKemasanController). Halaman "ubahkemasan" lamanya tidak ada di menu, jadi hanya
// 3 endpoint yang dipakai halaman Finalisasi UK dan Picking UK yang dipindahkan: getNoBukti,
// getDetailPenerimaan, spKoreksi.
class UbahKemasanController extends Controller
{

  public function getNoBukti (Request $req) {

    $periode = NewPeriode::where('user_id', \Auth::user()->username)->first();
    $inisial = DB::connection("SML")->select('select KMBJ from DBNOMOR');
    $username = \Auth::user()->username;
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

  public function getDetailPenerimaan (Request $req) {
    $detailPenerimaan = VWUbahKemasanOut::query()->whereRaw('NOBUKTI = cast(? as varchar(100))', [$req->input('NOBUKTI')])->get()->sortBy('URUT');
    $tempPenerimaan = [];
    foreach ($detailPenerimaan as $p) {
      array_push($tempPenerimaan, $p);
    }
    return $tempPenerimaan;
  }

  public function spKoreksi (Request $req) {

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
      $req->input('qntDB'),
      $req->input('qntTerima'),
    ];

    DB::connection('SML')->statement('exec SP_UBAHKEMASANWEB ?,?,?,?,?,?,?,?,?,?,?' ,$values);
    return 1;

  }

}
