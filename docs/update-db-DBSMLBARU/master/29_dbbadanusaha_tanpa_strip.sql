/* =====================================================================
   29 - Master Badan Usaha: tidak boleh '-' / kosong
   Untuk menu : Master > Supplier & Customer > Badan Usaha (/masterbadanusaha)
                + dropdown Bentuk Usaha di Master Customer, Supplier, Ekspedisi.
   Lanjutan   : 27_dbbadanusaha.sql (data awal '-' sempat ikut ter-insert).
   Isi        : 1. Hapus KodeUsaha yang tidak berisi huruf/angka ('-', '.', ...)
                   dari DBBADANUSAHA, jadi tidak muncul lagi di dropdown.
                2. CHECK constraint CK_DBBADANUSAHA_KODE: KodeUsaha wajib berisi
                   minimal satu huruf/angka (sama dengan validasi di aplikasi).
   Tidak diubah: data lama DBCUSTSUPP yang USAHA-nya '-' / kosong dibiarkan;
                saat di-edit wajib dipilih ulang dari master (lihat cek di bawah).
   Sifat      : Satu transaksi, aman dijalankan ulang.
   ===================================================================== */
USE DBSMLBARU;
GO
SET NOCOUNT ON;
SET XACT_ABORT ON;
GO
BEGIN TRAN;

-- 1. Hapus kode tanpa huruf/angka
DELETE FROM dbo.DBBADANUSAHA
WHERE KodeUsaha NOT LIKE '%[A-Za-z0-9]%';

-- 2. Cegah masuk lagi
IF NOT EXISTS (SELECT 1 FROM sys.check_constraints WHERE name = 'CK_DBBADANUSAHA_KODE')
ALTER TABLE dbo.DBBADANUSAHA
  ADD CONSTRAINT CK_DBBADANUSAHA_KODE CHECK (KodeUsaha LIKE '%[A-Za-z0-9]%');

COMMIT;
GO

-- Cek hasil
SELECT * FROM dbo.DBBADANUSAHA ORDER BY KodeUsaha;
-- Customer/Supplier/Ekspedisi yang Bentuk Usaha-nya masih '-' / kosong (perlu dipilih ulang saat edit)
SELECT COUNT(*) AS JmlUsahaKosongAtauStrip
FROM dbo.DBCUSTSUPP
WHERE ISNULL(USAHA, '') NOT LIKE '%[A-Za-z0-9]%';
GO

/* ---- ROLLBACK ----
ALTER TABLE dbo.DBBADANUSAHA DROP CONSTRAINT CK_DBBADANUSAHA_KODE;
INSERT INTO dbo.DBBADANUSAHA (KodeUsaha, PPH21, PPH23) VALUES ('-', 0, 2.5);
*/
