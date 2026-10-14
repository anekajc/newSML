<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // Tanggal filter periode dari browser (input type=date) bisa berisi tahun yang belum
    // selesai diketik/dimundurkan (mis. 0202-01-01) -- dikirim mentah ke SQL Server jadi error
    // "conversion of a nvarchar data type to a datetime data type resulted in an out-of-range
    // value". Kosong, tidak valid, atau di luar 1900-01-01..2079-06-06 (jangkauan smalldatetime)
    // -> null, pemanggil pakai `?: default periode`.
    protected function tanggalFilter ($nilai) {
      $nilai = trim((string) $nilai);
      if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $nilai, $m) || !checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
        return null;
      }
      $tgl = $m[1] . '-' . $m[2] . '-' . $m[3];
      return ($tgl < '1900-01-01' || $tgl > '2079-06-06') ? null : $nilai;
    }
}
