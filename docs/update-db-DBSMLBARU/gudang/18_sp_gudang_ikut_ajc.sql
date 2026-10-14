/* =====================================================================
   18 - SP/view gudang disamakan dengan DBSMLNEW (newProgramAJC)
   Keputusan user (2026-10-14): gudang ikut newProgramAJC (versi terbaru).
   Isi        : SP_UBAHKEMASAN, vwDetailUbahKemasan, sp_CetakSerahSample,
                SP_InsertPRTRANSFER, vwMasterKoreksi = isi DBSMLNEW.
   Hanya menambah: kolom view yang hanya ada di DBSMLBARU tetap ada
                (vwDetailUbahKemasan: HargaIn, HPP2; vwMasterKoreksi: NOTE),
                ditandai komentar [newSML].
   Perhatian  : - SP_UBAHKEMASAN versi DBSMLBARU menyimpan HPP dibagi ISI dan
                  vwDetailUbahKemasan lama mengalikannya lagi dengan ISI.
                  Versi DBSMLNEW tidak membagi/mengalikan, jadi ubah kemasan
                  LAMA yang disimpan per satuan dasar tampil HPP per satuan dasar.
                - SP_InsertPRTRANSFER sekarang menerima @urut dan @qty (sama
                  dengan pemanggil di TransferBarangController newSML).
                - vwOutPPL & VwPPL juga beda, tapi dipakai purchasing/marketing,
                  jadi TIDAK diubah.
   Sifat      : ALTER, satu transaksi (gagal = batal semua). Aman diulang.
   Dijalankan : 2026-10-14 di DBSMLBARU - sukses.
   ===================================================================== */
USE DBSMLBARU;
GO
-- vwDetailUbahKemasan : isi DBSMLNEW + kolom HargaIn, HPP2
ALTER View [dbo].[vwDetailUbahKemasan]
as
Select A.Nobukti,A.tanggal,A.note,A.ISCetak,b.kodebrg,C.namaBrg,
       b.Kodegdg,b.Qntdb,B.QntCr,b.Satuan,b.nosat,B.isi,b.HPP Harga, b.HargaIn biaya,b.urut,d.nama NamaGDG, 
       (b.qntdb-b.qntcr)*b.HPP as Total,   (b.qntdb)*b.HPP HrgAdi, (b.qntcr)*b.hpp HrgADO,b.Hpp,
       A.NOURUT,ISNULL(b.nobeli,'') NObeli,b.Urutbeli,
       B.HargaIn, b.HPP2  -- [newSML] kolom lama DBSMLBARU dipertahankan
From dbubahKemasan A 
     left outer join dbUbahKemasanDet B on b.nobukti=a.nobukti 
     left outer join dbBarang C on c.kodebrg=b.kodebrg 
     left outer join dbGudang D on d.kodegdg=b.kodegdg
GO
-- vwMasterKoreksi : isi DBSMLNEW + kolom NOTE
ALTER View [dbo].[vwMasterKoreksi]
as
Select a.nobukti+' Tanggal : '+convert(varchar(10),a.tanggal,105) + '   Gudang : '+a.KodeGdg as GroupNobukti, 
       A.Nobukti,a.Tanggal,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                       Case when A.IsOtorisasi3=1 then 1 else 0 end+
                       Case when A.IsOtorisasi4=1 then 1 else 0 end+
                       Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                  else 1
             end As Bit) NeedOtorisasi,A.NOURUT, 
    A.NoPerintahOP,c.NAMAHDGRP,d.NamaSubGrp,e.NAMAMERK,A.KodeGdg,F.NAMA NamaGdg,a.NOTE /* [newSML] kolom lama DBSMLBARU dipertahankan */
From dbKoreksi A 
Left Outer Join DBPerintahOP B on A.NoPerintahOP=B.NoBukti
left outer join DBHDGROUP c on c.KODEHDGRP=B.KodeHdGrp
left outer join DBSubGroup d on d.KodeSubGrp=B.KodeSubGrp and b.KodeHdGrp=D.KodeHdGrp
left outer join DBMERK e on e.KODEMERK=B.KodeMerk
Left Outer Join DBGUDANG F on A.KodeGdg=F.KODEGDG
GO
-- SP_UBAHKEMASAN : isi DBSMLNEW
ALTER Procedure [dbo].[SP_UBAHKEMASAN]
@Choice Varchar(1),
@Nobukti Varchar(20),
@Tanggal Datetime,
@Note ntext,
@urut int,
@Kodebrg varchar(25),
@Kodegdg varchar(15),
@Satuan varchar(5),
@Nosat tinyint,
@Isi Numeric(18,2),
@QntDB numeric(18,2),
@QntCR numeric(18,2),
@Harga numeric(18,2),
@NoUrut varchar(7),
@FlagTipe int,
@Biaya numeric(18,2)=0,
@hpp Numeric(18,2)=0,
@NObeli Varchar(30)='',
@UrutBeli INt=0


As
Begin tran

select @Isi= case when @Nosat=1 then ISI1 when @Nosat=2 then ISI2 when @Nosat=3 then ISI3 end
from DBBARANG where KODEBRG=@Kodebrg


