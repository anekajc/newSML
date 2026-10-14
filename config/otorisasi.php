<?php

use App\Otorisasi\KasOtorisasi;

/*
|--------------------------------------------------------------------------
| Modul yang mendukung "Minta Otorisasi" (push ke approver)
|--------------------------------------------------------------------------
|
| Kunci = DBMENUWEB.href halaman transaksinya (sama dengan $req->path() yang dipakai
| GlobalController::getAkses()). 'handler' mengimplementasikan
| App\Otorisasi\OtorisasiHandler. Tambah modul baru cukup dengan satu baris di sini
| plus satu kelas handler. KODEMENU tidak ditulis di sini - di-resolve dari
| DBMENUWEB lewat href (Kas = '0202'; $kodemenu '02011' di KasController::index()
| sudah usang dan memang tidak dipakai getAkses()).
|
*/

return [

    'kas' => [
        'label' => 'Kas',
        'handler' => KasOtorisasi::class,
    ],

];
