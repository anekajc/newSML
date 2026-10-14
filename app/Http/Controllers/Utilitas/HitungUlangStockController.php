<?php

namespace App\Http\Controllers\Utilitas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HitungUlangStockController extends Controller
{
  public function index(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    return view('utilitas.hitungulangstock', [
      "periode" => $periode,
      "akses" => $akses,
    ]);
  }

  public function proses(Request $req) {
    // Path POST-nya beda (hitungulangstockproses), jadi cek akses pakai href halamannya.
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', 'hitungulangstock');
    if (!$akses || !$akses->HASACCESS) {
        return ['success' => false, 'message' => 'Tidak punya akses'];
    }

    $bulan = (int) $req->bulan;
    $tahun = (int) $req->tahun;

    if ($bulan < 1 || $bulan > 12) {
        return ['success' => false, 'message' => 'Bulan tidak valid'];
    }
    if ($tahun < 2000 || $tahun > 2100) {
        return ['success' => false, 'message' => 'Tahun tidak valid'];
    }

    // SP bisa jalan lama, jangan sampai terpotong max_execution_time.
    set_time_limit(0);

    try {
      // SET NOCOUNT ON supaya pesan row count tidak membuat driver sqlsrv
      // kembali sebelum SP selesai.
      DB::connection('SML')->statement('SET NOCOUNT ON; EXEC SP_HitungUlangStock ?, ?', [$bulan, $tahun]);
    } catch (\Throwable $e) {
      return ['success' => false, 'message' => $e->getMessage()];
    }

    return ['success' => true];
  }
}
