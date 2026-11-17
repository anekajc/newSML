/* ROLLBACK 34 - kembalikan parameter sp_PBiaya ke varchar(15) seperti sebelum 17 November 2026.
   Kolom dbPBiaya.Kodebiaya dibiarkan varchar(30) (mengecilkannya bisa gagal kalau sudah ada
   KodeBiaya berisi No Bukti BPL yang lebih dari 15 karakter). */
USE DBSMLBARU;
GO

ALTER PROCEDURE [dbo].[sp_PBiaya]
@choice varchar(1),
@KodeBiaya varchar(15),
@Keterangan varchar(200),
@Nilai Numeric(18,2),
@KodeVls varchar(15),
@Kurs Numeric(18,2),
@NoBuktiInv Varchar(30),
@OldKode varchar(15)='',
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
