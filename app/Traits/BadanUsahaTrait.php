<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

/**
 * Master Badan Usaha (DBBADANUSAHA) untuk form Customer / Supplier / Ekspedisi.
 * Bentuk Usaha wajib dipilih dari master, dan PPh21/PPh23 selalu mengikuti master
 * (di form dikunci, di sini ditimpa lagi supaya tidak bisa diakali dari request).
 */
trait BadanUsahaTrait
{
    /** Daftar badan usaha untuk dropdown (KodeUsaha, PPH21, PPH23). */
    public function listBadanUsaha()
    {
        return DB::connection('SML')->select('SELECT KodeUsaha, PPH21, PPH23 FROM DBBADANUSAHA ORDER BY KodeUsaha');
    }

    /**
     * Kode badan usaha harus berisi minimal satu huruf/angka - kosong, '-', '.', '--'
     * dan sejenisnya tidak boleh (sama dengan CHECK CK_DBBADANUSAHA_KODE di DB).
     */
    public function kodeUsahaValid($kode)
    {
        return preg_match('/[A-Za-z0-9]/', (string) $kode) === 1;
    }

    /**
     * Cek kode badan usaha. Kembalikan pesan error (string) bila tidak valid, atau
     * null bila valid - sekaligus mengisi $req[$fieldPph21] / $req[$fieldPph23] dari master.
     */
    public function terapkanBadanUsaha($req, string $fieldUsaha, string $fieldPph21, string $fieldPph23)
    {
        $kode = trim((string) $req->input($fieldUsaha));
        if (! $this->kodeUsahaValid($kode)) {
            return 'Bentuk Usaha harus dipilih (tidak boleh kosong atau -)';
        }

        $row = DB::connection('SML')->select('SELECT KodeUsaha, PPH21, PPH23 FROM DBBADANUSAHA WHERE KodeUsaha = :kode', ['kode' => $kode]);
        if (! $row) {
            return 'Bentuk Usaha '.$kode.' tidak ada di Master Badan Usaha';
        }

        $req->merge([
            $fieldUsaha => $row[0]->KodeUsaha,
            $fieldPph21 => $row[0]->PPH21,
            $fieldPph23 => $row[0]->PPH23,
        ]);

        return null;
    }
}
