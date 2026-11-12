/* =====================================================================
   28 - Label menu sidebar "PR (Keagenan)" diganti menjadi "PR"
   Untuk menu : Pembelian > PR > PR (/pembelianpermintaanagen). Sejak file 25
                halaman ini sudah gabungan Agen / Non Agen / Non Stock, jadi
                label "(Keagenan)" menyesatkan.
   Isi        : DBMENUWEB 030102 Keterangan 'PR (Keagenan)' -> 'PR'.
                Hak akses DBFLMENUWEB 030102 tidak diubah.
   Sifat      : hanya UPDATE label, aman dijalankan ulang. Rollback di bawah.
   ===================================================================== */
USE DBSMLBARU;
GO

UPDATE dbo.DBMENUWEB SET Keterangan = 'PR'
 WHERE KODEMENU = '030102' AND Keterangan = 'PR (Keagenan)';
GO

-- Cek hasil: 030102 harus 'PR'
SELECT KODEMENU, Keterangan, href FROM dbo.DBMENUWEB WHERE KODEMENU IN ('0301', '030102');
GO

/* ---- ROLLBACK ----
UPDATE dbo.DBMENUWEB SET Keterangan = 'PR (Keagenan)' WHERE KODEMENU = '030102';
*/
