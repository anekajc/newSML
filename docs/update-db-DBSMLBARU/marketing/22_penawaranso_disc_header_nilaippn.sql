-- 22 - Penawaran SO: Disc header ikut ke DPP/PPN/Grand Total + tarif PPN pakai NILAIPPN (seperti Purchase Order)
-- Database : DBSMLBARU (newSML). Untuk DBSMLNEW (newProgramAJC) pakai skrip versi DBSMLNEW (default NILAIPPN 10).
-- Server   : SQL Server 2008 R2 (tanpa CREATE OR ALTER / DROP IF EXISTS).
-- Sifat    : aman dijalankan ulang. BACKUP DBSMLBARU dulu.
--
-- Masalah sebelumnya:
--   Computed column NDISKON/NDPP/NPPN/NNET (+ versi RP) di DBPENAWARANSODET menulis mati diskon header = 0
--   dan PPN = 11% (NDPPRP malah membagi 1.1, beda dengan NDPP yang membagi 1.11). Akibatnya Disc % / DiscRp
--   di form Penawaran SO tidak pernah mengurangi DPP, PPN, dan Grand Total.
--
-- Perubahan (disamakan dengan DBPODET + Sp_UpdatePO):
--   1. DBPENAWARANSODET tambah kolom DISC (float, default 0)  = Disc % header, disalin dari DBPENAWARANSO.DISC
--                                   dan NILAIPPN (int, default 11) = tarif PPN (%), sama seperti DBPODET.NilaiPPN.
--      Default 11 = tarif yang selama ini ditulis mati, jadi data lama nilainya tidak berubah.
--   2. Computed column NDISKON, NDPP, NDPPRP, NPPN, NPPNRP, NNET, NNETRP dibuat ulang memakai DISC dan NILAIPPN
--      (rumus sama persis dengan DBPODET). HRGNETTO, SUBTOTAL, SUBTOTALRP tidak diubah.
--      PPN 0 = non PPN, 1 = exclude, 2 = include.
--   3. SP baru Sp_UpdatePenawaranSO @NOBUKTI: salin PPN, DISC, KURS header ke semua baris detail.
--      Dipanggil aplikasi setiap item disimpan dan setiap Disc %/DiscRp/PPN/Kurs header diubah
--      (PenawaranSOController::spAdd & spUpdatePenawaranSO, route penawaransospupdate).
--   4. Data lama: jalankan penyelarasan sekali untuk semua penawaran (hanya penawaran yang punya Disc header
--      yang nilainya berubah - saat skrip dibuat ada 2 dokumen).
--   5. sp_refreshview untuk view yang membaca DBPENAWARANSODET.
--
-- Pemakai kolom ini (dicek 22 Oktober 2026, tidak ada yang schema-bound, tidak ada trigger/index di kolom ini):
--   View : DashOutPSO, DashOutRFQ, DashPSO, DashPSOblmjdSO, VWmasterPenawaran, VWmasterPenawaranOut,
--          VWmasterRefOut, vwpenawaransodet
--   SP   : sp_cetakpenawaranso, SP_InsertPSO, SP_InsertPSOweb, SP_Penawaranso, sp_TempOutPSO
--          (SP_Penawaranso memakai daftar kolom eksplisit saat INSERT, jadi kolom baru tidak mengganggu.)

USE DBSMLBARU
GO

-- 1. Kolom baru ----------------------------------------------------------------------------------
IF COL_LENGTH('dbo.DBPENAWARANSODET', 'DISC') IS NULL
  ALTER TABLE dbo.DBPENAWARANSODET ADD DISC float NOT NULL
    CONSTRAINT DF_DBPENAWARANSODET_DISC DEFAULT (0)
GO

IF COL_LENGTH('dbo.DBPENAWARANSODET', 'NILAIPPN') IS NULL
  ALTER TABLE dbo.DBPENAWARANSODET ADD NILAIPPN int NOT NULL
    CONSTRAINT DF_DBPENAWARANSODET_NILAIPPN DEFAULT (11)
GO

-- 2. Computed column dibuat ulang ----------------------------------------------------------------
BEGIN TRAN

ALTER TABLE dbo.DBPENAWARANSODET DROP COLUMN NDISKON, NDPP, NDPPRP, NPPN, NPPNRP, NNET, NNETRP

