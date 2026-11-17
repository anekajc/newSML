/* 34 - Biaya LPB di menu Pembelian Invoice (17 November 2026)
   KodeBiaya diisi No Bukti invoice (mis. SML/BPL/00004/1126, 18 karakter), padahal kolom
   dbPBiaya.Kodebiaya dan parameter @KodeBiaya/@OldKode di sp_PBiaya hanya varchar(15).
   Diperbesar jadi varchar(30). Data lama tidak berubah. Aman dijalankan ulang.
   Rollback SP: 34_sp_pbiaya_ROLLBACK.sql (kolom tidak perlu dikecilkan lagi). */
USE DBSMLBARU;
GO

IF COL_LENGTH('dbo.dbPBiaya', 'Kodebiaya') < 30
  ALTER TABLE dbo.dbPBiaya ALTER COLUMN Kodebiaya varchar(30) NOT NULL;
GO

ALTER PROCEDURE [dbo].[sp_PBiaya]
@choice varchar(1),
@KodeBiaya varchar(30),
@Keterangan varchar(200),
@Nilai Numeric(18,2),
@KodeVls varchar(15),
@Kurs Numeric(18,2),
@NoBuktiInv Varchar(30),
@OldKode varchar(30)='',
@urut Integer=0,
@NoBiayaPO varchar(25)='',
@urutBiayaPO int=0,
@KODECUSTSUPP VARCHAR(25)=''

AS

if @choice='I'
begin
    select @urut=isnull(max(urut),0)+1 from dbPBiaya Where NoBuktiInv=@NoBuktiInv
    If @urut is null Set @urut=1
	insert into dbPBiaya(KodeBiaya,Keterangan,Nilai,KodeVls, Kurs,NoBuktiInv,Urut,NoBiayaPO,urutBiayaPO,KODECUSTSUPP)
	values (@KodeBiaya,@Keterangan,@Nilai,@KodeVls, @Kurs,@NoBuktiInv,@urut,@NoBiayaPO,@urutBiayaPO,@KODECUSTSUPP)
end
if @choice='U'
begin
	Update dbPBiaya set KodeBiaya=@KodeBiaya,Keterangan=@Keterangan,Nilai=@Nilai,
		                KodeVls=@KodeVls, Kurs=@Kurs,KODECUSTSUPP=@KODECUSTSUPP
	where KodeBiaya=@OldKode and Urut=@urut and NoBuktiInv=@NoBuktiInv
end
if @choice='D'
begin
	delete dbPBiaya
	where KodeBiaya=@OldKode and Urut=@urut and NoBuktiInv=@NoBuktiInv
end
GO
