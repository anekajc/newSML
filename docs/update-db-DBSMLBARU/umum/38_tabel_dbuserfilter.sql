-- =====================================================================
-- 38 — Tabel DBUSERFILTER: filter laporan tersimpan per user (2026-11-20)
-- Dipakai oleh app/Http/Controllers/UserFilterController.php
-- (route /userfilter_get & /userfilter_save) dan public/js/user-filter.js.
-- 1 baris = 1 user x 1 halaman x 1 modal/wadah filter.
-- Aman dijalankan ulang (IF OBJECT_ID ... IS NULL).
-- =====================================================================

IF OBJECT_ID('dbo.DBUSERFILTER', 'U') IS NULL
BEGIN
  CREATE TABLE dbo.DBUSERFILTER (
    USERNAME   varchar(50)   NOT NULL,   -- DBFLPASS.username (= Auth::user()->username)
    HREF       varchar(150)  NOT NULL,   -- path halaman tanpa '/' depan-belakang, mis. 'reportaccountingkasharian'
    FILTERKEY  varchar(50)   NOT NULL,   -- id modal/wadah filter, mis. 'modalFilter'
    FILTERJSON nvarchar(max) NULL,       -- {"modalOtorisasi":"BELUM", ...}
    UPDATED_AT datetime      NOT NULL CONSTRAINT DF_DBUSERFILTER_UPDATED_AT DEFAULT GETDATE(),
    CONSTRAINT PK_DBUSERFILTER PRIMARY KEY (USERNAME, HREF, FILTERKEY)
  );
  PRINT 'DBUSERFILTER dibuat';
END
ELSE
  PRINT 'DBUSERFILTER sudah ada, dilewati';
GO

-- Cek hasil
SELECT COUNT(*) AS JUMLAH_BARIS FROM dbo.DBUSERFILTER;
GO

-- ROLLBACK (jalankan manual kalau fitur dibatalkan; semua filter tersimpan ikut hilang):
-- DROP TABLE dbo.DBUSERFILTER;
