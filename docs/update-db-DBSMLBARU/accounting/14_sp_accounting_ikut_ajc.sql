/* =====================================================================
   14 - SP accounting disamakan dengan DBSMLNEW (newProgramAJC)
   Keputusan user (2026-10-14): SP accounting ikut AJC, asal tidak
   memengaruhi purchasing & marketing.
   Isi:
   1. sp_TransaksiKasBankDPH  -> isi DBSMLNEW. Satu-satunya beda: Kas/Bank
      MASUK (BKM/BBM) dengan pelunasan piutang (Plok=1) sekarang ikut
      mencatat pelunasan ke DBHUTPIUT dari DBTempHUTPIUT, sama seperti
      Kas/Bank keluar. Dulu piutangnya tidak berkurang.
      Hanya dipanggil menu Kas, Bank, Giro Diterima, Giro Dibuka.
   2. CetakTTDPH -> isi DBSMLNEW. Versi lama membaca dbTempTTInvc (tabel
      Tanda Terima Invoice marketing) padahal menu Cetak Pengajuan DPH
      mengisi dbTempTTDPH; versi baru juga mengembalikan KL, DIBAYAR, valas.
      Hanya dipanggil Cetak Pengajuan DPH.
   3. SP_IsiNobukti -> isi DBSMLBARU APA ADANYA + satu cabang baru
      @inisial='TDP' (Penerimaan DPP) dari DBSMLNEW. Dulu Penerimaan DPP
      meminta nomor TDP tapi cabangnya tidak ada, jadi nomor urut tidak
      bertambah. Cabang lain (TTI, PO, SO, dst. yang dipakai purchasing &
      marketing) tidak diubah sama sekali.
   SENGAJA TIDAK DISALIN dari DBSMLNEW (lihat CATATAN-UPDATE-DB-ACCOUNTING.md):
      TRI_DEL_DBTRANSAKSI, vwHutPiut, sp_CariHutangJT, SP_ProsesKursBank.
   Sifat      : ALTER SP (bukan menambah). Disetujui user. Aman diulang.
   Dijalankan : 2026-10-14 di DBSMLBARU - sukses.
   ===================================================================== */
USE DBSMLBARU;
GO
-- 1. sp_TransaksiKasBankDPH = isi DBSMLNEW
ALTER PROCEDURE [dbo].[sp_TransaksiKasBankDPH]
--declare
@Choice varchar(1),
@Nobukti Varchar(30),
@NoUrut Varchar(5),
@Tanggal datetime,
@Note Varchar(500),
@Lampiran TinyInt,
@Devisi varchar(20),
@Perkiraan varchar(25),
@Lawan Varchar(25),
@Keterangan Varchar(8000),
@Keterangan2 Varchar(8000),
@Debet numeric(18,2),
@Kredit Numeric(18,2),
@Valas Varchar(15),
@Kurs Numeric(18,2),
@DebetRp numeric(18,2),
@KreditRp Numeric(18,2),
@TipeTrans varchar(3),
@TPHC Varchar(1),
@CustSuppP varchar(15),
@CustSuppL Varchar(15),
@Urut Int,
@NoAktivaP varchar(30),
@NoAktivaL Varchar(30),
@StatusAktivaP varchar(5),
@StatusAktivaL Varchar(5),
@NoBon varchar(20),
@Kodebag varchar(15),
@KodeP varchar(15),
@KodeL varchar(15),
@Statusgiro varchar(2),
@Simbol varchar(10),
@PerkiraanHd varchar(20),
@FlagSimbol Varchar(2)='',
@KodeCost varchar(30)='',
@KodeSubCost varchar(30)='',
@NoDPH varchar(25)='',
@UrutDph integer=0,
@DPPDPH VARCHAR(5)='',
@IdUser varchar(20)='',
@Tp varchar(2)='',
@ppklx varchar(20)='',
@nofaktur varchar(30)='',
@Plok bit=0,
@nobons varchar(25)='',
@Jmlrecord int,
@NoTitipan varchar(30)='',
@urutTitipan integer=0,
@ketdet varchar(200)='',
@pSKB Bit=0

AS
/*
select
@Choice ='I',
@Nobukti ='SPX/BKK/00003KK/0815',
@NoUrut='00003' ,
@Tanggal='08/11/2015' ,
@Note='' ,
@Lampiran =0,
@Devisi ='01',
@Perkiraan='21203',
@Lawan='111101' ,
@Keterangan='' ,
@Keterangan2='' ,
@Debet=13000 ,
@Kredit=0 ,
@Valas='IDR' ,
@Kurs=1 ,
@DebetRp=13000 ,
@KreditRp=0 ,
@TipeTrans='BKK' ,
@TPHC='C' ,
@CustSuppP='S_TUNAI' ,
@CustSuppL='',
@Urut=2 ,
@NoAktivaP='' ,
@NoAktivaL='',
@StatusAktivaP='HT-',
@StatusAktivaL='' ,
@NoBon='-' ,
@Kodebag='-' ,
@KodeP='HT' ,
@KodeL='',
@Statusgiro='',
@Simbol='KK' ,
@PerkiraanHd='111101',
@FlagSimbol='' ,
@KodeCost='' ,
@KodeSubCost='' ,
@NoDPH='' ,
@UrutDph=0 ,
@IdUser='SA',
@DPPDPH ='',
@TP='',
@ppklx='',
@nofaktur='',
@Plok=0,
@nobons varchar(25)=''
*/



Begin Tran
Declare @UrutKB Int, @urutbon int,@DPPUM NUMERIC(18,2)=0,@PPNUM NUMERIC(18,2)=0,@NOUM VARCHAR(30)='',@urutDEc Int


IF @Perkiraan='116100'
begin
SELECT top 1 @DPPUM=B.DPP,@PPNUM=B.PPN,@NOUM =A.NOFAKTUR
FROM DBDPHDET A
LEFT OUTER JOIN DBUMJUAL B ON A.NOFAKTUR=B.NOBUKTI and b.NoRetur=''
 WHERE A.NoBukti=@NoDPH AND A.Urut=@UrutDph
end 


IF @Lawan='116100'
begin

	Select @Urutdec=MAX(urut) from dbTransaksi where NoBukti=@Nobukti
SET @Urutdec= ISNULL(@Urutdec,0) + 1
SELECT top 1 @DPPUM=A.DPP,@PPNUM=A.PPN,@NOUM =A.NOBUKTI
FROM TEMPRUMJUAL A
 where  A.IDUSER=@IdUser AND A.NORETUR<>'' 
 and A.NOBUKTI= ( SELECT TOP 1 NOBUKTI FROM TEMPRUMJUAL WHERE NoTrans=@Nobukti and UrutTrans=@urutDEc AND IDUSER=@IdUser and NORETUR<>'' )
end 