ALTER TABLE dbo.DBPENAWARANSODET ADD
  NDISKON AS (([QNT]*([HARGA]-[DISCTOT]))*([DISC]/(100))),

  NDPP AS (case when [PPN]=(1) OR [PPN]=(0) then [QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100)
                when [PPN]=(2) then ([QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100))/((1.0)+[NILAIPPN]/(100.00))
                else (0) end),

  NDPPRP AS (case when [PPN]=(1) OR [PPN]=(0) then [QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100)
                  when [PPN]=(2) then ([QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100))/((1.0)+[NILAIPPN]/(100.00))
                  else (0) end*[KURS]),

  NPPN AS (case when [PPN]=(1) then [QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100)
                when [PPN]=(2) then ([QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100))/((1.0)+[NILAIPPN]/(100.00))
                else (0) end*([NILAIPPN]/(100.00))),

  NPPNRP AS ((case when [PPN]=(1) then [QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100)
                   when [PPN]=(2) then ([QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100))/((1.0)+[NILAIPPN]/(100.00))
                   else (0) end*([NILAIPPN]/(100.00)))*[KURS]),

  NNET AS (case when [PPN]=(1) OR [PPN]=(0) then [QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100)
                when [PPN]=(2) then ([QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100))/((1.0)+[NILAIPPN]/(100.00))
                else (0) end
         + case when [PPN]=(1) then [QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100)
                when [PPN]=(2) then ([QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100))/((1.0)+[NILAIPPN]/(100.00))
                else (0) end*([NILAIPPN]/(100.00))),

  NNETRP AS ((case when [PPN]=(1) OR [PPN]=(0) then [QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100)
                   when [PPN]=(2) then ([QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100))/((1.0)+[NILAIPPN]/(100.00))
                   else (0) end
            + case when [PPN]=(1) then [QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100)
                   when [PPN]=(2) then ([QNT]*([HARGA]-[DISCTOT])-(([QNT]*([HARGA]-[DISCTOT]))*[DISC])/(100))/((1.0)+[NILAIPPN]/(100.00))
                   else (0) end*([NILAIPPN]/(100.00)))*[KURS])

COMMIT
GO

-- 3. SP penyelaras header -> detail --------------------------------------------------------------
IF OBJECT_ID('dbo.Sp_UpdatePenawaranSO', 'P') IS NOT NULL
  DROP PROCEDURE dbo.Sp_UpdatePenawaranSO
GO

CREATE PROCEDURE dbo.Sp_UpdatePenawaranSO
  @NOBUKTI varchar(30)
AS
SET NOCOUNT ON

-- NILAIPPN = tarif dari DBNILAIPPN sesuai tanggal dokumen (sama dengan yang dipakai form).
update A set A.PPN = isnull(B.PPN, 0), A.DISC = isnull(B.DISC, 0), A.KURS = isnull(B.KURS, 1),
       A.NILAIPPN = isnull((select top 1 N.NilaiPPN from dbo.DBNILAIPPN N
                            where B.TANGGAL between N.TglAwal and N.TglAkhir order by N.Urut desc), 11)
from dbo.DBPENAWARANSODET A
inner join dbo.DBPENAWARANSO B on B.NOBUKTI = A.NOBUKTI
where A.NOBUKTI = @NOBUKTI
GO

-- 4. Selaraskan data lama sekali (semua dokumen) --------------------------------------------------
update A set A.PPN = isnull(B.PPN, 0), A.DISC = isnull(B.DISC, 0), A.KURS = isnull(B.KURS, 1),
       A.NILAIPPN = isnull((select top 1 N.NilaiPPN from dbo.DBNILAIPPN N
                            where B.TANGGAL between N.TglAwal and N.TglAkhir order by N.Urut desc), 11)
from dbo.DBPENAWARANSODET A
inner join dbo.DBPENAWARANSO B on B.NOBUKTI = A.NOBUKTI
GO

-- DBPENAWARANSO.DISCRP (rupiah) disamakan dengan Total x Disc % / 100, sama seperti yang
-- dilakukan form Penawaran SO (tampilDiscHeader) saat dokumen dibuka.
update B set B.DISCRP = round(isnull(X.TotDiskon, 0), 2)
from dbo.DBPENAWARANSO B
inner join (select NOBUKTI, sum(isnull(NDISKON, 0)) TotDiskon from dbo.DBPENAWARANSODET group by NOBUKTI) X
        on X.NOBUKTI = B.NOBUKTI
where isnull(B.DISC, 0) <> 0
GO

-- 5. Refresh view yang membaca DBPENAWARANSODET ---------------------------------------------------
EXEC sp_refreshview 'dbo.DashOutPSO'
EXEC sp_refreshview 'dbo.DashOutRFQ'
EXEC sp_refreshview 'dbo.DashPSO'
EXEC sp_refreshview 'dbo.DashPSOblmjdSO'
EXEC sp_refreshview 'dbo.VWmasterPenawaran'
EXEC sp_refreshview 'dbo.VWmasterPenawaranOut'
EXEC sp_refreshview 'dbo.VWmasterRefOut'
EXEC sp_refreshview 'dbo.vwpenawaransodet'
GO

-- Cek hasil: Disc header harus mengurangi DPP; DPP + PPN = Net.
-- select top 20 A.NOBUKTI, B.DISC DiscHeader, sum(A.SUBTOTAL) Total, sum(A.NDISKON) Diskon,
--        sum(A.NDPP) DPP, sum(A.NPPN) PPN, sum(A.NNET) Net, max(A.NILAIPPN) NilaiPPN
-- from DBPENAWARANSODET A join DBPENAWARANSO B on B.NOBUKTI = A.NOBUKTI
-- group by A.NOBUKTI, B.DISC order by abs(isnull(B.DISC,0)) desc
