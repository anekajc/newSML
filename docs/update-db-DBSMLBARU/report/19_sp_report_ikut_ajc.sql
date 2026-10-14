/* =====================================================================
   19 - SP/view laporan disamakan dengan DBSMLNEW (newProgramAJC)
   Keputusan user (2026-10-14): report ikut newProgramAJC (versi terbaru).
   Isi        : 9 view + 19 SP laporan = isi DBSMLNEW, ditambah 3 SP laporan
                yang belum ada di DBSMLBARU (sp_DEATFASTSLOW,
                Sp_reportBeligrafik2, SP_REPORTHISSOOUTweb).
   Hanya menambah:
                - VwreportOutSPB tetap punya kolom NNET.
                - sp_ReportSaldoHutangTO tetap menerima @pKpi dan
                  Sp_ReportOUtSPBDet tetap menerima @pagen (bernilai default,
                  tidak dipakai lagi), supaya pemanggil lama tidak error.
   Perhatian  : SP_TransferBlmTerima kini memakai @TGLA, @TGLB, @kirim (sama
                dengan pemanggil di LaporanTransferKeCabangBlmDiterimaController
                newSML). programajcSML memanggilnya dengan 1 parameter; tidak
                berpengaruh di sini karena programajcSML memakai database lain.
   Tidak diubah: vwOutstandingPO (dipakai Verifikasi Barang & Uang Muka Beli),
                CetakInvoicePenjualan (cetak invoice marketing).
   Sifat      : ALTER/CREATE, satu transaksi (gagal = batal semua). Aman diulang.
   Dijalankan : 2026-10-14 di DBSMLBARU - sukses.
   ===================================================================== */
USE DBSMLBARU;
GO
-- VwreportOutSPB : isi DBSMLNEW + kolom NNET
ALTER View [dbo].[VwreportOutSPB] 
as

Select A.*,B.NAMABRG NamaBarang,C.Tanggal,C.kodeCustSupp,D.NAMACUSTSUPP,
           A.Nobukti+Cast(A.Urut as varchar(5)) MyKey,A.NoBukti Nosox,x.TANGGAL TanggalSO,X.NoPesanan
          , b.PartNumber,ISnull(d.Agent,'') Agent ,Y.Tanggal tGLinv,Qnt2RSPB,QntRSPB,y.Qnt qNTinV,y.Qnt2 qNT2inv,
           m1.Nama NamaSLS,M2.HARGA,m2.NDPPRp,m2.NNET /* [newSML] kolom lama DBSMLBARU dipertahankan */,Cd.NAMAGROUPCUSTSUPP
from dbSPBDet A
     left outer join DBBARANG B on B.KODEBRG=A.Kodebrg
     Left Outer join dbSPB C on A.NoBukti = C.NoBukti
     Left Outer join DBCUSTSUPP D on c.KodeCustSupp = D.KODECUSTSUPP
     Left Outer join (Select x.NoSPB, x.UrutSPB,QNT Qnt,QNT2 Qnt2,Y.Tanggal
                      from dbInvoicePLDet  x
                      left outer join  dbinvoicepl y on x.NoBukti=y.NoBukti
                    ) y on y.NoSPB=A.NoBukti and A.Urut=y.UrutSPB
   
	left outer join DBSO x on A.NoSo=x.NOBUKTI    
     Left Outer join (Select x.NoSPB, x.UrutSPB, sum(Qnt) QntRSPB, sum(Qnt2) Qnt2RSPB,
                            SUM(x.NetW) NetWRSPB, SUM(x.GrossW) GrossWRSPB
                      from DBRSPBDet x
                      Group by  x.NoSPB, x.UrutSPB) H on H.NoSPB=A.NoBukti and H.UrutSPB=A.Urut  
     Left outer join dbKaryawan M1 on x.KODESLS=m1.KeyNIK
     Left Outer JOin DBSODET M2 on A.NoSo=M2.NOBUKTI and A.UrutSo=M2.Urut
    left outer join DBGROUPCUSTSUPP cd on  D.Agent=cd.KODEGROUPCUSTSUPP 
where c.IsFlag<>1
GO
-- sp_ReportSaldoHutangTO : isi DBSMLNEW + parameter @pKpi
ALTER PROCEDURE [dbo].[sp_ReportSaldoHutangTO]
--declare

@Perkiraan varchar(20),
@Tanggal1 datetime,
@Tanggal2 datetime,
@Awal varchar(15),
@Akhir varchar(15),
@Devisi varchar(10),
@Tipe int,
@KodeVls varchar(15),
@pKpi bit=0  -- [newSML] parameter lama DBSMLBARU, diterima tapi tidak dipakai
AS
/*
select
@Perkiraan ='113100',
@Tanggal1 ='01/01/2024',
@Tanggal2 ='01/31/2024',
@Awal ='CA001',
@Akhir ='DE024',
@Devisi ='01',
@Tipe =1,
@KodeVls ='IDR'


*/



declare @tgl1thlalu datetime,@tgl2thlalu datetime,@tottahunlalu numeric(18,2),@totthini numeric(18,2),
@totratahutang numeric(18,2),@totbelirata numeric(18,2),@totTO numeric(18,2)
set @tgl1thlalu= cast('12/31/'+CAST(YEAR(@Tanggal1)-1 as varchar(4)) as DATE)
set @tgl2thlalu= cast('12/31/'+CAST(YEAR(@Tanggal2)-1 as varchar(4)) as DATE)
--select @tgl1thlalu


select @tottahunlalu=(SUM(SaldoAkhirTHlalu)) from (

 	
						Select 	H.KodeCustSupp Kode, 
						sum(isnull(Kredit,0)-isnull(Debet,0)) SaldoAkhirTHlalu,
						(sum(isnull(Kredit,0)-isnull(Debet,0)) + sum(isnull(Kredit,0)-isnull(Debet,0)) )/2 RataHutang
 						from vwHutpiut H 	
 						where H.Tanggal<=@tgl1thlalu  and H.perkiraan=@perkiraan and (H.KodeCustSupp between @awal and @akhir)
 						group by H.KodeCustSupp
 						Having
 	   sum(case when H.Tanggal<@Tanggal1 then isnull(Kredit,0)-isnull(Debet,0) else 0 end)<>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debet,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debet,0) else 0 end) <>0 or
		sum(isnull(Kredit,0)-isnull(Debet,0)) <>0 or
		sum(case when H.Tanggal<@Tanggal1 then isnull(Kreditd,0)-isnull(Debetd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kreditd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debetd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debetd,0) else 0 end) <>0 or
		sum(isnull(Kreditd,0)-isnull(Debetd,0)) <>0 
	) A
	
select @totthini=SUM(saldo),@totbelirata=SUM(totbelirata) from (

 	
						Select 	H.KodeCustSupp Kode, 
						sum(isnull(Kredit,0)-isnull(Debet,0)) saldo,
						(sum(isnull(Kredit,0)-isnull(Debet,0)) + sum(isnull(Kredit,0)-isnull(Debet,0)) )/2 RataHutang,
						(sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) - 
						sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debet,0) else 0 end)) / 
						MONTH(@Tanggal2) totbelirata
 						from vwHutpiut H 	
 						where H.Tanggal<=@Tanggal2  and H.perkiraan=@perkiraan and (H.KodeCustSupp between @awal and @akhir)
 						group by H.KodeCustSupp
 						Having
 	   sum(case when H.Tanggal<@Tanggal1 then isnull(Kredit,0)-isnull(Debet,0) else 0 end)<>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debet,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debet,0) else 0 end) <>0 or
		sum(isnull(Kredit,0)-isnull(Debet,0)) <>0 or
		sum(case when H.Tanggal<@Tanggal1 then isnull(Kreditd,0)-isnull(Debetd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kreditd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debetd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debetd,0) else 0 end) <>0 or
		sum(isnull(Kreditd,0)-isnull(Debetd,0)) <>0 
	) A


set @totratahutang= (@tottahunlalu + @totthini)/2
set @totTO = (@totratahutang / @totbelirata) * 30

if @tipe=0 
begin

	Select 	--@tottahunlalu,@totthini,@totratahutang,@totbelirata,@totTO,
	H.KodeCustSupp Kode, isnull(S.NAMACUSTSUPP,'') Nama, isnull(S.Kota,'') Kota,
		sum(case when H.Tanggal<@Tanggal1 then isnull(Kredit,0)-isnull(Debet,0) else 0 end) Awal,
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) Jumlah,
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debet,0) else 0 end) Pelunasan,
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debet,0) else 0 end) Retur,
		sum(isnull(Kredit,0)-isnull(Debet,0)) SaldoAkhir,
		sum(case when H.Tanggal<@Tanggal1 then isnull(Kreditd,0)-isnull(Debetd,0) else 0 end) AwalD,
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kreditd,0) else 0 end) JumlahD,
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debetd,0) else 0 end) PelunasanD,
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debetd,0) else 0 end) ReturD,
		sum(isnull(Kreditd,0)-isnull(Debetd,0)) SaldoAkhirD,
		
		ISNULL(I.SaldoAkhirTHlalu,0) AkhirThLalu,
		
		(ISNULL(I.SaldoAkhirTHlalu,0) + 
		sum(isnull(Kredit,0)-isnull(Debet,0)) )/2 RataHutang,									
		
		
		(sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) - 
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debet,0) else 0 end)) / MONTH(@Tanggal2) BeliRata2,
		
		
		
		
						
		/*
		(((ISNULL(I.SaldoAkhirTHlalu,0) + 
		sum(isnull(Kredit,0)-isnull(Debet,0)) )/2) / 
		  
		 case when (sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) - 
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debet,0) else 0 end)) / MONTH(@Tanggal2)<>0 then
		
		((sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) - 
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debet,0) else 0 end)) / MONTH(@Tanggal2))
		
		else ((ISNULL(I.SaldoAkhirTHlalu,0) + 
		sum(isnull(Kredit,0)-isnull(Debet,0)) )/2) end 

		)*30 */
		
		(((ISNULL(I.SaldoAkhirTHlalu,0) + 
		sum(isnull(Kredit,0)-isnull(Debet,0)) )/2 ) / @totratahutang) * @totTO
		
		TurnOver
 	from vwHutpiut H
 	left outer join DBCUSTSUPP S on S.KODECUSTSUPP=H.KodeCustSupp
 	
 	left outer join (
						Select 	H.KodeCustSupp Kode, 
						sum(isnull(Kredit,0)-isnull(Debet,0)) SaldoAkhirTHlalu
 						from vwHutpiut H 	
 						where H.Tanggal<=@tgl1thlalu  and H.perkiraan=@perkiraan and (H.KodeCustSupp between @awal and @akhir)
 						group by H.KodeCustSupp
 						Having
 	   sum(case when H.Tanggal<@Tanggal1 then isnull(Kredit,0)-isnull(Debet,0) else 0 end)<>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debet,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debet,0) else 0 end) <>0 or
		sum(isnull(Kredit,0)-isnull(Debet,0)) <>0 or
		sum(case when H.Tanggal<@Tanggal1 then isnull(Kreditd,0)-isnull(Debetd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kreditd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debetd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debetd,0) else 0 end) <>0 or
		sum(isnull(Kreditd,0)-isnull(Debetd,0)) <>0 
 					 ) I on H.KodeCustSupp=I.Kode
 					 
 	where H.Tanggal<=@Tanggal2  and H.perkiraan=@perkiraan and (H.KodeCustSupp between @awal and @akhir)
 	group by H.KodeCustSupp, S.NAMACUSTSUPP, S.kota,ISNULL(I.SaldoAkhirTHlalu,0)
 	Having
 	   sum(case when H.Tanggal<@Tanggal1 then isnull(Kredit,0)-isnull(Debet,0) else 0 end)<>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debet,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debet,0) else 0 end) <>0 or
		sum(isnull(Kredit,0)-isnull(Debet,0)) <>0 or
		sum(case when H.Tanggal<@Tanggal1 then isnull(Kreditd,0)-isnull(Debetd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Kreditd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur='' then isnull(H.Debetd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.tanggal<=@Tanggal2 and H.NoRetur<>'' then isnull(H.Debetd,0) else 0 end) <>0 or
		sum(isnull(Kreditd,0)-isnull(Debetd,0))<>0 or (ISNULL(I.SaldoAkhirTHlalu,0)) <>0 
	order by H.KodeCustSupp
end
else
begin





select kode,nama,kota,SUM(Awal) awal,SUM(jumlah)Jumlah,SUM(pelunasan) pelunasan,SUM(retur) retur,
       SUM(saldoakhir)saldoakhir,SUM(awald)awald,SUM(jumlahd) jumlahd,SUM(pelunasand)pelunasand,SUM(returd)returd,
       sum(saldoakhird)saldoakhird,SUM(x.titip)titip,sum(SaldoAkhir-x.titip)akhir,SUM(saldo0)saldo0,SUM(saldo30)saldo30,
       SUM(saldo60)saldo60,SUM(saldo90)saldo90,SUM(saldo121)saldo121,
       
      	
		ISNULL(I.SaldoAkhirTHlalu,0) - isnull(J.TITIP,0) AkhirThLalu,
		isnull(j.titip,0) Saldoawal, 
		
		/*((((ISNULL(I.SaldoAkhirTHlalu,0) - j.titip) + 
		(sum(SaldoAkhir)-j.titip)) /2)*30)/
		case when SUM(jumlah)=0 then 1 else SUM(jumlah) End RTO */	
		
		
		CASE WHEN (SUM(jumlah) /  MONTH(@Tanggal2)) = 0 THEN 0
		 ELSE 
		
		(((ISNULL(I.SaldoAkhirTHlalu,0) - isnull(J.TITIP,0)  +  sum(SaldoAkhir-x.titip))/2) / 		 
		(SUM(jumlah) /  MONTH(@Tanggal2))) 
		* 30  eND RTO,
		
		(ISNULL(I.SaldoAkhirTHlalu,0) - isnull(J.TITIP,0)  +  sum(SaldoAkhir-x.titip))/2  RTPiutang,
		SUM(jumlah) /  MONTH(@Tanggal2) RTPenjualan							
		
		
       
       
       
       
       
       
from (
Select 	S.KodeCustSupp Kode, isnull(S.NAMACUSTSUPP,'') Nama, isnull(S.Kota,'') Kota,
		sum(case when H.Tanggal<@Tanggal1 then isnull(Debet,0)-isnull(Kredit,0) else 0 end) Awal,
		sum(case when H.Tanggal>=@Tanggal1 then isnull(H.Debet,0) else 0 end) Jumlah,
		sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) Pelunasan,
		sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur<>'' then isnull(H.Kredit,0) else 0 end) Retur,
		sum(isnull(Debet,0)-isnull(Kredit,0)) SaldoAkhir,
		sum(case when H.Tanggal<@Tanggal1 then isnull(Debetd,0)-isnull(Kreditd,0) else 0 end) AwalD,
		sum(case when H.Tanggal>=@Tanggal1 then isnull(H.Debetd,0) else 0 end) JumlahD,
		sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur='' then isnull(H.Kreditd,0) else 0 end) PelunasanD,
		sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur<>'' then isnull(H.Kreditd,0) else 0 end) ReturD,
		sum(isnull(Debetd,0)-isnull(Kreditd,0)) SaldoAkhirD,0 titip,0 akhir,0 saldo0,0 saldo30,0 saldo60,0 saldo90,0 saldo121 
 	from vwHutpiut H
 	left outer join DBCUSTSUPP S on  H.KodeCustSupp=S.KODECUSTSUPP
 	where H.Tanggal<=@Tanggal2 and S.KodeCustSupp>=@awal and S.KodeCustSupp<=@akhir  and s.KODECUSTSUPP<>'CZ999' and h.Perkiraan=@Perkiraan
 	group by S.KodeCustSupp, S.NAMACUSTSUPP, S.Kota
 	having sum(case when H.Tanggal<@Tanggal1 then isnull(Debet,0)-isnull(Kredit,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 then isnull(H.Debet,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur<>'' then isnull(H.Kredit,0) else 0 end) <>0 or
		sum(isnull(Debet,0)-isnull(Kredit,0)) <>0 or
		sum(case when H.Tanggal<@Tanggal1 then isnull(Debetd,0)-isnull(Kreditd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 then isnull(H.Debetd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur='' then isnull(H.Kreditd,0) else 0 end) <>0 or
		sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur<>'' then isnull(H.Kreditd,0) else 0 end)<>0 or
		sum(isnull(Debetd,0)-isnull(Kreditd,0)) <>0 
		
union all

Select 	S.KodeCustSupp Kode, isnull(S.NAMACUSTSUPP,'') Nama, isnull(S.Kota,'') Kota,
		0 Awal,	0 Jumlah,0 Pelunasan,0 Retur,0 SaldoAkhir,0 AwalD,0 JumlahD,0 PelunasanD,0 ReturD,0 SaldoAkhirD,
		(isnull(SUM(debetrp),0)- ISNULL(sum(d.DIBAYAR),0)-ISNULL(sum(LB),0)-ISNULL(sum(E.DEBET),0)) TITIP,

        0 AKHIR,0 saldo0,0 saldo30,0 saldo60,0 saldo90,0 saldo121 
 	from vwTransaksi H
 	left outer join DBCUSTSUPP S on  H.CustSuppL=S.KODECUSTSUPP
 	--left outer join dbterimadpp b on b.NoBukti=H.NOBUKTI and b.Tanggal<=@Tanggal2
 	LEFT OUTER JOIN (select A.NODPP,A.UrutDPP,SUM(A.DIBAYAR) DIBAYAR,SUM(A.LB) LB
				 from DBTerimaDPPDET A
				 LEFT OUTER JOIN DBTerimaDPP E ON A.NoBukti=E.NoBukti 
				 WHERE  E.Tanggal <=@Tanggal2
				 group by A.NODPP,A.UrutDPP 
				) D ON h.NObukti=D.NoDPP AND h.urut=D.UrutDPP 

 	LEFT OUTER JOIN (SELECT NOTITIPAN,URUTTITIPAN,SUM(Debet) DEBET
                 FROM dbTransaksi  WHERE Tanggal <=@Tanggal2 and Perkiraan='113400' and isnull(notitipan,'')<>'' --and custsuppp<>'CZ999'
                 group BY NOTITIPAN,URUTTITIPAN) E ON H.NoBukti=E.NOTITIPAN AND H.Urut=E.URUTTITIPAN
 	where H.Tanggal<=@Tanggal2 and S.KodeCustSupp>=@awal and S.KodeCustSupp<=@akhir  --and s.KODECUSTSUPP<>'CZ999' 
 	and H.Lawan='113400' and H.TANGGAL>'03/28/2016'
 	group by S.KodeCustSupp, S.NAMACUSTSUPP, S.Kota
 	having isnull(SUM(debetrp),0)- ISNULL(sum(d.DIBAYAR),0)-ISNULL(sum(LB),0)-ISNULL(sum(E.DEBET),0)>0
 
union all
 select 	A.KODECUSTSUPP kode,isnull(S.NAMACUSTSUPP,'') Nama, isnull(S.Kota,''),
		0 Awal,0 Jumlah,0 Pelunasan,0 Retur,0 SaldoAkhir,0 AwalD,0 JumlahD,0 PelunasanD,0 ReturD,0 SaldoAkhirD,0 titip,0 akhir,
                case when (DATEDIFF(DD,max(A.jatuhtempo),@Tanggal2)<= 0) then
  			case when @KodeVls='IDR' then sum(isnull(A.Debet,0)-isnull(A.Kredit,0)) else sum(isnull(A.DebetD,0)-isnull(A.KreditD,0)) end
         		else 0 end as Saldo0,
		        case when (DATEDIFF(DD,max(A.jatuhtempo),@Tanggal2)> 0) and (DATEDIFF(DD,max(A.jatuhtempo),@Tanggal2)<= 30) then
  			case when @KodeVls='IDR' then sum(isnull(A.Debet,0)-isnull(A.Kredit,0)) else sum(isnull(A.DebetD,0)-isnull(A.KreditD,0)) end
         		else 0 end as Saldo30,
         		case when (DATEDIFF(DD,max(A.jatuhtempo),@Tanggal2)> 30) and (DATEDIFF(DD,max(A.jatuhtempo),@Tanggal2)<= 60) then
  			case when @KodeVls='IDR' then sum(isnull(A.Debet,0)-isnull(A.Kredit,0)) else sum(isnull(A.DebetD,0)-isnull(A.KreditD,0)) end
         		else 0 end as Saldo60,
         		case when (DATEDIFF(DD,max(A.jatuhtempo),@Tanggal2)> 60) and (DATEDIFF(DD,max(A.jatuhtempo),@Tanggal2)<= 90) then
  			case when @KodeVls='IDR' then sum(isnull(A.Debet,0)-isnull(A.Kredit,0)) else sum(isnull(A.DebetD,0)-isnull(A.KreditD,0)) end
         		else 0 end as Saldo90,
         		case when (DATEDIFF(DD,max(A.jatuhtempo),@Tanggal2)> 90) then
  			case when @KodeVls='IDR' then sum(isnull(A.Debet,0)-isnull(A.Kredit,0)) else sum(isnull(A.DebetD,0)-isnull(A.KreditD,0)) end
         		else 0 end as Saldo121
  	from vwHutpiut A
  	left outer join DBCUSTSUPP S on  A.KodeCustSupp=S.KODECUSTSUPP
  	where A.Tanggal<=@Tanggal2 and A.KODECUSTSUPP>=@awal and A.KODECUSTSUPP<=@akhir and A.perkiraan=@perkiraan
	group by A.NoFaktur, A.KODECUSTSUPP,s.NAMACUSTSUPP,s.kota
  	having 	(sum(isnull(A.Debet,0)-isnull(A.Kredit,0)) <>0 and @KodeVls='IDR') or (sum(isnull(A.DebetD,0)-isnull(A.KreditD,0)) <>0 and @KodeVls<>'IDR')
	 	                

	
    
    
  	
  	) X
  	
  	left outer join (Select 	S.KodeCustSupp , 
					sum(isnull(Debet,0)-isnull(Kredit,0)) SaldoAkhirTHlalu		
 					from vwHutpiut H
 					left outer join DBCUSTSUPP S on  H.KodeCustSupp=S.KODECUSTSUPP
 					where H.Tanggal<=@tgl1thlalu and S.KodeCustSupp>=@awal and S.KodeCustSupp<=@akhir  and s.KODECUSTSUPP<>'CZ999' and h.Perkiraan=@Perkiraan
 					group by S.KodeCustSupp, S.NAMACUSTSUPP, S.Kota
 					having sum(case when H.Tanggal<@Tanggal1 then isnull(Debet,0)-isnull(Kredit,0) else 0 end) <>0 or
					sum(case when H.Tanggal>=@Tanggal1 then isnull(H.Debet,0) else 0 end) <>0 or
					sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur='' then isnull(H.Kredit,0) else 0 end) <>0 or
					sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur<>'' then isnull(H.Kredit,0) else 0 end) <>0 or
					sum(isnull(Debet,0)-isnull(Kredit,0)) <>0 or
					sum(case when H.Tanggal<@Tanggal1 then isnull(Debetd,0)-isnull(Kreditd,0) else 0 end) <>0 or
					sum(case when H.Tanggal>=@Tanggal1 then isnull(H.Debetd,0) else 0 end) <>0 or
					sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur='' then isnull(H.Kreditd,0) else 0 end) <>0 or
					sum(case when H.Tanggal>=@Tanggal1 and H.NoRetur<>'' then isnull(H.Kreditd,0) else 0 end)<>0 or
					sum(isnull(Debetd,0)-isnull(Kreditd,0)) <>0 
 					 ) I on X.Kode=I.KODECUSTSUPP
 					 
 					 
	left outer join (

Select 	S.KodeCustSupp ,
		(isnull(SUM(debetrp),0)- ISNULL(sum(d.DIBAYAR),0)-ISNULL(sum(LB),0)-ISNULL(sum(E.DEBET),0)) TITIP
 	from DBCUSTSUPP S 
 	
 	left outer join vwTransaksi H on  H.CUSTSUPPL=S.KODECUSTSUPP
 	LEFT OUTER JOIN (select A.NODPP,A.UrutDPP,SUM(A.DIBAYAR) DIBAYAR,SUM(A.LB) LB
					from DBTerimaDPPDET A
					LEFT OUTER JOIN DBTerimaDPP E ON A.NoBukti=E.NoBukti 
					WHERE  E.Tanggal <='12/31/2023'
					group by A.NODPP,A.UrutDPP 
					) D ON h.NObukti=D.NoDPP AND h.urut=D.UrutDPP 

 	LEFT OUTER JOIN (SELECT NOTITIPAN,URUTTITIPAN,SUM(Debet) DEBET
					FROM dbTransaksi  WHERE Tanggal <='12/31/2023' and Perkiraan='113400' and isnull(notitipan,'')<>'' --and custsuppp<>'CZ999'
					group BY NOTITIPAN,URUTTITIPAN) E ON H.NoBukti=E.NOTITIPAN AND H.Urut=E.URUTTITIPAN
 	where H.Tanggal<='12/31/2023' and S.KodeCustSupp>='CA001' and S.KodeCustSupp<='DE024'  --and s.KODECUSTSUPP<>'CZ999' 
 	and H.Lawan='113400' and H.TANGGAL>'03/28/2016'
 	
 	group by S.KodeCustSupp
 	having isnull(SUM(debetrp),0)- ISNULL(sum(d.DIBAYAR),0)-ISNULL(sum(LB),0)-ISNULL(sum(E.DEBET),0)>0
 --order by s.KODECUSTSUPP

					 ) J on X.Kode=J.KODECUSTSUPP
	
	--WHERE  X.Kode='CT123'				 
  	group by x.Kode,x.Nama,x.Kota,i.SaldoAkhirTHlalu  ,j.TITIP	
  	having (SUM(Awal) <>0 or SUM(jumlah)<>0 or SUM(pelunasan)<> 0 or SUM(retur)<>0 or SUM(x.titip)<>0 )
               
    order by kode	
  --  SUM(isnull(j.titip,0))
  
    
                     