if @choice='I'
begin
	Select @Urut=MAX(urut) from dbTransaksi where NoBukti=@Nobukti --and FlagSimbol<>'KB'
	Set @Urut=ISNULL(@urut,0)+1  
	---
	Select @UrutKB=MAX(urut) from dbTransaksi where NoBukti=@Nobukti and FlagSimbol=''--and FlagSimbol='KB'
	Set @UrutKB=ISNULL(@UrutKB,0)+1  

   	--if not exists(select * from dbtrans Where NoBukti=@NoBukti) 
   	 if @JmlRecord=0 
   	begin
     	  insert into dbTrans(NoBukti,NoUrut,tanggal,Note,Simbol, TipeTransHd, PerkiraanHd,IdUser,Tglinp)
     	  values (@NoBukti, @NoUrut, @tanggal,@Note,@Simbol,@TipeTrans,@PerkiraanHd,@IdUser,GETDATE())
     	  if @@Error<>0 Goto JikaSalah
   	end
 	insert into dbtransaksi (NoBukti, Tanggal, Devisi, Note, Lampiran, Perkiraan, Lawan, 
 	                         Keterangan, Keterangan2, DEBET, Kredit, Valas, Kurs, DebetRp, 
 	                         KreditRp, TipeTrans, TPHC, CustSuppP, CustSuppL, Urut, 
 	                         NoAktivaP, NoAktivaL, StatusAktivaP, StatusAktivaL, Nobon, 
 	                         KodeBag, KodeP, kodeL,StatusGiro,FlagSimbol,
 	                         KodeCost, KodeSubCost,NODPH,urutDPH,nobons,NOTITIPAN,URUTTITIPAN,ketdetail,pSKB )
 	values (@NoBukti, @Tanggal, @Devisi, @Note, @Lampiran, @Perkiraan, @Lawan, 
 	        @Keterangan, @Keterangan2, 
 	        CASE WHEN isnull(@NOUM,'') ='' THEN @Debet ELSE  case when @Lawan<>'116100' then @DPPUM else /*100.00/111.00 */ @DPPUM end END, 
 	        @Kredit, @Valas, @Kurs, 
 	        CASE WHEN isnull(@NOUM,'') ='' THEN @DebetRp ELSE case when @Lawan<>'116100' then @DPPUM * @Kurs else /*100.00/111.00 */ @DPPUM * @Kurs end END, 
 	        @KreditRp, @TipeTrans, @TPHC, @CustSuppP, @CustSuppL,@Urut --case when @FlagSimbol='KB' Then @UrutKB else @Urut end 
 	        , @NoAktivaP, 
 	        @NoAktivaL, @StatusAktivaP, @StatusAktivaL, @Nobon, 
 	        @KodeBag, @KodeP, @kodeL,@Statusgiro,@FlagSimbol,
 	        @KodeCost, @KodeSubCost,@NoDPH,@UrutDph,@nobons,@NoTitipan,@urutTitipan,@ketdet,@pSKB)
 	if @@Error<>0 Goto JikaSalah


IF @Perkiraan='116100' AND @PPNUM<>0
	BEGIN
	insert into dbtransaksi (NoBukti, Tanggal, Devisi, Note, Lampiran, Perkiraan, Lawan, 
 	                         Keterangan, Keterangan2, Debet, Kredit, Valas, Kurs, DebetRp, 
 	                         KreditRp, TipeTrans, TPHC, CustSuppP, CustSuppL, Urut, 
 	                         NoAktivaP, NoAktivaL, StatusAktivaP, StatusAktivaL, Nobon, 
 	                         KodeBag, KodeP, kodeL,StatusGiro,FlagSimbol,
 	                         KodeCost, KodeSubCost,NODPH,urutDPH,nobons,NOTITIPAN,URUTTITIPAN,ketdetail,pSKB )
 	values (@NoBukti, @Tanggal, @Devisi, @Note, @Lampiran, '118500', @Lawan, 
 	      --  @Keterangan, 
 	        (select b.NAMACUSTSUPP +'('+ a.KODECUSTSUPP +') No UM : '+A.NOFAKTUR
 	        from DBDPHDET A
 	        left outer join DBCUSTSUPP b on a.KODECUSTSUPP=b.KODECUSTSUPP
 	        
 	        where NoBukti=@NoDPH and Urut=@UrutDph),
 	        @Keterangan2, @PPNUM, @Kredit, @Valas, @Kurs, @PPNUM, 
 	        @KreditRp, @TipeTrans, @TPHC, @CustSuppP, @CustSuppL,@Urut+1 --case when @FlagSimbol='KB' Then @UrutKB else @Urut end 
 	        , @NoAktivaP, 
 	        @NoAktivaL, @StatusAktivaP, @StatusAktivaL, @Nobon, 
 	        @KodeBag, @KodeP, @kodeL,@Statusgiro,@FlagSimbol,
 	        @KodeCost, @KodeSubCost,@NoDPH,@UrutDph,@nobons,@NoTitipan,@urutTitipan,@ketdet,@pSKB)
 	if @@Error<>0 Goto JikaSalah

	
	END




