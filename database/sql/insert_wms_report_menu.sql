/*
  Memasukkan menu Report WMS (dari tabel new_menureport di ajcwms) ke DBMENUWEB, di bawah menu WMS (KODEMENU 21).

  Pemetaan KODEMENU lama (new_menureport) -> baru (DBMENUWEB):
    05    Report                              -> 2106    (header, L0=1)
    051   Kartu Stok                          -> 210601  reportkartustok
    052   Stok Lokasi                         -> 210602  reportstoklokasi
    053   Stok Gudang                         -> 210603  reportstokgudang
    054   Report SO-SPB                       -> 210604  reportsospb
    055   Report Outstanding Receiving        -> 210605  reportoutreceiving
    056   Report Outstanding Picking - SPB    -> 210606  reportpickspb
    057   Report Outs Pickinglist - Picking   -> 210607  reportplp
    058   Report Relokasi                     -> 210608  reportrelokasi
    059   Kartu Stock Gudang                  -> 210609  kartustockgudang
    0591  Report Outstanding Dispatch         -> 210610  reportoutdispatch
    0592  Report Opname Harian                -> 210611  reportopnameharian
    0593  Report Putaway                      -> 210612  reportputaway
    0594  Report Picking                      -> 210613  reportpicking

  Kode controller di newSML (app/Http/Controllers/Wms/Report*Controller.php, KartuStockGudangController.php)
  memakai kode baru di atas.

  ACCESS: 85 untuk header, 8501..8513 untuk anak (belum dipakai baris lain saat skrip ini dibuat).

  Aman dijalankan ulang: baris yang sudah ada dilewati. Dijalankan dalam satu transaksi; kalau ada
  KODEMENU/ACCESS yang bentrok dengan baris lain, seluruhnya dibatalkan.

  Bagian 2 (opsional) memberi hak akses ke user yang sudah punya akses QC LPB (210201). Hapus/sesuaikan
  bagian itu kalau tidak diinginkan. Akses per-user lain diatur lewat menu Set Pemakai.

  Rollback:
    DELETE FROM DBFLMENUWEB WHERE L1 LIKE '2106%';
    DELETE FROM DBMENUWEB   WHERE KODEMENU LIKE '2106%';
*/
SET XACT_ABORT ON;
BEGIN TRAN;

DECLARE @menu TABLE (KODEMENU varchar(25), Keterangan varchar(500), L0 tinyint, ACCESS int, href varchar(50));

INSERT INTO @menu (KODEMENU, Keterangan, L0, ACCESS, href) VALUES
 ('2106',   'Report',                            1, 85,   '#'),
 ('210601', 'Kartu Stok',                        2, 8501, 'reportkartustok'),
 ('210602', 'Stok Lokasi',                       2, 8502, 'reportstoklokasi'),
 ('210603', 'Stok Gudang',                       2, 8503, 'reportstokgudang'),
 ('210604', 'Report SO-SPB',                     2, 8504, 'reportsospb'),
 ('210605', 'Report Outstanding Receiving',      2, 8505, 'reportoutreceiving'),
 ('210606', 'Report Outstanding Picking - SPB',  2, 8506, 'reportpickspb'),
 ('210607', 'Report Outs Pickinglist - Picking', 2, 8507, 'reportplp'),
 ('210608', 'Report Relokasi',                   2, 8508, 'reportrelokasi'),
 ('210609', 'Kartu Stock Gudang',                2, 8509, 'kartustockgudang'),
 ('210610', 'Report Outstanding Dispatch',       2, 8510, 'reportoutdispatch'),
 ('210611', 'Report Opname Harian',              2, 8511, 'reportopnameharian'),
 ('210612', 'Report Putaway',                    2, 8512, 'reportputaway'),
 ('210613', 'Report Picking',                    2, 8513, 'reportpicking');

-- Batalkan kalau ACCESS dipakai baris lain (KODEMENU yang sama dianggap sudah ada dan dilewati).
IF EXISTS (SELECT 1 FROM @menu m JOIN DBMENUWEB d ON d.ACCESS = m.ACCESS AND d.KODEMENU <> m.KODEMENU)
BEGIN
    RAISERROR('ACCESS sudah dipakai menu lain, dibatalkan.', 16, 1);
    ROLLBACK TRAN;
    RETURN;
END;

-- 1. Menu
INSERT INTO DBMENUWEB (KODEMENU, Keterangan, L0, ACCESS, OL, TipeTrans, HeaderMenu, href)
SELECT m.KODEMENU, m.Keterangan, m.L0, m.ACCESS, 0, '', 21, m.href
FROM @menu m
WHERE NOT EXISTS (SELECT 1 FROM DBMENUWEB d WHERE d.KODEMENU = m.KODEMENU);

-- 2. (Opsional) Hak akses: salin dari hak akses QC LPB (210201) milik tiap user.
INSERT INTO DBFLMENUWEB (USERID, L1, HASACCESS, ISTAMBAH, ISKOREKSI, ISHAPUS, ISCETAK, ISEXPORT,
                         IsOtorisasi1, IsOtorisasi2, IsOtorisasi3, IsOtorisasi4, IsOtorisasi5,
                         TIPE, IsBatal, pembatalan)
SELECT f.USERID, m.KODEMENU, f.HASACCESS, f.ISTAMBAH, f.ISKOREKSI, f.ISHAPUS, f.ISCETAK, f.ISEXPORT,
       f.IsOtorisasi1, f.IsOtorisasi2, f.IsOtorisasi3, f.IsOtorisasi4, f.IsOtorisasi5,
       f.TIPE, f.IsBatal, f.pembatalan
FROM DBFLMENUWEB f
CROSS JOIN @menu m
WHERE f.L1 = '210201'
  AND NOT EXISTS (SELECT 1 FROM DBFLMENUWEB x WHERE x.USERID = f.USERID AND x.L1 = m.KODEMENU);

COMMIT TRAN;

SELECT KODEMENU, Keterangan, L0, ACCESS, href FROM DBMENUWEB WHERE KODEMENU LIKE '2106%' ORDER BY KODEMENU;
