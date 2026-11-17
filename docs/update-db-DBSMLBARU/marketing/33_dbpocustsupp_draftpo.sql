-- 33 - PO Customer: kolom Draft PO (pPO) + parameter @pPO di Sp_POCustomer (16 November 2026)
--
-- Menu Terima PO Customer (terimapocustomer) mendapat dropdown "Draft PO" (Tidak = 0, Ya = 1).
-- Nilainya dipakai menu SO: saat No PO dipilih, dbso.pPO otomatis diisi dari DBPOCUSTSUPP.pPO
-- (hanya sekali saat SO dibuat; perubahan PO Customer sesudahnya tidak mengubah SO).
--
-- 1. DBPOCUSTSUPP.pPO bit NOT NULL DEFAULT 0. PO Customer lama otomatis terisi 0.
-- 2. Sp_POCustomer: parameter baru @pPO bit = NULL (paling belakang).
--    - I : pPO = isnull(@pPO, 0)
--    - U : pPO = isnull(@pPO, pPO) -> pemanggil lama (programajcSML, 6 parameter) tidak
--          mereset Draft PO.
-- Rollback SP: 33_sp_pocustomer_ROLLBACK.sql (kolom pPO boleh dibiarkan).
-- Aman dijalankan ulang.

USE DBSMLBARU
GO

IF COL_LENGTH('dbo.DBPOCUSTSUPP', 'pPO') IS NULL
BEGIN
  ALTER TABLE dbo.DBPOCUSTSUPP ADD pPO bit NOT NULL
    CONSTRAINT DF_DBPOCUSTSUPP_pPO DEFAULT (0) WITH VALUES
END
GO

ALTER procedure [dbo].[Sp_POCustomer]
--declare
@CHOICE CHAR,
@TglTerima DaTETIME,
@POCUSTOMER VARCHAR(100),
@KODECUSTSUPP VARCHAR(30),
@ID INT,
@RpPO Numeric(18,2)=0,
@pPO bit = NULL


AS
Begin tran

IF @CHOICE='I'
BEGIN
INSERT INTO DBPOCUSTSUPP (TGLINPUT,TGLTERIMA,POCUSTOMER,KODECUSTSUPP,RpPO,pPO)
VALUES (GETDATE(),@TglTerima,@POCUSTOMER,@KODECUSTSUPP,@RpPO,isnull(@pPO,0))
END


IF @CHOICE='U'
BEGIN
UPDATE DBPOCUSTSUPP SET TGLTERIMA=@TglTerima,POCUSTOMER=@POCUSTOMER,KODECUSTSUPP=@KODECUSTSUPP,RpPO=@RpPO,
       pPO=isnull(@pPO,pPO)
 WHERE ID=@ID
END


IF @CHOICE='D'
BEGIN
DELETE DBPOCUSTSUPP WHERE ID=@ID
END

if @@error<>0  goto jikasalah
Commit tran
Return
JikaSalah: Rollback tran
           Return
GO
