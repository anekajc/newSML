<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

/**
 * Pengaman sisi server untuk aksi transaksi purchasing (tambah/edit/hapus item, otorisasi,
 * batal otorisasi). Tampilan sudah menyembunyikan tombol yang tidak boleh, tapi halaman yang
 * dibuka lama atau dua user yang bekerja bersamaan tetap bisa mengirim aksi itu ke server.
 * Aksi seperti itu ditolak di sini dengan jawaban {ok:0, status:0, pesan} yang dibaca JS.
 */
trait PenjagaTransaksi
{
    // ok/status 0 supaya JS yang memeriksa res.ok, res.status, res == 1 atau res > 0 tetap
    // menganggapnya gagal; pesan ditampilkan apa adanya ke user.
    protected function tolak($pesan)
    {
        return response()->json(['ok' => 0, 'status' => 0, 'pesan' => $pesan]);
    }

    // Header dokumen (satu baris) atau null. NoBukti di-cast ke varchar supaya index terpakai
    // (parameter teks PDO sqlsrv dikirim sebagai nvarchar). "select *" memberi nama kolom
    // sesuai katalog tabel (DBUMJUAL: ISOTORISASI1, DBPO: IsOtorisasi1), jadi setiap kolom
    // juga tersedia dengan nama huruf kecil - pakai $baris->isotorisasi1 dst.
    protected function headerDokumen($tabel, $nobukti)
    {
        $baris = DB::connection('SML')->selectOne(
            "select top 1 * from $tabel where NoBukti = cast(? as varchar(50))",
            [(string) $nobukti]
        );
        if (!$baris) {
            return null;
        }
        $kolom = (array) $baris;
        return (object) array_merge($kolom, array_change_key_case($kolom, CASE_LOWER));
    }

    // NoBukti dokumen lanjutan pertama yang memakai dokumen ini (mis. PO yang memakai PR),
    // atau null kalau belum dipakai. Dengan $kolomUrut/$urut dicek per item.
    protected function dipakaiOleh($tabel, $kolomRef, $nobukti, $kolomUrut = null, $urut = null)
    {
        $sql = "select top 1 NoBukti from $tabel where $kolomRef = cast(? as varchar(50))";
        $param = [(string) $nobukti];
        if ($kolomUrut !== null) {
            $sql .= " and $kolomUrut = ?";
            $param[] = (int) $urut;
        }
        $baris = DB::connection('SML')->selectOne($sql, $param);
        return $baris ? $baris->NoBukti : null;
    }

    protected function jumlahBaris($tabel, $nobukti)
    {
        return (int) DB::connection('SML')->selectOne(
            "select count(*) n from $tabel where NoBukti = cast(? as varchar(50))",
            [(string) $nobukti]
        )->n;
    }
}
