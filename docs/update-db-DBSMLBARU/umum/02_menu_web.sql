/* =====================================================================
   02 - Tabel menu & hak akses versi web (sidebar + cek akses semua menu)
   Untuk menu : WAJIB untuk semua menu newSML yang sudah di-upgrade
                (PR Agen, PR Non-Agen, PR Non-Stock, Closing PR, Closing PO).
                Tanpa ini sidebar kosong & menu error 500
                ("Invalid object name 'DBMENUWEB'").
   Sifat      : HANYA MENAMBAH. Satu transaksi (gagal = batal semua).
                Aman dijalankan ulang (pakai IF NOT EXISTS / WHERE NOT EXISTS).
   Dijalankan : 2026-10-02 di DBSMLBARU (lokal/dev) - sukses.

   SYARAT: database DBSMLNEW harus ada di SERVER YANG SAMA, karena isi
   DBMENUWEB, DBMENUREPORTWEB, dan kolom icon/tableCount DBMENU disalin
   dari DBSMLNEW. Kalau tidak ada, script berhenti di cek pertama tanpa
   mengubah apa pun - lihat purchasing/CATATAN-UPDATE-DB-PURCHASING.md bagian "Kalau DBSMLNEW
   tidak ada di server".

   Catatan: trigger Tri_ADD_DBMENU yang sudah ada di DBSMLBARU otomatis
   menambah baris akses SA di DBFLMENU untuk setiap menu baru di DBMENU
   (di dev: +2 baris). Itu perilaku trigger lama, bukan dari script ini.
   ===================================================================== */
USE DBSMLBARU;
GO
IF DB_ID('DBSMLNEW') IS NULL
BEGIN
  RAISERROR('Database DBSMLNEW tidak ada di server ini - script dihentikan, tidak ada yang diubah.', 16, 1);
  SET NOEXEC ON;
END
GO
SET NOCOUNT ON;
SET XACT_ABORT ON;
GO
-- 1a. DBMENU: tambah kolom icon, tableCount (nullable) seperti DBSMLNEW
IF COL_LENGTH('dbo.DBMENU','icon') IS NULL ALTER TABLE dbo.DBMENU ADD icon varchar(50) NULL;
IF COL_LENGTH('dbo.DBMENU','tableCount') IS NULL ALTER TABLE dbo.DBMENU ADD tableCount int NULL;
GO
BEGIN TRAN;
-- 1b. isi kolom baru hanya untuk menu yang kode & href-nya sama dengan DBSMLNEW (isi lama tidak diubah)
UPDATE b SET b.icon = n.icon, b.tableCount = n.tableCount
FROM dbo.DBMENU b JOIN DBSMLNEW.dbo.DBMENU n ON n.KODEMENU = b.KODEMENU AND ISNULL(n.href,'') = ISNULL(b.href,'')
WHERE b.icon IS NULL AND b.tableCount IS NULL;
-- 1c. tambah menu yang belum ada di BARU
INSERT INTO dbo.DBMENU (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, tableCount)
SELECT n.KODEMENU, n.Keterangan, n.L0, n.ACCESS, n.OL, n.TipeTrans, n.HeaderMenu, n.href, n.icon, n.tableCount
FROM DBSMLNEW.dbo.DBMENU n WHERE NOT EXISTS (SELECT 1 FROM dbo.DBMENU b WHERE b.KODEMENU = n.KODEMENU);

-- 2. DBMENUWEB (struktur + isi dari DBSMLNEW)
IF OBJECT_ID('dbo.DBMENUWEB') IS NULL
CREATE TABLE dbo.DBMENUWEB (
  KODEMENU varchar(25) NOT NULL CONSTRAINT DF_DBMENUWEB_KODEMENU DEFAULT (''),
  Keterangan varchar(500) NOT NULL CONSTRAINT DF_DBMENUWEB_Keterangan DEFAULT (''),
  L0 tinyint NOT NULL CONSTRAINT DF_DBMENUWEB_L0 DEFAULT ((0)),
  ACCESS int NOT NULL CONSTRAINT DF_DBMENUWEB_ACCESS DEFAULT ((0)),
  OL tinyint NOT NULL CONSTRAINT DF_DBMENUWEB_OL DEFAULT ((0)),
  TipeTrans varchar(8) NOT NULL CONSTRAINT DF_DBMENUWEB_TipeTrans DEFAULT (''),
  HeaderMenu int NULL, href varchar(50) NULL, icon varchar(50) NULL, color varchar(20) NULL,
  CONSTRAINT PK_DBMENUWEB PRIMARY KEY (KODEMENU));
INSERT INTO dbo.DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href, icon, color)
SELECT n.KODEMENU, n.Keterangan, n.L0, n.ACCESS, n.OL, n.TipeTrans, n.HeaderMenu, n.href, n.icon, n.color
FROM DBSMLNEW.dbo.DBMENUWEB n WHERE NOT EXISTS (SELECT 1 FROM dbo.DBMENUWEB b WHERE b.KODEMENU = n.KODEMENU);

