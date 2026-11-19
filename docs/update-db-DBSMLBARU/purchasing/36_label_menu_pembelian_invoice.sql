/* =====================================================================
   36 - Label menu sidebar "Kelengkapan Dokumen" diganti "Pembelian Invoice"
   Untuk menu : Pembelian > Kelengkapan Dokumen (/invoicepembelian). Judul
                halaman sudah "Pembelian Invoice", label sidebar belum ikut.
   Isi        : DBMENUWEB 0305 Keterangan 'Kelengkapan Dokumen' -> 'Pembelian Invoice'.
                Hak akses DBFLMENUWEB 0305 dan href tidak diubah.
   Sifat      : hanya UPDATE label, aman dijalankan ulang. Rollback di bawah.
   ===================================================================== */
USE DBSMLBARU;
GO

UPDATE dbo.DBMENUWEB SET Keterangan = 'Pembelian Invoice'
 WHERE KODEMENU = '0305' AND href = 'invoicepembelian' AND Keterangan = 'Kelengkapan Dokumen';
GO

-- Cek hasil: 0305 harus 'Pembelian Invoice'
SELECT KODEMENU, Keterangan, href FROM dbo.DBMENUWEB WHERE KODEMENU = '0305';
GO

/* ---- ROLLBACK ----
UPDATE dbo.DBMENUWEB SET Keterangan = 'Kelengkapan Dokumen' WHERE KODEMENU = '0305';
*/