---DPH ========================================
 	IF @TipeTrans='BKK' OR @TipeTrans='BBK'
	BEGIN  
	--Dibayar
	if @Tp='DB'
	begin
		insert into DBHUTPIUT (NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
 		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
 		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NOINVOICE, KodeVls_, Kurs_,FlagSimbol,
 		KBLB, NoDPH, UrutDPH) 

		select b.NOFAKTUR, '' NoRetur, 'L' TipeTrans, b.KODECUSTSUPP, @Nobukti NoBukti, @Urut NoMsk, b.Urut, 
		@Tanggal Tanggal, @Tanggal JatuhTempo, b.DIBAYAR*@Kurs Debet, 0 Kredit, @Valas, @Kurs,
		case when @Valas='IDR' then 0 else b.DIBAYAR end DebetD, 0 KreditD, a.Penagih KodeSales,
		'HT' Tipe, @Perkiraan Perkiraan, @Keterangan Catatan, 'LNS' NoInvoice, @Valas KodeVls_, @Kurs Kurs_, '' FlagSimbol,
		'' KBLB, a.NoBukti NoDPH, b.Urut UrutDPH 
		from DBDPH a
		left Outer join DBDPHDET b on A.NoBukti=B.NoBukti
		where a.NoBukti=@NoDPH and B.KODECUSTSUPP=@CustSuppp AND b.DIBAYAR<>0 and B.NOFAKTUR=@nofaktur
		if @@Error<>0 Goto JikaSalah
	End
	--kurang bayar
	if @Tp='KB'
	begin
		insert into DBHUTPIUT (NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
 		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NOINVOICE, KodeVls_, Kurs_,FlagSimbol,
 		KBLB, NoDPH, UrutDPH)

		select C.NOFAKTUR, '' NoRetur, 'L' TipeTrans, b.KODECUSTSUPP, @Nobukti NoBukti, @Urut NoMsk, b.Urut Urut, 
		@Tanggal Tanggal, @Tanggal JatuhTempo, SUM(C.NilaiK*@Kurs) Debet, 0 Kredit, @Valas, @Kurs,
		SUM(case when @Valas='IDR' then 0 else C.NilaiK end) DebetD, 0 KreditD, a.Penagih KodeSales,
		'HT' Tipe,@Perkiraan Perkiraan, @Keterangan Catatan, 'LNS' NoInvoice, @Valas KodeVls_, @Kurs Kurs_, '' FlagSimbol,
		'KB' KBLB, a.NoBukti NoDPH, b.Urut UrutDPH
		from DBDPH a
		Left Outer Join DBDPHDET b on A.NoBukti=B.NoBukti
		Left Outer join dbKurangBayar C on A.NoBukti=C.NoBukti and B.NOFAKTUR=C.nofaktur
		where a.NoBukti=@NoDPH and C.KODECUSTSUPP=@CustSuppp  
		and C.NilaiK>0 and c.Perkiraan=@ppklx AND c.NoFaktur=@nofaktur 
		GROUP BY C.NOFAKTUR, b.KODECUSTSUPP, b.Urut,a.Penagih,C.Perkiraan, a.NoBukti 
		if @@Error<>0 Goto JikaSalah
	End
	if @TP='' and @Plok=1
	begin
		insert into DBHUTPIUT (NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
 		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
 		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NOINVOICE, KodeVls_, Kurs_,FlagSimbol)
		select NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NoInvoice, Valas_, Kurs_,FlagSimbol
		from DBTempHUTPIUT
		where NoBukti=@Nobukti and NoMsk=@Urut and StatusUID in ('I','U')
		and FlagSimbol=@FlagSimbol
		if @@Error<>0 Goto JikaSalah
	End
	
    END
--DPP===========================    
    IF @TipeTrans='BKM' OR @TipeTrans='BBM'
    BEGIN
    -- select 1 
 
    insert into DBHUTPIUT (NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
 		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
 		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NOINVOICE, KodeVls_, Kurs_,FlagSimbol,
 		KBLB, NoDPH, UrutDPH) 

    select b.NOFAKTUR, '' NoRetur, 'L' TipeTrans, b.KODECUSTSUPP, @Nobukti NoBukti, @Urut NoMsk, b.Urut, 
	@Tanggal Tanggal, @Tanggal JatuhTempo,0 Debet, b.DIBAYAR*@Kurs Kredit, @Valas, @Kurs,
	0 DebetD,case when @Valas='IDR' then 0 else b.DIBAYAR end KreditD, a.Penagih KodeSales,
	'PT' Tipe, @Lawan Perkiraan, @Keterangan Catatan, 'LNS' NoInvoice, @Valas KodeVls_, @Kurs Kurs_, '' FlagSimbol,
	'' KBLB, a.NoBukti NoDPH, b.Urut UrutDPH 
	from DBDPH a
	left Outer join DBDPHDET b on A.NoBukti=B.NoBukti
	where a.NoBukti=@NoDPH and b.KODECUSTSUPP=@CustSuppL
	
	if @@Error<>0 Goto JikaSalah

	insert into DBHUTPIUT (NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
 		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
 		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NOINVOICE, KodeVls_, Kurs_,FlagSimbol,
 		KBLB, NoDPH, UrutDPH)

	
	select b.NOFAKTUR, '' NoRetur, 'L' TipeTrans, b.KODECUSTSUPP, @Nobukti NoBukti, @Urut+1 NoMsk, b.Urut Urut, 
	@Tanggal Tanggal, @Tanggal JatuhTempo,0 Debet, b.KL*@Kurs Kredit, @Valas, @Kurs,
	0 DebetD,case when @Valas='IDR' then 0 else b.KL end KreditD, a.Penagih KodeSales,
	'PT' Tipe, @Lawan Perkiraan, @Keterangan Catatan, 'LNS' NoInvoice, @Valas KodeVls_, @Kurs Kurs_, '' FlagSimbol,
	'KB' KBLB, a.NoBukti NoDPH, b.Urut UrutDPH  
	from DBDPH a
	Left Outer Join DBDPHDET b on A.NoBukti=B.NoBukti
	where a.NoBukti=@NoDPH and b.KODECUSTSUPP=@CustSuppL
	and b.KL>0
     
	if @@Error<>0 Goto JikaSalah
    END
    if @TP='' and @Plok=1
	begin
		insert into DBHUTPIUT (NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
 		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
 		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NOINVOICE, KodeVls_, Kurs_,FlagSimbol)
		select NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NoInvoice, Valas_, Kurs_,FlagSimbol
		from DBTempHUTPIUT
		where NoBukti=@Nobukti and NoMsk=@Urut and StatusUID in ('I','U')
		and FlagSimbol=@FlagSimbol
		if @@Error<>0 Goto JikaSalah
	End
-- BON   
      select @urutbon =isnull(max(urut),0)+1 from DBBON Where NoBukti=@nobons 
     insert into DBBON(Devisi,NoBukti,Tanggal,Penerima,Keterangan,Debet,Kredit
			,Perkiraan,TglInput,UserID,Urut,BuktiKas,UrutKas,KodeVls,Kurs,KreditD,DebetD)
      select distinct  Devisi,NoBukti,@Tanggal,Penerima,Keterangan,0 ,@Debet
			,Perkiraan,GETDATE(),@IdUser userid,@urutbon   urut,@Nobukti BuktiKas,@Urut urutkas,KodeVls,Kurs,DebetD,KreditD 
			from DBBON
	  Where NoBukti=@nobons	and Debet>0	 and Perkiraan=@PerkiraanHd     
	  
