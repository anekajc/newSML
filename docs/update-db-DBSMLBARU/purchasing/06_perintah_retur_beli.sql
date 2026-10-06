-- 06 - Menu Perintah Retur Beli (upgrade tampilan dari newProgramAJC)
-- Sifat: hanya menambah. Aman dijalankan ulang.
--
-- 1. View baru vwBrowsPrRBeli (belum ada di DBSMLBARU, disalin dari DBSMLNEW)
--      status = 'Belum' / 'Sebagian' / 'Sudah' per PRB, dari qty DBPRRBELIDET dibanding
--      qty yang sudah diretur di DBRBELIDET (lewat NOPRB).
--    Dipakai kolom Status di tabel Perintah Retur Beli (PerintahReturBeliController@loadAll).
--    Hanya membaca tabel DBPRRBELI, DBPRRBELIDET, DBRBELIDET.

USE DBSMLBARU
GO

IF OBJECT_ID('dbo.vwBrowsPrRBeli', 'V') IS NULL
  EXEC('
create View [dbo].[vwBrowsPrRBeli]
AS
select a.NOBUKTI,a.tanggal,a.IsOtorisasi1,a.OtoUser1,a.TglOto1,case when ISNULL(qntbeli,0)=0 then ''Belum'' else
case when qntpr>ISNULL(qntbeli,0) then ''Sebagian'' else ''Sudah'' end end status
from DBPRRBELI A
LEFT OUTER JOIN (select nobukti,sum(qnt)qntpr from DBPRRBELIdet group by nobukti)B ON A.NoBukti=B.NoBukti
LEFT OUTER JOIN (select noprb,SUM(qnt) qntbeli from DBRBELIDET group by NOPRB) D ON A.NoBukti = D.NOPRB
')
GO

-- Cek hasil (hanya SELECT)
select status, count(*) jumlah from dbo.vwBrowsPrRBeli group by status
GO
