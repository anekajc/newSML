-- 37. Kode menu WMS disamakan ke 21xxxx (2026-11-19)
--
-- Masalah: root menu WMS di DBMENUWEB berkode 10 (10, 1001..100310), padahal semua
-- controller WMS (app/Http/Controllers/Wms) cek akses dengan kode 21xxxx dan sebagian
-- menu sudah 21xxxx (210311..210314, 2104, 210401..210407, 2105, 210501).
-- Akibatnya menu 21xxxx (Transfer Gudang Out, Permintaan UK, Picking UK, Finalisasi UK,
-- Gudang, Dashboard) tidak punya induk di navbar dan tidak tampil, dan menu 10xxxx
-- yang tampil gagal di cek akses controller.
--
-- Perbaikan:
--   1. DBMENUWEB  : KODEMENU 10xxxx (HeaderMenu = 21) -> 21xxxx.
--   2. DBFLMENUWEB: baris L1 10xxxx dihapus bila baris 21xxxx untuk user yang sama sudah ada
--                   (baris 21xxxx dipertahankan karena berisi setting otorisasi/IsBatal);
--                   bila belum ada, L1-nya diganti ke 21xxxx.
--   3. DBREQOTORISASI: KODEMENU 10xxxx -> 21xxxx (kalau ada).
-- Backup: DBMENUWEB_BAK37, DBFLMENUWEB_BAK37 (dibuat sekali, berisi baris 10xxxx sebelum diubah).
-- Aman dijalankan ulang.

USE DBSMLBARU;
GO

SET XACT_ABORT ON;
BEGIN TRAN;

IF OBJECT_ID('dbo.DBMENUWEB_BAK37') IS NULL
    SELECT * INTO dbo.DBMENUWEB_BAK37 FROM dbo.DBMENUWEB
    WHERE HeaderMenu = 21 AND (KODEMENU = '10' OR KODEMENU LIKE '10__%');

IF OBJECT_ID('dbo.DBFLMENUWEB_BAK37') IS NULL
    SELECT * INTO dbo.DBFLMENUWEB_BAK37 FROM dbo.DBFLMENUWEB
    WHERE L1 IN (SELECT KODEMENU FROM dbo.DBMENUWEB_BAK37);

-- 1. DBMENUWEB
UPDATE m SET KODEMENU = '21' + SUBSTRING(m.KODEMENU, 3, 25)
FROM dbo.DBMENUWEB m
WHERE m.HeaderMenu = 21
  AND (m.KODEMENU = '10' OR m.KODEMENU LIKE '10__%')
  AND NOT EXISTS (SELECT 1 FROM dbo.DBMENUWEB x WHERE x.KODEMENU = '21' + SUBSTRING(m.KODEMENU, 3, 25));

-- 2. DBFLMENUWEB (hanya kode yang ada di backup = milik WMS)
DELETE f
FROM dbo.DBFLMENUWEB f
WHERE f.L1 IN (SELECT KODEMENU FROM dbo.DBMENUWEB_BAK37)
  AND EXISTS (SELECT 1 FROM dbo.DBFLMENUWEB x
              WHERE x.USERID = f.USERID AND x.L1 = '21' + SUBSTRING(f.L1, 3, 25));

UPDATE f SET L1 = '21' + SUBSTRING(f.L1, 3, 25)
FROM dbo.DBFLMENUWEB f
WHERE f.L1 IN (SELECT KODEMENU FROM dbo.DBMENUWEB_BAK37);

-- 3. DBREQOTORISASI
UPDATE dbo.DBREQOTORISASI SET KODEMENU = '21' + SUBSTRING(KODEMENU, 3, 25)
WHERE KODEMENU IN (SELECT KODEMENU FROM dbo.DBMENUWEB_BAK37);

COMMIT;
GO

-- Cek: harus 0 baris WMS berkode 10, dan menu 21 lengkap dengan induknya.
SELECT COUNT(*) AS SisaKode10 FROM dbo.DBMENUWEB
WHERE HeaderMenu = 21 AND (KODEMENU = '10' OR KODEMENU LIKE '10__%');

SELECT m.KODEMENU, m.Keterangan, m.L0, m.href,
       CASE WHEN m.L0 = 0 OR EXISTS (SELECT 1 FROM dbo.DBMENUWEB p
            WHERE p.KODEMENU = LEFT(m.KODEMENU, LEN(m.KODEMENU) - 2)) THEN 'OK' ELSE 'TANPA INDUK' END AS Induk
FROM dbo.DBMENUWEB m
WHERE m.HeaderMenu = 21
ORDER BY m.KODEMENU;
GO

-- ROLLBACK (jalankan manual bila perlu):
-- (Baris DBFLMENUWEB 21xxxx dibiarkan; baris 10xxxx dikembalikan dari backup.)
-- BEGIN TRAN;
-- UPDATE dbo.DBMENUWEB SET KODEMENU = '10' + SUBSTRING(KODEMENU,3,25)
--     WHERE KODEMENU IN (SELECT '21' + SUBSTRING(KODEMENU,3,25) FROM dbo.DBMENUWEB_BAK37);
-- INSERT INTO dbo.DBFLMENUWEB (USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT,
--        IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5, TIPE, IsBatal, pembatalan)
-- SELECT USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT,
--        IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5, TIPE, IsBatal, pembatalan
-- FROM dbo.DBFLMENUWEB_BAK37 b
-- WHERE NOT EXISTS (SELECT 1 FROM dbo.DBFLMENUWEB x WHERE x.USERID = b.USERID AND x.L1 = b.L1);
-- COMMIT;