--Retur UANG MUKA BELI 

   IF @KodeL='UHT' AND @Lawan=(SELECT Perkiraan FROM DBPOSTHUTPIUT WHERE Kode='UHT' AND Perkiraan=@Lawan )
   BEGIN
	INSERT INTO DBUMJUAL (NOBUKTI,NOURUT,NORETUR,TANGGAL,NOSO,PERSEN,VALAS,KURS,DPP,PPN,SUBTOTAL,MAXOL,FLAGTIPE,pBeli,Notrans,UrutTrans)
	select A.NOBUKTI,b.NOURUT,A.NORETUR,A.TANGGAL,A.NOSO,B.PERSEN,A.VALAS,A.KURS,-1*A.DPP,-1*A.PPN,-1*A.SUBTOTAL,B.MAXOL,B.FLAGTIPE,B.pBeli
	,@Nobukti,@urut
	from TEMPRUMJUAL A
	LEFT OUTER JOIN DBUMJUAL B ON A.NOBUKTI=B.NOBUKTI
	WHERE A.NORETUR<>'' 
	AND A.IDUSER=@IdUser 
	and A.NoTrans=@Nobukti 
	and A.UrutTrans=@urut
	group by A.NOBUKTI,b.NOURUT,A.NORETUR,A.TANGGAL,A.NOSO,B.PERSEN,A.VALAS,
	A.KURS,A.DPP,A.PPN,A.SUBTOTAL,B.MAXOL,B.FLAGTIPE,B.pBeli,A.IDUSER,A.NoTrans,A.UrutTrans
	--delete tempRUMJual where IDUSER=@IdUser
	
	
   END
   
   --RETUR UANG MUKA DARI MENU BARU
    IF @KodeL='HT' AND (@TipeTrans='BKM' OR @TipeTrans='BBM') AND @Lawan='116100'
   BEGIN
   
		IF @Lawan='116100' AND @PPNUM<>0
		BEGIN
		insert into dbtransaksi (NoBukti, Tanggal, Devisi, Note, Lampiran, Perkiraan, Lawan, 
 	                         Keterangan, Keterangan2, Debet, Kredit, Valas, Kurs, DebetRp, 
 	                         KreditRp, TipeTrans, TPHC, CustSuppP, CustSuppL, Urut, 
 	                         NoAktivaP, NoAktivaL, StatusAktivaP, StatusAktivaL, Nobon, 
 	                         KodeBag, KodeP, kodeL,StatusGiro,FlagSimbol,
 	                         KodeCost, KodeSubCost,NODPH,urutDPH,nobons,NOTITIPAN,URUTTITIPAN,ketdetail,pSKB )
 		values (@NoBukti, @Tanggal, @Devisi, @Note, @Lampiran,  @Perkiraan, '118500',
 	      --  @Keterangan, 
 	        (select top 1  b.NAMACUSTSUPP +'('+ a.KODESUPP +') Retur dr UM : '+A.NOBUKTI
 	        from TEMPRUMJUAL A
 	        left outer join DBCUSTSUPP b on a.KODESUPP=b.KODECUSTSUPP 	        
 	        where A.NORETUR<>'' AND A.IDUSER=@IdUser and A.NoTrans=@Nobukti and A.UrutTrans=@urut),
	
 	        @Keterangan2, @PPNUM, @Kredit, @Valas, @Kurs, @PPNUM, 
 	        @KreditRp, @TipeTrans, @TPHC, @CustSuppP, @CustSuppL,@Urut+1 --case when @FlagSimbol='KB' Then @UrutKB else @Urut end 
 	        , @NoAktivaP, 
 	        @NoAktivaL, @StatusAktivaP, @StatusAktivaL, @Nobon, 
 	        @KodeBag, @KodeP, @kodeL,@Statusgiro,@FlagSimbol,
 	        @KodeCost, @KodeSubCost,@NoDPH,@UrutDph,@nobons,@NoTitipan,@urutTitipan,@ketdet,@pSKB)
 		if @@Error<>0 Goto JikaSalah	
		END
   
   
           
	INSERT INTO DBUMJUAL (NOBUKTI,NOURUT,NORETUR,TANGGAL,NOSO,PERSEN,VALAS,KURS,DPP,PPN,SUBTOTAL,MAXOL,FLAGTIPE,pBeli,Notrans,UrutTrans)
	select A.NOBUKTI,b.NOURUT,A.NORETUR,A.TANGGAL,A.NOSO,B.PERSEN,A.VALAS,A.KURS,-1*A.DPP,-1*A.PPN,-1*A.SUBTOTAL,B.MAXOL,B.FLAGTIPE,B.pBeli
	,@Nobukti,@urut
	from TEMPRUMJUAL A
	LEFT OUTER JOIN DBUMJUAL B ON A.NOBUKTI=B.NOBUKTI
	WHERE A.NORETUR<>'' 
	AND A.IDUSER=@IdUser 
	and A.NoTrans=@Nobukti 
	and A.UrutTrans=@urut
	group by A.NOBUKTI,b.NOURUT,A.NORETUR,A.TANGGAL,A.NOSO,B.PERSEN,A.VALAS,
	A.KURS,A.DPP,A.PPN,A.SUBTOTAL,B.MAXOL,B.FLAGTIPE,B.pBeli,A.IDUSER,A.NoTrans,A.UrutTrans
	--delete tempRUMJual where IDUSER=@IdUser
	
	
	
	
	
   END
   
   
   
end
if @choice='U'
begin
 	Update dbtransaksi set Tanggal=@Tanggal, Devisi=@Devisi, Note=@Note, 
 	                       Lampiran=@Lampiran, Perkiraan=@Perkiraan, Lawan=@Lawan, 
 	                       Keterangan=@Keterangan, Keterangan2=@Keterangan2, 
 	                       Debet=@Debet, Kredit=@Kredit, Valas=@Valas, 
 	                       Kurs=@Kurs, DebetRp=@DebetRp, KreditRp=@KreditRp, 
 	                       TipeTrans=@TipeTrans, TPHC=@TPHC, CustSuppP=@CustSuppP, 
 	                       CustSuppL=@CustSuppL, Urut=@Urut, NoAktivaP=@NoAktivaP, 
 	                       NoAktivaL=@NoAktivaL, StatusAktivaP=@StatusAktivaP, 
 	                       StatusAktivaL=@StatusAktivaL, Nobon=@Nobon, Kodebag=@KodeBag,
 	                       KodeP=@KodeP, KodeL=@KodeL, StatusGiro=@Statusgiro,FlagSimbol=@FlagSimbol,
 	                       KodeCost=@KodeCost, KodeSubCost=@KodeSubCost ,nobons =@nobons ,
 	                       NOTITIPAN=@NoTitipan,URUTTITIPAN=@urutTitipan,ketdetail=@ketdet,pskb=@pSKB
 	where nobukti=@nobukti  and urut=case when @FlagSimbol='KB' Then @UrutKB else @urut end
 	and FlagSimbol=@FlagSimbol
 	if @@Error<>0 Goto JikaSalah
 	
 	Update dbtransaksi set Keterangan=@Keterangan 
 	where nobukti=@nobukti  and urut=case when @FlagSimbol='KB' Then @UrutKB else @urut end
 	and @FlagSimbol=''
 	if @@Error<>0 Goto JikaSalah
 	
 	delete DBHUTPIUT
 	from DBHUTPIUT A
 	left outer join DBTempHUTPIUT B on B.NoFaktur=A.NoFaktur and B.KodeCustSupp=A.KodeCustSupp and B.Perkiraan=A.Perkiraan
 		and B.NoBukti=A.NoBukti and B.NoMsk=A.NoMsk and B.Urut=A.Urut
 	where	B.NoBukti=@Nobukti and B.NoMsk=@Urut and B.StatusUID in ('D','U')
 	and B.NoBukti is not null and B.FlagSimbol=@FlagSimbol
 	if @@Error<>0 Goto JikaSalah
 	
 	insert into DBHUTPIUT (NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
 		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
 		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NOINVOICE, KodeVls_, Kurs_, FlagSimbol)
	select NoFaktur, NoRetur, TipeTrans, KodeCustSupp, 
		NoBukti, NoMsk, Urut, Tanggal, JatuhTempo, Debet, Kredit, Valas, Kurs, 
		DebetD, KreditD, KodeSales, Tipe, Perkiraan, Catatan, NoInvoice, Valas_, Kurs_, FlagSimbol
	from DBTempHUTPIUT
	where NoBukti=@Nobukti and NoMsk=@Urut and StatusUID in ('I','U')
	and isnull(FlagSimbol,'')=@FlagSimbol
	if @@Error<>0 Goto JikaSalah
    update DBBON set kredit=@Debet,Tanggal=@Tanggal where NoBukti=@nobons and BuktiKas=@Nobukti and UrutKas=@Urut	
    
    IF @KodeL='UHT' AND @Lawan=(SELECT Perkiraan FROM DBPOSTHUTPIUT WHERE Kode='UHT' AND Perkiraan=@Lawan )
   BEGIN
    delete DBUMJUAL where notrans=@Nobukti and uruttrans=@urut
	INSERT INTO DBUMJUAL (NOBUKTI,NOURUT,NORETUR,TANGGAL,NOSO,PERSEN,VALAS,KURS,DPP,PPN,SUBTOTAL,MAXOL,FLAGTIPE,pBeli,Notrans,UrutTrans)
	select A.NOBUKTI,b.NOURUT,A.NORETUR,A.TANGGAL,A.NOSO,B.PERSEN,A.VALAS,A.KURS,-1*A.DPP,-1*A.PPN,-1*A.SUBTOTAL,B.MAXOL,B.FLAGTIPE,B.pBeli,
	@Nobukti,@urut
	from TEMPRUMJUAL A
	LEFT OUTER JOIN DBUMJUAL B ON A.NOBUKTI=B.NOBUKTI
	WHERE A.NORETUR<>'' AND A.IDUSER=@IdUser 
	and A.NoTrans=@Nobukti --and B.UrutTrans=@urut
	--delete tempRUMJual where IDUSER=@IdUser
   END
    
    
