/* =====================================================================
   25 - Gabung menu PR Agen, PR Non Agen, PR Non Stock jadi satu
   Untuk menu : Permintaan Pembelian (PR) gabungan di /pembelianpermintaanagen
                (PembelianPermintaanAgenController). Jenis dibedakan lewat
                filter Jenis + dropdown "Jenis PR" di form.
   Isi        : href menu lama PR Non Agen dan PR Non Stock dikosongkan (NULL)
                di DBMENUWEB (sidebar web) dan DBMENU, supaya menu lama tidak
                bisa dibuka lagi. Route-nya di routes/purchasing.php juga
                sudah di-comment.
                  DBMENUWEB 030101 PR Stock (Non Keagenan) - pembelianpermintaannonagen
                  DBMENUWEB 030103 PR Non Stock            - pembelianpermintaannonstock
                  DBMENU    03001  Permintaan Pembelian (Non Keagenan) - pembelianpermintaannonagen
                  DBMENU    030013 Permintaan Pembelian Jasa           - pembelianpermintaannonstock
   Tidak diubah: baris menu & hak akses DBFLMENUWEB 030101/030103 TETAP ada -
                halaman gabungan memakainya sebagai hak akses jenis Non Agen dan
                Non Stock (hak akses per jenis).
   Sifat      : hanya UPDATE kolom href, aman dijalankan ulang. Rollback di bawah.
   ===================================================================== */
USE DBSMLBARU;
GO

UPDATE dbo.DBMENUWEB SET href = NULL
 WHERE KODEMENU IN ('030101', '030103')
   AND href IN ('pembelianpermintaannonagen', 'pembelianpermintaannonstock');

UPDATE dbo.DBMENU SET href = NULL
 WHERE href IN ('pembelianpermintaannonagen', 'pembelianpermintaannonstock');
GO

-- Cek hasil: 030101/030103 dan 03001/030013 href-nya harus NULL,
-- 030102 / 030011 tetap 'pembelianpermintaanagen'.
SELECT 'DBMENUWEB' tabel, KODEMENU, Keterangan, href FROM dbo.DBMENUWEB WHERE KODEMENU IN ('030101', '030102', '030103')
UNION ALL
SELECT 'DBMENU', KODEMENU, Keterangan, href FROM dbo.DBMENU WHERE KODEMENU IN ('03001', '030011', '030013');
GO

/* ---- ROLLBACK (kalau menu lama perlu dihidupkan lagi) ----
UPDATE dbo.DBMENUWEB SET href = 'pembelianpermintaannonagen'  WHERE KODEMENU = '030101';
UPDATE dbo.DBMENUWEB SET href = 'pembelianpermintaannonstock' WHERE KODEMENU = '030103';
UPDATE dbo.DBMENU    SET href = 'pembelianpermintaannonagen'  WHERE KODEMENU = '03001';
UPDATE dbo.DBMENU    SET href = 'pembelianpermintaannonstock' WHERE KODEMENU = '030013';
-- lalu buka lagi comment route nonagen/nonstock di routes/purchasing.php
*/