end
GO
-- Sp_ReportOUtSPBDet : isi DBSMLNEW + parameter @pagen
ALTER Procedure [dbo].[Sp_ReportOUtSPBDet] 
--declare
@SReport varchar(1),
@Ordr varchar(1),
@tgl1 datetime,
@tgl2 Datetime,
@isiList varchar(200),
@xx integer=1,
@IdUser Varchar(20),
@Tipe varchar(20),
@KodeAgen varchar(20)='',
@pagen tinyint=0  -- [newSML] parameter lama DBSMLBARU, diterima tapi tidak dipakai
as
--Declare @SReport varchar(1),@Ordr varchar(1),@tgl1 datetime,@tgl2 Datetime,@isiList varchar(200)
--select @SReport='T',@Ordr='N', @tgl1='10/01/2023',@tgl2='01/01/2015',@isiList='%%',@IdUser='SA',@Tipe='3033101' 

set @KodeAgen=case  when @KodeAgen IN ('','-') then '%%' else @KodeAgen end

if @SReport='T'
begin
	If @Ordr='N'
		begin
		  select 
		   NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		   UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		  ,DATEDIFF(Day,TanggalSO,@tgl1) SlsTglSO ,
		  QNT,QNT2, SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) qNTINV,
		  SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) qNT2INV,
		  SUM (QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) QntOut1,
          SUM (QNT2-ISnull(Qnt2RSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) QntOut2
          ,MAX(TGLINV) TGLINV,HARGA,ndpprp
		  from VwreportOutSPB 
		  where Agent like @KodeAgen AND Tanggal<= @tgl1		 		   
		GROUP BY NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		,tGLinv,Qnt2RSPB,QntRSPB,HARGA,ndpprp
		 
		HAVING		  
		 case when NOSAT=1 then SUM(QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNTINV,0) ELSE 0 END )
			  when NOSAT IN (2,3) then SUM(QNT2-ISnull(Qnt2RSPB,0))  - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNT2INV,0) ELSE 0 END )
			  end >0
		   
		   order by Tanggal,NoBukti
		end 
	else If @Ordr='B'
		begin
		    select 
		   NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		   UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		  ,DATEDIFF(Day,TanggalSO,@tgl1) SlsTglSO ,
		  QNT,QNT2, SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) qNTINV,
		  SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) qNT2INV,
		  SUM (QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) QntOut1,
          SUM (QNT2-ISnull(Qnt2RSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) QntOut2
          ,MAX(TGLINV) TGLINV,HARGA,ndpprp
		  from VwreportOutSPB 
		  where Agent like @KodeAgen AND Tanggal<= @tgl1			 		   
		GROUP BY NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		,tGLinv,Qnt2RSPB,QntRSPB,HARGA,ndpprp
		 
		HAVING		  
		 case when NOSAT=1 then SUM(QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNTINV,0) ELSE 0 END )
			  when NOSAT IN (2,3) then SUM(QNT2-ISnull(Qnt2RSPB,0))  - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNT2INV,0) ELSE 0 END )
			  end >0
		   
		   order by KodeBrg
		end 
	else If @Ordr='C'
		begin
		    select 
		   NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		   UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		  ,DATEDIFF(Day,TanggalSO,@tgl1) SlsTglSO ,
		  QNT,QNT2, SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) qNTINV,
		  SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) qNT2INV,
		  SUM (QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) QntOut1,
          SUM (QNT2-ISnull(Qnt2RSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) QntOut2
          ,MAX(TGLINV) TGLINV,HARGA,ndpprp
		  from VwreportOutSPB 
		  where Agent like @KodeAgen AND Tanggal<= @tgl1			 		   
		GROUP BY NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		,tGLinv,Qnt2RSPB,QntRSPB,HARGA,ndpprp
		 
		HAVING		  
		 case when NOSAT=1 then SUM(QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNTINV,0) ELSE 0 END )
			  when NOSAT IN (2,3) then SUM(QNT2-ISnull(Qnt2RSPB,0))  - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNT2INV,0) ELSE 0 END )
			  end >0
		   
		   order by Tanggal,KodeCustSupp
		end 
	else If @Ordr='D'
		begin
		  select * from VwreportOutSPBZX where Tanggal <= @tgl1 
		  -- and Agent like @KodeAgen
		  order by Tanggal,KodeCustSupp
		end 
	else If @Ordr='X'
		begin

		select  NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		   UrutSo,SATX,DBASAL,NamaBarangX NamaBRG,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		  ,DATEDIFF(Day,TanggalSO,@tgl1) SlsTglSO ,
		  A.QNT,A.QNT2, SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNTINV ELSE 0 END ) qNTINV,
		  SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END ) qNT2INV,
		  SUM (QNT-ISnull(H.QntRSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNTINV ELSE 0 END ) QntOut1,
          SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END ) QntOut2
          ,MAX(E.TGLINV) TGLINV,
          
          ((case when A.[ppn]=(1) OR A.[ppn]=(0) then (SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )   *([HARGA]-A.[DISCTOT]) -
          (( SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END ) *([HARGA]-[DISCTOT]))* A.[DISC])/(100) )
          
          when A.[ppn]=(2) then (  SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )   *([HARGA]-[DISCTOT])-
          ((   SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )   *([HARGA]-[DISCTOT]))*A.[DISC])/(100))/(1.1) else (0) end +
          
          case when A.[ppn]=(1) AND [ppnbrg]<>(1) then SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )    *([HARGA]-[DISCTOT])-
          ((   SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )  *([HARGA]-[DISCTOT]))*A.[DISC])/(100) 
          
          when A.[ppn]=(2) AND [ppnbrg]<>(1) then (  SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )   *([HARGA]-[DISCTOT])-
          ((   SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )  *([HARGA]-[DISCTOT]))*A.[DISC])/(100))/(1.1) else (0) end*(0.1))*A.[Kurs]) NNETRPZX
		  
		  
		  ,a.HARGA,a.Namabarangx,
		  
		  case when	A.[ppn]=(1) OR A.[ppn]=(0) then (SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )) *  ([HARGA]-[DISCTOT])   
		   -  ((    (SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END ))*([HARGA]-[DISCTOT]))*A.[DISC])/(100) 
		  
		  when 
		  
		  A.[ppn]=(2) then   (SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )) *([HARGA]-[DISCTOT]) 
		  - ((  (SUM (QNT2-ISnull(H.Qnt2RSPB,0)) - SUM (CASE WHEN E.tGLINV <=@tgl1 THEN E.qNT2INV ELSE 0 END )) *([HARGA]-[DISCTOT]))*A.[DISC])/(100)/(1.1) else (0) end
		  
		  
		  *A.[Kurs] NDPPRPZX,Disctot,A.ppn
		  from VwreportOutSPBHRGSO A
		  Left Outer join (select A.NoSPB,A.UrutSPB,SUM(A.QNT) QntINV,SUM(A.QNT2) Qnt2INV ,b.Tanggal TGLINV
                      from dbInvoicePLDet A
                      left outer join dbinvoicepl b on a.NoBukti=b.NoBukti
                      GROUP BY NoSPB,UrutSPB,b.Tanggal ) E ON A.NoBukti=E.NoSPB AND A.Urut=E.UrutSPB 
			Left Outer join (Select x.NoSPB, x.UrutSPB, sum(Qnt) QntRSPB, sum(Qnt2) Qnt2RSPB,
                 SUM(x.NetW) NetWRSPB, SUM(x.GrossW) GrossWRSPB
                 from DBRSPBDet x
                 Group by  x.NoSPB, x.UrutSPB) H on H.NoSPB=A.NoBukti and H.UrutSPB=A.Urut  
		  where Tanggal <= @tgl1 
		  and Agent like @KodeAgen
		  
		  GROUP BY NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		UrutSo,SATX,DBASAL,NamaBarangX,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		,E.TGLINV,H.Qnt2RSPB,H.QntRSPB,A.ppn,harga,disctot,disc,ppnbrg,kurs
		HAVING		  
		 case when NOSAT=1 then SUM(QNT-ISnull(H.QntRSPB,0)) - SUM (CASE WHEN ISNULL(E.tGLINV,'') <=@tgl1 THEN ISNULL(E.qNTINV,0) ELSE 0 END )
			  when NOSAT IN (2,3) then SUM(QNT2-ISnull(H.Qnt2RSPB,0))  - SUM (CASE WHEN ISNULL(E.tGLINV,'') <=@tgl1 THEN ISNULL(E.qNT2INV,0) ELSE 0 END )
			  end >0
		  order by Tanggal,KodeCustSupp 
		end 
	else If @Ordr='Y'
		begin
		  select * from VwreportOutSPBORI where Tanggal <= @tgl1  
		  order by Tanggal,KodeCustSupp 
		end 
End
else
begin
    If @Ordr='N'
		begin
		    select 
		   NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		   UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		  ,DATEDIFF(Day,TanggalSO,@tgl1) SlsTglSO ,
		  QNT,QNT2, SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) qNTINV,
		  SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) qNT2INV,
		  SUM (QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) QntOut1,
          SUM (QNT2-ISnull(Qnt2RSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) QntOut2
          ,MAX(TGLINV) TGLINV
		  from VwreportOutSPB 
		  where Agent like @KodeAgen	
		  and NoBukti In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@Tipe)
		   and Agent like @KodeAgen AND Tanggal<= @tgl1	
		   GROUP BY NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		,tGLinv,Qnt2RSPB,QntRSPB
		 
		HAVING		  
		 case when NOSAT=1 then SUM(QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNTINV,0) ELSE 0 END )
			  when NOSAT IN (2,3) then SUM(QNT2-ISnull(Qnt2RSPB,0))  - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNT2INV,0) ELSE 0 END )
			  end >0
		  order by Tanggal,NoBukti
		end
	else If @Ordr='B'
		begin
		   select 
		   NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		   UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		  ,DATEDIFF(Day,TanggalSO,@tgl1) SlsTglSO ,
		  QNT,QNT2, SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) qNTINV,
		  SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) qNT2INV,
		  SUM (QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) QntOut1,
          SUM (QNT2-ISnull(Qnt2RSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) QntOut2
          ,MAX(TGLINV) TGLINV
		  from VwreportOutSPB 
		  where  Tanggal <= @tgl1
		  and KOdebrg In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		   and Agent like @KodeAgen
		   GROUP BY NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		,tGLinv,Qnt2RSPB,QntRSPB
		 
		HAVING		  
		 case when NOSAT=1 then SUM(QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNTINV,0) ELSE 0 END )
			  when NOSAT IN (2,3) then SUM(QNT2-ISnull(Qnt2RSPB,0))  - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNT2INV,0) ELSE 0 END )
			  end >0
		  order by KodeBrg
		end 
	else If @Ordr='C'
		begin
		    select 
		   NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		   UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		  ,DATEDIFF(Day,TanggalSO,@tgl1) SlsTglSO ,
		  QNT,QNT2, SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) qNTINV,
		  SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) qNT2INV,
		  SUM (QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNTINV ELSE 0 END ) QntOut1,
          SUM (QNT2-ISnull(Qnt2RSPB,0)) - SUM (CASE WHEN tGLINV <=@tgl1 THEN qNT2INV ELSE 0 END ) QntOut2
          ,MAX(TGLINV) TGLINV
		  from VwreportOutSPB 
		  where  Tanggal <= @tgl1
		  and kodeCustSupp In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		   and Agent like @KodeAgen
		   GROUP BY NoBukti,Urut,NoSPP,UrutSPP,KodeBrg,Namabrg,QNT,QNT2,SAT_1,SAT_2,NOSAT,ISI,NetW,GrossW,HPP,KodeGdg,isCetakKitir,NOBatch,NoSo,
		UrutSo,SATX,DBASAL,NamaBarang,Tanggal,kodeCustSupp,NAMACUSTSUPP,MyKey,Nosox,TanggalSO,NoPesanan,PartNumber,Agent
		,tGLinv,Qnt2RSPB,QntRSPB
		 
		HAVING		  
		 case when NOSAT=1 then SUM(QNT-ISnull(QntRSPB,0)) - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNTINV,0) ELSE 0 END )
			  when NOSAT IN (2,3) then SUM(QNT2-ISnull(Qnt2RSPB,0))  - SUM (CASE WHEN ISNULL(tGLINV,'') <=@tgl1 THEN ISNULL(qNT2INV,0) ELSE 0 END )
			  end >0
		  order by Tanggal,KodeCustSupp
		end 
	else If @Ordr='D'
		begin
		  select * from VwreportOutSPBZX where Tanggal <= @tgl1
		  and kodeCustSupp In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  order by Tanggal,KodeCustSupp
		end 
	else If @Ordr='X'
		begin
		  select * from VwreportOutSPBHRGSO where Tanggal <= @tgl1
		  and kodeCustSupp In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and NNETRPZX>0.00
		   and Agent like @KodeAgen AND Tanggal<= @tgl1
		  order by Tanggal,KodeCustSupp
		end
	else If @Ordr='Y'
		begin
		  select * from VwreportOutSPBORI where Tanggal <= @tgl1
		  and kodeCustSupp In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)		  
		  order by Tanggal,KodeCustSupp
		end 
End
GO
-- VwReportBeliGudang : isi DBSMLNEW
ALTER View [dbo].[VwReportBeliGudang]
as

Select 	A.NoBukti,A.TANGGAL, B.NoPO, B.UrutPO,A.KODESUPP KodeCustSupp,I.NAMACUSTSUPP,
	    B.Urut, B.KodeBrg, H.NamaBrg,b.NOSAT,b.QNT as qntbeli,b.SATUAN as satbeli,
	    isnull(B.Qnt1Terima,0) as qnt, h.SAT1 as satuan,
        isnull(b.Qnt2Terima,0) as Qnt2,h.SAT2 As satuan2,
        B.Qnt1Reject as qntreject,
        B.Qnt2Reject as qnt2reject,
        B.Harga, B.HrgNetto,B.DiscP,B.DiscTot,
        case when a.kodevls<>'IDR' then B.NDPP else 0 end as NDPP
        ,B.NDPPRp,b.NPPNRp,b.NNETRp,a.NuangMuka,
        B.KodeGdg,A.KODEVLS,A.KURS,A.FAKTURSUPP,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,J.NAMA NamaGdg,A.TIPEBAYAR,m1.NOPOCUST,h.PartNumber
        ,case when m2.nosat=1 then b.QntTerima-b.QntReject 
        when m2.NOSAT=2 then b.Qnt2Terima-b.QntReject end Qntx ,
        h.KodeMerk,m5.NAMAMERK,
            h.KODESUBGRP,H.KODESUBKATEGORI,M3.NamaSubGrp KATEGORI,M4.KETERANGAN SUBKATEGORI    
           ,m6.KODECUST ,m7.NAMACUSTSUPP namacust,m8.KODELOKASI,m8.KETERANGAN,a.pJasa--cast(ISNULL(a.pJasa,0) as varchar(2)) pJasa  
           , case when A.PPN=0 then 0 else 1 End ppn
From dbBeliDet B 
Left Outer Join dbBeli A On A.NoBukti=b.NoBukti
Left Outer Join dbBarang H on H.KodeBrg=B.KodeBrg
Left Outer Join DBCUSTSUPP I on A.KODESUPP = I.KODECUSTSUPP
left outer join DBGUDANG J on B.KodeGdg=J.KODEGDG
left outer jOIn dbpo m1 ON B.NoPO=m1.nobukti
Left Outer join DBPODET m2 on b.NoPO=m2.NOBUKTI and b.UrutPO=m2.URUT
Left Outer join dbSubGroup M3 on M3.Kodegrp=H.KODEGRP 
				and M3.KodeSubGrp=H.KodeSubGrp and M3.KodeHdGrp=H.KodeHdGrp
left outer join DBSubGroupJnsTambah M4 on H.KODEGRP=M4.KodeGrp and H.KodeHdGrp=M4.hdgroup
and H.KODESUBGRP=M4.KodeSubGrp AND H.KODESUBKATEGORI=M4.Urut	
left outer join DBMERK m5 on h.KodeMerk=m5.KODEMERK
left outer join DBSO m6 on m1.NOSO=m6.NOBUKTI
left outer join DBCUSTSUPP m7 on m7.KODECUSTSUPP=m6.KODECUST
Left outer join DBMLOKASI m8 on h.Mlokasi=m8.KODELOKASI
/*where   case when A.nobukti like '%SML%' then
                case when A.TANGGAL <='01/31/2024' then 1  when ISNULL(a.pJasa,0)=1 then 1 else isnull(pQC,0) End
   else 1 End

 =1
 */
GO
-- VwreportBP : isi DBSMLNEW
ALTER View [dbo].[VwreportBP]
as
Select 	A.NoBukti,A.NoBPPB,
	B.Urut, B.KodeBrg, H.NamaBrg, B.Qnt, B.NoSat, B.Isi Isi, B.Sat Satuan,b.HPP, B.Qnt*B.HPP NilaiHPP, A.Tanggal,
	Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi ,b.KodePerkiraan