end
if @choice='D'
begin
 	 	delete DBBON where NoBukti=@nobons and BuktiKas=@Nobukti 
 	delete dbtransaksi where nobukti=@nobukti and urut=@urut /*and (FlagSimbol=@FlagSimbol or @FlagSimbol='')*/
 	if @@Error<>0 Goto JikaSalah

 	 	--and UrutKas=@Urut
 	delete DBHUTPIUT where NoBukti=@Nobukti and NoMsk=@Urut /*and (FlagSimbol=@FlagSimbol or @FlagSimbol='')*/
 	if @@Error<>0 Goto JikaSalah
 	if not exists( select nobukti from dbtransaksi where nobukti=@nobukti)
 	begin
  		delete dbTrans where nobukti=@nobukti
  		if @@Error<>0 Goto JikaSalah 
 	end
    
    delete DBUMJUAL where notrans=@Nobukti and uruttrans=@urut
end
/*
if not exists(select 'TRUE'  from DBBON where NoBukti=@nobons and @Choice ='I'
group by NoBukti having SUM(Debet )<=sum(Kredit)  ) 
    begin
      select @urutbon =isnull(max(urut),0)+1 from DBBON Where NoBukti=@nobons 
     insert into DBBON(Devisi,NoBukti,Tanggal,Penerima,Keterangan,Debet,Kredit
			,Perkiraan,TglInput,UserID,Urut,BuktiKas,UrutKas,KodeVls,Kurs,KreditD,DebetD)
      select distinct  Devisi,NoBukti,Tanggal,Penerima,Keterangan,0 ,@Debet
			,Perkiraan,GETDATE(),@IdUser userid,@urutbon   urut,@Nobukti BuktiKas,@Urut urutkas,KodeVls,Kurs,DebetD,KreditD from DBBON
	  Where NoBukti=@nobons	 
	  
	  /*	if @choice ='U' 
	  	begin
	  	  update DBBON set kredit=@kredit where NoBukti=@nobons and BuktiKas=@Nobukti and UrutKas=@Urut
	  	end */
	  	
	  	
    End */


Commit Tran
Return
JikaSalah: RollBack Tran
           Return
GO
-- 2. CetakTTDPH = isi DBSMLNEW
ALTER PROCEDURE [dbo].[CetakTTDPH]
--declare 
--@Urut integer
@Nobukti varchar(30)
as
--select @nobukti='SJY/INVC/0713/00005'
Select A.NoBukti, A.Tanggal, b.KODECUSTSUPP,
       Case when C.USAHA<>'' then C.USAHA+'. ' else '' end+C.NAMACUSTSUPP NamaCustSupp,
       SUM(b.DIBAYAR) NILAINOTA,c.bank,c.NoAcc,C.ATN,m1.KL,m1.DIBAYAR , a.valas
   
from DBDPH A
     left outer join DBDPHDET b on b.NoBukti=A.NoBukti     
     left outer join DBCUSTSUPP c on c.KODECUSTSUPP=b.KODECUSTSUPP
     LEFT Outer join (select Nobukti,Sum(DIBAYAR) DIBAYAR,SUM(KL) KL from DBDPHDET 
						where NoBukti in(Select NoInvoice from dbTempTTDPH where Pilih=1) group by NoBukti) m1 on a.NoBukti = m1.NoBukti
     where A.NoBukti in(Select NoInvoice from dbTempTTdph where Pilih=1)
Group by A.NoBukti, A.Tanggal, B.KodeCustSupp,a.NilaiNota,
         c.USAHA,c.NAMACUSTSUPP,c.bank,c.NoAcc,c.ATN ,m1.KL,m1.KL,m1.DIBAYAR , a.valas
GO
-- 3. SP_IsiNobukti = isi DBSMLBARU + cabang TDP
ALTER PROCEDURE [dbo].[SP_IsiNobukti]
--declare
@inisial varchar(5),
@bulan integer,
@tahun integer,
@Iduser varchar(20),

@NoPRKMBJ varchar(30)=''

AS
--select @inisial='TTI',@bulan=7 ,@tahun=2026,@Iduser='sa'

declare
@urut integer,
@inperusahaan varchar(5),
@pemisah varchar(1),
@periodBLN Varchar(2),
@periodThn Varchar(4),
@Nourut varchar(6),
@PPN Bit=0
--select TRS from DBNOMOR

set @PPN=1

if (@inisial='INVCN') or (@inisial='SON') or (@inisial='SJN') or (@inisial='PON') or (@inisial='INVJN') or (@inisial='RSPBN')
or (@inisial='IVRJN') or (@inisial='SPRN') or (@inisial='PRJN') or (@inisial='KNN') or (@inisial='BGCN') or (@inisial='BGTN') or (@inisial='BCGN')  or (@inisial='BBGN') 
begin
set @PPN=0
End

