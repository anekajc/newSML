/* =====================================================================
   27 - Master Badan Usaha (PT, CV, UD, ...) + setting PPh21 / PPh23
   Untuk menu : Master > Supplier & Customer > Badan Usaha
                (/masterbadanusaha, MasterBadanUsahaController).
                Dropdown "Bentuk Usaha" di Master Customer, Supplier, dan
                Ekspedisi sekarang diambil dari tabel ini, dan PPh21/PPh23
                di form tersebut terisi otomatis (dikunci) dari sini.
   Isi        : 1. Tabel baru DBBADANUSAHA (KodeUsaha, PPH21, PPH23).
                   Minimal salah satu PPh harus > 0 (CHECK constraint).
                   KodeUsaha yang disimpan ke DBCUSTSUPP.USAHA (varchar 20).
                2. Data awal = 8 opsi dropdown lama (tanpa '-', lihat script 29). PPh21 = 0, PPh23 sesuai
                   logika lama changePph23(): PT/CV = 2, lainnya = 2.5.
                3. Menu DBMENUWEB 01003014 "Badan Usaha" di group 01003.
                4. Hak akses DBFLMENUWEB: baris untuk semua user, akses
                   penuh hanya SA dan SYAIDI; user lain 0 (atur lewat
                   Berkas > Pemakai).
   Tidak diubah: data lama DBCUSTSUPP (USAHA/NPPH22/NPPH23) dibiarkan. Data
                yang USAHA-nya tidak ada di master (mis. 'PT.', 'KOP', kosong)
                wajib dipilih ulang saat di-edit.
   Sifat      : HANYA MENAMBAH. Satu transaksi, aman dijalankan ulang.
   ===================================================================== */
USE DBSMLBARU;
GO
SET NOCOUNT ON;
SET XACT_ABORT ON;
GO
IF OBJECT_ID('dbo.DBBADANUSAHA') IS NULL
CREATE TABLE dbo.DBBADANUSAHA (
  KodeUsaha varchar(20) NOT NULL,
  PPH21 numeric(18,2) NOT NULL CONSTRAINT DF_DBBADANUSAHA_PPH21 DEFAULT 0,
  PPH23 numeric(18,2) NOT NULL CONSTRAINT DF_DBBADANUSAHA_PPH23 DEFAULT 0,
  CONSTRAINT PK_DBBADANUSAHA PRIMARY KEY (KodeUsaha),
  CONSTRAINT CK_DBBADANUSAHA_PPH CHECK (PPH21 > 0 OR PPH23 > 0)
);
GO
BEGIN TRAN;

-- 2. Data awal (8 opsi dropdown lama)
INSERT INTO dbo.DBBADANUSAHA (KodeUsaha, PPH21, PPH23)
SELECT v.KodeUsaha, v.PPH21, v.PPH23
FROM (VALUES
  ('PT', 0, 2), ('CV', 0, 2), ('PD', 0, 2.5), ('UD', 0, 2.5), ('TOKO', 0, 2.5),
  ('BAPAK', 0, 2.5), ('IBU', 0, 2.5), ('EXP', 0, 2.5)
  -- ('-' sempat ikut di sini, dihapus lewat 29_dbbadanusaha_tanpa_strip.sql)
) v (KodeUsaha, PPH21, PPH23)
WHERE NOT EXISTS (SELECT 1 FROM dbo.DBBADANUSAHA b WHERE b.KodeUsaha = v.KodeUsaha);

-- 3. Menu sidebar web
IF NOT EXISTS (SELECT 1 FROM dbo.DBMENUWEB WHERE KODEMENU = '01003014')
INSERT INTO dbo.DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
VALUES ('01003014', 'Badan Usaha', 2, 1003015, 0, '', 2, 'masterbadanusaha', NULL, NULL);

-- 4. Hak akses: SA & SYAIDI penuh, user lain 0
INSERT INTO dbo.DBFLMENUWEB (USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT,
  IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5, TIPE, IsBatal, pembatalan)
SELECT u.USERID, '01003014', x.a, x.a, x.a, x.a, x.a, x.a, 0, 0, 0, 0, 0, 'STK', 0, NULL
FROM (SELECT DISTINCT USERID FROM dbo.DBFLMENUWEB) u
CROSS APPLY (SELECT CAST(CASE WHEN u.USERID IN ('SA', 'SYAIDI') THEN 1 ELSE 0 END AS bit) a) x
WHERE NOT EXISTS (SELECT 1 FROM dbo.DBFLMENUWEB d WHERE d.USERID = u.USERID AND d.L1 = '01003014');

COMMIT;
GO

-- Cek hasil
SELECT * FROM dbo.DBBADANUSAHA ORDER BY KodeUsaha;
SELECT KODEMENU, Keterangan, href FROM dbo.DBMENUWEB WHERE KODEMENU = '01003014';
SELECT USERID, HASACCESS FROM dbo.DBFLMENUWEB WHERE L1 = '01003014' AND HASACCESS = 1;
GO

/* ---- ROLLBACK ----
DELETE FROM dbo.DBFLMENUWEB WHERE L1 = '01003014';
DELETE FROM dbo.DBMENUWEB   WHERE KODEMENU = '01003014';
DROP TABLE dbo.DBBADANUSAHA;
*/
