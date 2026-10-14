<?php

namespace App\Otorisasi;

/**
 * Satu kelas per modul transaksi yang bisa diminta otorisasinya lewat push
 * (didaftarkan di config/otorisasi.php).
 */
interface OtorisasiHandler
{
    /**
     * Data untuk halaman approve mobile:
     * ['header' => [label => nilai, ...], 'lines' => [[label => nilai, ...], ...], 'total' => float]
     * atau null kalau nobukti tidak ditemukan.
     */
    public function summary(string $nobukti): ?array;

    public function isApproved(string $nobukti): bool;

    /** Mengembalikan jumlah baris DBTRANS yang ter-update. */
    public function approve(string $nobukti, string $username): int;
}