-- 3. DBMENUREPORTWEB (struktur + isi dari DBSMLNEW)
IF OBJECT_ID('dbo.DBMENUREPORTWEB') IS NULL
CREATE TABLE dbo.DBMENUREPORTWEB (
  KODEMENU varchar(25) NOT NULL CONSTRAINT DF_DBMENUREPORTWEB_KODEMENU DEFAULT (''),
  Keterangan varchar(500) NOT NULL CONSTRAINT DF_DBMENUREPORTWEB_Keterangan DEFAULT (''),
  L0 tinyint NOT NULL CONSTRAINT DF_DBMENUREPORTWEB_L0 DEFAULT ((0)),
  ACCESS int NOT NULL CONSTRAINT DF_DBMENUREPORTWEB_ACCESS DEFAULT ((0)),
  OL tinyint NOT NULL CONSTRAINT DF_DBMENUREPORTWEB_OL DEFAULT ((0)),
  HeaderMenu int NULL, href varchar(50) NULL, color varchar(20) NULL, icon varchar(20) NULL,
  CONSTRAINT PK_DBMENUREPORTWEB PRIMARY KEY (KODEMENU));
INSERT INTO dbo.DBMENUREPORTWEB (KODEMENU, Keterangan, L0, ACCESS, OL, HeaderMenu, href, color, icon)
SELECT n.KODEMENU, n.Keterangan, n.L0, n.ACCESS, n.OL, n.HeaderMenu, n.href, n.color, n.icon
FROM DBSMLNEW.dbo.DBMENUREPORTWEB n WHERE NOT EXISTS (SELECT 1 FROM dbo.DBMENUREPORTWEB b WHERE b.KODEMENU = n.KODEMENU);

-- 4. DBFLMENUWEB: diturunkan dari hak akses SML sendiri (DBFLMENU BARU)
IF OBJECT_ID('dbo.DBFLMENUWEB') IS NULL
CREATE TABLE dbo.DBFLMENUWEB (
  USERID varchar(25) NOT NULL, L1 varchar(25) NOT NULL,
  HASACCESS bit NOT NULL, ISTAMBAH bit NOT NULL, ISKOREKSI bit NOT NULL, ISHAPUS bit NOT NULL, ISCETAK bit NOT NULL, ISEXPORT bit NOT NULL,
  IsOtorisasi1 bit NOT NULL CONSTRAINT DF_DBFLMENUWEB_IsOtorisasi1 DEFAULT ((0)),
  IsOtorisasi2 bit NOT NULL CONSTRAINT DF_DBFLMENUWEB_IsOtorisasi2 DEFAULT ((0)),
  IsOtorisasi3 bit NOT NULL CONSTRAINT DF_DBFLMENUWEB_IsOtorisasi3 DEFAULT ((0)),
  IsOtorisasi4 bit NOT NULL CONSTRAINT DF_DBFLMENUWEB_IsOtorisasi4 DEFAULT ((0)),
  IsOtorisasi5 bit NOT NULL CONSTRAINT DF_DBFLMENUWEB_IsOtorisasi5 DEFAULT ((0)),
  TIPE varchar(3) NOT NULL,
  IsBatal bit NOT NULL CONSTRAINT DF_DBFLMENUWEB_IsBatal DEFAULT ((0)),
  pembatalan bit NULL,
  CONSTRAINT PK_DBFLMENUWEB_1 PRIMARY KEY (USERID, L1));

