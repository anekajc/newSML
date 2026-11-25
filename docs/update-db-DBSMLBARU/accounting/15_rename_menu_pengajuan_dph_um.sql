-- 15 — Ganti nama menu sidebar Accounting > DPH > "Pengajuan DPH UM"
--      menjadi "Pengajuan Pembayaran Uang Muka" (2026-11-23)
-- Aman dijalankan ulang.
-- ROLLBACK:
--   UPDATE DBMENUWEB SET Keterangan = 'Pengajuan DPH UM' WHERE KODEMENU = '02072' AND href = 'pengajuandph';

UPDATE DBMENUWEB
SET Keterangan = 'Pengajuan Pembayaran Uang Muka'
WHERE KODEMENU = '02072' AND href = 'pengajuandph';

SELECT KODEMENU, Keterangan, href FROM DBMENUWEB WHERE KODEMENU = '02072';