From dbPenyerahanBhn A
Left Outer join  dbPenyerahanBhnDet B on B.NoBukti=a.NoBukti
Left Outer join dbBarang H on H.KodeBrg=b.KodeBrg
GO
-- VwreportPO : isi DBSMLNEW
ALTER view [dbo].[VwreportPO]
as
Select B.NoBukti,I.TANGGAL,I.KODESUPP KodeCustSupp,J.NAMACUSTSUPP, '' NoSPP, 0 UrutSPP,
	B.Urut, B.KodeBrg, H.NamaBrg, B.Qnt-ISNULL(b.QntBatal,0) Qnt, B.NoSat, B.Isi, B.Satuan,
        0.00 Qnt2, '' SatuanRoll, B.HARGA HARGAASLI,B.PPN,
     
         case when B.PPN=0 then B.Harga --+ (B.HARGA*b.nilaippn/100.00)
             when b.PPN=1 then b.HARGA
             when b.PPN=2 then b.HARGA/(1.0 + (b.nilaippn/100.00))
        end HRGEX,(((B.NDISKON+B.DISCTOT)*i.kurs)/case when b.QNT=0 then 1 else b.QNT end) DISCP,
     
        (case when B.PPN=0 then B.Harga 
             when b.PPN=1 then b.HARGA
             when b.PPN=2 then b.HARGA/(1.0 + (b.nilaippn/100.00))
        end - (( case when B.PPN=0 then B.Harga 
             when b.PPN=1 then b.HARGA
             when b.PPN=2 then b.HARGA/(1.0 + (b.nilaippn/100.00))
        end))*(b.DISCP/100)) HARGA,
        (B.NDISKON+B.DISCTOT)*i.kurs Disctotal,
        
        ( (case when B.[ppn]=(1) OR B.[ppn]=(0) then (B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT])-(((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) then ((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT])-(((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end) +
        (case when B.[ppn]=(1) then (b.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT])-(((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) then ((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT])-(((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end*(b.nilaippn/100.00)))*i.kurs TotalIDR, 
        (case when B.[ppn]=(1) OR B.[ppn]=(0) then (B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT])-(((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) then ((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT])-(((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end) 
        *i.kurs NDPP,
        (case when B.[ppn]=(1) then (B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT])-(((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) then ((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT])-(((B.[Qnt]-ISNULL(b.QntBatal,0))*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end*(b.nilaippn/100.00))
        *i.kurs NPPN, B.BYAngkut Beban, B.SubTotal + B.BYAngkut Total,
        case when i.kurs=1 then 0 else b.disctot end as disctotusd,
        case when i.kurs=1 then 0 else b.ndpp end as Ndppusd,
        case when i.kurs=1 then 0 else b.nppn end as NPPNusd,
        case when i.kurs=1 then 0 else b.subtotal end as totalusd,
        I.kurs,I.KODEVLS,
        Cast(Case when Case when I.IsOtorisasi1=1 then 1 else 0 end+
                      Case when I.IsOtorisasi2=1 then 1 else 0 end+
                      Case when I.IsOtorisasi3=1 then 1 else 0 end+
                      Case when I.IsOtorisasi4=1 then 1 else 0 end+
                      Case when I.IsOtorisasi5=1 then 1 else 0 end=I.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,ISNULL(B.QntBatal,0) QntBatal,B.TglBatal,B.UserBatal,B.Ketbatal,H.PartNumber,
             CAST(CASE WHEN ISNULL(QntBatal,0)>0 THEN 1 ELSE 0 END AS BIT) ISCLose ,c.QNT qntLPB  
From  dbPODet B 
Left Outer Join dbBarang H on H.KodeBrg=B.KodeBrg
Left Outer join DBPO I on B.NOBUKTI = I.NOBUKTI
Left Outer Join DBCUSTSUPP J on I.KODESUPP = J.KODECUSTSUPP 
Left Outer Join DBBELIDET c on c.NoPO=b.NOBUKTI and c.UrutPO=b.urut
GO
-- VwreportSPB : isi DBSMLNEW
ALTER View [dbo].[VwreportSPB]
as
select 	B.NOBUKTI, B.URUT, B.NoSPP NoSC, B.UrutSPP UrutSC, c.PartNumber KODEBRG, C.NAMABRG, '' Jns_Kertas, ''Ukr_Kertas,
        b.nosat,B.QNT, B.QNT2, B.SAT_1, B.SAT_2, B.ISI, B.NetW, B.GrossW, '' KetDetail,
        A.Tanggal,a.KodeCustSupp,D.NAMACUSTSUPP, 
		Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
		Case when A.IsOtorisasi2=1 then 1 else 0 end+
		Case when A.IsOtorisasi3=1 then 1 else 0 end+
		Case when A.IsOtorisasi4=1 then 1 else 0 end+
		Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
		else 1
		end As Bit)NeedOtorisasi, e.NoPesanan NoPOCustomer,C.PartNumber,
		case when year(A.TGLKIRIM)=1899 then null else A.TGLKIRIM END TGLKIRIM,
        CASE WHEN YEAR(A.TGLTERIMA)=1899 THEN NULL ELSE A.TGLTERIMA END TGLTERIMA 
        ,case when B.Nosat=1 then B.Qnt when B.nosat=2 then B.Qnt2 When B.nosat=3 then B.Qnt2 End * Isnull(C.berat,0) NBerat,
        case when ISnull(C.pBerat,0)=0 then 'Volume' else 'Berat' End JBerat,
        b.KodeGdg,g.NAMA namaGdg,
        case when TGLTERIMA IS not null and year(tglterima)>2000 then 'tt' when TGLTERIMA IS null Or YEAR(TGLTERIMA)<2000 then 'nt' end myTT
        ,isnull(h.QNT,0) outstanding,f.HARGA,f.NDPPRp
from	dbSPBDet B
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
Left Outer join dbSPB A on B.NoBukti = A.NoBukti
Left Outer Join DBCUSTSUPP D on A.KodeCustSupp = D.KODECUSTSUPP
left outer join DBSO E on E.NOBUKTI=b.NoSo
left outer join DBSODET F on b.NoSo=F.NOBUKTI and B.UrutSo=F.Urut
left outer join DBGUDANG g on b.KodeGdg=g.KODEGDG
left outer join dbInvoicePLDet h on h.NoSPB=a.NoBukti and h.UrutSPB=b.Urut
GO
-- VwreportInvoice : isi DBSMLNEW
ALTER View  [dbo].[VwreportInvoice]
as


Select  a.NoBukti,c.kurs,c.kodevls,b.NoBukti NoBeli ,b.kodebrg,b.qnt,b.SATUAN,
		e.namabrg,b.harga,B.DISCTOT,case when c.kodevls<>'IDR' then NDPPVLS else 0 end as NDPPVLS,
		B.NDPP,B.NPPN,B.NNET,C.KodeSupp KodeCustSupp,D.NAMACUSTSUPP,C.TANGGAL,c.NoFaktur,c.NoFakturPajak,c.TglFakturPajak,
		Cast(Case when Case when C.IsOtorisasi1=1 then 1 else 0 end+
                      Case when C.IsOtorisasi2=1 then 1 else 0 end+
                      Case when C.IsOtorisasi3=1 then 1 else 0 end+
                      Case when C.IsOtorisasi4=1 then 1 else 0 end+
                      Case when C.IsOtorisasi5=1 then 1 else 0 end=C.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,F.NOBUKTI NOPO,NoInvoice,TglInvoice
From  dbInvoiceDet a
Left Outer Join (select a.NoBukti,b.kodebrg,sum(b.qnt) qnt,b.satuan,b.harga,b.disctot, sum(ndpp) ndppvls
				,Sum(NDPPrp)NDPP,Sum(NPPNrp)NPPN,Sum(NNETrp)NNET,B.NoPO 
				from dbBeli a Left Outer Join dbBeliDet b On a.NoBukti=b.noBukti 
				Group by a.NoBukti,b.KODEBRG,b.SATUAN,b.HARGA,b.DISCTOT,B.NoPO )b On a.NoBeli=b.NoBukti
Left Outer join DBInvoice C on A.NOBUKTI = C.NOBUKTI 
Left Outer Join dbCustSupp D On C.KodeSupp=D.KodeCustSupp
left Outer Join DBBARANG E on E.KODEBRG=b.KODEBRG
Left Outer join DBPO F ON B.NoPO=F.NOBUKTI
GO
-- VwreportInvoicePenjualan : isi DBSMLNEW
ALTER View [dbo].[VwreportInvoicePenjualan]
as
select 	B.NoBukti, B.Urut, B.NoSPB, B.UrutSPB, B.KodeBrg, C.NAMABRG,x.Tanggal tglSPB,B.NoSPP,
        a.KURS,a.Valas,p.nama,
        case when b.NOSAT=1 then B.QNT else B.QNT2 end as qnt,
        case when b.NOSAT=1 then B.SAT_1 else B.SAT_2 end as satuan,
        B.HARGA, B.DiscP, B.DISCTOT, B.NDPP,B.NDPPRp,
        case when a.KodeCustSupp='CA001'then 0 else B.NPPNRp end NPPNRP, 
        case when a.KodeCustSupp='CA001'then b.nnetrp-b.nppnrp else B.NnetRp end NNETRp, B.KetDetail,a.NUangMuka,
        isnull(h.HPP,0) HPP,(isnull(h.HPP,0)*b.QNT) hpprp,b.NDPPRp-(isnull(h.HPP,0)*b.QNT) laba,
        A.Tanggal,A.KodeCustSupp,D.NAMACUSTSUPP,   
		--Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
		--Case when A.IsOtorisasi2=1 then 1 else 0 end+
		--Case when A.IsOtorisasi3=1 then 1 else 0 end+
		--Case when A.IsOtorisasi4=1 then 1 else 0 end+
		--Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
		cast(case when a.IsOtorisasi1=1 then 0
		else 1
		end As Bit) Needotorisasi,
		z.KODEKEBUN,J.Nama NamaKebun,Z.KODESLS,P.Nama NamaSls,
       --  C.KodeHdGrp
        -- ,M.NAMAHDGRP,m3.Nama namaPIC,m4.NamaSubGrp,m5.Keterangan NamaSubkategori,m6.NAMAMERK,Z.KodePF
         ISnull(M.NAMAHDGRP,'')+ISNULL(C.KodeHdGrp,'') NAMAHDGRP
         ,ISnull(m3.Nama,'')+ISNULL(m3.KODEPIC,'') namaPIC
         ,ISnull(m4.NamaSubGrp,'')+ISNULL(m4.KODESUBGRP,'') NamaSubGrp
         ,ISnull(m5.Keterangan,'')+ISNULL(m5.Urut,'') NamaSubkategori
         ,ISnull(m6.NAMAMERK,'')+ISNULL(C.KodeMerk,'') NAMAMERK,Z.KodePF,C.pAgen,
         ISnull(d.Agent,'') Agent,m7.NAMAGROUPCUSTSUPP,ISnull(d.JenisCustSupp,'') JenisCustSupp,m8.NamaJenis,
         case when isnull(C.pAgen,0)=0 then '--' else C.KodeMerk End pMERK,M9.NAMAMERK pNAMAMERK
         ,isnull(A.FlagKasir,0) Flagkasir,z.NoPesanan,Z.HARI
         ,isnull(Z.boffice,0) boffice,m10.Nama NamaBoffice, a.nopajak, a.tglfpj
from	dbInvoicePLDet B
left outer join dbBarang C on C.KodeBrg=B.KodeBrg
left outer join DBInvoicePL A on B.NoBukti = A.NoBukti
Left outer join DBCUSTSUPP D on A.KodeCustSupp = D.KODECUSTSUPP
left outer join dbSPB X on B.NoSPB = X.NoBukti
left outer join dbSPBDet h on h.NoBukti=x.NoBukti and b.UrutSPB=h.Urut
left outer join dbSPP y on y.NoBukti=x.NoSPP
left outer join DBSO z on z.NOBUKTI=B.NoSO
Left Outer join DBKEBUNCUSTSUPP J on Z.KODEKEBUN=J.KODEKEBUN and Z.KODECUST=J.KODECUSTSUPP
left outer join dbKaryawan p on p.KeyNIK=z.KODESLS
LEFT OUTER JOIN DBHDGROUP M ON C.KodeHdGrp=M.KODEHDGRP
Left Outer Join DBPICCUSTSUPP M3 on Z.KodePF=M3.KODEPIC and Z.KODECUST=m3.KODECUSTSUPP
Left Outer join dbSubGroup m4 on m4.KodeGrp=C.KODEGRP and m4.KodeSubGrp=C.KODESUBGRP and C.KodeHdGrp=m4.KodeHDGrp
left outer join DBSubGroupJnsTambah m5 on C.KODEGRP=M5.KodeGrp and C.KodeHdGrp=M5.hdgroup
and C.KODESUBGRP=m5.KodeSubGrp AND C.KODESUBKATEGORI=m5.Urut
left Outer join DBMERK m6 on C.KodeMerk=m6.KODEMERK
left outer Join DBGROUPCUSTSUPP m7 on D.Agent=m7.KODEGROUPCUSTSUPP
Left Outer Join DbJenisCustSupp m8 on D.JenisCustSupp=m8.KodeJenis
left Outer join DBMERK M9 ON case when isnull(C.pAgen,0)=0 then '--' else C.KodeMerk End=M9.KODEMERK
left outer join dbKaryawan m10 on Z.BOffice=m10.KeyNIK
where ISNULL(a.IsBatal,0)=0
GO
-- VwReportPurchasingReq : isi DBSMLNEW
ALTER view [dbo].[VwReportPurchasingReq]
as

Select 	A.NoBukti,A.Tanggal,H.KODESUPP KodeCustSupp,I.NAMACUSTSUPP, 
	B.Urut, B.KodeBrg, H.NamaBrg, B.Qnt,isnull(C.QNT,0) QNTPO, B.NoSat, B.Isi Isi, B.Sat Satuan,B.Keterangan,
	 Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,isnull(B.Qntbatal,0) qntbatal,B.Tglbatal,b.UserBatal,h.PartNumber,a.IsBatal batalpr,b.Isbatal batalbrg

From dbPPL A
Left Outer join dbPPLDet B on B.NoBukti=a.NoBukti
Left Outer join dbBarang H on H.KodeBrg=b.KodeBrg
Left Outer Join DBCUSTSUPP I on H.KODESUPP=i.KODECUSTSUPP
left outer join DBPODET c on c.NoPPL=b.Nobukti and c.UrutPPL=b.urut
GO
-- VwReportSO : isi DBSMLNEW
ALTER View [dbo].[VwReportSO]
as
Select 	A.NoBukti, A.NoSPB, B.UrutSPB,A.TANGGAL,I.KODECUSTSUPP,I.NAMACUSTSUPP,
	A.NoBukti+right('0000000000'+cast(B.Urut as varchar(10)),10) NoBuktiUrut,
        B.Urut, B.KodeBrg, H.NamaBrg,
        case when b.NOSAT=1 then B.Qnt-ISNULL(B.QNTBATAL,0) else b.QNT2-ISNULL(B.QNTBATAL,0) end as qntjual
        ,b.QNT - ISNULL(B.QNTBATAL,0) QNT, B.NoSat, B.Isi, 
        case when  isnull(B.satx,'')='' then B.SATUAN else B.satx End Satuan ,
        B.Qnt2-ISNULL(B.QNTBATAL,0) QNT2, H.Sat2 Satuan2,  
        (case when B.PPN=0 then B.Harga 
             when b.PPN=1 then b.HARGA
             when b.PPN=2 then b.HARGA/(1.0 + (b.nilaippn/100.00))
        end - (( case when B.PPN=0 then B.Harga 
             when b.PPN=1 then b.HARGA
             when b.PPN=2 then b.HARGA/(1.0 + (b.nilaippn/100.00))
        end))*(DISCP1/100)) HARGA,
        B.DiscP1,B.DiscTot,B.NDPP,a.KURS,a.KODEVLS,B.NDPPRp,B.NPPNRp,B.NNETRp,
        Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.NoPesanan,A.KODEKEBUN,J.Nama NamaKebun,A.KODESLS,K.Nama NamaSls,
         L.KodeHdGrp
         ,ISnull(M.NAMAHDGRP,'')+ISNULL(M.KODEHDGRP,'') NAMAHDGRP
         ,ISnull(m3.Nama,'')+ISNULL(A.KodePF,'') namaPIC
         ,ISnull(m4.NamaSubGrp,'')+ISNULL(m4.KODESUBGRP,'') NamaSubGrp
         ,ISnull(m5.Keterangan,'')+ISNULL(m5.Urut,'') NamaSubkategori
         ,ISnull(m6.NAMAMERK,'')+ISNULL(l.KodeMerk,'') NAMAMERK,A.KodePF,l.PartNumber,
         (case when B.[ppn]=(1) OR B.[ppn]=(0) then (B.[Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) then (([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end*B.[Kurs]) NDPPRPZX,
((case when B.[ppn]=(1) AND [ppnbrg]<>(1) then ([Qnt2]-isnull(B.QNTBATAL,0))*(([HARGA]-isnull(B.QNTBATAL,0))-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) AND [ppnbrg]<>(1) then (([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end*(b.nilaippn/100.00))*B.[Kurs]) NPPNRPX         ,
((case when B.[ppn]=(1) OR B.[ppn]=(0) then ([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) then (([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end+case when B.[ppn]=(1) AND [ppnbrg]<>(1) then ([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) AND [ppnbrg]<>(1) then (([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end*(b.nilaippn/100.00))*B.[Kurs]) NNETRPZX,
(case when B.[ppn]=(1) OR B.[ppn]=(0) then ([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) then (([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end) DPPVLSZX,

((case when B.[ppn]=(1) AND [ppnbrg]<>(1) then ([Qnt2]-isnull(B.QNTBATAL,0))*(([HARGA]-isnull(B.QNTBATAL,0))-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) AND [ppnbrg]<>(1) then (([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end*(b.nilaippn/100.00))) NPPNRPVLSX    ,
((case when B.[ppn]=(1) OR B.[ppn]=(0) then ([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) then (([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end+case when B.[ppn]=(1) AND [ppnbrg]<>(1) then ([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100) when B.[ppn]=(2) AND [ppnbrg]<>(1) then (([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT])-((([Qnt2]-isnull(B.QNTBATAL,0))*([HARGA]-[DISCTOT]))*B.[DISC])/(100))/((1.0 + (b.nilaippn/100.00))) else (0) end*(b.nilaippn/100.00))) NNETRPVLSZX,
isnull(L.pAgen,0) pAgen,case when year(A.tglpo) < 2000 then null else A.TglPO End TglPO,isnull(A.boffice,0) boffice,m7.Nama NamaBoffice,B.NilaiPPN
,isnull(x.qntspb,0) outstanding
From dbSO A
Left Outer join dbSODet B on B.NoBukti=a.NoBukti
Left Outer Join vwSatuanBrg H on H.KodeBrg=B.KodeBrg --and H.NoSat=B.NoSat
Left Outer join DBCUSTSUPP I on a.KODECUST = I.KODECUSTSUPP
Left Outer join DBKEBUNCUSTSUPP J on A.KODEKEBUN=J.KODEKEBUN and A.KODECUST=J.KODECUSTSUPP
Left Outer join dbKaryawan K on A.KODESLS=K.KeyNIK
Left Outer join DBBARANG L ON B.KODEBRG=L.KODEBRG
LEFT OUTER JOIN DBHDGROUP M ON L.KodeHdGrp=M.KODEHDGRP
Left Outer Join DBPICCUSTSUPP M3 on A.KodePF=M3.KODEPIC and A.KODECUST=m3.KODECUSTSUPP
Left Outer join dbSubGroup m4 on m4.KodeGrp=l.KODEGRP and m4.KodeSubGrp=l.KODESUBGRP and L.KodeHdGrp=m4.KodeHDGrp
left outer join DBSubGroupJnsTambah m5 on L.KODEGRP=M5.KodeGrp and L.KodeHdGrp=M5.hdgroup
and L.KODESUBGRP=m5.KodeSubGrp AND l.KODESUBKATEGORI=m5.Urut
left Outer join DBMERK m6 on l.KodeMerk=m6.KODEMERK
left outer join dbKaryawan m7 on a.BOffice=m7.KeyNIK
left outer join (select noso,urutso,sum(qnt) qntspb from dbSPBDet group by noso,urutso) x on x.noso=b.NOBUKTI and x.UrutSo=b.urut
GO
-- vwDetailKoreksiWEb : isi DBSMLNEW
ALTER view [dbo].[vwDetailKoreksiWEb]
as
Select month(A.tanggal) Bulan,YEAR(A.TANGGAL) Tahun,A.Nobukti,A.tanggal,A.note,A.ISCetak,b.kodebrg,C.namaBrg,c.KodeGrp,c.KodeSubGrp,
       b.SaldoComp,b.QntOpname,b.Selisih,
       A.Kodegdg, b.Qntdb,B.QntCr, c.Sat1 Satuan,b.nosat,B.isi,b.Harga,b.urut,d.nama NamaGDG,
       (b.qntdb*b.HPP)-(b.qntcr*b.HPP) as Total,
       (b.qntdb)*b.hpp  HrgAdi,
       (b.qntcr)*b.hpp HrgAdo, 
       b.HPP,
       Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                      Case when A.IsOtorisasi2=1 then 1 else 0 end+
                      Case when A.IsOtorisasi3=1 then 1 else 0 end+
                      Case when A.IsOtorisasi4=1 then 1 else 0 end+
                      Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                 else 1
            end As Bit) NeedOtorisasi,A.NOURUT ,
            B.QntOpname - B.SaldoComp SLS,B.Lokasi,B.Rak,case when a.NOBUKTI like '%OPN%' then 'OPN' else 'KRS' end Tipe
From dbKoreksi A 
     left outer join dbKoreksiDet B on b.nobukti=a.nobukti 
     left outer join dbBarang C on c.kodebrg=b.kodebrg 
     left outer join dbGudang D on d.kodegdg=A.kodegdg 
	--WHERE month(A.tanggal)=1 AND YEAR(A.TANGGAL)=2023
	--where a.NOBUKTI like '%OPN%'
GO
-- ReportHisSerahSampleN : isi DBSMLNEW
ALTER Procedure [dbo].[ReportHisSerahSampleN]
--declare
@TglAwal DateTime,
@TglAkhir Datetime,
@IDuser varchar(20)=''


--select @TglAkhir='01/01/2018',@TglAwal='01/31/2018'
as

if not exists(select ID from DBCUSTOMIZE where Tipe=408135)
begin
Select A.nobukti, a.NoUrut, a.Tanggal,  A.Note Keterangan,A.KODECUSTSUPP,B.NAMACUSTSUPP,A.KODESLS,C.Nama NamaSls,E.KODEBRG,F.NAMABRG,
case when e.NOSAT=1 then e.qnt when e.NOSAT=2 then e.QNT2  when e.NOSAT=3 then e.QNT2 end Qnt,
case when e.nosat=1 then e.SAT_1 when e.NOSAT=2 then e.SAT_2 when e.NOSAT=3 then e.SAT_2 end sat ,'SSP' Tipe
,G.NOBUKTI NOSO,G.qNT QNTSO,H.NOBUKTI NORSS,H.Qnt1 QNTRSS,B1.Nobukti NOBBS,B1.Qnt1 QNTBBS,
 case when e.NOSAT=1 then e.qnt when e.NOSAT=2 then e.QNT2  when e.NOSAT=3 then e.QNT2 end -
 ISNULL(G.qNT,0) - ISNULL(H.Qnt1,0) - ISNULL(B1.Qnt1,0) sISA
from DBSERAHSAMPLE a
Left Outer Join DBCUSTSUPP B on A.KODECUSTSUPP=B.KODECUSTSUPP
left outer join dbKaryawan C on A.KODESLS=C.KeyNIK
left outer join DBSERAHSAMPLEDET E on a.NOBUKTI=E.NOBUKTI
left outer join DBPRSAMPLE E1 on E.NOPRSAMPLE=e1.NOBUKTI
left outer join DBBARANG f on E.KODEBRG=f.KODEBRG
left outer join (select C.NOBUKTI,C.KodeBrg,C.QNT qNT,C.QNT2 qNT2,C.NOSAT ,B.RefPR
				from   DBSODET A
				LEFT OUTER JOIN DBSO B ON A.NOBUKTI=B.NOBUKTI	
				LEFT OUTER JOIN dbSPBDet C ON A.NOBUKTI=C.NoSo AND A.URUT=C.UrutSo			
				) G ON E1.RefPR=G.RefPR and g.KODEBRG=e.KODEBRG
left outer join (select A.NoSerahsample,A.UrutSerahsample,A.QNT Qnt1,A.NOBUKTI,
				 A.QNT2 Qnt2 
				 from DBRSERAHSAMPLEDET A				
				 ) H on E.NOBUKTI=H.NoSerahsample and E.URUT=H.UrutSerahsample

 left outer join (select NoRSerahSample,UrutRSerahSample,Qnt1,Qnt2,Nobukti
				  from DBBEBANSAMPLEDET 
				  ) B1 on A.NOBUKTI=b1.NoRSerahSample and e.URUT=B1.UrutRSerahSample
where a.TANGGAL between @TglAwal and @TglAkhir				  

end
else
begin
Select A.nobukti, a.NoUrut, a.Tanggal,  A.Note Keterangan,A.KODECUSTSUPP,B.NAMACUSTSUPP,A.KODESLS,C.Nama NamaSls,E.KODEBRG,F.NAMABRG,
case when e.NOSAT=1 then e.qnt when e.NOSAT=2 then e.QNT2  when e.NOSAT=3 then e.QNT2 end Qnt,
case when e.nosat=1 then e.SAT_1 when e.NOSAT=2 then e.SAT_2 when e.NOSAT=3 then e.SAT_2 end sat ,'SSP' Tipe
,G.NOBUKTI NOSO,G.qNT QNTSO,H.NOBUKTI NORSS,H.Qnt1 QNTRSS,B1.Nobukti NOBBS,B1.Qnt1 QNTBBS,
 case when e.NOSAT=1 then e.qnt when e.NOSAT=2 then e.QNT2  when e.NOSAT=3 then e.QNT2 end -
 ISNULL(G.qNT,0) - ISNULL(H.Qnt1,0) - ISNULL(B1.Qnt1,0) sISA
from DBSERAHSAMPLE a
Left Outer Join DBCUSTSUPP B on A.KODECUSTSUPP=B.KODECUSTSUPP
left outer join dbKaryawan C on A.KODESLS=C.KeyNIK
left outer join DBSERAHSAMPLEDET E on a.NOBUKTI=E.NOBUKTI
left outer join DBPRSAMPLE E1 on E.NOPRSAMPLE=e1.NOBUKTI
left outer join DBBARANG f on E.KODEBRG=f.KODEBRG
left outer join (select C.NOBUKTI,C.KodeBrg,C.QNT qNT,C.QNT2 qNT2,C.NOSAT ,B.RefPR
				from   DBSODET A
				LEFT OUTER JOIN DBSO B ON A.NOBUKTI=B.NOBUKTI	
				LEFT OUTER JOIN dbSPBDet C ON A.NOBUKTI=C.NoSo AND A.URUT=C.UrutSo			
				) G ON E1.RefPR=G.RefPR and g.KODEBRG=e.KODEBRG
left outer join (select A.NoSerahsample,A.UrutSerahsample,A.QNT Qnt1,A.NOBUKTI,
				 A.QNT2 Qnt2 
				 from DBRSERAHSAMPLEDET A				
				 ) H on E.NOBUKTI=H.NoSerahsample and E.URUT=H.UrutSerahsample

 left outer join (select NoRSerahSample,UrutRSerahSample,Qnt1,Qnt2,Nobukti
				  from DBBEBANSAMPLEDET 
				  ) B1 on A.NOBUKTI=b1.NoRSerahSample and e.URUT=B1.UrutRSerahSample
where 	A.KODECUSTSUPP in (select ID from DBCUSTOMIZE where IDUSER=@IDuser)			  
End
GO
-- sp_NerajaLajur : isi DBSMLNEW
ALTER PROCEDURE [dbo].[sp_NerajaLajur] 
@Masuk varchar(1),
@Bulan int,
@Tahun int,
@Devisi varchar(10),
@IdUser varchar(15)
--select @masuk='D',@bulan=5,@tahun=2008,@iduser='sa',@devisi='01'
AS
 Set @Devisi=Case when @Devisi in ('-','') then '%' else @Devisi end 
 select A.Perkiraan,a.keterangan,
 case when a.DK=0 then b.AwalDRp 
      else 0
 end as SaldoAwD,
 case when a.DK=1 then b.AwalKRp 
      else 0
 end as SaldoAwk, MD=b.MDRp, MK=b.MKRp, JPD=b.JPDRp, JPK=b.JPKRp, RLD=b.RLDRp, RLK=b.RLKRp,
 case when a.DK=0 then b.AkhirDRp
      else 0
 end as SaldoAkD,
 case when a.DK=1 then b.AkhirKRp
      else 0
 end as SaldoAkK,a.Kelompok
 from dbPerkiraan A
      left outer join DBNERACA b on b.Perkiraan=A.Perkiraan
 where
 a.Perkiraan in (select perkiraan from dbAksesPerkiraan where userid=@IdUser) and 
 (b.AwalDRp<>0 or b.AwalKRp<>0 or b.MDRp<>0 or b.MKRp<>0 or b.JPDRp<>0 or b.JPKRp<>0 or b.RLDRp<>0 or b.RLKRp<>0 or
  b.AkhirDRp<>0 or b.AkhirKRp<>0) and b.Devisi like @Devisi
  and b.Bulan=@Bulan and b.Tahun=@Tahun
 order by a.perkiraan
GO
-- Sp_ReportBukuTambahan : isi DBSMLNEW
ALTER Procedure [dbo].[Sp_ReportBukuTambahan] 
--declare
@awal varchar(25),
@akhir varchar(25),
@tglawal datetime,
@tglakhir datetime,
@devisi varchar(15),
@IdUser varchar(15),
@penutup varchar(1),
@Ketx tinyint=0
As
Declare @Mulai Datetime
--Select @awal='72600',@akhir='72600',@tglawal='03-01-2024',@tglakhir='03-31-2024',@devisi='01',@IdUser='SA',@penutup='y',@Ketx=0

IF @penutup='Y' 
begin

Select @Mulai=DATEADD(dd,-(DAY(@tglawal)-1),@tglawal);
With Transaksi (Nobukti,Tanggal, Note, Keterangan, Perkiraan, Lawan, DK, Debet, DebetRp, Valas, Kurs, Kredit, KreditRP, SaldoAkhir, SaldoAkhirD,
      Devisi, Nourut, NoACC, Nama, Urut,ketdetail, Jenis) AS
(
Select 'SALDO AWAL' Nobukti,DATEADD(DD,-1,@tglawal) Tanggal,''Note, '' Keterangan, ''Perkiraan, '' Lawan, 
       A.DK, 0.00 Debet, 0.00 DebetRp, '', 1.00 Kurs, 0.00 Kredit, 0.00 KreditD,
       Case when B.DK=0 then A.AwalDRp 
            when B.DK=1 then A.AwalKRp
            else 0
       end SaldoAkhir,
       Case when B.DK=0 then A.AwalD
            when B.DK=1 then A.AwalK
            else 0
       end SaldoAkhirD, A.Devisi, '' NoUrut, B.Perkiraan NoACC, /*B.Keterangan*/'' Nama, 0 Urut,'' ketdetail, '' Jenis
From DBNERACA A
     Left Outer Join DBPERKIRAAN B on B.Perkiraan=A.Perkiraan
where (A.Perkiraan between @awal and @akhir) and A.Devisi like @devisi and A.Bulan=MONTH(@tglawal) and A.Tahun=YEAR(@tglawal)
union All
Select 'SALDO AWAL' Nobukti, DATEADD(DD,-1,@tglawal) Tanggal,''Note, '' Keterangan,
       ''Perkiraan,  '' Lawan,
       A.DK, 0.00 Debet, 0.00 DebetRp, '', 1.00 Kurs, 0.00 Kredit, 0.00 KreditD,
       SUM(Case when Datepart(DD,@tglawal)=1 then 0
                else
       Case when A.DK=0 and B.PERKIRAAN=A.Perkiraan then B.DEBETRP
            when A.DK=1 and B.PERKIRAAN=A.Perkiraan then -B.DEBETRP
            when A.DK=0 and B.LAWAN=A.Perkiraan then -B.DEBETRP
            when A.DK=1 and B.LAWAN=A.Perkiraan then B.DEBETRP
            else 0
       end end) SaldoAkhir,
       SUM(Case when Datepart(DD,@tglawal)=1 then 0
                else
       Case when A.DK=0 and B.PERKIRAAN=A.Perkiraan and b.VALAS<>'IDR' then B.DEBET 
            when A.DK=1 and B.PERKIRAAN=A.Perkiraan and b.VALAS<>'IDR' then -B.DEBET 
            when A.DK=0 and B.LAWAN=A.Perkiraan and b.VALAS<>'IDR' then -B.DEBET  
            when A.DK=1 and B.LAWAN=A.Perkiraan and b.VALAS<>'IDR' then B.DEBET 
            else 0
       end end) SaldoAkhirD, B.Devisi, '' NoUrut, A.Perkiraan NoACC, /*A.Keterangan*/ ''Nama, 0 Urut,'' Ketdetail, '' Jenis
From DBPERKIRAAN A
     left Outer join vwTransaksi B on B.PERKIRAAN=A.Perkiraan or B.LAWAN=A.Perkiraan
where (B.Tanggal >=@Mulai and B.Tanggal<@tglawal) and B.Devisi like @devisi and (A.Perkiraan between @awal and @akhir) 
Group by A.Perkiraan, A.DK, B.DEVISI, B.Jenis --, A.Keterangan,B.ketdetail
union all
Select B.Nobukti, B.TANGGAL,B.NOTE, B.KETERANGAN, B.PERKIRAAN,
       Case when B.PERKIRAAN=A.Perkiraan then B.LAWAN 
            when B.LAWAN=A.Perkiraan then B.PERKIRAAN
            else ''
       end LAWAN,
       A.DK, 
       Case when B.PERKIRAAN=A.Perkiraan and b.VALAS<>'IDR' then B.DEBET  else 0 end Debet, 
       Case when B.PERKIRAAN=A.Perkiraan then B.DEBETRP else 0 end DebetRp, 
       B.VALAS, B.KURS, 
       Case when B.LAWAN=A.Perkiraan and b.VALAS<>'IDR'  then B.DEBET  else 0 end Kredit, 
       Case when B.LAWAN=A.Perkiraan then B.DEBETRP else 0 end KreditRp,
       (
       Case when A.DK=0 and B.PERKIRAAN=A.Perkiraan then B.DEBETRP
            when A.DK=1 and B.PERKIRAAN=A.Perkiraan then -B.DEBETRP
            when A.DK=0 and B.LAWAN=A.Perkiraan then -B.DEBETRP
            when A.DK=1 and B.LAWAN=A.Perkiraan then B.DEBETRP
            else 0
       end) SaldoAkhir,
       (
       Case when A.DK=0 and B.PERKIRAAN=A.Perkiraan  and b.VALAS<>'IDR'then B.DEBET 
            when A.DK=1 and B.PERKIRAAN=A.Perkiraan and b.VALAS<>'IDR'  then -B.DEBET 
            when A.DK=0 and B.LAWAN=A.Perkiraan  and b.VALAS<>'IDR' then -B.DEBET 
            when A.DK=1 and B.LAWAN=A.Perkiraan and b.VALAS<>'IDR' then B.DEBET 
            else 0
       end) SaldoAkhirD, B.Devisi, B.NOURUT, A.Perkiraan NoACC, A.Keterangan Nama, B.Urut,B.Ketdetail, B.Jenis
From DBPERKIRAAN A
     left Outer join vwTransaksi B on B.PERKIRAAN=A.Perkiraan or B.LAWAN=A.Perkiraan
where (B.Tanggal between @tglawal and @tglakhir) and B.Devisi like @devisi and (A.Perkiraan between @awal and @akhir))

Select Nobukti,Tanggal, Note,case when @Ketx=0 then rtrim(KETERANGAN)+ CASE WHEN Nobukti LIKE '%PBG%' THEN dbo.DataDetailitem(nobukti) ELSE '' END else rtrim(Ketdetail) end  Keterangan, Perkiraan, Lawan, DK, 
      Debet DebetD, DebetRp Debet, Valas, Kurs, Kredit kreditD, KreditRp kredit, 
      Sum(SaldoAkhir) SaldoAkhir, Sum(SaldoAkhirD) SaldoAkhirD,
       Devisi, Nourut, NoACC,(select Keterangan from DBPERKIRAAN where Perkiraan=A.NoACC) Nama, urut,''Ketdetail, Jenis
From Transaksi A
where (A.NoACC between @awal and @akhir) and (A.Tanggal between DATEADD(DD,-1,@Mulai) and @tglakhir)
Group by Nobukti,Tanggal, Note, Keterangan, Perkiraan, Lawan, DK, Debet, DebetRp, Valas, Kurs, Kredit, KreditRP,
         Devisi, Nourut, NoACC, Nama, Urut ,ketdetail, Jenis
Having Sum(SaldoAkhir)<>0 Or Sum(SaldoAkhirD) <>0
Order by  NoACC,Tanggal,NoUrut,Nobukti, Urut

end
else               ---------- ELSE
begin

Select @Mulai=DATEADD(dd,-(DAY(@tglawal)-1),@tglawal);
With Transaksi (Nobukti,Tanggal, Note, Keterangan, Perkiraan, Lawan, DK, Debet, DebetRp, Valas, Kurs, Kredit, KreditRP, SaldoAkhir, SaldoAkhirD,
      Devisi, Nourut, NoACC, Nama, Urut,ketdetail, Jenis) AS
(
Select 'SALDO AWAL' Nobukti,DATEADD(DD,-1,@tglawal) Tanggal,''Note, '' Keterangan, ''Perkiraan, '' Lawan, 
       A.DK, 0.00 Debet, 0.00 DebetRp, '', 1.00 Kurs, 0.00 Kredit, 0.00 KreditD,
       Case when B.DK=0 then sum(case when a.bulan=1 then a.awaldrp else 0 end)+
                             sum(case when MONTH(@tglawal)=a.bulan then 0 else a.MDRp-a.MKRp+a.JPDRp-a.JPKRp end)
            when B.DK=1 then sum(case when a.bulan=1 then a.AwalKRp else 0 end)+
                             sum(case when MONTH(@tglawal)=a.bulan then 0 else a.MKRp-a.MDRp+a.JPKRp-a.JPDRp end)
            else 0
       end SaldoAkhir,
       Case when B.DK=0 then sum(case when a.bulan=1 then a.awald else 0 end)+
                             sum(case when MONTH(@tglawal)=a.bulan then 0 else a.MD-a.MK+a.JPD-a.JPK end)
            when B.DK=1 then sum(case when a.bulan=1 then a.AwalK else 0 end)+
                             sum(case when MONTH(@tglawal)=a.bulan then 0 else a.MK-a.MD+a.JPK-a.JPD end)
            else 0
       end SaldoAkhirD, A.Devisi, '' NoUrut, B.Perkiraan NoACC, B.Keterangan Nama, 0 Urut,'' ketdetail, '' Jenis
From DBNERACA A
     Left Outer Join DBPERKIRAAN B on B.Perkiraan=A.Perkiraan
where (A.Perkiraan between @awal and @akhir) and A.Devisi like @devisi and A.Bulan<=MONTH(@tglawal) and A.Tahun=YEAR(@tglawal)
group by a.DK,b.DK,a.Devisi,b.Perkiraan,b.Keterangan
union All
Select 'SALDO AWAL' Nobukti, DATEADD(DD,-1,@tglawal) Tanggal,''Note, '' Keterangan,
       ''Perkiraan,  '' Lawan,
       A.DK, 0.00 Debet, 0.00 DebetRp, '', 1.00 Kurs, 0.00 Kredit, 0.00 KreditD,
       SUM(Case when Datepart(DD,@tglawal)=1 then 0
                else
       Case when A.DK=0 and B.PERKIRAAN=A.Perkiraan then B.DEBETRP
            when A.DK=1 and B.PERKIRAAN=A.Perkiraan then -B.DEBETRP
            when A.DK=0 and B.LAWAN=A.Perkiraan then -B.DEBETRP
            when A.DK=1 and B.LAWAN=A.Perkiraan then B.DEBETRP
            else 0
       end end) SaldoAkhir,
       SUM(Case when Datepart(DD,@tglawal)=1 then 0
                else
       Case when A.DK=0 and B.PERKIRAAN=A.Perkiraan and b.VALAS<>'IDR' then B.DEBET 
            when A.DK=1 and B.PERKIRAAN=A.Perkiraan and b.VALAS<>'IDR' then -B.DEBET 
            when A.DK=0 and B.LAWAN=A.Perkiraan and b.VALAS<>'IDR' then -B.DEBET  
            when A.DK=1 and B.LAWAN=A.Perkiraan and b.VALAS<>'IDR' then B.DEBET 
            else 0
       end end) SaldoAkhirD, B.Devisi, '' NoUrut, A.Perkiraan NoACC, A.Keterangan Nama, 0 Urut,b.ketdetail, B.Jenis
From DBPERKIRAAN A
     left Outer join vwTransaksi B on B.PERKIRAAN=A.Perkiraan or B.LAWAN=A.Perkiraan
where (B.Tanggal >=@Mulai and B.Tanggal<@tglawal) and B.Devisi like @devisi and (A.Perkiraan between @awal and @akhir) 
      and b.TIPETRANS<>'R/L'
Group by A.Perkiraan, A.DK, B.DEVISI, A.Keterangan,B.ketdetail, B.Jenis
union all
Select B.Nobukti, B.TANGGAL,B.NOTE, B.KETERANGAN+'XXX',
       B.PERKIRAAN,  
       Case when B.PERKIRAAN=A.Perkiraan then B.LAWAN 
            when B.LAWAN=A.Perkiraan then B.PERKIRAAN
            else ''
       end LAWAN,
       A.DK, 
       Case when B.PERKIRAAN=A.Perkiraan and b.VALAS<>'IDR' then B.DEBET  else 0 end Debet, 
       Case when B.PERKIRAAN=A.Perkiraan then B.DEBETRP else 0 end DebetRp, 
       B.VALAS, B.KURS, 
       Case when B.LAWAN=A.Perkiraan and b.VALAS<>'IDR'  then B.DEBET  else 0 end Kredit, 
       Case when B.LAWAN=A.Perkiraan then B.DEBETRP else 0 end KreditRp,
       (
       Case when A.DK=0 and B.PERKIRAAN=A.Perkiraan then B.DEBETRP
            when A.DK=1 and B.PERKIRAAN=A.Perkiraan then -B.DEBETRP
            when A.DK=0 and B.LAWAN=A.Perkiraan then -B.DEBETRP
            when A.DK=1 and B.LAWAN=A.Perkiraan then B.DEBETRP
            else 0
       end) SaldoAkhir,
       (
       Case when A.DK=0 and B.PERKIRAAN=A.Perkiraan  and b.VALAS<>'IDR'then B.DEBET 
            when A.DK=1 and B.PERKIRAAN=A.Perkiraan and b.VALAS<>'IDR'  then -B.DEBET 
            when A.DK=0 and B.LAWAN=A.Perkiraan  and b.VALAS<>'IDR' then -B.DEBET 
            when A.DK=1 and B.LAWAN=A.Perkiraan and b.VALAS<>'IDR' then B.DEBET 
            else 0
       end) SaldoAkhirD, B.Devisi, B.NOURUT, A.Perkiraan NoACC, A.Keterangan Nama, B.Urut,b.ketdetail, B.Jenis
From DBPERKIRAAN A
     left Outer join vwTransaksi B on B.PERKIRAAN=A.Perkiraan or B.LAWAN=A.Perkiraan
where (B.Tanggal between @tglawal and @tglakhir) and B.Devisi like @devisi and (A.Perkiraan between @awal and @akhir)and b.TIPETRANS<>'R/L')
       
       
Select Nobukti,Tanggal, Note, 
case when @Ketx=0 then RTRIM(keterangan) + CASE WHEN Nobukti LIKE '%PBG%' THEN dbo.DataDetailitem(nobukti) ELSE '' END else rtrim(Ketdetail) end Keterangan, 
Perkiraan, Lawan, DK, 
      Debet DebetD, DebetRp Debet, Valas, Kurs, Kredit kreditD, KreditRp kredit, 
      Sum(SaldoAkhir) SaldoAkhir, Sum(SaldoAkhirD) SaldoAkhirD,
       Devisi, Nourut, NoACC,(select Keterangan from DBPERKIRAAN where Perkiraan=A.NoACC)  Nama, urut,''ketdetail, Jenis
From Transaksi A
where (A.NoACC between @awal and @akhir) and (A.Tanggal between DATEADD(DD,-1,@Mulai) and @tglakhir)
        
Group by Nobukti,Tanggal, Note, Keterangan, Perkiraan, Lawan, DK, Debet, DebetRp, Valas, Kurs, Kredit, KreditRP,
         Devisi, Nourut, NoACC, Nama, Urut,ketdetail, Jenis
Having Sum(SaldoAkhir)<>0 Or Sum(SaldoAkhirD) <>0
Order by NoACC,Tanggal,  NoUrut,Nobukti, Urut

end



--SELECT * FROM DBPenyerahanBhn
GO
-- Sp_ReportCosting : isi DBSMLNEW
ALTER Procedure [dbo].[Sp_ReportCosting] 
--DECLARE
@Tipe smallint=0,
@KodeCost varchar(20)='',
@KodeSubCost varchar(20)='',
@Bulan int=0,
@Tahun int=0
as
--SELECT @KodeCost='KEND',@KodeSubCost='-',@Tipe=1,@Tahun=2023,@Bulan=5

if @KodeCost = '-' set @KodeCost = '%%'
if @KodeSubCost = '-' set @KodeSubCost = '%%'
if @KodeCost = 'KEND' set @KodeCost='KEND'
if @Tipe = 0
  begin	
  /*
	select KodePerk, KodeCost, KodeSubCost, NIK, Nama, SUM(Saldo) Saldo,KETERANGAN
	from VwCost
	where KodeCost = @KodeCost and KodeSubCost like @KodeSubCost and MONTH(Tanggal) = @Bulan and YEAR(Tanggal) = @Tahun
	group by KodePerk, KodeCost, KodeSubCost, NIK, Nama,KETERANGAN
	*/
	select KodePerk, KodeCost, KodeSubCost, NIK, Nama, Saldo,KETERANGAN,note,tanggal,NOBUKTI
	from VwCost
	where KodeCost = @KodeCost and KodeSubCost like @KodeSubCost and MONTH(Tanggal) = @Bulan and YEAR(Tanggal) = @Tahun
	--group by KodePerk, KodeCost, KodeSubCost, NIK, Nama,KETERANGAN
  end
else
  begin
  /*
	select KodePerk, KodeCost, KodeSubCost, NIK, Nama, keterangan,
			ISNULL(case when MONTH(Tanggal)=1 then SUM(Saldo) end,0) Saldo1,
			ISNULL(case when MONTH(Tanggal)=2 then SUM(Saldo) end,0) Saldo2,
			ISNULL(case when MONTH(Tanggal)=3 then SUM(Saldo) end,0) Saldo3,
			ISNULL(case when MONTH(Tanggal)=4 then SUM(Saldo) end,0) Saldo4,
			ISNULL(case when MONTH(Tanggal)=5 then SUM(Saldo) end,0) Saldo5,
			ISNULL(case when MONTH(Tanggal)=6 then SUM(Saldo) end,0) Saldo6,
			ISNULL(case when MONTH(Tanggal)=7 then SUM(Saldo) end,0) Saldo7,
			ISNULL(case when MONTH(Tanggal)=8 then SUM(Saldo) end,0) Saldo8,
			ISNULL(case when MONTH(Tanggal)=9 then SUM(Saldo) end,0) Saldo9,
			ISNULL(case when MONTH(Tanggal)=10 then SUM(Saldo) end,0) Saldo10,
			ISNULL(case when MONTH(Tanggal)=11 then SUM(Saldo) end,0) Saldo11,
			ISNULL(case when MONTH(Tanggal)=12 then SUM(Saldo) end,0) Saldo12,
			ISNULL(case when MONTH(Tanggal)=1 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=2 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=3 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=4 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=5 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=6 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=7 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=8 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=9 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=10 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=11 then SUM(Saldo) end,0)+
			ISNULL(case when MONTH(Tanggal)=12 then SUM(Saldo) end,0) as TOTAL
	from VwCost
	where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun	
	group by MONTH(Tanggal), KodePerk, KodeCost, KodeSubCost, NIK, Nama,KETERANGAN
	*/
	
	select a.KodePerk, a.KodeCost, a.KodeSubCost, NIK, Nama, keterangan,
        b.saldo1,c.saldo2,d.saldo3,e.saldo4,f.saldo5,g.saldo6,h.saldo7,i.saldo8,j.saldo9,k.saldo10,l.saldo11,m.saldo12
        , total--,A.NOBUKTI
	from VwCost a
	left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo1
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=1	
	    group by KodePerk, KodeCost,KodeSubCost) b on b.KodeCost=a.KodeCost and b.KodePerk=a.KodePerk and b.KodeSubCost=a.KodeSubCost
	left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo2
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=2	
	    group by KodePerk, KodeCost,KodeSubCost) C on c.KodeCost=a.KodeCost and c.KodePerk=a.KodePerk and c.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo3
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=3	
	    group by KodePerk, KodeCost,KodeSubCost) D on d.KodeCost=a.KodeCost and d.KodePerk=a.KodePerk and d.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo4
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=4	
	    group by KodePerk, KodeCost,KodeSubCost) E on e.KodeCost=a.KodeCost and e.KodePerk=a.KodePerk and e.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo5
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=5	
	    group by KodePerk, KodeCost,KodeSubCost) F on f.KodeCost=a.KodeCost and f.KodePerk=a.KodePerk and f.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo6
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=6	
	    group by KodePerk, KodeCost,KodeSubCost) g on g.KodeCost=a.KodeCost and g.KodePerk=a.KodePerk and g.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo7
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=7	
	    group by KodePerk, KodeCost,KodeSubCost) H on h.KodeCost=a.KodeCost and h.KodePerk=a.KodePerk and h.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo8
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=8	
	    group by KodePerk, KodeCost,KodeSubCost) I on i.KodeCost=a.KodeCost and i.KodePerk=a.KodePerk and i.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo9
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=9	
	    group by KodePerk, KodeCost,KodeSubCost) J on j.KodeCost=a.KodeCost and j.KodePerk=a.KodePerk and j.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo10
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=10	
	    group by KodePerk, KodeCost,KodeSubCost) K on k.KodeCost=a.KodeCost and k.KodePerk=a.KodePerk and k.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo11
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=11	
	    group by KodePerk, KodeCost,KodeSubCost) L on l.KodeCost=a.KodeCost and l.KodePerk=a.KodePerk and l.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) saldo12
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun and MONTH(tanggal)=12	
	    group by KodePerk, KodeCost,KodeSubCost) M on m.KodeCost=a.KodeCost and m.KodePerk=a.KodePerk and m.KodeSubCost=a.KodeSubCost
    left outer join (select KodePerk,KodeCost,KodeSubCost,isnull(SUM(Saldo),0) total
        from VwCost 
	    where KodeCost like @KodeCost and KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun 	
	    group by KodePerk, KodeCost,KodeSubCost) N on n.KodeCost=a.KodeCost and n.KodePerk=a.KodePerk and n.KodeSubCost=a.KodeSubCost

	where a.KodeCost like @KodeCost and a.KodeSubCost like @KodeSubCost and YEAR(Tanggal) = @Tahun	AND CAST(ISNULL(A.NAMA,'') AS VARCHAR(100))<>''
	group by a.KodePerk, a.KodeCost, a.KodeSubCost, NIK, Nama,KETERANGAN,
	b.saldo1,c.saldo2,d.saldo3,e.saldo4,f.saldo5,g.saldo6,h.saldo7,i.saldo8,
	j.saldo9,k.saldo10,l.saldo11,m.saldo12,total
	
	
	
	order by a.KodeSubCost
	
	
	
  end
GO
-- SP_REPORTHISPO : isi DBSMLNEW
ALTER PROCEDURE [dbo].[SP_REPORTHISPO]
--declare
@TGLAWAL DATETIME,
@TGLAKHIR DATETIME,
@KodeCust varchar(30),
@Lokasi varchar(70)='',
@IDUser varchar(30),
@TipeTrans varchar(10)


AS
/*
SELECT
@TGLAWAL ='07/01/2017',
@TGLAKHIR ='09/30/2017',
@KodeCust ='-',
@Lokasi='-',
@IDUser='SA',
@TipeTrans=''
*/

	SELECT A.NOBUKTI NOPO,B.KODESUPP,J.NAMACUSTSUPP,A.KODEBRG,F.NAMABRG,A.QNT QNTPO,A.NOSAT NOSATPO,
	C.NOBUKTI NOBELI,C.QNT QNTBELI,C.NOSAT NOSATBELI,b.Tanggal TGLPO,C1.Tanggal TGLBELI,TglKirim
	FROM DBPODET A
	LEFT OUTER JOIN DBPO B ON A.NOBUKTI=B.NOBUKTI
	LEFT OUTER JOIN DBBELIDET C ON A.NOBUKTI=C.NoPO AND A.URUT=C.UrutPO
	LEFT OUTER JOIN DBBELI C1 ON C.NOBUKTI=C1.NOBUKTI
	LEFT OUTER JOIN DBBARANG F ON A.KODEBRG=F.KODEBRG
	LEFT OUTER JOIN DBCUSTSUPP J ON B.KODESUPP=J.KODECUSTSUPP
	--LEFT OUTER JOIN DBHDGROUP M ON F.KodeHdGrp=M.KODEHDGRP
	--Left Outer join dbSubGroup m4 on m4.KodeGrp=F.KODEGRP and m4.KodeSubGrp=F.KODESUBGRP and F.KodeHdGrp=m4.KodeHDGrp	
	--left outer join DBSubGroupJnsTambah m5 on F.KODEGRP=M5.KodeGrp and F.KodeHdGrp=M5.hdgroup
	--				and F.KODESUBGRP=m5.KodeSubGrp AND F.KODESUBKATEGORI=m5.Urut
	--left Outer join DBMERK m6 on F.KodeMerk=m6.KODEMERK
	WHERE B.TANGGAL BETWEEN @TGLAWAL AND @TGLAKHIR 
    and (b.KODESUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End)
ORDER BY B.TANGGAL,a.NOBUKTI
GO
-- SP_REPORTHISSOOUT : isi DBSMLNEW
ALTER PROCEDURE [dbo].[SP_REPORTHISSOOUT]
--DECLARE
@TGLAWAL DATETIME,
@TGLAKHIR DATETIME,
@KodeCust varchar(30),
@Lokasi varchar(70)='',
@IDUser varchar(30),
@TipeTrans varchar(10)

/*
SELECT 
@TGLAWAL='01/01/2019',
@TGLAKHIR='03/31/2020',
@KodeCust='-',
@Lokasi='-',
@IDUser='SA',
@TipeTrans=''
*/

AS
if not exists(select ID from DBCUSTOMIZE where IDuser=@IDUser and Tipe=@TipeTrans) 
begin
select A.NOBUKTI
,A.URUT,A.NOBUKTI + CAST(A.URUT as varchar(3)) Keynobukti
,B.TANGGAL,B.NoPesanan,A.KODEBRG
,CASE WHEN A.NAMABRG='' THEN F.NAMABRG ELSE A.NAMABRG END NAMABRG,
CASE WHEN A.SATX='' THEN A.SATUAN ELSE A.SATX END SATUAN,A.QNT2 QNT,
D.NoBukti NOBUKTISPB,D.Tanggal TGLSPB,E.NoBukti NOBINV,G.Tanggal TGLINV,
B.KODECUST,J.NAMACUSTSUPP,
a.QNT QNTSO,C.QNT2 QNTSPB,E.QNT2 QNTINV ,C1.NoBukti NORSPB,C2.Tanggal TGLRSPB,C1.QNT2 QNTRSPB,

 ISnull(M.NAMAHDGRP,'')+ISNULL(F.KodeHdGrp,'') NAMAHDGRP
         ,ISnull(m3.Nama,'')+ISNULL(B.KodePF,'') namaPIC
         ,ISnull(m4.NamaSubGrp,'')+ISNULL(m4.KODESUBGRP,'') NamaSubGrp
         ,ISnull(m5.Keterangan,'')+ISNULL(m5.Urut,'') NamaSubkategori
         ,ISnull(m6.NAMAMERK,'')+ISNULL(F.KodeMerk,'') NAMAMERK,B.KodePF,
         m7.KODEKEBUN,m7.Nama Namakebun,B.TGLKIRIM,DATEDIFF(Day,ISNULL(d.Tanggal,Getdate()),B.TglKirim) LeadTime
,dbo.SaldoQntDJHIS(a.KODEBRG,'G01',B.TANGGAL) sTOCK ,
dATEDIFF(DAY,G.TANGGAL,D.TANGGAL) SLISIHSPBINVOICE,a.HARGA,a.QNTBATAL
,CD.KODEGROUPCUSTSUPP,CD.NAMAGROUPCUSTSUPP
,A.QNT2 - A.QNTBATAL - C.QNT2 + ISNULL(C1.QNT2,0) OSSO
,a.QNT2 -  ISNULL(b9.QntSpb,0) Sisa,
case when year(D.TGLTERIMA)=1899 then Null else D.TGLTERIMA end Tglterima,
(a.QNT), (ISNULL(A.QNTBATAL,0) * A.ISI) ,ISNULL(h2.QNTINvoice,0) inv

from DBSODET a
left outer join DBSO b on a.NOBUKTI=b.NOBUKTI
left outer join dbSPBDet c on a.NOBUKTI=c.NoSo and a.URUT=c.UrutSo
LEFT OUTER JOIN DBRSPBDet C1 ON C.NOBUKTI=C1.NoSPB AND C.Urut=C1.UrutSPB
LEFT OUTER JOIN DBRSPB C2 ON C.NOBUKTI=C2.NoBukti 
left outer join dbSPB d on  c.NoBukti=d.NoBukti
left outer join dbInvoicePLDet e on c.NoBukti=e.NoSPB and c.Urut=E.UrutSPB 
LEFT OUTER JOIN DBBARANG F ON A.KODEBRG=F.KODEBRG
LEFT OUTER JOIN DBINVOICEPL G ON E.NoBukti=G.NoBukti

LEFT OUTER JOIN DBCUSTSUPP J ON B.KODECUST=J.KODECUSTSUPP

LEFT OUTER JOIN DBHDGROUP M ON F.KodeHdGrp=M.KODEHDGRP
Left Outer Join DBPICCUSTSUPP M3 on B.KodePF=M3.KODEPIC and B.KODECUST=m3.KODECUSTSUPP
Left Outer join dbSubGroup m4 on m4.KodeGrp=F.KODEGRP and m4.KodeSubGrp=F.KODESUBGRP and F.KodeHdGrp=m4.KodeHDGrp
left outer join DBSubGroupJnsTambah m5 on F.KODEGRP=M5.KodeGrp and F.KodeHdGrp=M5.hdgroup
				and F.KODESUBGRP=m5.KodeSubGrp AND F.KODESUBKATEGORI=m5.Urut
left Outer join DBMERK m6 on F.KodeMerk=m6.KODEMERK
Left outer join DBKEBUNCUSTSUPP m7 on B.KODECUST=m7.KODECUSTSUPP 
and m7.KODEKEBUN=case when isnull(B.KODEKEBUN,'')='' then ISnull(B.KODEKEBUN,'') else ISnull(B.KODEKEBUN,'')End


left outer join (select b.NoSo,b.UrutSo,SUM(a.QNT) QNTINvoice ,SUM(a.QNT2) QNT2Nvoice 
				 from dbInvoicePLDet a
				 left outer join dbSPBDet b on a.NoSPB=b.NoBukti and a.UrutSPB=b.Urut
				 group by b.NoSo,b.UrutSo) h2 on a.NOBUKTI=H2.NoSo and a.URUT=h2.UrutSo						 
left outer join DBGROUPCUSTSUPP cd on  J.agent=cd.KODEGROUPCUSTSUPP

left outer join (select b.NoSo,b.UrutSo,sum(b.QNT2) - ISNULL(SUM(ISNULL(b1.Qntrspb,0)),0) QntSpb,C.QNT2
			      from  dbSPBDet b 
				  left outer join (select nospb,UrutSPB,SUM(QNT2) Qntrspb
											         from DBRSPBDet
											         group by NoSPB,UrutSPB
												  )b1 on b.NoBukti=b1.NoSPB and b.Urut=b1.UrutSPB
											  
LEFT OUTER JOIN DBSODET C ON B.NoSo=C.NOBUKTI AND B.UrutSo=C.URUT												  
				 group by b.NoSo,b.UrutSo,C.QNT2			  
				)B9 on a.NOBUKTI=b9.NoSo and a.URUT=b9.UrutSo 


WHERE B.TANGGAL <=@TGLAKHIR --and A.NOBUKTI like '%SPL/SO/00300/1215%'
and b.KODECUST like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
and ISNULL(M7.Nama,'')+ISNULL(m7.KODEKEBUN,'') like case when @Lokasi='-' OR @Lokasi='' then '%' else @Lokasi End

and 

--(a.QNT)- (ISNULL(A.QNTBATAL,0) * A.ISI) - ISNULL(h2.QNTINvoice,0)>0

case when a.NOSAT=1 then
(a.QNT)- (ISNULL(A.QNTBATAL,0) * A.ISI) - ISNULL(h2.QNTINvoice,0)
else 
(a.QNT2)- (ISNULL(A.QNTBATAL,0)) - ISNULL(h2.QNT2Nvoice,0)
end
>0

and a.NOBUKTI+CAST(A.URUT as varchar(4))<>'MKS/SO/00054/031942'--='MKS/SO/00170/1118' and a.URUT=13

--and a.NOBUKTI='MKS/SO/00042/1219' AND A.KODEBRG='02.046.00002'
ORDER BY B.TANGGAL,a.NOBUKTI
End
else
begin
select A.NOBUKTI
,A.URUT,A.NOBUKTI + CAST(A.URUT as varchar(3)) Keynobukti
,B.TANGGAL,B.NoPesanan,A.KODEBRG
,CASE WHEN A.NAMABRG='' THEN F.NAMABRG ELSE A.NAMABRG END NAMABRG,
CASE WHEN A.SATX='' THEN A.SATUAN ELSE A.SATX END SATUAN,A.QNT2 QNT,
D.NoBukti NOBUKTISPB,D.Tanggal TGLSPB,E.NoBukti NOBINV,G.Tanggal TGLINV,
B.KODECUST,J.NAMACUSTSUPP,
a.QNT QNTSO,C.QNT2 QNTSPB,E.QNT2 QNTINV ,C1.NoBukti NORSPB,C2.Tanggal TGLRSPB,C1.QNT2 QNTRSPB,

 ISnull(M.NAMAHDGRP,'')+ISNULL(F.KodeHdGrp,'') NAMAHDGRP
         ,ISnull(m3.Nama,'')+ISNULL(B.KodePF,'') namaPIC
         ,ISnull(m4.NamaSubGrp,'')+ISNULL(m4.KODESUBGRP,'') NamaSubGrp
         ,ISnull(m5.Keterangan,'')+ISNULL(m5.Urut,'') NamaSubkategori
         ,ISnull(m6.NAMAMERK,'')+ISNULL(F.KodeMerk,'') NAMAMERK,B.KodePF,
         m7.KODEKEBUN,m7.Nama Namakebun,B.TGLKIRIM,DATEDIFF(Day,ISNULL(d.Tanggal,Getdate()),B.TglKirim) LeadTime
,dbo.SaldoQntDJHIS(a.KODEBRG,'G01',B.TANGGAL) sTOCK ,
dATEDIFF(DAY,G.TANGGAL,D.TANGGAL) SLISIHSPBINVOICE,a.HARGA,a.QNTBATAL
,CD.KODEGROUPCUSTSUPP,CD.NAMAGROUPCUSTSUPP
,A.QNT2 - A.QNTBATAL - C.QNT2 + ISNULL(C1.QNT2,0) OSSO
,a.QNT2 -  ISNULL(b9.QntSpb,0) Sisa,
case when year(D.TGLTERIMA)=1899 then Null else D.TGLTERIMA end Tglterima,
(a.QNT), (ISNULL(A.QNTBATAL,0) * A.ISI) ,ISNULL(h2.QNTINvoice,0) inv

from DBSODET a
left outer join DBSO b on a.NOBUKTI=b.NOBUKTI
left outer join dbSPBDet c on a.NOBUKTI=c.NoSo and a.URUT=c.UrutSo
LEFT OUTER JOIN DBRSPBDet C1 ON C.NOBUKTI=C1.NoSPB AND C.Urut=C1.UrutSPB
LEFT OUTER JOIN DBRSPB C2 ON C.NOBUKTI=C2.NoBukti 
left outer join dbSPB d on  c.NoBukti=d.NoBukti
left outer join dbInvoicePLDet e on c.NoBukti=e.NoSPB and c.Urut=E.UrutSPB 
LEFT OUTER JOIN DBBARANG F ON A.KODEBRG=F.KODEBRG
LEFT OUTER JOIN DBINVOICEPL G ON E.NoBukti=G.NoBukti

LEFT OUTER JOIN DBCUSTSUPP J ON B.KODECUST=J.KODECUSTSUPP

LEFT OUTER JOIN DBHDGROUP M ON F.KodeHdGrp=M.KODEHDGRP
Left Outer Join DBPICCUSTSUPP M3 on B.KodePF=M3.KODEPIC and B.KODECUST=m3.KODECUSTSUPP
Left Outer join dbSubGroup m4 on m4.KodeGrp=F.KODEGRP and m4.KodeSubGrp=F.KODESUBGRP and F.KodeHdGrp=m4.KodeHDGrp
left outer join DBSubGroupJnsTambah m5 on F.KODEGRP=M5.KodeGrp and F.KodeHdGrp=M5.hdgroup
				and F.KODESUBGRP=m5.KodeSubGrp AND F.KODESUBKATEGORI=m5.Urut
left Outer join DBMERK m6 on F.KodeMerk=m6.KODEMERK
Left outer join DBKEBUNCUSTSUPP m7 on B.KODECUST=m7.KODECUSTSUPP 
and m7.KODEKEBUN=case when isnull(B.KODEKEBUN,'')='' then ISnull(B.KODEKEBUN,'') else ISnull(B.KODEKEBUN,'')End


left outer join (select b.NoSo,b.UrutSo,SUM(a.QNT) QNTINvoice ,SUM(a.QNT2) QNT2Nvoice 
				 from dbInvoicePLDet a
				 left outer join dbSPBDet b on a.NoSPB=b.NoBukti and a.UrutSPB=b.Urut
				 group by b.NoSo,b.UrutSo) h2 on a.NOBUKTI=H2.NoSo and a.URUT=h2.UrutSo						 
left outer join DBGROUPCUSTSUPP cd on  J.agent=cd.KODEGROUPCUSTSUPP

left outer join (select b.NoSo,b.UrutSo,sum(b.QNT2) - ISNULL(SUM(ISNULL(b1.Qntrspb,0)),0) QntSpb,C.QNT2
			      from  dbSPBDet b 
				  left outer join (select nospb,UrutSPB,SUM(QNT2) Qntrspb
											         from DBRSPBDet
											         group by NoSPB,UrutSPB
												  )b1 on b.NoBukti=b1.NoSPB and b.Urut=b1.UrutSPB
											  
LEFT OUTER JOIN DBSODET C ON B.NoSo=C.NOBUKTI AND B.UrutSo=C.URUT												  
				 group by b.NoSo,b.UrutSo,C.QNT2			  
				)B9 on a.NOBUKTI=b9.NoSo and a.URUT=b9.UrutSo 


WHERE  B.TANGGAL <=@TGLAKHIR --and A.NOBUKTI like '%SPL/SO/00300/1215%'
and b.KODECUST like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
and ISNULL(M7.Nama,'')+ISNULL(m7.KODEKEBUN,'') like case when @Lokasi='-' OR @Lokasi='' then '%' else @Lokasi End
and b.NoPesanan in (select ID from DBCUSTOMIZE where IDUser=@IDUser and tipe=@TipeTrans)
and
case when a.NOSAT=1 then
(a.QNT)- (ISNULL(A.QNTBATAL,0) * A.ISI) - ISNULL(h2.QNTINvoice,0)
else 
(a.QNT2)- (ISNULL(A.QNTBATAL,0)) - ISNULL(h2.QNT2Nvoice,0)
end
>0


and a.NOBUKTI+CAST(A.URUT as varchar(4))<>'MKS/SO/00054/031942'

ORDER BY B.TANGGAL,a.NOBUKTI
End
GO
-- Sp_ReportKartuPiutang : isi DBSMLNEW
ALTER PROCEDURE [dbo].[Sp_ReportKartuPiutang]
@awal datetime,
@akhir datetime,
@kodesupp varchar(10),
@kodesupp1 varchar(10),
@devisi varchar(10),
@Urut int,
@Perkiraan varchar(20),
@rekap tinyint,
@KodeVls varchar(15)

AS
if @Rekap=0
begin
if @urut=0
begin
 	Select Dateadd(dd,-1,@Awal) Tanggal,'Saldo Awal'NoFaktur,''NoBukti,
		0 as kredit1,
		0 as debet1, 
		0 as kreditd1, 0 as debetd1,
		H.KODECUSTSUPP as kode,S.NAMACUSTSUPP as nama, S.Alamat1 Alamat ,S.kota,
  		Sum(case 	when H.Debet <> 0 then H.Debet
       			when H.Kredit <> 0 then H.Kredit*-1
  		end) as SaldoRp,
  		Sum(case 	when H.DebetD <> 0 then H.DebetD
       			when H.KreditD <> 0 then H.KreditD*-1
  		end) as SaldoD,
		0 as Awal, 0 as AwalD,''NoRetur,0urut,0 as totBayar,''P, 1 as xurut,0 kurs,
		Sum(case when H.TipeTrans='J' then H.Kredit-H.Debet else 0 end) SelisihKurs,'' PONo,''xx
 	from vwHutpiut H
 	left outer join DBCUSTSUPP S on S.KODECUSTSUPP=H.KodeCustSupp 
 	where H.KodeCustSupp>=@kodesupp and H.KodeCustSupp<=@kodesupp1
 	and H.Tanggal<@awal and H.perkiraan=@perkiraan
 	and ((H.Valas=@KodeVls and @KodeVls<>'IDR') or (H.Valas like '%' and @KodeVls='IDR'))
 	Group by H.KODECUSTSUPP ,S.NAMACUSTSUPP , S.Alamat1, S.kota
 	union all
 	Select H.Tanggal, H.NoFaktur ,	H.NoBukti,
 	    --case when H.TipeTrans='J' then 0 else case when H.NoRetur='' then H.Kredit else 0 end end as kredit1,
		--case when H.TipeTrans='J' then 0 else case when H.NoRetur='' then H.Debet else -H.Kredit end end as debet1, 
		--case when H.NoRetur='' then H.KreditD else 0 end as kreditd1,
        --case when H.NoRetur='' then H.DebetD else -H.KreditD end debetd1, 
        case when H.TipeTrans='J' then 0 else H.Kredit end as kredit1,
		case when H.TipeTrans='J' then 0 else H.Debet end as debet1,
		H.KreditD as kreditd1,
        H.DebetD as debetd1,
        H.KODECUSTSUPP as kode,S.NAMACUSTSUPP as nama, s.Alamat1 Alamat, S.kota,
  		case when H.Debet <> 0 then H.Debet
       			when H.Kredit <> 0 then H.Kredit*-1 end as SaldoRp,
  		case when H.DebetD <> 0 then H.DebetD
       			when H.KreditD <> 0 then H.KreditD*-1 end as SaldoD,
		H.Kredit as Awal, 0 as AwalD, NoRetur, h.urut, H.Kredit as totBayar,
		right(h.nofaktur,7),3 as xurut, h.kurs,
		case when H.TipeTrans='J' then H.Kredit-H.Debet else 0 end SelisihKurs,H.PONo,''xx
 	from vwHutpiut H
 	left outer join DBCUSTSUPP S on S.KODECUSTSUPP=H.KODECUSTSUPP
 	where H.KODECUSTSUPP>=@kodesupp and H.KODECUSTSUPP<=@kodesupp1 
 	 and (H.Tanggal>=@awal and H.Tanggal<=@Akhir)
 	 and H.perkiraan=@perkiraan
 	 and ((H.Valas=@KodeVls and @KodeVls<>'IDR') or (H.Valas like '%' and @KodeVls='IDR'))
 	order by H.KODECUSTSUPP, xurut, tanggal,urut,NoFaktur,NoRetur
end
else if @urut=1
begin
 	Select 	Dateadd(dd,-1,@Awal) Tanggal,'Saldo Awal' NoFaktur,'' NoBukti, 
		0 as kredit1,
		0 as debet1, 
		0 as kreditd1,
      0 as debetd1, H.KODECUSTSUPP as kode,S.NAMACUSTSUPP as nama, S.ALAMAT1 Alamat,S.kota,
  		Sum(case when H.Debet <> 0 then H.Debet
       			when H.Kredit <> 0 then H.Kredit*-1 end) as SaldoRp,
  		Sum(case when H.DebetD <> 0 then H.DebetD
       			when H.KreditD <> 0 then H.KreditD*-1 end) as SaldoD,
		0 as Awal,0 as AwalD,''NoRetur,0urut,0 as totBayar,
		''P, 1 as xurut,'' H, 0 kurs,
		Sum(case when H.TipeTrans='J' then H.Kredit-H.Debet else 0 end) SelisihKurs,'' PONo
 	from 	vwHutpiut H
 	left outer join DBCUSTSUPP S on S.KODECUSTSUPP=H.KODECUSTSUPP 
 	where H.Tanggal<@awal and H.KODECUSTSUPP>=@kodesupp and H.KODECUSTSUPP<=@kodesupp1 
 	and H.perkiraan=@perkiraan
 	and ((H.Valas=@KodeVls and @KodeVls<>'IDR') or (H.Valas like '%' and @KodeVls='IDR'))
 	Group by H.KODECUSTSUPP ,S.NAMACUSTSUPP , S.Alamat1, S.kota
 	union all
 	Select H.Tanggal,H.NoFaktur, H.NoBukti,
		case when H.TipeTrans='J' then 0 else H.Kredit end as kredit1,
		case when H.TipeTrans='J' then 0 else H.Debet end as debet1,
		--case when H.TipeTrans='J' then 0 else case when H.NoRetur='' then H.Kredit else 0 end end as kredit1,
		--case when H.TipeTrans='J' then 0 else case when H.NoRetur='' then H.Debet else -H.Kredit end end as debet1,  
		H.KreditD as kreditd1,
        H.DebetD as debetd1,
        --case when H.NoRetur='' then H.KreditD else 0 end as kreditd1,
        --case when H.NoRetur='' then H.DebetD else -H.KreditD end debetd1,  
        H.KODECUSTSUPP as kode, S.NAMACUSTSUPP nama, s.ALAMAT1 Alamat,S.kota,
  		case when H.Debet <> 0 then H.Debet
       			when H.Kredit <> 0 then H.Kredit*-1 end as SaldoRp,
  		case when H.DebetD <> 0 then H.DebetD
       			when H.KreditD <> 0 then H.KreditD*-1
  		end as SaldoD,
		H.Debet as Awal, 0 as AwalD, NoRetur,h.urut, H.Kredit as totBayar,
		right(h.nofaktur,7),3 as xurut,right(h.nofaktur,4), h.Kurs,
		case when H.TipeTrans='J' then H.Kredit-H.Debet else 0 end SelisihKurs,H.PONo
 	from vwHutpiut H
 	left outer join DBCUSTSUPP S on (S.KODECUSTSUPP=H.KODECUSTSUPP) 
 	where H.KODECUSTSUPP>=@kodesupp and H.KODECUSTSUPP<=@kodesupp1 
 	and (H.Tanggal>=@awal and H.Tanggal<=@Akhir)
 	and H.perkiraan=@perkiraan
 	and ((H.Valas=@KodeVls and @KodeVls<>'IDR') or (H.Valas like '%' and @KodeVls='IDR'))
 	order by h.KODECUSTSUPP,xurut,H,P,NoFaktur,tanggal,urut
end
end
else
begin
if @urut=0
begin
 	Select 	null as tanggal,'Saldo Awal' as NoFaktur,'AWL' as NoBukti,0 as kredit1,0 as debet1,0 as kreditd1,
        		0 as debetd1, H.KODECUSTSUPP Kode, S.NAMACUSTSUPP nama,'' alamat,S.kota,
  		sum(case when H.Debet <> 0 then H.Debet
       		when H.Kredit <> 0 then H.Kredit*-1
  		end) as saldoRp,
		sum(case when H.DebetD <> 0 then H.DebetD
       		when H.KreditD <> 0 then H.KreditD*-1
  		end) as saldoD,0 as Awal,0 as AwalD,'' NoRetur,9999 as urut,0 as totBayar,'',2 as xurut, null kurs, null SelisihKurs
 	,''PONo
 	from vwHutpiut  H
 	left outer join DBCUSTSUPP S on S.KODECUSTSUPP=H.KODECUSTSUPP
 	where H.KODECUSTSUPP>=@kodesupp and H.KODECUSTSUPP<=@kodesupp1 
 	and H.perkiraan=@perkiraan and h.Tanggal<@Awal
 	and ((H.Valas=@KodeVls and @KodeVls<>'IDR') or (H.Valas like '%' and @KodeVls='IDR'))
 	group by H.KODECUSTSUPP,s.NAMACUSTSUPP, S.kota
 	union all
 	Select H.Tanggal, H.NoFaktur,H.NoBukti,
		--case when H.TipeTrans='J' then 0 else H.Kredit end as kredit1,
		--case when H.TipeTrans='J' then 0 else H.Debet end as debet1,
		case when H.TipeTrans='J' then 0 else case when H.NoRetur='' then H.Kredit else 0 end end as kredit1,
		case when H.TipeTrans='J' then 0 else case when H.NoRetur='' then H.Debet else -H.Kredit end end as debet1,  
		--H.KreditD as kreditd1,
        --H.DebetD as debetd1, 
        case when H.NoRetur='' then H.KreditD else 0 end as kreditd1,
        case when H.NoRetur='' then H.DebetD else -H.KreditD end debetd1, 
        H.KODECUSTSUPP as kode,S.NAMACUSTSUPP as nama,'' alamat,S.kota,
  		case when H.Debet <> 0 then H.Debet
       			when H.Kredit <> 0 then H.Kredit*-1
  		end as SaldoRp,
  		case when H.DebetD <> 0 then H.DebetD
       		when H.KreditD <> 0 then H.KreditD*-1
  		end as SaldoD, H.Debet as Awal, H.DebetD as AwalD, NoRetur,h.urut, H.Kredit as totBayar,
		right(h.nofaktur,7),3 as xurut, h.kurs,
		case when H.TipeTrans='J' then H.Kredit-H.Debet else 0 end SelisihKurs
		,H.PONo
 	from vwHutpiut H
 	left outer join DBCUSTSUPP S on S.KODECUSTSUPP=H.KODECUSTSUPP 
 	where H.KODECUSTSUPP>=@kodesupp and H.KODECUSTSUPP<=@kodesupp1 
 	and (H.Tanggal>=@awal and H.Tanggal<=@Akhir)
 	and H.perkiraan=@perkiraan
 	and ((H.Valas=@KodeVls and @KodeVls<>'IDR') or (H.Valas like '%' and @KodeVls='IDR'))
 	order by H.KODECUSTSUPP,xurut,tanggal,urut,NoFaktur,NoRetur
end
else if @urut=1
begin
	Select null as tanggal,'Saldo Awal' as NoFaktur,'AWL' as NoBukti,0 as kredit1,0 as debet1,0 as kreditd1,
        		0 as debetd1, H.KODECUSTSUPP as kode,S.NAMACUSTSUPP as nama,'' alamat,S.kota,
  		sum(case when H.Debet <> 0 then H.Debet
       		when H.Kredit <> 0 then H.Kredit*-1
  		end) as saldoRp,
		sum(case when H.DebetD <> 0 then H.DebetD
       			when H.KreditD <> 0 then H.KreditD*-1
  		end) as saldoD,0 as Awal,0 as AwalD,''NoRetur,9999 as urut,0 as totBayar,'',2 as xurut,'', null kurs, null SelisihKurs
	,''PONo
	from vwHutpiut H
	left outer join DBCUSTSUPP S on S.KODECUSTSUPP=H.KODECUSTSUPP
	where H.KODECUSTSUPP>=@kodesupp and H.KODECUSTSUPP<=@kodesupp1 
	and H.perkiraan=@perkiraan and H.Tanggal<=@Awal
	and ((H.Valas=@KodeVls and @KodeVls<>'IDR') or (H.Valas like '%' and @KodeVls='IDR'))
	group by H.KODECUSTSUPP,s.NAMACUSTSUPP, S.kota
	having sum(kredit)-sum(debet)<>0

 	union all
 	Select H.Tanggal,H.NoFaktur,H.NoBukti,
		case when H.TipeTrans='J' then 0 else H.Kredit end as kredit1,
		case when H.TipeTrans='J' then 0 else H.Debet end as debet1,
		H.KreditD as kreditd1,
        H.DebetD as debetd1,
		--case when H.TipeTrans='J' then 0 else case when H.NoRetur='' then H.Kredit else 0 end end as kredit1,
		--case when H.TipeTrans='J' then 0 else case when H.NoRetur='' then H.Debet else -H.Kredit end end as debet1,  
		--case when H.NoRetur='' then H.KreditD else 0 end as kreditd1,
        --case when H.NoRetur='' then H.DebetD else -H.KreditD end debetd1, 
        H.KODECUSTSUPP as kode,S.NAMACUSTSUPP as nama,'' alamat,S.kota,
  case when H.Debet <> 0 then H.Debet
       when H.Kredit <> 0 then H.Kredit*-1
  end as SaldoRp,
  case when H.DebetD <> 0 then H.DebetD
       when H.KreditD <> 0 then H.KreditD*-1
  end as SaldoD,H.Debet as Awal, H.DebetD as AwalD,NoRetur,h.urut,H.Kredit as totBayar,right(h.nofaktur,7),3 as xurut,right(h.nofaktur,4), h.kurs,
	case when H.TipeTrans='J' then H.Kredit-H.Debet else 0 end SelisihKurs
 ,H.PONo
 from vwHUTPIUT H
 left outer join DBCUSTSUPP S on S.KODECUSTSUPP=H.KODECUSTSUPP
 where H.KODECUSTSUPP>=@kodesupp and H.KODECUSTSUPP<=@kodesupp1 
	 and (H.Tanggal>=@awal and H.Tanggal<=@Akhir)
	 and H.perkiraan=@perkiraan
	 and ((H.Valas=@KodeVls and @KodeVls<>'IDR') or (H.Valas like '%' and @KodeVls='IDR'))
 order by H.KODECUSTSUPP,xurut,nofaktur,tanggal,urut
end
end
GO
-- Sp_ReportLabaRugi : isi DBSMLNEW
ALTER PROCEDURE [dbo].[Sp_ReportLabaRugi]

@Bulan int,@tahun int,@devisi varchar(10),@prosesRlHpp bit,@jumlahA numeric(18,2),@jumlahB numeric(18,2),@jumlahC numeric(18,2)

AS

Set @devisi= Case when @devisi in ('-','') then '%' else @devisi end

   select case when jumlah<>'' then '    '+a.keterangan else a.Keterangan end keterangan
        ,jumlah,TotalA ,TotalB, TotalC,a.tipe,tanda,Persen,
        case when @jumlahA<>0 then (TotalA/@jumlahA)*100 else 0 end P1,
        case when @jumlahB<>0 then (TotalB/@jumlahB)*100 else 0 end P2,
        case when @jumlahC<>0 then (TotalC/@jumlahC)*100 else 0 end P3,
        IsLRHPP, Devisi,Bulan,Tahun,
        a.Perkiraan+Jumlah perkiraan,b.Kelompok,b.Keterangan
   from DBLRHPP a
   left outer join DBPERKIRAAN b on b.Perkiraan=a.perkiraan
   where bulan=@Bulan and tahun=@tahun and devisi Like @devisi and tampil='Y' and IsLRHPP=@prosesRlHpp 
   and ISNULL(totala+TotalB+TotalC,0) <> case when a.Perkiraan='' and Jumlah='' then -1 else 0 end
   order by Nomor
GO
-- Sp_ReportLabaRugiTH : isi DBSMLNEW
ALTER PROCEDURE [dbo].[Sp_ReportLabaRugiTH]

@Bulan int,@tahun int,@devisi varchar(10),@prosesRlHpp bit
,@jumlahA numeric(18,2),@jumlahB numeric(18,2),@jumlahC numeric(18,2)


AS

Set @devisi= Case when @devisi in ('-','') then '%' else @devisi end

   select nomor,a.keterangan,
        sum(case when bulan=1 then totalb else 0 end) bln1,
        (sum(case when bulan=1 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=1 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=1 and Tahun=@tahun and Persen='A') end))*100  P1,
        
        sum(case when bulan=2 then totalb else 0 end) bln2,
	    (sum(case when bulan=2 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=2 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=2 and Tahun=@tahun and Persen='A') end))*100  P2,
        
        sum(case when bulan=3 then totalb else 0 end) bln3,
        (sum(case when bulan=3 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=3 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=3 and Tahun=@tahun and Persen='A') end))*100  P3,
        
        sum(case when bulan=4 then totalb else 0 end) bln4,
        (sum(case when bulan=4 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=4 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=4 and Tahun=@tahun and Persen='A') end))*100  P4,
        
        sum(case when bulan=5 then totalb else 0 end) bln5,
        (sum(case when bulan=5 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=5 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=5 and Tahun=@tahun and Persen='A') end))*100  P5,
        
        sum(case when bulan=6 then totalb else 0 end) bln6,
        (sum(case when bulan=6 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=6 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=6 and Tahun=@tahun and Persen='A') end))*100  P6,
        
        sum(case when bulan=7 then totalb else 0 end) bln7,
        (sum(case when bulan=7 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=7 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=7 and Tahun=@tahun and Persen='A') end))*100  P7,
        
        sum(case when bulan=8 then totalb else 0 end) bln8,
        (sum(case when bulan=8 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=8 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=8 and Tahun=@tahun and Persen='A') end))*100  P8,
        
        sum(case when bulan=9 then totalb else 0 end) bln9,
        (sum(case when bulan=9 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=9 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=9 and Tahun=@tahun and Persen='A') end))*100  P9,
        
        sum(case when bulan=10 then totalb else 0 end) bln10,
        (sum(case when bulan=10 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=10 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=10 and Tahun=@tahun and Persen='A') end))*100  P10,
        
        sum(case when bulan=11 then totalb else 0 end) bln11,
        (sum(case when bulan=11 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=11 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=11 and Tahun=@tahun and Persen='A') end))*100  P11,
        
        sum(case when bulan=12 then totalb else 0 end) bln12,
        (sum(case when bulan=12 then totalb else 0 end)/(
        case when (select totalb from DBLRHPP where Bulan=12 and Tahun=@tahun and Persen='A')=0 then 1 else
        (select totalb from DBLRHPP where Bulan=12 and Tahun=@tahun and Persen='A') end))*100  P12,
        
        sum(totalb) bln,
        (sum(totalb)/(
        case when (select sum(totalb) from DBLRHPP where Bulan<=@bulan and Tahun=@tahun and Persen='A')=0 then 1 else
        (select sum(totalb) from DBLRHPP where Bulan<=@bulan and Tahun=@tahun and Persen='A') end))*100  P13,
        IsLRHPP,jumlah,a.Perkiraan+Jumlah perkiraan,b.Kelompok,b.Keterangan
   from DBLRHPP a
   left outer join DBPERKIRAAN b on b.Perkiraan=a.perkiraan
   where bulan<=@Bulan and tahun=@tahun and devisi Like @devisi and tampil='Y' and IsLRHPP=@prosesRlHpp 
   and ISNULL(totala+TotalB+TotalC,0) <> case when a.Perkiraan='' and Jumlah='' then -1 else 0 end
   group by nomor,a.Keterangan,a.Perkiraan,IsLRHPP,jumlah,b.Kelompok,b.Keterangan
   order by Nomor
GO
-- Sp_ReportMutasi : isi DBSMLNEW
-- Query: exec Sp_ReportMutasi 6, 2026, '01', 'sa'
ALTER PROCEDURE [dbo].[Sp_ReportMutasi]
@bulan int,
@tahun int,
@devisi varchar(10),
@IdUser varchar(15)
AS
Select A.Perkiraan perkiraan, A.Keterangan, 
	   Case when A.DK=0 then sum(case when b.bulan=1 then B.AwalDRp-B.AwalKRp else 0 end)+
                             sum(case when B.bulan<@bulan  then b.MDRp-b.MKRp+b.JPDRp-b.JPKRp else 0 end)
            when A.DK=1 then sum(case when b.bulan=1 then B.AwalKRp-B.AwalDRp else 0 end)+
                             sum(case when B.bulan<@bulan  then b.MKRp-b.MDRp+b.JPKRp-b.JPDRp else 0 end)
            else 0
       end  SaldoAwal,
       sum(case when B.Bulan=@bulan then B.MDRp else 0 end) MD,
       sum(case when B.bulan=@bulan then B.MKRp else 0 end) MK,
       sum(case when B.bulan=@bulan then B.JPDRP else 0 end) JPD, 
       sum(case when B.bulan=@bulan then B.JPKrp else 0 end) JPK,
       Case when A.DK=0 then sum(case when b.bulan=1 then B.AwalDRp-B.AwalKRp else 0 end)+
                             sum(case when B.bulan<@bulan  then b.MDRp-b.MKRp+b.JPDRp-b.JPKRp else 0 end)
            when A.DK=1 then sum(case when b.bulan=1 then B.AwalKRp-B.AwalDRp else 0 end)+
                             sum(case when B.bulan<@bulan  then b.MKRp-b.MDRp+b.JPKRp-b.JPDRp else 0 end)
            else 0
       end  +
       sum(case when B.Bulan=@bulan then B.MDRp else 0 end) -
       sum(case when B.bulan=@bulan then B.MKRp else 0 end) +
       sum(case when B.bulan=@bulan then B.JPDRP else 0 end) -
       sum(case when B.bulan=@bulan then B.JPKrp else 0 end) SAldoAkhir,
       sum(case when B.bulan=@bulan then B.AkhirDRp else 0 end) SaldoDAkhir,
       sum(case when B.bulan=@bulan then B.AkhirKRp else 0 end) SaldoKAkhir, a.Kelompok
from DBPERKIRAAN A
     Left Outer join DBNERACA B on B.Perkiraan=A.Perkiraan
         and B.Bulan<=@bulan and B.Tahun=@tahun and B.Devisi like Case when @devisi='-' then '%' else @devisi end
where a.Tipe=1
group by a.Perkiraan,a.Keterangan,a.dk, a.Kelompok
having 
(
  Case when A.DK=0 then sum(case when b.bulan=1 then B.AwalDRp-B.AwalKRp else 0 end)+
                             sum(case when B.bulan<@bulan  then b.MDRp-b.MKRp+b.JPDRp-b.JPKRp else 0 end)
            when A.DK=1 then sum(case when b.bulan=1 then B.AwalKRp-B.AwalDRp else 0 end)+
                             sum(case when B.bulan<@bulan  then b.MKRp-b.MDRp+b.JPKRp-b.JPDRp else 0 end)
            else 0
       end <>0  or

sum(case when B.Bulan=@bulan then B.MDRp else 0 end) <>0 or
sum(case when B.bulan=@bulan then B.MKRp else 0 end) <>0 or
sum(case when B.bulan=@bulan then B.JPDRP else 0 end) <>0 or 
sum(case when B.bulan=@bulan then B.JPKrp else 0 end) <>0 

)
Order by A.Perkiraan
GO
-- Sp_ReportOpnameBarang : isi DBSMLNEW
ALTER Procedure [dbo].[Sp_ReportOpnameBarang]
@SReport varchar(1),
@Ordr varchar(1),
@tgl1 datetime,
@tgl2 Datetime,
@isiList varchar(200),
@NeeDoto SmallInt,
@Iduser varchar(20),
@Tipe Varchar(20)
as
--Declare @SReport varchar(1),@Ordr varchar(1),@tgl1 datetime,@tgl2 Datetime,@isiList varchar(200)
--select @SReport='T',@Ordr='S', @tgl1='01/01/2011',@tgl2='01/01/2013',@isiList='%%' 

if @SReport='T'
begin
	If @Ordr='N'
		begin
		 If @NeeDoto=0 Or @NeeDoto=1
		  select * from vwDetailKoreksiWEb where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeeDoto
		  order by NoBukti,TANGGAL
		 If @NeeDoto=2
		  select * from vwDetailKoreksiWEb where Tanggal between @tgl1 and @tgl2 
		  order by NoBukti,TANGGAL
		end 
	else If @Ordr='B'
		begin
		If @NeeDoto=0 Or @NeeDoto=1
		  select * from VwreportOPnamebarang where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeeDoto
		  order by KodeBrg
		If @NeeDoto=2
		  select * from VwreportOPnamebarang where Tanggal between @tgl1 and @tgl2 -- and NeedOtorisasi=@NeeDoto
		  order by KodeBrg
		end 
End
else
begin
If @Ordr='N'
		begin
		 If @NeeDoto=0 Or @NeeDoto=1
		  select * from vwDetailKoreksiWEb where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeeDoto
		  order by NoBukti,TANGGAL
		 If @NeeDoto=2
		  select * from vwDetailKoreksiWEb where Tanggal between @tgl1 and @tgl2 
		  order by NoBukti,TANGGAL
		end 
	else If @Ordr='B'
		begin
		If @NeeDoto=0 Or @NeeDoto=1
		  select * from VwreportOPnamebarang where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeeDoto
		  order by KodeBrg
		If @NeeDoto=2
		  select * from VwreportOPnamebarang where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeeDoto
		  order by KodeBrg
		end 
End
GO
-- Sp_reportoutStandingPR : isi DBSMLNEW
ALTER Procedure [dbo].[Sp_reportoutStandingPR]
@SReport varchar(1),
@Ordr varchar(1),
@tgl1 datetime,
@tgl2 Datetime,
@isiList varchar(200),
@x int=1,
@IDuser varchar(20),
@Tipe varchar(20)
as
--Declare @SReport varchar(1),@Ordr varchar(1),@tgl1 datetime,@tgl2 Datetime,@isiList varchar(200)
--select @SReport='T',@Ordr='S', @tgl1='01/01/2011',@tgl2='01/01/2013',@isiList='%%' 

if @SReport='T'
begin
	If @Ordr='N'
		begin
		  select * from [vwOutPPL] where QNTPR-qntbatal>QNTPO and Tanggal<=@tgl2 order by NoBukti,Tanggal
		end 
	else If @Ordr='B'
		begin
		  select * from [vwOutPPL] where QNTPR-qntbatal>QNTPO and Tanggal<=@tgl2 order by KodeBrg
		end 
End
else
begin
If @Ordr='N'
		begin
		  select * from [vwOutPPL] 
		  where NoBukti In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  order by NoBukti,Tanggal
		end 
	else If @Ordr='B'
		begin
		  select * from [vwOutPPL] 
		  where Kodebrg In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  order by KodeBrg
		end 
End
GO
-- Sp_ReportSODet : isi DBSMLNEW
ALTER Procedure [dbo].[Sp_ReportSODet]
@SReport varchar(1),
@Ordr varchar(2),
@tgl1 datetime,
@tgl2 Datetime,
@isiList varchar(200),
@NeedOto smallint=2,
@IDuser varchar(20),
@Tipe varchar(20),
@KodeCust varchar(30),
@group Varchar(50),
@PIC varchar(50),
@Kategori varchar(50),
@Subkategori varchar(50),
@KodeMerk Varchar(50),
@pagen tinyint=0

as
--Declare @SReport varchar(1),@Ordr varchar(1),@tgl1 datetime,@tgl2 Datetime,@isiList varchar(200)
--select @SReport='T',@Ordr='S', @tgl1='01/01/2011',@tgl2='01/01/2013',@isiList='%%' 



if @SReport='T'
begin
	If @Ordr='N'
		begin
		  if @NeedOto=0 or @needoto=1
			select a.*,b.qntspb outstanding from VwReportSO a
			left outer join (select noso,urutso,sum(qnt) qntspb from dbSPBdet group by noso,urutso) b on b.NoSo=a.NoBukti and b.UrutSo=a.urut			
			where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
			and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
			Order By TANGGAL,NoBukti
		if @NeedOto=2
			select a.*,b.qntspb outstanding from VwReportSO a
			left outer join (select noso,urutso,sum(qnt) qntspb from dbSPBdet group by noso,urutso) b on b.NoSo=a.NoBukti and b.UrutSo=a.urut			
			where Tanggal between @tgl1 and @tgl2 
			and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
			Order By TANGGAL,NoBukti
		end 
	else If @Ordr='B'
		begin
		 if @NeedOto=0 or @needoto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
		    and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		    order by KodeBrg,TANGGAL,NoBukti
		  if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
			order by KodeBrg,TANGGAL,NoBukti
		end 
	else If @Ordr='C'
		begin
		if @NeedOto=0 or @needOto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
		    and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KODECUSTSUPP,TANGGAL,NoBukti
		if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
		    and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KODECUSTSUPP ,TANGGAL,NoBukti
		end 
	else If @Ordr='S'
		begin
		if @NeedOto=0 or @needOto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end 
		  order by KodeSls,NamaSls,TANGGAL,NoBukti
		if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KodeSls,NamaSls,TANGGAL,NoBukti
		end 
	else If @Ordr='HG'
		begin
		if @NeedOto=0 or @needOto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
		if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End  
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		 order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
		end 	
	else If @Ordr='CB'
		begin
		  if Not exists (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@Tipe+'SL')
			begin
				if @NeedOto=0 or @needOto=1	
					select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
					and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
					and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
					and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
					and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
					and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
					and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
					and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
					order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
				if @NeedOto=2
					select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
					and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
					and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
					and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
					and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
					and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
					and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
					and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
				order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
			end
		else
		   begin
		      if @NeedOto=0 or @needOto=1	
					select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
					and KodeBrg In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE+'SL')
					and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
					order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
				if @NeedOto=2
					select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
					and KodeBrg In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE+'SL')
					and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
				order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
		   end
		end 
		If @Ordr='P'
		begin
		  if @NeedOto=0 or @needoto=1
			select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
			and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 	
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end		
			order by kodepf,namaPIC,TANGGAL,NoBukti
		if @NeedOto=2
			select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
			and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
			order by kodepf,namaPIC,TANGGAL,NoBukti
		end 	
	If @Ordr='L'
		begin
		  if @NeedOto=0 or @needoto=1
			select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
			and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 	
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end		
			order by KODEKEBUN,NamaKebun,TANGGAL,NoBukti
		if @NeedOto=2
			select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
			and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
			order by KODEKEBUN,NamaKebun,TANGGAL,NoBukti
		end 	
End







if @SReport='F'
begin
If @Ordr='N'
		begin
		  if @NeedOto=0 or @needoto=1
			select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
			and NoBukti In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
			and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
			order by TANGGAL,NoBukti
		if @NeedOto=2
			select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
			and NoBukti In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
			and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
			order by TANGGAL,NoBukti
		end 
	else If @Ordr='B'
		begin
		 if @NeedOto=0 or @needoto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and KodeBrg In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		   order by KodeBrg,TANGGAL,NoBukti
		  if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
		  and KodeBrg In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order By KodeBrg,TANGGAL,NoBukti
		end 
	else If @Ordr='C'
		begin
		if @NeedOto=0 or @needOto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and KODECUSTSUPP In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KODECUSTSUPP,TANGGAL,NoBukti
		if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
		  and KODECUSTSUPP In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KODECUSTSUPP ,TANGGAL,NoBukti
		end 
	else If @Ordr='S'
		begin
		if @NeedOto=0 or @needOto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and KodeSls In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KodeSls,NamaSls,TANGGAL,NoBukti
		if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
		  and KodeSls In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KodeSls,NamaSls ,TANGGAL,NoBukti
		end
	else If @Ordr='HG'
		begin
		if @NeedOto=0 or @needOto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and KodeHdGrp In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
		if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
		  and KodeHdGrp In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
		end
	else If @Ordr='CB'
		begin
		  if Not exists (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@Tipe+'SL')
			begin
				if @NeedOto=0 or @needOto=1	
					select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
					and KODECUSTSUPP In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
					and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
					order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
				if @NeedOto=2
					select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
					and KODECUSTSUPP In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
					and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
				order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
			end
		else
		   begin
		      if @NeedOto=0 or @needOto=1	
					select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
					and KODECUSTSUPP In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
					and KodeBrg In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE+'SL')
					and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
					order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
				if @NeedOto=2
					select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
					and KODECUSTSUPP In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
					and KodeBrg In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE+'SL')
					and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
				order by KodeHdGrp,NAMAHDGRP,TANGGAL,NoBukti
		   end
		end 
		else If @Ordr='P'
		begin
		 if @NeedOto=0 or @needoto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and Kodepf In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		   order by Kodepf,namaPIC,TANGGAL,NoBukti
		  if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
		  and Kodepf In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by Kodepf,namaPIC,TANGGAL,NoBukti
		end 
else If @Ordr='L'
		begin
		 if @NeedOto=0 or @needoto=1	
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 and NeedOtorisasi=@NeedOto
		  and KODEKEBUN In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		   order by KODEKEBUN,NamaKebun,TANGGAL,NoBukti
		  if @NeedOto=2
		  select * from VwReportSO where Tanggal between @tgl1 and @tgl2 
		  and KODEKEBUN In (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@tIPE)
		  and KODECUSTSUPP like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
			and NAMAHDGRP like case when @group='-' or @group=''  then '%' else @group End 
			and NAMAPIC like case when @PIC='-' or @pic='' then '%' else @PIC End 
			and NAMASUBGRP LIKE case when @Kategori='-' or @Kategori='' then '%' else @Kategori End 
		    and NAMASUBKATEGORI LIKE case when @Subkategori='-' or @Subkategori='' then '%' else @Subkategori End 
			and nAMAMERK LIKE case when @KodeMerk='-' or @KodeMerk='' then '%' else @KodeMerk End 
			and CAST(pagen as varchar(1)) like case when @pagen=0 then '1' when @pagen=1 then '0' when @pagen=2 then '%' end
		  order by KODEKEBUN,NamaKebun,TANGGAL,NoBukti
		end		
End
GO
-- Sp_reportStockQtyRp : isi DBSMLNEW
ALTER procedure [dbo].[Sp_reportStockQtyRp]
--Declare
@Bulan Integer,
@Tahun Integer,
@isi Integer,
@Kodegdg Varchar(20), 
@KodeGrp varchar(20),
@Sreport varchar(1),
@IDuser varchar(20),
@Tipe varchar(10),
@IsMinus Bit,
@jenis int,
@KodeHdGrp varchar(500)='',
@Kategori varchar(50)='',
@SubKategori varchar(50)='',
@KodeMerk varchar(50)=''

/*
exec Sp_reportStockQtyRp 5, 2026, 1, '', '-', 'T', 'SA', '', 0, 0, '-', '-', '-', '-'

select @bulan=5,@tahun=2025,@isi=1,@Kodegdg='',@Kodegrp='-',@Sreport='T',@IDuser='SA',@IsMinus=0
,@KodeHdGrp='-',@Kategori='-',@SubKategori='-',@KodeMerk='-',@isi=1,@jenis=0
*/
as

Set @kodegdg=Case when @kodegdg in ('','-') then '%' else @kodegdg end
Set @KodeGrp=Case when @KodeGrp in ('','-') then '' else @KodeGrp end
--Set @KodeHdGrp=Case when @KodeHdGrp in ('','-') then '%' else @KodeHdGrp end
if @SReport='T'
begin
If @isi=1
begin
   if @IsMinus=0
    if @Kodegdg='.' --- SEMENTARA
    begin
    	Select a.KODEGDG,''KODEBRG,''NAMABRG,''Satuan, 
	sum(a.QNTAWAL/a.Isi1) QntAwal, sum(a.HRGAWAL)HRGAWAL, 
	sum(a.QNTPBL/a.Isi1) QNTPBL, sum(a.HRGPBL)HRGPBL,
	sum(a.QntRPB/a.Isi1) QNTRBP, sum(a.HRGRPB) HRGRBP,
	sum(a.QNTPMK/a.Isi1) QNTPMK,   sum(a.HRGPMK)HRGPMK,  
	sum(a.QntRPK/a.Isi1) QNTRPK, sum(a.HRGRPK)HRGRPK, 
	sum(a.QNTRPB/a.Isi1) QNTRPB, sum(a.HRGRPB)HRGRPB, 
	sum(a.QNTPNJ/a.Isi1) QNTPNJ, sum(a.HRGPNJ)HRGPNJ,
	sum(a.QNTPRJ/a.Isi1) QNTPRJ, sum(a.HRGPRJ)HRGPRJ,
	sum(a.QNTRPJ/a.Isi1) QNTRPJ, sum(a.HRGRPJ)HRGRPJ, 
	sum(a.QNTADI/a.Isi1) QNTADI, sum(a.HRGADI)HRGADI, 
	sum(a.QNTADO/a.Isi1) QNTADO, sum(a.HRGADO)HRGADO, 
	sum(a.QNTUKI/a.Isi1) QNTUKI, sum(a.HRGUKI)HRGUKI, 
	sum(a.QNTUKO/a.Isi1) QNTUKO, sum(a.HRGUKO)HRGUKO, 
	sum(a.QNTTRI/a.Isi1) QNTTRI, sum(a.HRGTRI)HRGTRI, 
	sum(a.QNTTRO/a.Isi1) QNTTRO, sum(a.HRGTRO)HRGTRO,
	sum(a.qntrspb/a.ISI1) qntrspb,sum(a.hrgrspb)hrgrspb,
	sum(a.qntrgtc/a.ISI1)qntrgtc,sum(a.hrgrgtc)hrgrgtc,
	sum(A.QntHPrd/a.ISI1) QntHPrd, sum(A.HRGHPrd)HRGHPrd, 	
	sum(a.HRGRATA)HRGRATA, sum(a.QNTIN/a.Isi1) QNTIN, sum(a.RPIN)RPIN, 
	sum(a.QNTOUT/a.Isi1)QNTOUT, sum(a.RPOUT)RPOUT, 
	sum(a.SALDOQNT/a.Isi1) SALDOQNT, sum(a.SALDORP)SALDORP,
	'1' ,''partNumber,''NAMAMERK,''lokasi
	from vwReportStockBrg a
	Where a.Bulan=@Bulan and a.Tahun=@Tahun and a.KODEGRP <> 'JS' --and A.KODEGDG Like @Kodegdg
	and a.KodeGrp like @KodeGrp+'%' 
	and abs(a.QNTAWAL)+a.qntpbl+a.QNTIN+a.QNTOUT <>0
	--and abs(a.SALDORP)>10
	and cast (A.pagen as varchar(1)) like  (case when @jenis=0 then '0' when @jenis=1 then '1' when @jenis=2 then '%' End )
	and isrusak like (case when @kodegdg='%' then '0' else '%' End) 	
		
	and isnull(NAMAHDGRP,'') like case when @KodeHdGrp='-' or @KodeHdGrp=''  then '%' else @KodeHdGrp End 			
	AND ISNULL(NAMASUBGRP,'') LIKE CASE WHEN @KATEGORI='-' OR @KATEGORI='' THEN '%' ELSE @KATEGORI END 
    AND ISNULL(NAMASUBKATEGORI,'') LIKE CASE WHEN @SUBKATEGORI='-' OR @SUBKATEGORI='' THEN '%' ELSE @SUBKATEGORI END 
	AND ISNULL(NAMAMERK,'')+ISNULL(KodeMerk,'') LIKE CASE WHEN @KODEMERK='-' OR @KODEMERK='' THEN '%' ELSE @KODEMERK END 
    group by a.KODEGDG
	order by a.kodegdg
	end
    else
	begin
	Select a.KODEGDG,A.KODEBRG,A.NAMABRG,
	Case when @isi=1 then A.SAT1
       when @isi=2 then A.SAT2
       when @isi=3 then ''
       else ''
	end Satuan, 
	a.QNTAWAL/a.Isi1 QntAwal, a.HRGAWAL, 
	a.QNTPBL/a.Isi1 QNTPBL, a.HRGPBL,
	a.QntRPB/a.Isi1 QNTRBP, a.HRGRPB HRGRBP,
	a.QNTPMK/a.Isi1 QNTPMK,   a.HRGPMK,  
	a.QntRPK/a.Isi1 QNTRPK, a.HRGRPK, 
	a.QNTRPB/a.Isi1 QNTRPB, a.HRGRPB, 
	a.QNTPNJ/a.Isi1 QNTPNJ, a.HRGPNJ,
	a.QNTPRJ/a.Isi1 QNTPRJ, a.HRGPRJ,
	a.QNTRPJ/a.Isi1 QNTRPJ, a.HRGRPJ, 
	a.QNTADI/a.Isi1 QNTADI, a.HRGADI, 
	a.QNTADO/a.Isi1 QNTADO, a.HRGADO, 
	a.QNTUKI/a.Isi1 QNTUKI, a.HRGUKI, 
	a.QNTUKO/a.Isi1 QNTUKO, a.HRGUKO, 
	a.QNTTRI/a.Isi1 QNTTRI, a.HRGTRI, 
	a.QNTTRO/a.Isi1 QNTTRO, a.HRGTRO,
	a.qntrspb/a.ISI1 qntrspb,a.hrgrspb,
	a.qntrgtc/a.ISI1 qntrgtc,a.hrgrgtc,
	A.QntHPrd/a.ISI1 QntHPrd, A.HRGHPrd, 	
	a.HRGRATA, a.QNTIN/a.Isi1 QNTIN, a.RPIN, 
	a.QNTOUT/a.Isi1 QNTOUT, a.RPOUT, 
	a.SALDOQNT/a.Isi1 SALDOQNT, a.SALDORP,
	'1' ,A.partNumber,A.NAMAMERK,a.lokasi,a.RPQNT0
	from vwReportStockBrg a
	Where a.Bulan=@Bulan and a.Tahun=@Tahun and a.KODEGRP <> 'JS' and A.KODEGDG Like @Kodegdg
	and a.KodeGrp like @KodeGrp+'%' 
	and (a.QNTAWAL+a.qntpbl+a.QNTIN+a.QNTOUT) <>0
	--and a.SALDORP<>0
	and cast (A.pagen as varchar(1)) like  (case when @jenis=0 then '0' when @jenis=1 then '1' when @jenis=2 then '%' End )
	and isrusak like (case when @kodegdg='%' then '0' else '%' End) 	
		
	and isnull(NAMAHDGRP,'') like case when @KodeHdGrp='-' or @KodeHdGrp=''  then '%' else @KodeHdGrp End 			
	AND ISNULL(NAMASUBGRP,'') LIKE CASE WHEN @KATEGORI='-' OR @KATEGORI='' THEN '%' ELSE @KATEGORI END 
    AND ISNULL(NAMASUBKATEGORI,'') LIKE CASE WHEN @SUBKATEGORI='-' OR @SUBKATEGORI='' THEN '%' ELSE @SUBKATEGORI END 
	AND ISNULL(NAMAMERK,'')+ISNULL(KodeMerk,'') LIKE CASE WHEN @KODEMERK='-' OR @KODEMERK='' THEN '%' ELSE @KODEMERK END 

	order by a.kodebrg,a.kodegdg
	end
	
	if @IsMinus=1
	Select a.KODEGDG,A.KODEBRG,A.NAMABRG,
	Case when @isi=1 then A.SAT1
       when @isi=2 then A.SAT2
       when @isi=3 then ''
       else ''
	end Satuan, 
	a.QNTAWAL/a.Isi1 QntAwal, a.HRGAWAL, 
	a.QNTPBL/a.Isi1 QNTPBL, a.HRGPBL,
	a.QntRPB/a.Isi1 QNTRBP, a.HRGRPB HRGRBP,
	a.QNTPMK/a.Isi1 QNTPMK,   a.HRGPMK,  
	a.QntRPK/a.Isi1 QNTRPK, a.HRGRPK, 
	a.QNTRPB/a.Isi1 QNTRPB, a.HRGRPB, 
	a.QNTPNJ/a.Isi1 QNTPNJ, a.HRGPNJ,
	a.QNTPRJ/a.Isi1 QNTPRJ, a.HRGPRJ, 
	a.QNTRPJ/a.Isi1 QNTRPJ, a.HRGRPJ, 
	a.QNTADI/a.Isi1 QNTADI, a.HRGADI, 
	a.QNTADO/a.Isi1 QNTADO, a.HRGADO, 
	a.QNTUKI/a.Isi1 QNTUKI, a.HRGUKI, 
	a.QNTUKO/a.Isi1 QNTUKO, a.HRGUKO, 
	a.QNTTRI/a.Isi1 QNTTRI, a.HRGTRI, 
	a.QNTTRO/a.Isi1 QNTTRO, a.HRGTRO,
	a.qntrspb/a.ISI1 qntrspb,a.hrgrspb,
	a.qntrgtc/a.ISI1 qntrgtc,a.hrgrgtc,
	A.QntHPrd/a.ISI1 QntHPrd, A.HRGHPrd, 
	a.HRGRATA, a.QNTIN/a.Isi1 QNTIN, a.RPIN, 
	a.QNTOUT/a.Isi1 QNTOUT, a.RPOUT, 
	a.SALDOQNT/a.Isi1 SALDOQNT, a.SALDORP,
	'1' ,A.partNumber,A.NAMAMERK,a.lokasi
	from vwReportStockBrg a
	Where a.Bulan=@Bulan and a.Tahun=@Tahun and a.KODEGRP <> 'JS'and A.KODEGDG Like @Kodegdg
	and a.KodeGrp like @KodeGrp+'%' and a.SALDOQNT/a.Isi2 <0
	and cast (A.pagen as varchar(1)) like  (case when @jenis=0 then '0' when @jenis=1 then '1' when @jenis=2 then '%' End )
	and isrusak like (case when @kodegdg='%' then '0' else '%' End) 
	and A.KODEhdGRP Like @KodeHdGrp
	order by a.kodebrg,a.kodegdg
	
end else 
If @isi=2
begin 
  if @IsMinus=0
	Select a.KODEGDG,A.KODEBRG,A.NAMABRG,
	Case when @isi=1 then A.SAT1
       when @isi=2 then A.SAT2
       when @isi=3 then ''
       else ''
	end Satuan, 
	a.QNTAWAL/a.Isi2 QntAwal, a.HRGAWAL, 
	a.QNTPBL/a.Isi2 QNTPBL, a.HRGPBL,
	a.QntRPB/a.Isi2 QNTRBP, a.HRGRPB HRGRBP, 
	a.QNTPMK/a.Isi2 QNTPMK, a.HRGPMK,
	a.QNTRPB/a.Isi2 QNTRPB, a.HRGRPB,
	a.QNTRPK/a.Isi2 QNTRPK, a.HRGRPK, 
	a.QNTPNJ/a.Isi2 QNTPNJ, a.HRGPNJ,
	a.QNTPRJ/a.Isi1 QNTPRJ, a.HRGPRJ,		
	a.QNTRPJ/a.Isi2 QNTRPJ, a.HRGRPJ, 
	a.QNTADI/a.Isi2 QNTADI, a.HRGADI, 
	a.QNTADO/a.Isi2 QNTADO, a.HRGADO, 
	a.QNTUKI/a.Isi2 QNTUKI, a.HRGUKI, 
	a.QNTUKO/a.Isi2 QNTUKO, a.HRGUKO, 
	a.QNTTRI/a.Isi2 QNTTRI, a.HRGTRI, 
	a.QNTTRO/a.Isi2 QNTTRO, a.HRGTRO,
	a.qntrspb/a.ISI2 qntrspb,a.hrgrspb,
	a.qntrgtc/a.ISI2 qntrgtc,a.hrgrgtc,
	A.QntHPrd/a.ISI2 QntHPrd, A.HRGHPrd, 
	a.HRGRATA, a.QNTIN/a.Isi2 QNTIN, a.RPIN,  
	a.QNTOUT/a.Isi2 QNTOUT, a.RPOUT, 
	a.SALDOQNT/a.Isi2 SALDOQNT, a.SALDORP,
	'2' ,A.partNumber,A.NAMAMERK,a.lokasi
	from vwReportStockBrg a
	Where a.Bulan=@Bulan and a.Tahun=@Tahun and A.KODEGDG Like @Kodegdg and a.KODEGRP <> 'JS'
	and a.KodeGrp like @KodeGrp+'%' and a.SALDOQNT/a.Isi2 >0
	and isrusak like (case when @kodegdg='%' then '0' else '%' End) 
	order by a.kodebrg,a.kodegdg
	if @IsMinus=1
	Select a.KODEGDG,A.KODEBRG,A.NAMABRG,
	Case when @isi=1 then A.SAT1
       when @isi=2 then A.SAT2
       when @isi=3 then ''
       else ''
	end Satuan, 
	a.QNTAWAL/a.Isi2 QntAwal, a.HRGAWAL, 
	a.QNTPBL/a.Isi2 QNTPBL, a.HRGPBL,
	a.QntRPB/a.Isi2 QNTRBP, a.HRGRPB HRGRBP, 
	a.QNTPMK/a.Isi2 QNTPMK, a.HRGPMK, 
	a.QNTRPB/a.Isi2 QNTRPB, a.HRGRPB,
	a.QNTRPK/a.Isi2 QNTRPK, a.HRGRPK, 
	a.QNTPNJ/a.Isi2 QNTPNJ, a.HRGPNJ,
	a.QNTPRJ/a.Isi1 QNTPRJ, a.HRGPRJ,		
	a.QNTRPJ/a.Isi2 QNTRPJ, a.HRGRPJ, 
	a.QNTADI/a.Isi2 QNTADI, a.HRGADI, 
	a.QNTADO/a.Isi2 QNTADO, a.HRGADO, 
	a.QNTUKI/a.Isi2 QNTUKI, a.HRGUKI, 
	a.QNTUKO/a.Isi2 QNTUKO, a.HRGUKO, 
	a.QNTTRI/a.Isi2 QNTTRI, a.HRGTRI, 
	a.QNTTRO/a.Isi2 QNTTRO, a.HRGTRO,
	a.qntrspb/a.ISI2 qntrspb,a.hrgrspb,
	a.qntrgtc/a.ISI2 qntrgtc,a.hrgrgtc,	
	A.QntHPrd/a.ISI2 QntHPrd, A.HRGHPrd, 
	a.HRGRATA, a.QNTIN/a.Isi2 QNTIN, a.RPIN,  
	a.QNTOUT/a.Isi2 QNTOUT, a.RPOUT, 
	a.SALDOQNT/a.Isi2 SALDOQNT, a.SALDORP,
	'2' ,A.partNumber,A.NAMAMERK,a.lokasi
	from vwReportStockBrg a
	Where a.Bulan=@Bulan and a.Tahun=@Tahun and A.KODEGDG Like @Kodegdg and a.KODEGRP <> 'JS'
	and a.KodeGrp like @KodeGrp+'%' and a.SALDOQNT/a.Isi2 <0
	and cast (A.pagen as varchar(1)) like  (case when @jenis=0 then '0' when @jenis=1 then '1' when @jenis=2 then '%' End )
	and isrusak like (case when @kodegdg='%' then '0' else '%' End) 
	and A.KODEhdGRP Like @KodeHdGrp
	order by a.kodebrg,a.kodegdg
end else 
If @isi=3
begin
  select 1

end 

End

else
begin
If @isi=1
begin
  if @IsMinus=0
	Select a.KODEGDG,A.KODEBRG,A.NAMABRG,
	Case when @isi=1 then A.SAT1
       when @isi=2 then A.SAT2
       when @isi=3 then ''
       else ''
	end Satuan, 
	a.QNTAWAL/a.Isi1 QntAwal, a.HRGAWAL, 
	a.QNTPBL/a.Isi1 QNTPBL, a.HRGPBL,
	a.QntRPB/a.Isi1 QNTRBP, a.HRGRPB HRGRBP,
	a.QNTPMK/a.Isi1 QNTPMK,   a.HRGPMK,  
	a.QntRPK/a.Isi1 QNTRPK, a.HRGRPK, 
	a.QNTRPB/a.Isi1 QNTRPB, a.HRGRPB, 
	a.QNTPNJ/a.Isi1 QNTPNJ, a.HRGPNJ,
	a.QNTPRJ/a.Isi1 QNTPRJ, a.HRGPRJ, 
	a.QNTRPJ/a.Isi1 QNTRPJ, a.HRGRPJ, 
	a.QNTADI/a.Isi1 QNTADI, a.HRGADI, 
	a.QNTADO/a.Isi1 QNTADO, a.HRGADO, 
	a.QNTUKI/a.Isi1 QNTUKI, a.HRGUKI, 
	a.QNTUKO/a.Isi1 QNTUKO, a.HRGUKO, 
	a.QNTTRI/a.Isi1 QNTTRI, a.HRGTRI, 
	a.QNTTRO/a.Isi1 QNTTRO, a.HRGTRO,
	a.qntrspb/a.ISI1 qntrspb,a.hrgrspb,
	a.qntrgtc/a.ISI1 qntrgtc,a.hrgrgtc,
	A.QntHPrd/a.ISI1 QntHPrd, A.HRGHPrd, 
	a.HRGRATA, a.QNTIN/a.Isi1 QNTIN, a.RPIN, 
	a.QNTOUT/a.Isi1 QNTOUT, a.RPOUT, 
	a.SALDOQNT/a.Isi1 SALDOQNT, a.SALDORP,
	'1' ,A.partNumber,A.NAMAMERK,a.lokasi
	from vwReportStockBrg a
	Where a.Bulan=@Bulan and a.Tahun=@Tahun and A.KODEGDG Like @Kodegdg and a.KODEGRP <> 'JS'
	and a.KodeGrp like @KodeGrp+'%'
	and a.KODEBRG in (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@Tipe)
	and a.SALDOQNT/a.Isi1 <0
	and cast (A.pagen as varchar(1)) like  (case when @jenis=0 then '0' when @jenis=1 then '1' when @jenis=2 then '%' End )
	and isrusak like (case when @kodegdg='%' then '0' else '%' End) 
	and A.KODEhdGRP Like @KodeHdGrp
	order by a.kodebrg,a.kodegdg
	
	if @IsMinus=1
	Select a.KODEGDG,A.KODEBRG,A.NAMABRG,
	Case when @isi=1 then A.SAT1
       when @isi=2 then A.SAT2
       when @isi=3 then ''
       else ''
	end Satuan, 
	a.QNTAWAL/a.Isi1 QntAwal, a.HRGAWAL, 
	a.QNTPBL/a.Isi1 QNTPBL, a.HRGPBL,
	a.QntRPB/a.Isi1 QNTRBP, a.HRGRPB HRGRBP,
	a.QNTPMK/a.Isi1 QNTPMK,   a.HRGPMK,  
	a.QntRPK/a.Isi1 QNTRPK, a.HRGRPK, 
	a.QNTRPB/a.Isi1 QNTRPB, a.HRGRPB, 
	a.QNTPNJ/a.Isi1 QNTPNJ, a.HRGPNJ,
	a.QNTPRJ/a.Isi1 QNTPRJ, a.HRGPRJ, 
	a.QNTRPJ/a.Isi1 QNTRPJ, a.HRGRPJ, 
	a.QNTADI/a.Isi1 QNTADI, a.HRGADI, 
	a.QNTADO/a.Isi1 QNTADO, a.HRGADO, 
	a.QNTUKI/a.Isi1 QNTUKI, a.HRGUKI, 
	a.QNTUKO/a.Isi1 QNTUKO, a.HRGUKO, 
	a.QNTTRI/a.Isi1 QNTTRI, a.HRGTRI, 
	a.QNTTRO/a.Isi1 QNTTRO, a.HRGTRO,
	a.qntrspb/a.ISI1 qntrspb,a.hrgrspb,
	a.qntrgtc/a.ISI1 qntrgtc,a.hrgrgtc,
	A.QntHPrd/a.ISI1 QntHPrd, A.HRGHPrd, 
	a.HRGRATA, a.QNTIN/a.Isi1 QNTIN, a.RPIN, 
	a.QNTOUT/a.Isi1 QNTOUT, a.RPOUT, 
	a.SALDOQNT/a.Isi1 SALDOQNT, a.SALDORP,
	'1' ,A.partNumber,A.NAMAMERK,a.lokasi
	from vwReportStockBrg a
	Where a.Bulan=@Bulan and a.Tahun=@Tahun and A.KODEGDG Like @Kodegdg and a.KODEGRP <> 'JS'
	and a.KodeGrp like @KodeGrp+'%'
	and a.KODEBRG in (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@Tipe)
	and a.SALDOQNT/a.ISI1 <0
	and cast (A.pagen as varchar(1)) like  (case when @jenis=0 then '0' when @jenis=1 then '1' when @jenis=2 then '%' End )
	and isrusak like (case when @kodegdg='%' then '0' else '%' End) 
	and A.KODEhdGRP Like @KodeHdGrp
	order by a.kodebrg,a.kodegdg
	
	
	
end else 
If @isi=2
begin 
if @IsMinus=0
	Select a.KODEGDG,A.KODEBRG,A.NAMABRG,
	Case when @isi=1 then A.SAT1
       when @isi=2 then A.SAT2
       when @isi=3 then ''
       else ''
	end Satuan, 
	a.QNTAWAL/a.Isi2 QntAwal, a.HRGAWAL, 
	a.QNTPBL/a.Isi2 QNTPBL, a.HRGPBL,
	a.QntRPB/a.Isi2 QNTRBP, a.HRGRPB HRGRBP, 
	a.QNTPMK/a.Isi2 QNTPMK, a.HRGPMK, 
	a.QNTRPK/a.Isi2 QNTRPK, a.HRGRPK, 
	a.QNTPNJ/a.Isi2 QNTPNJ, a.HRGPNJ,
	a.QNTPRJ/a.Isi1 QNTPRJ, a.HRGPRJ, 
	a.QNTRPJ/a.Isi2 QNTRPJ, a.HRGRPJ, 
	a.QNTADI/a.Isi2 QNTADI, a.HRGADI, 
	a.QNTADO/a.Isi2 QNTADO, a.HRGADO, 
	a.QNTUKI/a.Isi2 QNTUKI, a.HRGUKI, 
	a.QNTUKO/a.Isi2 QNTUKO, a.HRGUKO, 
	a.QNTTRI/a.Isi2 QNTTRI, a.HRGTRI, 
	a.QNTTRO/a.Isi2 QNTTRO, a.HRGTRO,
	a.qntrspb/a.ISI2 qntrspb,a.hrgrspb,
	a.qntrgtc/a.ISI2 qntrgtc,a.hrgrgtc,
	A.QntHPrd/a.ISI2 QntHPrd, A.HRGHPrd, 
	a.HRGRATA, a.QNTIN/a.Isi2 QNTIN, a.RPIN,  
	a.QNTOUT/a.Isi2 QNTOUT, a.RPOUT, 
	a.SALDOQNT/a.Isi2 SALDOQNT, a.SALDORP,
	'2' ,A.partNumber,A.NAMAMERK,a.lokasi
	from vwReportStockBrg a
	Where a.Bulan=@Bulan and a.Tahun=@Tahun and A.KODEGDG Like @Kodegdg and a.KODEGRP <> 'JS'
	and a.KodeGrp like @KodeGrp+'%'
	and a.KODEBRG in (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@Tipe)
	and a.SALDOQNT/a.ISI2 >0
	and cast (A.pagen as varchar(1)) like  (case when @jenis=0 then '0' when @jenis=1 then '1' when @jenis=2 then '%' End )
	and isrusak like (case when @kodegdg='%' then '0' else '%' End) 
	and A.KODEhdGRP Like @KodeHdGrp
	order by a.kodebrg,a.kodegdg
if @IsMinus=1
	Select a.KODEGDG,A.KODEBRG,A.NAMABRG,
	Case when @isi=1 then A.SAT1
       when @isi=2 then A.SAT2
       when @isi=3 then ''
       else ''
	end Satuan, 
	a.QNTAWAL/a.Isi2 QntAwal, a.HRGAWAL, 
	a.QNTPBL/a.Isi2 QNTPBL, a.HRGPBL,
	a.QntRPB/a.Isi2 QNTRBP, a.HRGRPB HRGRBP, 
	a.QNTPMK/a.Isi2 QNTPMK, a.HRGPMK, 
	a.QNTRPK/a.Isi2 QNTRPK, a.HRGRPK, 
	a.QNTPNJ/a.Isi2 QNTPNJ, a.HRGPNJ,
	a.QNTPRJ/a.Isi1 QNTPRJ, a.HRGPRJ, 
	a.QNTRPJ/a.Isi2 QNTRPJ, a.HRGRPJ, 
	a.QNTADI/a.Isi2 QNTADI, a.HRGADI, 
	a.QNTADO/a.Isi2 QNTADO, a.HRGADO, 
	a.QNTUKI/a.Isi2 QNTUKI, a.HRGUKI, 
	a.QNTUKO/a.Isi2 QNTUKO, a.HRGUKO, 
	a.QNTTRI/a.Isi2 QNTTRI, a.HRGTRI, 
	a.QNTTRO/a.Isi2 QNTTRO, a.HRGTRO,
	a.qntrspb/a.ISI2 qntrspb,a.hrgrspb,
	a.qntrgtc/a.ISI2 qntrgtc,a.hrgrgtc,
	A.QntHPrd/a.ISI2 QntHPrd, A.HRGHPrd, 
	a.HRGRATA, a.QNTIN/a.Isi2 QNTIN, a.RPIN,  
	a.QNTOUT/a.Isi2 QNTOUT, a.RPOUT, 
	a.SALDOQNT/a.Isi2 SALDOQNT, a.SALDORP,
	'2' ,A.partNumber,A.NAMAMERK,a.lokasi
	from vwReportStockBrg a
	Where a.Bulan=@Bulan and a.Tahun=@Tahun and A.KODEGDG Like @Kodegdg and a.KODEGRP <> 'JS'
	and a.KodeGrp like @KodeGrp+'%'
	and a.KODEBRG in (select ID from Dbcustomize Where Iduser=@Iduser and Tipe=@Tipe)
	and a.SALDOQNT/a.ISI2 <0
	and cast (A.pagen as varchar(1)) like  (case when @jenis=0 then '0' when @jenis=1 then '1' when @jenis=2 then '%' End )
	and isrusak like (case when @kodegdg='%' then '0' else '%' End) 
	and A.KODEhdGRP Like @KodeHdGrp
	order by a.kodebrg,a.kodegdg
		
end else 
If @isi=3
begin
  select 1

end
End
GO
-- Sp_reportUbahKemasanBahan : isi DBSMLNEW
ALTER Procedure [dbo].[Sp_reportUbahKemasanBahan]
--declare
@SReport varchar(1),
@Ordr varchar(1),
@tgl1 datetime,
@tgl2 Datetime,
@isiList varchar(200),
@x int=1,
@userid varchar(15),
@Tipe varchar(15)
as
--Declare @SReport varchar(1),@Ordr varchar(1),@tgl1 datetime,@tgl2 Datetime,@isiList varchar(200)
--select @SReport='T',@Ordr='N', @tgl1='06/01/2023',@tgl2='06/30/2023',@isiList='%%' 

if @SReport='T'
begin
	If @Ordr='N'
		begin
		  select A.*
		  /*,
		    (select top 1 HPP from vwKartuStock 
			where Tanggal < A.TANGGAL
			and KodeBrg=A.KODEBRG and Kodegdg=A.Kodegdg and Tipe<>'awl'
			order by Tanggal desc, Prioritas desc, NoBukti desc, Urut desc) HPPKartu
			
			*/
		  from vwDetailUbahKemasan A where A.Tanggal between @tgl1 and @tgl2 and NoBukti Like '%KMB%'
		  order by A.NoBukti,A.TANGGAL
		end 
	else If @Ordr='B'
		begin
		  select A.*, 
		    (select top 1 HPP from vwKartuStock 
			where Tanggal < A.TANGGAL
			and KodeBrg=A.KODEBRG and Kodegdg=A.Kodegdg and Tipe<>'awl'
			order by Tanggal desc, Prioritas desc, NoBukti desc, Urut desc) HPPKartu 
			from VwReportUbahKemasanBahan A where A.Tanggal between @tgl1 and @tgl2 
			-- and kodebrg='10201031020017'
			order by A.KodeBrg
		end 
End
else
begin
If @Ordr='N'
		begin
		  select A.*, 
		    (select top 1 HPP from vwKartuStock 
			where Tanggal < A.TANGGAL
			and KodeBrg=A.KODEBRG and Kodegdg=A.Kodegdg and Tipe<>'awl'
			order by Tanggal desc, Prioritas desc, NoBukti desc, Urut desc) HPPKartu 
			from VwReportUbahKemasanBahan A where A.Tanggal between @tgl1 and @tgl2 
		  and A.NOBUKTI in (select ID from DBCUSTOMIZE where IDuser=@userId and Tipe=@Tipe)
		  order by A.NoBukti,A.TANGGAL
		end 
	else If @Ordr='B'
		begin
		  select A.*, 
		    (select top 1 HPP from vwKartuStock 
			where Tanggal < A.TANGGAL
			and KodeBrg= A.KODEBRG and Kodegdg=A.Kodegdg and Tipe<>'awl'
			order by Tanggal desc, Prioritas desc, NoBukti desc, Urut desc) HPPKartu 
			from VwReportUbahKemasanBahan A where A.Tanggal between @tgl1 and @tgl2 
		 and A.kodebrg in (select ID from DBCUSTOMIZE where IDuser=@userId and Tipe=@Tipe) 
		  order by A.KodeBrg
		end 
End
GO
-- SP_TransferBlmTerima : isi DBSMLNEW
ALTER Procedure [dbo].[SP_TransferBlmTerima]
--DECLARE 
@TGLA DATETIME,@TGLB datetime,@kirim int

--SELECT @TANGGAL=getdate()
as

if @kirim=0
begin
SELECT A.NOBUKTI,A.URUT,b.TANGGAL,A.KODEBRG,D.NAMABRG,CASE WHEN A.NOSAT=1 THEN A.SAT_1 else A.sat_2 END SATUAN,a.hpp,
CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END QNT,(CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END)*a.hpp hppkirim,
CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END QNTTERIMA,(CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END)*a.hpp hppterima,
CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END-ISNULL(CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END,0) SISA,
(CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END-ISNULL(CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END,0)) * A.HPP TOTHPP,
CASE WHEN A.NOSAT=1 THEN A.SAT_1 else A.sat_2 END SATUAN,
A.GDGASAL,A.GDGTUJUAN,c.noterima nobuktiterima,c.tglterima
FROM DBTRANSFERDET A
LEFT OUTER JOIN DBTRANSFER B ON A.NOBUKTI=B.NOBUKTI
LEFT OUTER JOIN (SELECT b.nobukti noterima,b.TANGGAL tglterima,NOPRTRANSFER, URUTPRTRANSFER,SUM(QNT) QNT,SUM(QNT2) QNT2
				 FROM DBTRANSFERDET A
				 left outer join dbtransfer B on A.nobukti=B.nobukti
				 where Isnull(Pterima,0)=1 and isnull(Nooutbrg,'')<>''
				 GROUP BY b.nobukti,b.TANGGAL,NOPRTRANSFER, URUTPRTRANSFER ) C ON A.NOBUKTI=C.NOPRTRANSFER AND A.Urut=C.URUTPRTRANSFER
Left outer join DBBARANG d on a.KODEBRG=d.KODEBRG				 
WHERE year(b.tanggal)>=2026 and isnull(a.qnt,0)<>0 and a.GDGASAL<>a.GDGTUJUAN and ISNULL(B.pTERIMA,0)=0 and b.TANGGAL between @tgla and @tglb
order by a.nobukti

end
else

if @kirim=2
begin
SELECT A.NOBUKTI,A.URUT,b.TANGGAL,A.KODEBRG,D.NAMABRG,CASE WHEN A.NOSAT=1 THEN A.SAT_1 else A.sat_2 END SATUAN,a.hpp,
CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END QNT,(CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END)*a.hpp hppkirim,
CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END QNTTERIMA,(CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END)*a.hpp hppterima,
CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END-ISNULL(CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END,0) SISA,
(CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END-ISNULL(CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END,0)) * A.HPP TOTHPP,
CASE WHEN A.NOSAT=1 THEN A.SAT_1 else A.sat_2 END SATUAN,
A.GDGASAL,A.GDGTUJUAN,c.noterima nobuktiterima,c.tglterima
FROM DBTRANSFERDET A
LEFT OUTER JOIN DBTRANSFER B ON A.NOBUKTI=B.NOBUKTI
LEFT OUTER JOIN (SELECT b.nobukti noterima,b.TANGGAL tglterima,NOPRTRANSFER, URUTPRTRANSFER,SUM(QNT) QNT,SUM(QNT2) QNT2
				 FROM DBTRANSFERDET A
				 left outer join dbtransfer B on A.nobukti=B.nobukti
				 where Isnull(Pterima,0)=1 and isnull(Nooutbrg,'')<>''
				 GROUP BY b.nobukti,b.TANGGAL,NOPRTRANSFER, URUTPRTRANSFER ) C ON A.NOBUKTI=C.NOPRTRANSFER AND A.Urut=C.URUTPRTRANSFER
Left outer join DBBARANG d on a.KODEBRG=d.KODEBRG				 
WHERE year(b.tanggal)>=2026 and ISNULL(B.pTERIMA,0)=0 and a.GDGASAL<>a.GDGTUJUAN  and b.TANGGAL <= @tglb and isnull(a.qnt,0)<>0 and isnull(C.QNT,0)=0
order by a.nobukti

end


else
begin
SELECT A.NOBUKTI,A.URUT,b.TANGGAL,A.KODEBRG,D.NAMABRG,CASE WHEN A.NOSAT=1 THEN A.SAT_1 else A.sat_2 END SATUAN,a.hpp,
CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END QNT,(CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END)*a.hpp hppkirim,
CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END QNTTERIMA,(CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END)*a.hpp hppterima,
CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END-ISNULL(CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END,0) SISA,
(CASE WHEN A.NOSAT=1 THEN a.QNT else a.QNT2 END-ISNULL(CASE WHEN A.NOSAT=1 THEN isnull(C.QNT,0) else isnull(C.QNT2,0) END,0)) * A.HPP TOTHPP,
CASE WHEN A.NOSAT=1 THEN A.SAT_1 else A.sat_2 END SATUAN,
A.GDGASAL,A.GDGTUJUAN,c.noterima nobuktiterima,c.tglterima
FROM DBTRANSFERDET A
LEFT OUTER JOIN DBTRANSFER B ON A.NOBUKTI=B.NOBUKTI
LEFT OUTER JOIN (SELECT b.nobukti noterima,b.TANGGAL tglterima,NOPRTRANSFER, URUTPRTRANSFER,SUM(QNT) QNT,SUM(QNT2) QNT2
				 FROM DBTRANSFERDET A
				 left outer join dbtransfer B on A.nobukti=B.nobukti
				 where Isnull(Pterima,0)=1 and isnull(Nooutbrg,'')<>''
				 GROUP BY b.nobukti,b.TANGGAL,NOPRTRANSFER, URUTPRTRANSFER ) C ON A.NOBUKTI=C.NOPRTRANSFER AND A.Urut=C.URUTPRTRANSFER
Left outer join DBBARANG d on a.KODEBRG=d.KODEBRG				 
WHERE year(b.tanggal)>=2026 and ISNULL(B.pTERIMA,0)=1 and a.GDGASAL<>a.GDGTUJUAN  and b.TANGGAL between @tgla and @tglb
order by a.nobukti
end
GO
IF OBJECT_ID('dbo.sp_DEATFASTSLOW') IS NULL EXEC('CREATE PROCEDURE dbo.sp_DEATFASTSLOW AS RETURN');
GO
-- sp_DEATFASTSLOW : SP baru dari DBSMLNEW (belum ada di DBSMLBARU)
ALTER PROCEDURE [dbo].[sp_DEATFASTSLOW] 
@Bulan Integer,
@Tahun Integer

AS


------------- DEAD STOCK
select a.KODEBRG,b.NAMABRG,b.SAT1,sld qtydead,rp rpdead , 0 qtyfast,0 rpfast,0 qtyslow,0 rpslow,0 qtystock,0 qtykebutuhan
from DBSTOCKBRG a
left outer join DBBARANG b on b.KODEBRG=a.KODEBRG
left outer join (select kodebrg,sum(saldoqnt)sld,SUM(saldorp)rp from dbstockbrg where TAHUN=@Tahun and BULAN=@Bulan group by KODEBRG) c on c.KODEBRG=a.KODEBRG
where ((TAHUN=@Tahun-1 and BULAN>@Bulan) or (TAHUN=@Tahun and BULAN<=@Bulan)) and b.KODEGRP='BJ'
group by a.kodebrg ,b.NAMABRG,b.SAT1,sld,rp
having SUM(qntpnj)=0 and sum(qntout)=0 and ISNULL(sld,0)>=1
union all
------------- FAST STOCK
select x.KODEBRG,namabrg,b.sat1,0,0, c.sld,c.rp,0,0,0,0
from (
select KODEBRG,tahun,bulan,case when tahun=@Tahun-1 then bulan-@Bulan else bulan+(12-5) end jml from DBSTOCKBRG 
where ((TAHUN=@Tahun-1 and BULAN>@Bulan) or (TAHUN=@Tahun and BULAN<=@Bulan))
group by KODEBRG,TAHUN,BULAN
having SUM(qntpnj)+SUM(QNTUKO)<>0
)x
left outer join DBBARANG b on b.KODEBRG=x.KODEBRG 
left outer join (select kodebrg,sum(saldoqnt)sld,SUM(saldorp)rp from dbstockbrg where TAHUN=@Tahun and BULAN=@bulan group by KODEBRG) c on c.KODEBRG=x.KODEBRG
group by x.kodebrg,namabrg,SAT1, c.sld,c.rp
having SUM(jml)>=57 and ISNULL(c.sld,0)<>0

---------------SLOW
union all
select x.KODEBRG,namabrg,b.sat1,0,0,0,0, c.sld,c.rp,0,0
from (
select KODEBRG,tahun,bulan,case when tahun=@Tahun-1 then bulan-@Bulan else bulan+(12-@Bulan) end jml from DBSTOCKBRG 
where ((TAHUN=@Tahun-1 and BULAN>@Bulan) or (TAHUN=@Tahun and BULAN<=@Bulan))
group by KODEBRG,TAHUN,BULAN
having SUM(qntpnj)+SUM(QNTUKO)<>0
)x
left outer join DBBARANG b on b.KODEBRG=x.KODEBRG 
left outer join (select kodebrg,sum(saldoqnt)sld,SUM(saldorp)rp from dbstockbrg where TAHUN=@Tahun and BULAN=@bulan group by KODEBRG) c on c.KODEBRG=x.KODEBRG

group by x.kodebrg,namabrg,sat1, c.sld,c.rp
having SUM(jml)<57 and ISNULL(c.sld,0)<>0
-------------------------- STOCK 0
union all
select x.KODEBRG,namabrg,b.sat1,0,0,0,0,0,0, c.sld,SUM(klr)/sum(bln) rata
from (
select KODEBRG,tahun,bulan,case when tahun=2026-1 then bulan-5 else bulan+(12-5) end jml,1 bln ,SUM(qntpnj)+SUM(QNTUKO) klr from DBSTOCKBRG 
where ((TAHUN=2026-1 and BULAN>5) or (TAHUN=2026 and BULAN<=5))
group by KODEBRG,TAHUN,BULAN
having SUM(qntpnj)+SUM(QNTUKO)<>0
)x
left outer join DBBARANG b on b.KODEBRG=x.KODEBRG 
left outer join (select kodebrg,sum(saldoqnt)sld,SUM(saldorp)rp from dbstockbrg where TAHUN=@Tahun and BULAN=@bulan group by KODEBRG) c on c.KODEBRG=x.KODEBRG
group by x.kodebrg,namabrg,sat1, c.sld,c.rp
having SUM(jml)>57 and SUM(klr)/sum(bln)>ISNULL(c.sld,0)
GO
IF OBJECT_ID('dbo.Sp_reportBeligrafik2') IS NULL EXEC('CREATE PROCEDURE dbo.Sp_reportBeligrafik2 AS RETURN');
GO
-- Sp_reportBeligrafik2 : SP baru dari DBSMLNEW (belum ada di DBSMLBARU)
ALTER PROCEDURE [dbo].[Sp_reportBeligrafik2]
    @tgl1 datetime
AS
BEGIN

    -- PENJUALAN
    SELECT
        'JUAL' AS Tipe,
        MONTH(a.Tanggal) AS Bulan,
        YEAR(a.Tanggal) AS Tahun,
        SUM(b.NDPPRp) AS Total
    FROM DBInvoicePL a
    LEFT JOIN DBInvoicePLDET b
        ON b.NoBukti = a.NoBukti
    WHERE YEAR(a.Tanggal) = YEAR(@tgl1)
    GROUP BY MONTH(a.Tanggal), YEAR(a.Tanggal)

    UNION ALL

    -- PEMBELIAN
    SELECT
        'BELI' AS Tipe,
        MONTH(a.Tanggal) AS Bulan,
        YEAR(a.Tanggal) AS Tahun,
        SUM(b.NDPPRp) AS Total
    FROM DBBELI a
    LEFT JOIN DBBELIDET b
        ON b.NoBukti = a.NoBukti
    WHERE YEAR(a.Tanggal) = YEAR(@tgl1)
    GROUP BY MONTH(a.Tanggal), YEAR(a.Tanggal)

END
GO
IF OBJECT_ID('dbo.SP_REPORTHISSOOUTweb') IS NULL EXEC('CREATE PROCEDURE dbo.SP_REPORTHISSOOUTweb AS RETURN');
GO
-- SP_REPORTHISSOOUTweb : SP baru dari DBSMLNEW (belum ada di DBSMLBARU)
ALTER PROCEDURE [dbo].[SP_REPORTHISSOOUTweb]
--DECLARE
@TGLAWAL DATETIME,
@TGLAKHIR DATETIME,
@KodeCust varchar(30),
@Lokasi varchar(70)='',
@IDUser varchar(30),
@TipeTrans varchar(10),
@isout tinyint=0

/*
SELECT 
@TGLAWAL='01/01/2019',
@TGLAKHIR='03/31/2020',
@KodeCust='-',
@Lokasi='-',
@IDUser='SA',
@TipeTrans=''
*/

AS

if @isout=0
begin

select A.NOBUKTI
,A.URUT,A.NOBUKTI + CAST(A.URUT as varchar(3)) Keynobukti
,B.TANGGAL,B.NoPesanan,A.KODEBRG
,CASE WHEN A.NAMABRG='' THEN F.NAMABRG ELSE A.NAMABRG END NAMABRG,
CASE WHEN A.SATX='' THEN A.SATUAN ELSE A.SATX END SATUAN,A.QNT2 QNT,
D.NoBukti NOBUKTISPB,D.Tanggal TGLSPB,E.NoBukti NOBINV,G.Tanggal TGLINV,
B.KODECUST,J.NAMACUSTSUPP,
a.QNT QNTSO,C.QNT2 QNTSPB,E.QNT2 QNTINV ,C1.NoBukti NORSPB,C2.Tanggal TGLRSPB,C1.QNT2 QNTRSPB,

 ISnull(M.NAMAHDGRP,'')+ISNULL(F.KodeHdGrp,'') NAMAHDGRP
         ,ISnull(m3.Nama,'')+ISNULL(B.KodePF,'') namaPIC
         ,ISnull(m4.NamaSubGrp,'')+ISNULL(m4.KODESUBGRP,'') NamaSubGrp
         ,ISnull(m5.Keterangan,'')+ISNULL(m5.Urut,'') NamaSubkategori
         ,ISnull(m6.NAMAMERK,'')+ISNULL(F.KodeMerk,'') NAMAMERK,B.KodePF,
         m7.KODEKEBUN,m7.Nama Namakebun,B.TGLKIRIM,DATEDIFF(Day,ISNULL(d.Tanggal,Getdate()),B.TglKirim) LeadTime
--,dbo.SaldoQntDJHIS(a.KODEBRG,'G01',B.TANGGAL) sTOCK ,
,isnull(xz.SaldoQnt,0) sTOCK,
dATEDIFF(DAY,G.TANGGAL,D.TANGGAL) SLISIHSPBINVOICE,a.HARGA,a.QNTBATAL
,CD.KODEGROUPCUSTSUPP,CD.NAMAGROUPCUSTSUPP
,A.QNT2 - A.QNTBATAL - C.QNT2 + ISNULL(C1.QNT2,0) OSSO
,a.QNT2 -  ISNULL(b9.QntSpb,0) Sisa,
case when year(D.TGLTERIMA)=1899 then Null else D.TGLTERIMA end Tglterima,
(a.QNT), (ISNULL(A.QNTBATAL,0) * A.ISI) ,ISNULL(h2.QNTINvoice,0) inv

from DBSODET a
left outer join DBSO b on a.NOBUKTI=b.NOBUKTI
left outer join dbSPBDet c on a.NOBUKTI=c.NoSo and a.URUT=c.UrutSo
LEFT OUTER JOIN DBRSPBDet C1 ON C.NOBUKTI=C1.NoSPB AND C.Urut=C1.UrutSPB
LEFT OUTER JOIN DBRSPB C2 ON C.NOBUKTI=C2.NoBukti 
left outer join dbSPB d on  c.NoBukti=d.NoBukti
left outer join dbInvoicePLDet e on c.NoBukti=e.NoSPB and c.Urut=E.UrutSPB 
LEFT OUTER JOIN DBBARANG F ON A.KODEBRG=F.KODEBRG
LEFT OUTER JOIN DBINVOICEPL G ON E.NoBukti=G.NoBukti

LEFT OUTER JOIN DBCUSTSUPP J ON B.KODECUST=J.KODECUSTSUPP

LEFT OUTER JOIN DBHDGROUP M ON F.KodeHdGrp=M.KODEHDGRP
Left Outer Join DBPICCUSTSUPP M3 on B.KodePF=M3.KODEPIC and B.KODECUST=m3.KODECUSTSUPP
Left Outer join dbSubGroup m4 on m4.KodeGrp=F.KODEGRP and m4.KodeSubGrp=F.KODESUBGRP and F.KodeHdGrp=m4.KodeHDGrp
left outer join DBSubGroupJnsTambah m5 on F.KODEGRP=M5.KodeGrp and F.KodeHdGrp=M5.hdgroup
				and F.KODESUBGRP=m5.KodeSubGrp AND F.KODESUBKATEGORI=m5.Urut
left Outer join DBMERK m6 on F.KodeMerk=m6.KODEMERK
Left outer join DBKEBUNCUSTSUPP m7 on B.KODECUST=m7.KODECUSTSUPP 
and m7.KODEKEBUN=case when isnull(B.KODEKEBUN,'')='' then ISnull(B.KODEKEBUN,'') else ISnull(B.KODEKEBUN,'')End


left outer join (select b.NoSo,b.UrutSo,SUM(a.QNT) QNTINvoice ,SUM(a.QNT2) QNT2Nvoice 
				 from dbInvoicePLDet a
				 left outer join dbSPBDet b on a.NoSPB=b.NoBukti and a.UrutSPB=b.Urut
				 group by b.NoSo,b.UrutSo) h2 on a.NOBUKTI=H2.NoSo and a.URUT=h2.UrutSo						 
left outer join DBGROUPCUSTSUPP cd on  J.agent=cd.KODEGROUPCUSTSUPP

left outer join (select b.NoSo,b.UrutSo,sum(b.QNT2) - ISNULL(SUM(ISNULL(b1.Qntrspb,0)),0) QntSpb,C.QNT2
			      from  dbSPBDet b 
				  left outer join (select nospb,UrutSPB,SUM(QNT2) Qntrspb
											         from DBRSPBDet
											         group by NoSPB,UrutSPB
												  )b1 on b.NoBukti=b1.NoSPB and b.Urut=b1.UrutSPB
											  
LEFT OUTER JOIN DBSODET C ON B.NoSo=C.NOBUKTI AND B.UrutSo=C.URUT												  
				 group by b.NoSo,b.UrutSo,C.QNT2			  
				)B9 on a.NOBUKTI=b9.NoSo and a.URUT=b9.UrutSo 
left outer join dbo.SaldoQntDJHIStbl(getdate()) xz on a.kodebrg=xz.kodebrg

WHERE B.TANGGAL <=@TGLAKHIR --and A.NOBUKTI like '%SPL/SO/00300/1215%'
and b.KODECUST like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
and ISNULL(M7.Nama,'')+ISNULL(m7.KODEKEBUN,'') like case when @Lokasi='-' OR @Lokasi='' then '%' else @Lokasi End

and 

--(a.QNT)- (ISNULL(A.QNTBATAL,0) * A.ISI) - ISNULL(h2.QNTINvoice,0)>0

case when a.NOSAT=1 then
(a.QNT)- (ISNULL(A.QNTBATAL,0) * A.ISI) - ISNULL(h2.QNTINvoice,0)
else 
(a.QNT2)- (ISNULL(A.QNTBATAL,0)) - ISNULL(h2.QNT2Nvoice,0)
end
>0



--and a.NOBUKTI='MKS/SO/00042/1219' AND A.KODEBRG='02.046.00002'
ORDER BY B.TANGGAL,a.NOBUKTI,a.URUT

end
else
begin

select A.NOBUKTI
,A.URUT,A.NOBUKTI + CAST(A.URUT as varchar(3)) Keynobukti
,B.TANGGAL,B.NoPesanan,A.KODEBRG
,CASE WHEN A.NAMABRG='' THEN F.NAMABRG ELSE A.NAMABRG END NAMABRG,
CASE WHEN A.SATX='' THEN A.SATUAN ELSE A.SATX END SATUAN,A.QNT2 QNT,
D.NoBukti NOBUKTISPB,D.Tanggal TGLSPB,E.NoBukti NOBINV,G.Tanggal TGLINV,
B.KODECUST,J.NAMACUSTSUPP,
a.QNT QNTSO,C.QNT2 QNTSPB,E.QNT2 QNTINV ,C1.NoBukti NORSPB,C2.Tanggal TGLRSPB,C1.QNT2 QNTRSPB,

 ISnull(M.NAMAHDGRP,'')+ISNULL(F.KodeHdGrp,'') NAMAHDGRP
         ,ISnull(m3.Nama,'')+ISNULL(B.KodePF,'') namaPIC
         ,ISnull(m4.NamaSubGrp,'')+ISNULL(m4.KODESUBGRP,'') NamaSubGrp
         ,ISnull(m5.Keterangan,'')+ISNULL(m5.Urut,'') NamaSubkategori
         ,ISnull(m6.NAMAMERK,'')+ISNULL(F.KodeMerk,'') NAMAMERK,B.KodePF,
         m7.KODEKEBUN,m7.Nama Namakebun,B.TGLKIRIM,DATEDIFF(Day,ISNULL(d.Tanggal,Getdate()),B.TglKirim) LeadTime
--,dbo.SaldoQntDJHIS(a.KODEBRG,'G01',B.TANGGAL) sTOCK ,
,isnull(xz.SaldoQnt,0) sTOCK,
dATEDIFF(DAY,G.TANGGAL,D.TANGGAL) SLISIHSPBINVOICE,a.HARGA,a.QNTBATAL
,CD.KODEGROUPCUSTSUPP,CD.NAMAGROUPCUSTSUPP
,A.QNT2 - A.QNTBATAL - C.QNT2 + ISNULL(C1.QNT2,0) OSSO
,a.QNT2 -  ISNULL(b9.QntSpb,0) Sisa,
case when year(D.TGLTERIMA)=1899 then Null else D.TGLTERIMA end Tglterima,
(a.QNT), (ISNULL(A.QNTBATAL,0) * A.ISI) ,ISNULL(h2.QNTINvoice,0) inv

from DBSODET a
left outer join DBSO b on a.NOBUKTI=b.NOBUKTI
left outer join dbSPBDet c on a.NOBUKTI=c.NoSo and a.URUT=c.UrutSo
LEFT OUTER JOIN DBRSPBDet C1 ON C.NOBUKTI=C1.NoSPB AND C.Urut=C1.UrutSPB
LEFT OUTER JOIN DBRSPB C2 ON C.NOBUKTI=C2.NoBukti 
left outer join dbSPB d on  c.NoBukti=d.NoBukti
left outer join dbInvoicePLDet e on c.NoBukti=e.NoSPB and c.Urut=E.UrutSPB 
LEFT OUTER JOIN DBBARANG F ON A.KODEBRG=F.KODEBRG
LEFT OUTER JOIN DBINVOICEPL G ON E.NoBukti=G.NoBukti

LEFT OUTER JOIN DBCUSTSUPP J ON B.KODECUST=J.KODECUSTSUPP

LEFT OUTER JOIN DBHDGROUP M ON F.KodeHdGrp=M.KODEHDGRP
Left Outer Join DBPICCUSTSUPP M3 on B.KodePF=M3.KODEPIC and B.KODECUST=m3.KODECUSTSUPP
Left Outer join dbSubGroup m4 on m4.KodeGrp=F.KODEGRP and m4.KodeSubGrp=F.KODESUBGRP and F.KodeHdGrp=m4.KodeHDGrp
left outer join DBSubGroupJnsTambah m5 on F.KODEGRP=M5.KodeGrp and F.KodeHdGrp=M5.hdgroup
				and F.KODESUBGRP=m5.KodeSubGrp AND F.KODESUBKATEGORI=m5.Urut
left Outer join DBMERK m6 on F.KodeMerk=m6.KODEMERK
Left outer join DBKEBUNCUSTSUPP m7 on B.KODECUST=m7.KODECUSTSUPP 
and m7.KODEKEBUN=case when isnull(B.KODEKEBUN,'')='' then ISnull(B.KODEKEBUN,'') else ISnull(B.KODEKEBUN,'')End


left outer join (select b.NoSo,b.UrutSo,SUM(a.QNT) QNTINvoice ,SUM(a.QNT2) QNT2Nvoice 
				 from dbInvoicePLDet a
				 left outer join dbSPBDet b on a.NoSPB=b.NoBukti and a.UrutSPB=b.Urut
				 group by b.NoSo,b.UrutSo) h2 on a.NOBUKTI=H2.NoSo and a.URUT=h2.UrutSo						 
left outer join DBGROUPCUSTSUPP cd on  J.agent=cd.KODEGROUPCUSTSUPP

left outer join (select b.NoSo,b.UrutSo,sum(b.QNT2) - ISNULL(SUM(ISNULL(b1.Qntrspb,0)),0) QntSpb,C.QNT2
			      from  dbSPBDet b 
				  left outer join (select nospb,UrutSPB,SUM(QNT2) Qntrspb
											         from DBRSPBDet
											         group by NoSPB,UrutSPB
												  )b1 on b.NoBukti=b1.NoSPB and b.Urut=b1.UrutSPB
											  
LEFT OUTER JOIN DBSODET C ON B.NoSo=C.NOBUKTI AND B.UrutSo=C.URUT												  
				 group by b.NoSo,b.UrutSo,C.QNT2			  
				)B9 on a.NOBUKTI=b9.NoSo and a.URUT=b9.UrutSo 
left outer join dbo.SaldoQntDJHIStbl(getdate()) xz on a.kodebrg=xz.kodebrg

WHERE B.TANGGAL between @TGLAWAL and @TGLAKHIR
and b.KODECUST like case when @KodeCust='-' OR @KodeCust='' then '%' else @KodeCust End
and ISNULL(M7.Nama,'')+ISNULL(m7.KODEKEBUN,'') like case when @Lokasi='-' OR @Lokasi='' then '%' else @Lokasi End

 

ORDER BY B.TANGGAL,a.NOBUKTI,a.URUT

end
GO
