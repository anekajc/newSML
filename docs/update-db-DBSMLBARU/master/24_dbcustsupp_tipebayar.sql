/* =====================================================================
   24 - Kolom Pembayaran (Tunai/CBD / Kredit) di master customer
   Untuk menu : Master Customer (MasterCustomerController) + Marketing SO
                (SOController::cekKreditHari, endpoint socekkredithari).
   Kebutuhan  : master customer punya pilihan Pembayaran
                0 = Tunai/CBD (TOP boleh 0), 1 = Kredit (TOP wajib > 0).
                Saat buat SO, Pembayaran terisi otomatis dari customer.
   Sifat      : HANYA MENAMBAH kolom baru TipeBayar (default 0) dan mengisi
                data lama: customer dengan TOP (HARI) > 0 -> Kredit,
                selain itu Tunai/CBD (sama dengan logika SO sebelumnya).
                Aman dijalankan ulang (backfill hanya saat kolom baru dibuat).
   ===================================================================== */
USE DBSMLBARU;
GO
IF COL_LENGTH('dbo.DBCUSTSUPP', 'TipeBayar') IS NULL
BEGIN
  ALTER TABLE dbo.DBCUSTSUPP ADD TipeBayar tinyint NOT NULL
    CONSTRAINT DF_DBCUSTSUPP_TipeBayar DEFAULT 0;

  EXEC('UPDATE dbo.DBCUSTSUPP SET TipeBayar = 1 WHERE JENIS = 1 AND ISNULL(HARI, 0) > 0');
END
GO