if @choice='I'
begin
  select @urut=isnull(max(urut),0)+1 from DBUBAHKEMASANdet Where NoBukti=@NoBukti
  If @urut is null Set @urut=1
  if not exists(select * from DBUBAHKEMASAN Where NoBukti=@NoBukti) 
  begin
    insert into DBUBAHKEMASAN (NOBUKTI,Nourut,TANGGAL,NOTE, FlagTipe)
    values (@NOBUKTI,@Nourut, @TANGGAL,@NOTE, @FlagTipe)
   			
  end
  insert into DBUBAHKEMASANDET (NOBUKTI,URUT,KODEBRG,Kodegdg,SATUAN,NOSAT,ISI,QNTDB,QNTCR,HARGA,HargaIn,HPP,NObeli,UrutBeli)
  values(@NOBUKTI,@URUT,@KODEBRG,@Kodegdg,@SATUAN,@NOSAT,@ISI,@QNTDB,@QNTCR,@hpp,@Biaya,@hpp,@NObeli,@UrutBeli)
  --values('SJY/KMBJ/0413/0001',1,'AE2.B008','G001','ML',1,1,0,100,10)
end
if @choice='U'
begin
  update DBUBAHKEMASANDET set Kodebrg=@KODEBRG,Kodegdg=@Kodegdg,Satuan=@SATUAN,Nosat=@NOSAT,
  Isi=  @ISI
  ,QntDb=@QNTDB,QntCR=@QNTCR,Harga=@hpp,
								HargaIn=@Biaya,HPP=@hpp,NObeli=@NObeli,UrutBeli=@UrutBeli
  where nobukti=@nobukti and urut=@urut
end
if @choice='D'
begin
  delete DBUBAHKEMASANDET where nobukti=@nobukti and  urut=@urut 
  if not exists( select nobukti from DBUBAHKEMASANDET where nobukti=@nobukti)
  begin
    delete DBUBAHKEMASAN where nobukti=@nobukti
  end
end
--if @@error<>0  goto jikasalah
Commit tran
Return
JikaSalah: Rollback tran
           Return
GO
-- sp_CetakSerahSample : isi DBSMLNEW
ALTER Procedure [dbo].[sp_CetakSerahSample]
--declare 
@NoBukti varchar(30)
AS
--select 	@NoBukti='SJE/RJR/0814/0001'

select A.NOBUKTI, A.NOURUT, A.TANGGAL, Isnull(F.NAMACUSTSUPP,A.NOTE) Keterangan,
        B.URUT, B.KODEBRG, C.NamaBrg, '' Jns_Kertas, '' Ukr_Kertas,
        CASE WHEN B.NOSAT=1 THEN QNT WHEN B.NOSAT=2 THEN QNT2 WHEN B.NOSAT=3 THEN QNT2 END QNT, B.QNT2, B.SAT_1, B.SAT_2, B.NoSat, B.ISI, 
        B.gdgAsal, D.NAMA+' ('+B.gdgAsal+')' NamaGgdAsal, 0.00 GSM,
        B.gdgTujuan, E.NAMA+' ('+B.gdgTujuan+')' NamaGgdTujuan,
         A.KODECUSTSUPP,F.NamaCustSupp,a.KODESLS,G.Nama NAMASLS ,B.pbonus,H.refPR,i.jmlrecord,
         CASE WHEN B.NOSAT=1 THEN SAT1 WHEN B.NOSAT=2 THEN SAT2 WHEN B.NOSAT=3 THEN B.SAT_2 END SATUAN,c.Mlokasi
From DBserahSAMPLE A
Left Outer Join DBserahSAMPLEdet B on B.NoBukti=A.NoBukti
Left Outer Join dbBarang C On C.KodeBrg=B.KodeBrg
left Outer join DBGUDANG D on D.KODEGDG=B.GdgAsal
left Outer join DBGUDANG E on E.KODEGDG=B.GdgTujuan
Left Outer join DbCustSupp F on F.KOdeCustSupp=A.KODECUSTSUPP
LEFT OUTER JOIN dbKaryawan G ON A.KODESLS=G.KeyNIK
Left Outer Join DBPRSAMPLE H on b.NOPRSAMPLE=h.NOBUKTI
left outer join (select COUNT(*) jmlrecord from DBSERAHSAMPLEDET where NOBUKTI=@NoBukti) i on 1=1
where a.NoBukti=@NoBukti
order By B.Urut
GO
-- SP_InsertPRTRANSFER : isi DBSMLNEW
ALTER Procedure [dbo].[SP_InsertPRTRANSFER] 
--declare 
@Nobukti varchar(25),
@Nourut varchar(10),
@Nob varchar(25),
@urut integer,
@IdUser varchar(20),
@MaxOL integer,
@DBASAL varchar(20),
@DBTUJUAN varchar(20),
@tgl Datetime,
@qty numeric(18,2)


--select @Nobukti='SPL/TRF/00002/1015',@Nourut='00002'
--,@Nob='SPL/PRT/00002/1015',@IdUser='SA',@MaxOL=1,@DBASAL='DBSPL',@DBTUJUAN='DBSPLSPT'

