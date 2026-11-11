<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * NPWP/NIK wajib diisi dan tidak boleh sama di DBCUSTSUPP, dicek per Jenis
 * (0 = supplier, 1 = customer, 3 = ekspedisi): supplier dan customer boleh punya NPWP/NIK yang sama.
 * Saat membandingkan, titik, strip, dan spasi diabaikan; nilai tetap disimpan apa adanya.
 *
 * Controller yang memakai trait ini mengisi jenisCustSupp().
 */
trait NpwpTrait
{
    abstract protected function jenisCustSupp(): int;

    protected function normalisasiNpwp($npwp): string
    {
        return preg_replace('/[.\-\s]/', '', (string) $npwp);
    }

    // Baris lain (kode berbeda, jenis sama) yang NPWP/NIK-nya sama, atau null.
    protected function npwpDipakai($npwp, $kecualiKode = null)
    {
        $npwp = $this->normalisasiNpwp($npwp);
        if ($npwp === '') {
            return null;
        }

        return DB::connection('SML')->selectOne(
            "SELECT TOP 1 KODECUSTSUPP, NAMACUSTSUPP FROM DBCUSTSUPP
             WHERE Jenis = :jenis
               AND REPLACE(REPLACE(REPLACE(ISNULL(NPWP, ''), '.', ''), '-', ''), ' ', '') = :npwp
               AND KODECUSTSUPP <> :kode",
            ['jenis' => $this->jenisCustSupp(), 'npwp' => $npwp, 'kode' => (string) $kecualiKode]
        );
    }

    // Pesan error untuk disimpan/dikembalikan ke JS, atau null kalau NPWP/NIK valid.
    protected function validasiNpwp($npwp, $kecualiKode = null)
    {
        if ($this->normalisasiNpwp($npwp) === '') {
            return 'NPWP/NIK wajib diisi';
        }
        $ada = $this->npwpDipakai($npwp, $kecualiKode);

        return $ada ? $this->pesanNpwpDipakai($ada) : null;
    }

    protected function pesanNpwpDipakai($baris): string
    {
        return 'NPWP/NIK sudah ada (dipakai ' . trim($baris->KODECUSTSUPP) . ' - ' . trim((string) $baris->NAMACUSTSUPP) . ')';
    }

    // Dipanggil JS saat NPWP/NIK diketik: {ada: bool, pesan}.
    public function cekNpwp(Request $req)
    {
        $ada = $this->npwpDipakai($req->npwp, $req->kode);

        return response()->json([
            'ada' => (bool) $ada,
            'pesan' => $ada ? $this->pesanNpwpDipakai($ada) : '',
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
