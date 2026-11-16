<?php

namespace App\Http\Controllers\Wms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Dipindah dari ajcwms (LockPeriodeController@check). Dipakai halaman WMS (retur jual, retur surat jalan,
// retur serah sample, terima ubah kemasan, transfer lokasi in, persiapan SPB) lewat POST /lockperiodecheck.
class LockPeriodeController extends Controller
{
  public function check (Request $req) {
    return DB::connection("SML")->select('select * from dblockperiode where TAHUN = :tahun and BULAN = :bulan', ['tahun' => $req->tahun, 'bulan' => $req->bulan]);
  }
}
