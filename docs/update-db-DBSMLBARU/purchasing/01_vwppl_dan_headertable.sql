/* =====================================================================
   01 - VwPPL + tabel pengaturan kolom (DBHEADERTABLE / DBHEADERTABLEALIAS)
   Untuk menu : Permintaan Pembelian Agen, Non-Agen, Non-Stock,
                Closing PR (pengaturan kolom)
   Sifat      : HANYA MENAMBAH. Aman dijalankan ulang.
   Dijalankan : 2026-10-02 di DBSMLBARU (lokal/dev) - sukses.

   PERHATIAN sebelum menjalankan di server:
   ALTER VIEW mengganti SELURUH definisi VwPPL. Definisi di bawah =
   definisi lama DBSMLBARU + 2 kolom baru (QNTBATAL, qntpo) + 1 join baru
   (subquery C ke DBPODET). Cocokkan dulu definisi VwPPL di server:
       EXEC sp_helptext 'dbo.VwPPL'
   Kalau di server berbeda dengan bagian "definisi lama" di bawah,
   JANGAN langsung jalankan - cukup tambahkan 2 kolom + join C ke
   definisi milik server.
   ===================================================================== */
USE DBSMLBARU;
GO
SET NOCOUNT ON;
GO

-- 1. VwPPL: tambah kolom QNTBATAL dan qntpo (dipakai badge Status di 3 menu PR)
ALTER VIEW [dbo].[VwPPL]
as
Select A.KDDep , A.IsClose, B.IsClose IsClosed, H.SAT1,H.SAT2,H.SAT3,H.ISI1,H.ISI2,H.ISI3,	A.NoBukti, A.Nourut, A.Tanggal,MONTH(a.Tanggal) Bulan, YEAR(A.Tanggal) Tahun,
	B.Urut, B.KodeBrg, B.NamaBrg, B.Qnt, B.NoSat, B.Isi Isi,
	case when B.Nosat=1 then H.SAT1 when B.Nosat=2 then H.SAT2 When B.Nosat=3 then H.SAT3 End Satuan,B.Keterangan,
	ISNULL(A.pAgen,0) pAgen,ISNULL(B.IsJasa,0) IsJasa,B.NOSO,B.URUTSO,B.NoSOCust, a.IsOtorisasi1, a.OtoUser1, a.TglOto1
	,QNTBATAL,isnull(c.qnt,0) qntpo                                                    -- BARU
From dbPPL A
Left Outer join dbPPLDet B on B.NoBukti=a.NoBukti
Left Outer join dbBarang H on H.KodeBrg=b.KodeBrg
left outer join (select noppl,urutppl,sum(qnt)qnt  from DBPODET group by NoPPL,urutppl) C on C.NoPPL=b.Nobukti and c.UrutPPL=b.urut   -- BARU
GO

-- 2. Tabel pengaturan kolom tabel per user (dibuat hanya kalau belum ada)
IF OBJECT_ID('dbo.DBHEADERTABLE') IS NULL
CREATE TABLE dbo.DBHEADERTABLE (username varchar(20) NULL, href varchar(50) NULL, header varchar(1000) NULL, tipe varchar(1000) NULL, isnumber varchar(1000) NULL, value varchar(1000) NULL, isshown varchar(1000) NULL, urut numeric(18,0) NULL)
GO
IF OBJECT_ID('dbo.DBHEADERTABLEALIAS') IS NULL
CREATE TABLE dbo.DBHEADERTABLEALIAS (href varchar(50) NULL, value varchar(1000) NULL, urut numeric(18,0) NULL)
GO

-- 3. Alias kolom untuk 3 menu PR (diisi hanya kalau belum ada)
INSERT INTO dbo.DBHEADERTABLEALIAS (href, value) SELECT 'pembelianpermintaanagen', '[{"value":"NoBukti","alias":"NoBukti"},{"value":"Tanggal","alias":"Tanggal"}]' WHERE NOT EXISTS (SELECT 1 FROM dbo.DBHEADERTABLEALIAS WHERE href='pembelianpermintaanagen')
INSERT INTO dbo.DBHEADERTABLEALIAS (href, value) SELECT 'pembelianpermintaannonagen', '[{"value":"NoBukti","alias":"NoBukti"},{"value":"Tanggal","alias":"Tanggal"}]' WHERE NOT EXISTS (SELECT 1 FROM dbo.DBHEADERTABLEALIAS WHERE href='pembelianpermintaannonagen')
INSERT INTO dbo.DBHEADERTABLEALIAS (href, value) SELECT 'pembelianpermintaannonstock', '[{"value":"NoBukti","alias":"NoBukti"},{"value":"Tanggal","alias":"Tanggal"}]' WHERE NOT EXISTS (SELECT 1 FROM dbo.DBHEADERTABLEALIAS WHERE href='pembelianpermintaannonstock')
GO