-- peta kode menu web -> kode DBMENU BARU: lewat href asli, kalau tidak ada lewat kode yang namanya sama / induk '#'
-- MIN: kalau satu href dipakai >1 menu (mis. retursuratjalan = 041015 lama & 041016 dari DBSMLNEW), pakai kode lama
SELECT w.KODEMENU wk, MIN(m.KODEMENU) bk INTO #MapMenu
FROM dbo.DBMENUWEB w JOIN dbo.DBMENU m ON m.href = w.href
WHERE ISNULL(w.href,'') NOT IN ('','#')
GROUP BY w.KODEMENU;
INSERT INTO #MapMenu (wk, bk)
SELECT w.KODEMENU, m.KODEMENU FROM dbo.DBMENUWEB w JOIN dbo.DBMENU m ON m.KODEMENU = w.KODEMENU
WHERE NOT EXISTS (SELECT 1 FROM #MapMenu x WHERE x.wk = w.KODEMENU)
  AND (m.Keterangan = w.Keterangan OR ISNULL(m.href,'') IN ('','#'));

SELECT u.USERID, w.KODEMENU L1, w.href,
  CAST(ISNULL(f.HASACCESS,0) AS bit) HASACCESS, CAST(ISNULL(f.ISTAMBAH,0) AS bit) ISTAMBAH, CAST(ISNULL(f.ISKOREKSI,0) AS bit) ISKOREKSI,
  CAST(ISNULL(f.ISHAPUS,0) AS bit) ISHAPUS, CAST(ISNULL(f.ISCETAK,0) AS bit) ISCETAK, CAST(ISNULL(f.ISEXPORT,0) AS bit) ISEXPORT,
  CAST(ISNULL(f.IsOtorisasi1,0) AS bit) IsOtorisasi1, CAST(ISNULL(f.IsOtorisasi2,0) AS bit) IsOtorisasi2, CAST(ISNULL(f.IsOtorisasi3,0) AS bit) IsOtorisasi3,
  CAST(ISNULL(f.IsOtorisasi4,0) AS bit) IsOtorisasi4, CAST(ISNULL(f.IsOtorisasi5,0) AS bit) IsOtorisasi5,
  ISNULL(f.TIPE,'') TIPE, CAST(ISNULL(f.IsBatal,0) AS bit) IsBatal, f.pembatalan
INTO #FlWeb
FROM (SELECT DISTINCT USERID FROM dbo.DBFLMENU) u
CROSS JOIN dbo.DBMENUWEB w
LEFT JOIN #MapMenu mp ON mp.wk = w.KODEMENU
LEFT JOIN dbo.DBFLMENU f ON f.USERID = u.USERID AND f.L1 = mp.bk;

-- menu induk ('#' / kosong): dapat akses kalau ada submenu yang diakses
UPDATE h SET h.HASACCESS = 1
FROM #FlWeb h
WHERE ISNULL(h.href,'') IN ('','#') AND h.HASACCESS = 0
  AND EXISTS (SELECT 1 FROM #FlWeb c WHERE c.USERID = h.USERID AND c.L1 <> h.L1 AND c.L1 LIKE h.L1 + '%' AND c.HASACCESS = 1);

INSERT INTO dbo.DBFLMENUWEB (USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT, IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5, TIPE, IsBatal, pembatalan)
SELECT USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT, IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5, TIPE, IsBatal, pembatalan
FROM #FlWeb x WHERE NOT EXISTS (SELECT 1 FROM dbo.DBFLMENUWEB d WHERE d.USERID = x.USERID AND d.L1 = x.L1);

-- 5. DBFLMENUREPORTWEB: diturunkan dari DBFLMENUREPORT BARU
IF OBJECT_ID('dbo.DBFLMENUREPORTWEB') IS NULL
CREATE TABLE dbo.DBFLMENUREPORTWEB (
  UserID varchar(25) NOT NULL CONSTRAINT DF_DBFLMENUREPORTWEB_UserID DEFAULT (''),
  L1 varchar(25) NOT NULL CONSTRAINT DF_DBFLMENUREPORTWEB_L1 DEFAULT (''),
  Access bit NOT NULL CONSTRAINT DF_DBFLMENUREPORTWEB_Access DEFAULT ((0)),
  IsDesign bit NOT NULL CONSTRAINT DF_DBFLMENUREPORTWEB_IsDesign DEFAULT ((0)),
  Isexport bit NOT NULL CONSTRAINT DF_DBFLMENUREPORTWEB_Isexport DEFAULT ((0)),
  CONSTRAINT PK_DBFLMENUREPORTWEB PRIMARY KEY (UserID, L1));

SELECT w.KODEMENU wk, MIN(m.KODEMENU) bk INTO #MapRpt
FROM dbo.DBMENUREPORTWEB w JOIN dbo.DBMENUREPORT m ON m.href = w.href
WHERE ISNULL(w.href,'') NOT IN ('','#') GROUP BY w.KODEMENU;
INSERT INTO #MapRpt (wk, bk)
SELECT w.KODEMENU, m.KODEMENU FROM dbo.DBMENUREPORTWEB w JOIN dbo.DBMENUREPORT m ON m.KODEMENU = w.KODEMENU
WHERE NOT EXISTS (SELECT 1 FROM #MapRpt x WHERE x.wk = w.KODEMENU) AND m.Keterangan = w.Keterangan;

SELECT u.UserID, w.KODEMENU L1, w.href,
  CAST(ISNULL(f.Access,0) AS bit) Access, CAST(ISNULL(f.IsDesign,0) AS bit) IsDesign, CAST(ISNULL(f.Isexport,0) AS bit) Isexport
INTO #FlRpt
FROM (SELECT DISTINCT UserID FROM dbo.DBFLMENUREPORT) u
CROSS JOIN dbo.DBMENUREPORTWEB w
LEFT JOIN #MapRpt mp ON mp.wk = w.KODEMENU
LEFT JOIN dbo.DBFLMENUREPORT f ON f.UserID = u.UserID AND f.L1 = mp.bk;

UPDATE h SET h.Access = 1
FROM #FlRpt h
WHERE ISNULL(h.href,'') IN ('','#') AND h.Access = 0
  AND EXISTS (SELECT 1 FROM #FlRpt c WHERE c.UserID = h.UserID AND c.L1 <> h.L1 AND c.L1 LIKE h.L1 + '%' AND c.Access = 1);

INSERT INTO dbo.DBFLMENUREPORTWEB (UserID, L1, Access, IsDesign, Isexport)
SELECT UserID, L1, Access, IsDesign, Isexport FROM #FlRpt x
WHERE NOT EXISTS (SELECT 1 FROM dbo.DBFLMENUREPORTWEB d WHERE d.UserID = x.UserID AND d.L1 = x.L1);

COMMIT;
GO
SET NOEXEC OFF;
GO
