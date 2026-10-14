/* =====================================================================
   13 - Tabel riwayat lokasi barang (Master > Set Lokasi Barang)
   Untuk menu : Master Set Lokasi Barang (MasterSetLokasiBarangController),
                disamakan dengan DBSMLNEW (newProgramAJC).
   Masalah    : controller menulis/membaca DBHISLOKASIBRG saat lokasi
                barang diubah dan saat melihat riwayatnya, tapi tabel ini
                tidak ada di DBSMLBARU ("Invalid object name").
   Sifat      : HANYA MENAMBAH tabel baru (struktur sama dengan DBSMLNEW).
                Aman dijalankan ulang.
   Dijalankan : 2026-10-14 di DBSMLBARU - sukses.
   ===================================================================== */
USE DBSMLBARU;
GO
IF OBJECT_ID('dbo.DBHISLOKASIBRG') IS NULL
CREATE TABLE dbo.DBHISLOKASIBRG (
  KODEBRG varchar(30) NULL,
  LOKASI  varchar(30) NULL,
  TGL     datetime    NULL,
  IDUSER  varchar(20) NULL
);
GO
