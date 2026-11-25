-- 39 — Menu Report Gudang: Pemakaian, Opname & Koreksi, Sample, Transfer, Konsinyasi dijadikan cabang (2026-11-25)
-- Tiap menu (dulu langsung membuka report) sekarang jadi cabang (href '#') berisi:
--   xx01 = report lama (href dan ACCESS lama dipindah ke sini)
--   xx02 = report Permintaan / Perintah yang baru
-- Hak akses user (DBFLMENUREPORTWEB) dari kode lama disalin ke kedua sub-menu.
-- Aman dijalankan ulang.

SET NOCOUNT ON;

-- Backup baris lama (sekali saja)
IF OBJECT_ID('dbo.DBMENUREPORTWEB_BAK39') IS NULL
  SELECT * INTO DBMENUREPORTWEB_BAK39 FROM DBMENUREPORTWEB
  WHERE KODEMENU IN ('04010','04030','04040','04050','04060');

DECLARE @m TABLE (Parent varchar(25), Kode varchar(25), Ket varchar(500), Href varchar(50), Akses int, Color varchar(20), Icon varchar(20));
INSERT INTO @m VALUES
 ('04010','040101','Pemakaian',             'laporanpemakaianqty',               401020,  'c-cyan',   'trending-up'),
 ('04010','040102','Permintaan Pemakaian',  'laporanpermintaanpemakaian',        0,       'c-blue',   'clipboard-list'),
 ('04030','040301','Opname & Koreksi',      'laporanopname',                     4080,    'c-yellow', 'clipboard-list'),
 ('04030','040302','Perintah Opname',       'laporanperintahopname',             0,       'c-pink',   'clipboard-clock'),
 ('04040','040401','Sample',                'laporanhistoripenyerahansample',    408135,  'c-purple', 'layers'),
 ('04040','040402','Permintaan Sample',     'laporanpermintaansample',           0,       'c-teal',   'clipboard-list'),
 ('04050','040501','Transfer',              'laporantransferkecabangblmditerima',40991,   'c-teal',   'arrowdownup'),
 ('04050','040502','Permintaan Transfer',   'laporanpermintaantransfer',         0,       'c-blue',   'clipboard-list'),
 ('04060','040601','Konsinyasi',            'laporangudangoutkonsin',            4099333, 'c-red',    'layers'),
 ('04060','040602','Permintaan Konsinyasi', 'laporangudangpermintaankonsinyasi', 0,       'c-indigo', 'clipboard-list');

-- 1. Sub-menu baru
INSERT INTO DBMENUREPORTWEB (KODEMENU, Keterangan, L0, ACCESS, OL, HeaderMenu, href, color, icon)
SELECT m.Kode, m.Ket, 3, m.Akses, 0, 1, m.Href, m.Color, m.Icon
FROM @m m
WHERE NOT EXISTS (SELECT 1 FROM DBMENUREPORTWEB w WHERE w.KODEMENU = m.Kode);

-- 2. Menu induk jadi cabang
UPDATE DBMENUREPORTWEB SET href = '#', ACCESS = 0
WHERE KODEMENU IN ('04010','04030','04040','04050','04060');

-- 3. Hak akses user: salin dari kode induk ke sub-menu
INSERT INTO DBFLMENUREPORTWEB (UserID, L1, Access, IsDesign, Isexport)
SELECT f.UserID, m.Kode, f.Access, f.IsDesign, f.Isexport
FROM DBFLMENUREPORTWEB f
JOIN @m m ON m.Parent = f.L1
WHERE NOT EXISTS (SELECT 1 FROM DBFLMENUREPORTWEB x WHERE x.UserID = f.UserID AND x.L1 = m.Kode);

/* ROLLBACK
DELETE FROM DBFLMENUREPORTWEB WHERE L1 IN ('040101','040102','040301','040302','040401','040402','040501','040502','040601','040602');
DELETE FROM DBMENUREPORTWEB   WHERE KODEMENU IN ('040101','040102','040301','040302','040401','040402','040501','040502','040601','040602');
UPDATE w SET w.href = b.href, w.ACCESS = b.ACCESS
FROM DBMENUREPORTWEB w JOIN DBMENUREPORTWEB_BAK39 b ON b.KODEMENU = w.KODEMENU;
*/
