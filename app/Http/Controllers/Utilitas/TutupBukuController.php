<?php

namespace App\Http\Controllers\Utilitas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TutupBukuController extends Controller
{
  public function index(Request $req) {
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', $req->path());
    if (!$akses || !$akses->HASACCESS) {
        return redirect('/home');
    }

    $periode = app('App\Http\Controllers\GlobalController')->getPeriode();

    return view('utilitas.tutupbuku', [
      "periode" => $periode,
      "akses" => $akses,
    ]);
  }

  // Dropdown 0 - Semua
  public function prosesSemua(Request $req) {
    return $this->jalankanSp('', $req); // TODO: isi nama SP
  }

  // Dropdown 1 - Selisih Kurs (2 SP berurutan: kurs bank, lalu kurs hutang lunas)
  public function prosesSelisihKurs(Request $req) {
    return $this->jalankanSp(['SP_ProsesKursBank', 'SP_PROSESKURSHLUNAS'], $req);
  }

  // Dropdown 2 - Proses Aktiva
  public function prosesAktiva(Request $req) {
    return $this->jalankanSp('', $req); // TODO: isi nama SP
  }

  // Dropdown 3 - Hitung Ulang Neraca
  public function prosesHitungUlangNeraca(Request $req) {
    return $this->jalankanSp('', $req); // TODO: isi nama SP
  }

  // Dropdown 4 - HPP dan Rugi Laba
  public function prosesHppRugiLaba(Request $req) {
    return $this->jalankanSp('', $req); // TODO: isi nama SP
  }

  // Dropdown 5 - Proses Dashboard
  public function prosesDashboard(Request $req) {
    return $this->jalankanSp('', $req); // TODO: isi nama SP
  }

  // Cek akses + validasi bulan/tahun, lalu EXEC tiap SP @bulan, @tahun di SML.
  // $sp boleh satu nama SP atau array beberapa SP yang dijalankan berurutan;
  // kalau satu gagal, proses berhenti dan SP sebelumnya tidak di-rollback.
  private function jalankanSp(string|array $sp, Request $req) {
    // Path POST-nya beda (tutupbukuxxx), jadi cek akses pakai href halamannya.
    $akses = app('App\Http\Controllers\GlobalController')->getAkses1('', 'tutupbuku');
    if (!$akses || !$akses->HASACCESS) {
        return ['success' => false, 'message' => 'Tidak punya akses'];
    }

    $daftarSp = array_values(array_filter((array) $sp));
    if (!$daftarSp) {
        return ['success' => false, 'message' => 'SP untuk proses ini belum diatur'];
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

    foreach ($daftarSp as $namaSp) {
      try {
        // SET NOCOUNT ON supaya pesan row count tidak membuat driver sqlsrv
        // kembali sebelum SP selesai.
        DB::connection('SML')->statement('SET NOCOUNT ON; EXEC ' . $namaSp . ' ?, ?', [$bulan, $tahun]);
      } catch (\Throwable $e) {
        return ['success' => false, 'message' => $namaSp . ' gagal: ' . $e->getMessage()];
      }
    }

    return ['success' => true];
  }
}
