/* =====================================================================
   35 - Master Barang: Keagenan (DBBARANG.pAgen) mengikuti Master Merk
   Untuk menu : Master > Barang (/masterbarang) dan Master > Merk.
   Latar      : Dulu centang Keagenan di Master Barang diisi manual oleh user,
                sehingga banyak barang tidak sama dengan keagenan merk-nya.
                Mulai sekarang aplikasi mengisi pAgen barang dari DBMERK.pAgen
                (add/edit barang) dan ikut mengubah semua barang saat keagenan
                merk diubah. Script ini menyamakan data lama sekaligus.
   Isi        : UPDATE DBBARANG.pAgen = ISNULL(DBMERK.pAgen, 0) untuk barang
                yang merk-nya ada di DBMERK dan nilainya berbeda.
                Hanya perubahan data, tanpa perubahan struktur, tanpa backup.
   Tidak diubah: barang yang KodeMerk-nya kosong / tidak ada di DBMERK.
   Sifat      : Satu transaksi, aman dijalankan ulang.
   ===================================================================== */
USE DBSMLBARU;
GO
SET NOCOUNT ON;
SET XACT_ABORT ON;
GO
BEGIN TRAN;

UPDATE b SET b.pAgen = ISNULL(m.pAgen, 0)
FROM dbo.DBBARANG b
JOIN dbo.DBMERK m ON m.KODEMERK = b.KodeMerk
WHERE ISNULL(b.pAgen, 0) <> ISNULL(m.pAgen, 0);

SELECT @@ROWCOUNT AS JmlBarangDiubah;

COMMIT;
GO

-- Bersihkan tabel backup versi lama script ini (bila sempat dibuat)
IF OBJECT_ID('dbo.DBBARANG_PAGEN_BAK35') IS NOT NULL
  DROP TABLE dbo.DBBARANG_PAGEN_BAK35;
GO

-- Cek hasil: harus 0
SELECT COUNT(*) AS JmlBarangBedaDenganMerk
FROM dbo.DBBARANG b
JOIN dbo.DBMERK m ON m.KODEMERK = b.KodeMerk
WHERE ISNULL(b.pAgen, 0) <> ISNULL(m.pAgen, 0);
GO
