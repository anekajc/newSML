/* =====================================================================
   03 - Cek hasil setelah 01 dan 02 dijalankan (hanya SELECT, tidak mengubah apa pun)
   Angka pembanding = hasil di DBSMLBARU dev tanggal 2026-10-02.
   Di server angkanya bisa beda (tergantung jumlah user/menu di sana),
   yang penting semua objek ADA dan tidak ada yang 0.
   ===================================================================== */
USE DBSMLBARU;
GO
-- Kolom baru VwPPL harus ada (2 baris: QNTBATAL, qntpo)
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'VwPPL' AND COLUMN_NAME IN ('QNTBATAL', 'qntpo');

-- Kolom baru DBMENU harus ada (2 baris: icon, tableCount)
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'DBMENU' AND COLUMN_NAME IN ('icon', 'tableCount');

-- Jumlah isi tabel (dev: lihat komentar di tiap baris)
SELECT
  (SELECT COUNT(*) FROM dbo.DBMENU)             AS DBMENU,             -- dev 170 (168 lama + 2 baru)
  (SELECT COUNT(*) FROM dbo.DBMENUWEB)          AS DBMENUWEB,          -- dev 174
  (SELECT COUNT(*) FROM dbo.DBMENUREPORTWEB)    AS DBMENUREPORTWEB,    -- dev 125
  (SELECT COUNT(*) FROM dbo.DBFLMENUWEB)        AS DBFLMENUWEB,        -- dev 15.486
  (SELECT COUNT(*) FROM dbo.DBFLMENUREPORTWEB)  AS DBFLMENUREPORTWEB,  -- dev 10.750
  (SELECT COUNT(*) FROM dbo.DBHEADERTABLEALIAS) AS DBHEADERTABLEALIAS, -- minimal 3
  (SELECT COUNT(*) FROM dbo.VwPPL)              AS VwPPL;              -- harus SAMA dengan sebelum ALTER

-- Akses SA ke 3 menu PR (harus muncul 3 baris dengan HASACCESS = 1)
SELECT w.KODEMENU, w.Keterangan, w.href, f.HASACCESS, f.ISTAMBAH, f.IsOtorisasi1
FROM dbo.DBMENUWEB w
JOIN dbo.DBFLMENUWEB f ON f.L1 = w.KODEMENU AND f.USERID = 'SA'
WHERE w.href IN ('pembelianpermintaanagen', 'pembelianpermintaannonagen', 'pembelianpermintaannonstock');
GO