select @inperusahaan= case when @ppn=1 then ALIAS else ALIAS end,
@pemisah=case when  PEMISAH=0 then ':'
when PEMISAH=1 then '-' when PEMISAH=2 then '/' End from DBNOMOR

set @periodBLN=case when @bulan<10 then '0' + CAST(@bulan AS varchar(3))
					else CAST(@bulan as varchar(2) ) End
set @periodThn=CAST(@tahun as varchar(4))					

--================================KODE NOBUKTI====================================================


-----


if @inisial='TTI' 
begin
   select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBNOMORTT A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End

if @inisial='PNW' 
begin
    select @urut=(
    select top 1  cast(LEFT(NOBUKTI,4) as int) + 1
    from dbpenawaranso A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by  cast(LEFT(NOBUKTI,4) as int) desc)
End

if @inisial='TTD' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBNOMORDPH A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End


if @inisial='PCK' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPACKING A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End


if @inisial='RSS' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBRSERAHSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End

if @inisial='TRT' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBTRANSFER A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun AND ISNULL(pTERIMA,0) = 1 and NOBUKTI Not like '%FLK%'
	order by CAST(NOURUT as int) desc)
End	


if @inisial='PRU' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dboutbrg A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun  and NOBUKTI like '%PRU%'
    order by CAST(NOURUT as int) desc)
End
	
/*

if @inisial='LPB' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBBELI A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
	order by CAST(NOURUT as int) desc)
End	
*/	

/*
if @inisial='SPR' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbSPBRJual A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End
*/

/*
if @inisial='RSPB' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBRSPB A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End
*/	
if @inisial='TRF' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBTRANSFER A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pTERIMA,0)=0 and NOBUKTI Not like '%FLK%'
    order by CAST(NOURUT as int) desc)
End

--select * from DBTRANSFER where NOBUKTI = 'SML/TRF/00001/0523'
/*
if @inisial='SJ' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbspb A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti not like '%POS%'
    order by CAST(NOURUT as int) desc)
End
*/

/*
if @inisial='PBG' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPenyerahanBhn A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun 
    order by CAST(NOURUT as int) desc)
End
*/
/*
if @inisial='SSP' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBSERAHSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pKonsi,0)=0
    order by CAST(NOURUT as int) desc)
End	
*/
if @inisial='SPK' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBSERAHSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pKonsi,0)=1
    order by CAST(NOURUT as int) desc)
End	

/*
if @inisial='IB' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBAlokasiBrg A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End	
*/
if @inisial='PL' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBOutBrg A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti not like '%PLN%'
    order by CAST(NOURUT as int) desc)
End	

if @inisial='PLN' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBOutBrg A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%PLN%'
    order by CAST(NOURUT as int) desc)
End	



if @inisial='TFL' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBOutBrg A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%TFL%'
    order by CAST(NOURUT as int) desc)
End	
/*
if @inisial='RPB' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBRBELI A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End	
*/

/*
if @inisial='OPN' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBKOREKSI A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End			
*/					
		/*

if @inisial='KMBJ' 
begin

  if not exists(select nobukti from DBUBAHKEMASANDET where noprubah=@NoPRKMBJ)
  begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBUBAHKEMASAN A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
  End
  Else
  begin
    select @urut=  (select top 1  CAST(NOURUT as int) 
    from DBUBAHKEMASAN A
    left outer join DBUBAHKEMASANDET b on A.NOBUKTI=B.NOBUKTI and b.NoPrUbah=@NoPRKMBJ
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and
     B.NOBUKTI is not null
    )
  End
End			
*/



if @inisial='FLK' 
begin

  if not exists(select nobukti from DBTRANSFERDET where NOPRTRANSFER=@NoPRKMBJ)
  begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBTRANSFER A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NOBUKTI like '%FLK%'
    order by CAST(NOURUT as int) desc)
  End
  Else
  begin
    select @urut=  (select top 1  CAST(NOURUT as int) 
    from DBTRANSFER A
    left outer join DBTRANSFERdet b on A.NOBUKTI=B.NOBUKTI and NOPRTRANSFER=@NoPRKMBJ
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and  A.NOBUKTI like '%FLK%' and
     B.NOBUKTI is not null
    )
  End
End			


if @inisial='TLO' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBTRANSFER A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pTerima,0) = 1 and NOBUKTI like '%TLO%'
    order by CAST(NOURUT as int) desc)
End	




if @inisial='PRP' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPRPenyerahanBhn A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End	



/*
if @inisial='PRU' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPRUBAHKEMASAN A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End	
*/
/*
if @inisial='PRU' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dboutbrg A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun  and NOBUKTI like '%PRU%'
    order by CAST(NOURUT as int) desc)
End
*/
if @inisial='TOH' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBKOREKSIOPN A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End			
		

if @inisial='RLB' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBTRANSFER A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun  and NOBUKTI like '%RLB%'
    order by CAST(NOURUT as int) desc)
End			













-----


if @inisial='RSS' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBRSERAHSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pKonsi,0)=0
    order by CAST(NOURUT as int) desc)
End	

if @inisial='RSK' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBRSERAHSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pKonsi,0)=1
    order by CAST(NOURUT as int) desc)
End	


if @inisial='INVC' or @inisial='INVCN'
begin
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbinvoicepl A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun  and isnull(pJasa,0) = 0
    and NoBukti  like '%' +@inperusahaan + '%' 
    order by CAST(NOURUT as int) desc)

	set @inisial='INVC'
End




if @inisial='INVJ' or @inisial='INVJN'
begin
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbinvoicepl A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun  and isnull(pJasa,0) = 1
    and NoBukti  like '%' +@inperusahaan + '%' 
    order by CAST(NOURUT as int) desc)

	set @inisial='INVJ'
End



if @inisial='BGT' or @inisial='BGTN'
begin
set @inisial='BGT'
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbtrans A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    and NoBukti  like '%' +@inperusahaan + '%' and NoBukti like '%' + @inisial + '/%'
    order by CAST(NOURUT as int) desc)

	
End

if @inisial='BGC' or @inisial='BGCN'
begin
set @inisial='BGC'
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbtrans A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    and NoBukti  like '%' +@inperusahaan + '%' and NoBukti like '%' + @inisial + '/%'
    order by CAST(NOURUT as int) desc)

	
End


if @inisial='BBG' or @inisial='BBGN'
begin
set @inisial='BBG'
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbtrans A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    and NoBukti  like '%' +@inperusahaan + '%' and NoBukti like '%' + @inisial + '/%'
    order by CAST(NOURUT as int) desc)

	