as

DECLARE @KODEGDG VARCHAR(20)
SET @KODEGDG=(SELECT KODEGDG FROM DBGUDANG WHERE pSampit=1)

begin tran 

insert into DBTRANSFER (NoBukti,NoUrut,Tanggal,IDUser,MaxOL,NOTE)
	select @Nobukti,@Nourut,@tgl,@IdUser,@MaxOL,NOTE 
	from DBPRTRANSFER where NoBukti=@Nob
	if @@error <> 0 goto jikasalah
    
	insert into DBTRANSFERDET(NoBukti,Urut,NoPrTRANSFER,URUTPRTRANSFER,KodeBrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,GdgAsal,GdgTujuan) 
	--select @Nobukti,urut,NoBukti,Urut,KodeBrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,GDGASAL,GDGTUJUAN,QNT2
	select @Nobukti,urut,NoBukti,Urut,KodeBrg,@qty * ISI as qty, case when NOSAT = 1 then @qty/ISI else @qty end qty2,SAT_1,SAT_2,NOSAT,ISI,GDGASAL,GDGTUJUAN
	from DBPRTRANSFERDET where NoBukti=@Nob and URUT = @urut
	--order by Urut
	if @@error <> 0 goto jikasalah
/*
IF EXISTS (SELECT GDGASAL,GDGTUJUAN FROM DBPRTRANSFERDET WHERE NoBukti=@Nob
			AND (GDGASAL<>@KODEGDG AND GDGTUJUAN<>@KODEGDG)) 
BEGIN
	insert into DBTRANSFER (NoBukti,NoUrut,Tanggal,IDUser,MaxOL,NOTE)
	select @Nobukti,@Nourut,@tgl,@IdUser,@MaxOL,NOTE 
	from DBPRTRANSFER where NoBukti=@Nob
	if @@error <> 0 goto jikasalah
    
	insert into DBTRANSFERDET(NoBukti,Urut,NoPrTRANSFER,URUTPRTRANSFER,KodeBrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,GdgAsal,GdgTujuan,QtyMinta) 
	--select @Nobukti,urut,NoBukti,Urut,KodeBrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,GDGASAL,GDGTUJUAN,QNT2
	select @Nobukti,urut,NoBukti,Urut,KodeBrg,0,0,SAT_1,SAT_2,NOSAT,ISI,GDGASAL,GDGTUJUAN,QNT2
	from DBPRTRANSFERDET where NoBukti=@Nob 
	--order by Urut
	if @@error <> 0 goto jikasalah
END
ELSE if (@DBASAL = 'DBSPL' )and  (@DBTUJUAN = 'DBSPLSPT' )
begin
	insert into DBTRANSFER (NoBukti,NoUrut,Tanggal,IDUser,MaxOL,NOTE)
	select @Nobukti,@Nourut,@tgl,@IdUser,@MaxOL,NOTE 
	from DBSPLSPT..DBPRTRANSFER where NoBukti=@Nob
    if @@error <> 0 goto jikasalah
    
	insert into DBTRANSFERDET(NoBukti,Urut,NoPrTRANSFER,URUTPRTRANSFER,KodeBrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,GdgAsal,GdgTujuan,QtyMinta) 
	--select @Nobukti,urut,NoBukti,Urut,KodeBrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,GDGASAL,GDGTUJUAN,QNT2
	select @Nobukti,urut,NoBukti,Urut,KodeBrg,0,0,SAT_1,SAT_2,NOSAT,ISI,GDGASAL,GDGTUJUAN,QNT2
	from DBSPLSPT..DBPRTRANSFERDET where NoBukti=@Nob 
	--order by Urut
	if @@error <> 0 goto jikasalah
end

else if (@DBASAL = 'DBSPLSPT' )and  (@DBTUJUAN = 'DBSPL' )
begin
	insert into DBTRANSFER (NoBukti,NoUrut,Tanggal,IDUser,MaxOL,NOTE)
	select @Nobukti,@Nourut,@tgl,@IdUser,@MaxOL,NOTE 
	from DBSPL..DBPRTRANSFER where NoBukti=@Nob
    if @@error <> 0 goto jikasalah
    
	insert into DBTRANSFERDET(NoBukti,Urut,NoPrTRANSFER,URUTPRTRANSFER,KodeBrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,GdgAsal,GdgTujuan,QtyMinta) 
	--select @Nobukti,urut,NoBukti,Urut,KodeBrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,GDGASAL,GDGTUJUAN,QNT2
	select @Nobukti,urut,NoBukti,Urut,KodeBrg,0,0,SAT_1,SAT_2,NOSAT,ISI,GDGASAL,GDGTUJUAN,QNT2
	from DBSPL..DBPRTRANSFERDET where NoBukti=@Nob 
	--order by Urut
	if @@error <> 0 goto jikasalah
	
end
*/
commit tran
return
jikasalah:
	rollback tran
	return
GO
-- refresh view turunan: VwReportUbahKemasanBahan = SELECT * FROM vwDetailUbahKemasan
EXEC sp_refreshview 'dbo.VwReportUbahKemasanBahan';
GO