End
if @inisial='BCG' or @inisial='BCGN'
begin
set @inisial='BCG'
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbtrans A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    and NoBukti  like '%' +@inperusahaan + '%' and NoBukti like '%' + @inisial + '/%'
    order by CAST(NOURUT as int) desc)

	
End





if @inisial='BPL' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbinvoice A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End

if @inisial='BAP' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBBAOP A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End


if @inisial='PPL' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBPPL A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and pJasa = 0 and pAgen = 0
	order by CAST(NOURUT as int) desc)
End	

if @inisial='PPLA' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBPPL A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and pJasa = 0 and pAgen = 1
	order by CAST(NOURUT as int) desc)
End	

if @inisial='PNS' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBPPL A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and pJasa = 1
	order by CAST(NOURUT as int) desc)
End	
	
if @inisial='PR' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBPPL A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and pJasa = 0 and pAgen = 0
	order by CAST(NOURUT as int) desc)
End	

if @inisial='PRA' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBPPL A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and pJasa = 0 and pAgen = 1
	order by CAST(NOURUT as int) desc)
End

if @inisial='TRT' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBTRANSFER A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun AND ISNULL(pTERIMA,0) = 1
	order by CAST(NOURUT as int) desc)
End	
	


if @inisial='LPB' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBBELI A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and isnull(Pjasa,0)=0
	order by CAST(NOURUT as int) desc)
End	

if @inisial='PBJ' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBBELI A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and isnull(Pjasa,0)=1
	order by CAST(NOURUT as int) desc)
End	

if @inisial='SO' or @inisial='SON' 
begin
    select @urut=(
	select top 1  CAST(NOURUT as int) + 1
	from DBSO A
	where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun 
	and NoBukti  like '%' +@inperusahaan + '%' 
	order by CAST(NOURUT as int) desc)
	
	set @inisial='SO'
End	
	
if @inisial='SPR' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbSPBRJual A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End

if @inisial='RSPB' or @inisial='RSPBN'
begin
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBRSPB A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun 
    and NoBukti  like '%' +@inperusahaan + '%' 
    order by CAST(NOURUT as int) desc)

	set @inisial='RSPB'
End
	/*
if @inisial='TRF' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBTRANSFER A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pTERIMA,0)=0
    order by CAST(NOURUT as int) desc)
End
*/
--select * from DBTRANSFER where NOBUKTI = 'SML/TRF/00001/0523'

if @inisial='SJ' or @inisial='SJN' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbspb A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti not like '%POS%'
    and NoBukti  like '%' +@inperusahaan + '%' 
    order by CAST(NOURUT as int) desc)
    
    set @inisial='SJ'
End

/*if @inisial='SJ' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbspb A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti not like '%POS%'
    order by CAST(NOURUT as int) desc)
End*/


if @inisial='PBG' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPenyerahanBhn A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun 
    order by CAST(NOURUT as int) desc)
End


if @inisial='SSP' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBSERAHSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pKonsi,0)=0
    order by CAST(NOURUT as int) desc)
End	

if @inisial='SSK' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBSERAHSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pKonsi,0)=1
    order by CAST(NOURUT as int) desc)
End	


if @inisial='IB' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBAlokasiBrg A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End	

if @inisial='OB' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBOutBrg A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti not like '%OBN%'
    order by CAST(NOURUT as int) desc)
End	

if @inisial='OBN' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBOutBrg A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%OBN%'
    order by CAST(NOURUT as int) desc)
End	

if @inisial='UMJ' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBUMJUAL A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%UMJ%'
    order by CAST(NOURUT as int) desc)
End	




if @inisial='RPB' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBRBELI A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End	

if @inisial='OPN' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBKOREKSI A
     where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NOBUKTI like '%OPN%'
    order by CAST(NOURUT as int) desc)
End		
	
if @inisial='SPR' or @inisial='SPRN'
begin
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbSPBRJual A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun 
    and NoBukti  like '%' +@inperusahaan + '%' 
    order by CAST(NOURUT as int) desc)

	set @inisial='SPR'
End


if @inisial='PRJ' or @inisial='PRJN'
begin
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbprrjual A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun 
    and NoBukti  like '%' +@inperusahaan + '%' 
    order by CAST(NOURUT as int) desc)

	set @inisial='PRJ'
End
					
if @inisial='IVRJ' or @inisial='IVRJN'
begin
	select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBINVOICERPJ A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun 
    and NoBukti  like '%' +@inperusahaan + '%' 
    order by CAST(NOURUT as int) desc)

	set @inisial='IVRJ'
End

if @inisial='RUM' 
begin
    select @urut=(
    select 1 + MAX ( a.nomor) from (
Select Isnull(Max(case when isnumeric(SUBSTRING(NORETUR,9,5))=1 then cast(SUBSTRING(NORETUR,9,5) as int) else 0 end),0) Nomor from tempRUMJual
Union ALL Select Isnull(Max(case when isnumeric(SUBSTRING(NORETUR,9,5))=1 then cast(SUBSTRING(NORETUR,9,5) as int) else 0 end
              ),0) Nomor from DBUMJUAL        
      where month(Tanggal)= @bulan and year(Tanggal)= @tahun
                and isnumeric(Right(NoBukti,4))=1) a)
End		

if @inisial='DPH' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBDPH A
    where MONTH(TANGGAL)= @bulan and YEAR(TANGGAL)= @tahun and NoBukti like '%DPH%'
    order by CAST(NOURUT as int) desc)
End



if @inisial='KN' or @inisial='KNN' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbKreditNote A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun 
    and NoBukti  like '%' +@inperusahaan + '%' 
    order by CAST(NOURUT as int) desc)
    
    set @inisial='KN'
End

if @inisial='PRP' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPRPenyerahanBhn A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End

if @inisial='DN' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBDebetNote A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%DN%'
    order by CAST(NOURUT as int) desc)
End

-- [2026-10-14] cabang TDP (Penerimaan DPP) dari DBSMLNEW
if @inisial='TDP' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBTerimaDPP A
    where MONTH(TANGGAL)= @bulan and YEAR(TANGGAL)= @tahun and NoBukti like '%TDP%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='PLD' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBTerimaDPP A
    where MONTH(TANGGAL)= @bulan and YEAR(TANGGAL)= @tahun and NoBukti like '%PLD%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='PRB' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPRRBELI A
    where MONTH(TANGGAL)= @bulan and YEAR(TANGGAL)= @tahun and NoBukti like '%PRB%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='BMM' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbtrans A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%BMM%'
    order by CAST(NOURUT as int) desc)
End	

if @inisial='BJK' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from dbtrans A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%BJK%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='PRS' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPRSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%PRS%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='PO' or @inisial='PON' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPO A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun  
   and NoBukti  like '%' +@inperusahaan + '%' and ISNULL(pJasa,0)=0
    order by CAST(NOURUT as int) desc)
    
    set @inisial='PO'
End

if @inisial='POB' or @inisial='POBN' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPO A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun  
   and NoBukti  like '%' +@inperusahaan + '%' and ISNULL(pJasa,0)=1
    order by CAST(NOURUT as int) desc)
    
    set @inisial='POB'
End



if @inisial='PRT' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPRTRANSFER A
    where MONTH(TANGGAL)= @bulan and YEAR(TANGGAL)= @tahun and NoBukti like '%PRT%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='POP' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPERINTAHOP A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun
    order by CAST(NOURUT as int) desc)
End

if @inisial='KMBJ' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBUBAHKEMASAN A
    where MONTH(TANGGAL)= @bulan and YEAR(TANGGAL)= @tahun and NoBukti like '%' + @inisial + '%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='BBS' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBBEBANSAMPLE A
    where MONTH(TANGGAL)= @bulan and YEAR(TANGGAL)= @tahun and NoBukti like '%' + @inisial + '%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='KRS' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBKOREKSI A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and nobukti like '%KRS%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='RSK' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBRSERAHSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and ISNULL(pKonsi,0)=1
    order by CAST(NOURUT as int) desc)
End	

if @inisial='DPP' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBDPH A
    where MONTH(TANGGAL)= @bulan and YEAR(TANGGAL)= @tahun and NoBukti like '%DPP%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='PRK' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBPRSAMPLE A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%PRK%'
    order by CAST(NOURUT as int) desc)
End

if @inisial='UMB' 
begin
    select @urut=(
    select top 1  CAST(NOURUT as int) + 1
    from DBUMJUAL A
    where MONTH(TANGGAL)=@bulan and YEAR(TANGGAL)=@tahun and NoBukti like '%UMB%'
    order by CAST(NOURUT as int) desc)
End


---============================= END KODE NOBUKTI =============================================





set @urut=ISNULL(@urut,1)		
set @Nourut=case when LEN(@urut)=1 then '0000'	
				 when LEN(@urut)=2 then '000'
				 when LEN(@urut)=3 then '00'
				 when LEN(@urut)=4 then '0'
				 when LEN(@urut)=5 then ''
			End + CAST(@urut as varchar(5))
			
if @inisial='PNW'
begin

--select @Nourut,@periodBLN,@periodThn

select right(@Nourut,4) +'/' +'SML' + '/'+ 'PNW' + '/' +
case when @periodBLN ='01' then 'I'
	 when @periodBLN ='02' then 'II'
	 when @periodBLN ='03' then 'III'
	 when @periodBLN ='04' then 'IV'
	 when @periodBLN ='05' then 'V'
	 when @periodBLN ='06' then 'VI'
	 when @periodBLN ='07' then 'VII'
	 when @periodBLN ='08' then 'VIII'
	 when @periodBLN ='09' then 'IX'
	 when @periodBLN ='10' then 'X'
	 when @periodBLN ='11' then 'XI'
	 when @periodBLN ='12' then 'XII'
	 End + '/' + @periodThn Nobukti,@Nourut Nourut


end
else
begin			
	
select case when FORMAT1=0 then @inperusahaan
			when FORMAT1=1 then @inisial
			when FORMAT1=2 then @periodBLN + RIGHT(@periodThn,2)
			when FORMAT1=3 then @periodBLN + @periodThn
			when FORMAT1=4 then @Nourut
			when FORMAT1=5 then RIGHT(@periodThn,2) + @periodBLN
			when FORMAT1=6 then @periodThn + @periodBLN
		End + @pemisah +
	   case when FORMAT2=0 then @inperusahaan
			when FORMAT2=1 then @inisial
			when FORMAT2=2 then @periodBLN + RIGHT(@periodThn,2)
			when FORMAT2=3 then @periodBLN + @periodThn
			when FORMAT2=4 then @Nourut
			when FORMAT2=5 then RIGHT(@periodThn,2) + @periodBLN
			when FORMAT2=6 then @periodThn + @periodBLN
		End + @pemisah +
		
		 case when FORMAT3=0 then @inperusahaan
			when FORMAT3=1 then @inisial
			when FORMAT3=2 then @periodBLN + RIGHT(@periodThn,2)
			when FORMAT3=3 then @periodBLN + @periodThn
			when FORMAT3=4 then @Nourut
			when FORMAT3=5 then RIGHT(@periodThn,2) + @periodBLN
			when FORMAT3=6 then @periodThn + @periodBLN
		End + @pemisah +
		
		 case when FORMAT4=0 then @inperusahaan
			when FORMAT4=1 then @inisial
			when FORMAT4=2 then @periodBLN + RIGHT(@periodThn,2)
			when FORMAT4=3 then @periodBLN + @periodThn
			when FORMAT4=4 then @Nourut
			when FORMAT4=5 then RIGHT(@periodThn,2) + @periodBLN
			when FORMAT4=6 then @periodThn + @periodBLN
		End Nobukti,
		@Nourut Nourut
		
		
from DBNOMOR

end			
			
			
/*	
select case when FORMAT1=0 then @inperusahaan
			when FORMAT1=1 then @inisial
			when FORMAT1=2 then @periodBLN + RIGHT(@periodThn,2)
			when FORMAT1=3 then @periodBLN + @periodThn
			when FORMAT1=4 then @Nourut
			when FORMAT1=5 then RIGHT(@periodThn,2) + @periodBLN
			when FORMAT1=6 then @periodThn + @periodBLN
		End + @pemisah +
	   case when FORMAT2=0 then @inperusahaan
			when FORMAT2=1 then @inisial
			when FORMAT2=2 then @periodBLN + RIGHT(@periodThn,2)
			when FORMAT2=3 then @periodBLN + @periodThn
			when FORMAT2=4 then @Nourut
			when FORMAT2=5 then RIGHT(@periodThn,2) + @periodBLN
			when FORMAT2=6 then @periodThn + @periodBLN
		End + @pemisah +
		
		 case when FORMAT3=0 then @inperusahaan
			when FORMAT3=1 then @inisial
			when FORMAT3=2 then @periodBLN + RIGHT(@periodThn,2)
			when FORMAT3=3 then @periodBLN + @periodThn
			when FORMAT3=4 then @Nourut
			when FORMAT3=5 then RIGHT(@periodThn,2) + @periodBLN
			when FORMAT3=6 then @periodThn + @periodBLN
		End + @pemisah +
		
		 case when FORMAT4=0 then @inperusahaan
			when FORMAT4=1 then @inisial
			when FORMAT4=2 then @periodBLN + RIGHT(@periodThn,2)
			when FORMAT4=3 then @periodBLN + @periodThn
			when FORMAT4=4 then @Nourut
			when FORMAT4=5 then RIGHT(@periodThn,2) + @periodBLN
			when FORMAT4=6 then @periodThn + @periodBLN
		End Nobukti,
		@Nourut Nourut
		
		
from DBNOMOR

*/
GO
