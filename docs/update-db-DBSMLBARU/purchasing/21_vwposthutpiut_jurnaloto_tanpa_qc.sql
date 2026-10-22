-- =====================================================================
-- 21 - vwPostHutPiut & vwPostJurnalOto: posting hutang & jurnal pembelian
--      barang tidak lagi menunggu QC (disamakan dengan DBSMLNEW)
-- Menu: Penerimaan ACC gabungan (newpobeliacc) - tombol Otorisasi
-- Tanggal: 21 Oktober 2026 (sudah dijalankan di DBSMLBARU)
--
-- Masalah: saat Otorisasi, controller memanggil sp_ProsesPostingHutPiut dan
--   sp_ProsesPostingJurnalOto. Di DBSMLBARU kedua view sumbernya hanya
--   memuat pembelian barang yang sudah QC (pQC=1). Pembelian yang di-ACC
--   sebelum QC tidak pernah masuk DBHUTPIUT/jurnal (Sp_QC tidak memposting
--   ulang), sehingga kolom Hutang di menu tampil "Lunas".
--
-- Perubahan (definisi diambil persis dari DBSMLNEW):
--   1. Syarat  where ISNULL(pqc,0)= case when ISNULL(pJasa,0)=0 then 1 else 0 End
--      (vwPostHutPiut) dan  where (ISNULL(pQC,0)=1 Or isnull(a.pJasa,0)<>0)
--      (vwPostJurnalOto) dinonaktifkan (dijadikan komentar).
--   2. Tanggal hutang/jurnal pembelian = A.TANGGAL (tanggal LPB),
--      bukan lagi TglQC. Jatuh tempo = A.TANGGAL + A.HARI.
--   Nama & urutan kolom view tidak berubah.
--
-- Aman dijalankan ulang (ALTER VIEW berisi definisi lengkap).
-- =====================================================================
GO












































ALTER View [dbo].[vwPostHutPiut]
as
--- UANG MUKA PEMBELIAN
SELECT  A.NoBukti NoFaktur, '' NoRetur, 'T' TipeTrans, d.KODESUPP KODECUSTSUPP, 
	'' NoBukti, 1 NoMsk, 1 Urut, A.TANGGAL, A.TANGGAL+isnull(D.HARI,0) JatuhTempo, 
	0 Debet, 
	--round(sum(a.DPP+a.PPN),0)*a.KURS Kredit
	case when A.VALAS='IDR' then round(sum(a.DPP+a.PPN),0) *a.KURS else Sum(a.DPP+a.PPN)*a.KURS End Kredit
	, Sum(a.DPP+a.PPN)*a.KURS Saldo, 
	A.Valas, A.KURS, 
	 0 DebetD, case when A.Valas='IDR' then 0 else sum(round(a.DPP,2)+round(a.PPN,2)) end kreditD,
	case when A.Valas='IDR' then 0 else sum(round(a.DPP,2)+round(a.PPN,2)) end SaldoD, 
    '' KodeSales, 'HT' Tipe, case when a.tanggal<'11-01-2024' then  '21201' else '116100'end PERKIRAAN, '' Catatan, 
    'UMJ' NoInvoice, A.NoBukti NoPajak, A.Valas KodeVls_, A.Kurs Kurs_,
    A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   dbUMJUAL A      
left outer join DBPO D on D.NOBUKTI=a.NOSO
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='HT'AND X.Urut=2) J on 1=1 
where pBeli=1 and a.NoRetur=''
Group by A.NoBukti, d.KODESUPP, A.TANGGAL, D.HARI,
	A.Valas, A.Kurs, A.IsOtorisasi1, A.NoJurnal,J.Perkiraan

union all
-- PEMBELIAN KREDIT

SELECT A.NOBUKTI NoFaktur, '' NoRetur, 'T' TipeTrans,case when a.TIPEBAYAR=1 then A.KodeSupp else 'S_TUNAI' end KODECUSTSUPP, 
	isnull(c.NOBUKTI,''), 1 NoMsk, 1 Urut,
	--cast(case when ISNULL(pJasa,0)=0 then A.TglQC else A.TANGGAL End as Date) tglQc, 
	A.TANGGAL,
	--A.TglQC
	--cast(case when ISNULL(pJasa,0)=0 then A.TglQC else A.TANGGAL End 
	CAST(A.TANGGAL+A.HARI as Date) JatuhTempo,
	0 Debet,
	case when ISNULL(a.NuangMuka,0)=0 then sum(B.NNETRp)  else 
	case when a.ppn<>0 then 
	   SUM(B.ndpprp)-round(ISNULL(a.NuangMuka,0)*case when a.KODEVLS='IDR' then 1 else 
	   case when isnull(d.NOBUKTI,'')<>'' then 
	   (select kurs from dbumjual where NOBUKTI=d.NOBUKTI) else c.kurs end end,0) 
     +(SUM(B.ndpprp)-round(ISNULL(a.NuangMuka,0)*case when a.KODEVLS='IDR' then 1 else 
        case when isnull(d.NOBUKTI,'')<>'' then 
        (select kurs from dbumjual where NOBUKTI=d.NOBUKTI) else c.kurs end end,0))*max(b.nilaippn)/100 
	else SUM(B.ndpprp)-round(ISNULL(a.NuangMuka,0)*case when a.KODEVLS='IDR' then 1 else 
	case when isnull(d.NOBUKTI,'')<>'' then 
	   (select kurs from dbumjual where NOBUKTI=d.NOBUKTI) else c.kurs end end,0) 
	end
	end	- case when a.pJasa=0 then 0 else sum(B.NDPPRp)*isnull(a.NPPH23,0)/100 end	Kredit,
	case when ISNULL(a.NuangMuka,0)=0 then sum(B.NNETRp)  else 
	case when a.ppn<>0 then 
	   SUM(B.ndpprp)-round(ISNULL(a.NuangMuka,0)*case when a.KODEVLS='IDR' then 1 else 
	   case when isnull(d.NOBUKTI,'')<>'' then 
	   (select kurs from dbumjual where NOBUKTI=d.NOBUKTI) else c.kurs end end,0) 
     +(SUM(B.ndpprp)-round(ISNULL(a.NuangMuka,0)*case when a.KODEVLS='IDR' then 1 else 
        case when isnull(d.NOBUKTI,'')<>'' then 
        (select kurs from dbumjual where NOBUKTI=d.NOBUKTI) else c.kurs end end,0))*max(b.nilaippn)/100 
	else SUM(B.ndpprp)-round(ISNULL(a.NuangMuka,0)*case when a.KODEVLS='IDR' then 1 else 
	case when isnull(d.NOBUKTI,'')<>'' then 
	   (select kurs from dbumjual where NOBUKTI=d.NOBUKTI) else c.kurs end end,0) 
	end
	end - case when a.pJasa=0 then 0 else sum(B.NDPPRp)*isnull(a.NPPH23,0)/100 end	saldo, 
	A.KodeVls Valas, A.KURS, 
	0 DebetD,
	case when A.KODEVLS='IDR' then 0 else 
	case when a.NuangMuka=0 then sum(B.NNET) else 
	case when a.ppn<>0 then (SUM(B.NDPP)-round(ISNULL(a.NuangMuka,0),2)+(SUM(B.NDPP)-round(ISNULL(a.NuangMuka,0),2))*max(b.nilaippn)/100) else SUM(B.ndpp)-round(ISNULL(a.NuangMuka,0),2) end
	end end KreditD, 
	case when A.KODEVLS='IDR' then 0 else 
	case when ISNULL(a.NuangMuka,0)=0 then sum(B.NNET) else 
	case when a.ppn<>0 then (SUM(B.ndpp)-round(ISNULL(a.NuangMuka,0),2)+(SUM(B.ndpp)-round(ISNULL(a.NuangMuka,0),2))*max(b.nilaippn)/100) else SUM(B.ndpp)-round(ISNULL(a.NuangMuka,0),2) end
	end end SaldoD, 
    '' KodeSales, 'HT' Tipe, case when a.TIPEBAYAR=0  then '21203'  else J.Perkiraan end PERKIRAAN, '' Catatan, 
    'BPL' NoInvoice, A.NOBUKTI NoPajak, A.KodeVls KodeVls_, A.Kurs Kurs_,
    A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   DBBELI A      
LEft Outer join DBBELIDET B on B.NOBUKTI=A.NOBUKTI

left outer join (select a.NOSO,b.nobukti,sum(a.DPP*a.KURS)/case when SUM(a.dpp)=0 then 1 else SUM(a.dpp) end  Kurs
                 from dbo.DBUMJUAL a
                 left outer join dbo.DBBELI b on b.NoPOHd=a.NOSO
                 where a.NOSO<>'' and a.TANGGAL<=b.TANGGAL  
                 group by a.NOSO,b.nobukti) 
                 C on C.NOSO=a.NoPOHd and C.NOBUKTI=a.NOBUKTI	
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='HT'AND X.Urut=2) J on 1=1
left outer join (select a.NOBUKTI,a.NOSO,b.NOBUKTI BBUKTI
                 from dbo.DBUMJUAL a
                 left outer join dbo.DBBELI b on b.NoPOHd=a.NOSO
				 where a.NOSO<>'' and a.TANGGAL<=b.TANGGAL  and a.dpp=b.nuangmuka and a.dpp<>0
                 group by a.NOSO,a.tanggal,a.NOBUKTI,b.nobukti) 
                 D on D.NOSO=a.NoPOHd and D.BBUKTI=a.NOBUKTI 
--where   ISNULL(pqc,0)= case when ISNULL(pJasa,0)=0 then 1 else 0 End
--WHERE A.NOBUKTI='SML/LPB/00007/0926'
Group by A.NoBukti, A.KodeSupp, A.TglQC, A.HARI,a.NPPH23,a.pJasa,a.TIPEBAYAR,
	A.KodeVls, A.Kurs, A.IsOtorisasi1, A.NoJurnal,J.Perkiraan,a.NuangMuka,a.PPN,c.Kurs,
	isnull(c.NOBUKTI,''),d.nobukti,a.TANGGAL
--having SUM(b.ndpp)-isnull(a.NuangMuka,0)<>0


-- Retur Pembelian
union all
SELECT --case when B.nopbl='-' then a.NOBUKTI else b.NOPBL end NoFaktur,
a.NOBUKTI nofaktur,
a.NOBUKTI NoRetur, 'T' TipeTrans, A.KodeSupp KODECUSTSUPP, 
	'' NoBukti, 1 NoMsk, 1 Urut, A.TANGGAL, A.TANGGAL JatuhTempo, 
	sum(B.NNETRp) Debet, 0 Kredit, -1*Sum(B.NNETRp) Saldo, 
	A.KodeVls Valas, A.KURS, 
	case when A.KODEVLS='IDR' then 0 else sum(B.NNet) end DebetD, 0 KreditD, 
	-1*case when A.KODEVLS='IDR' then 0 else sum(B.NNet) end SaldoD, 
    '' KodeSales, 'HT' Tipe, J.Perkiraan PERKIRAAN, '' Catatan, 
    'RPB' NoInvoice, A.NOBUKTI NoPajak, ISNULL(Bl.KodeVls,A.KodeVls) KodeVls_, isnull(Bl.Kurs,A.Kurs) Kurs_,
    A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   DBRBELI A      
LEft Outer join DBRBELIDET B on B.NOBUKTI=A.NOBUKTI
left outer join DBBELI Bl on Bl.NOBUKTI=A.NOBELI
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='HT'AND X.Urut=2) J on 1=1
Group by A.NoBukti, A.KodeSupp, A.TANGGAL, A.HARI,bl.NOBUKTI,bl.TIPEBAYAR,
	A.KodeVls, A.Kurs, Bl.KODEVLS, Bl.KURS, A.IsOtorisasi1, A.NoJurnal,J.PERKIRAAN

-- Debet Note
union all
select B.NoInv NoFaktur, A.NOBUKTI NoRetur, 'T' Tipetrans, A.KodeSupp KODECUSTSUPP, 
       '' NoBukti, 1 NoMsk, 1 Urut, A.TANGGAL, A.TANGGAL JatuhTempo, 
       sum(B.NilaiRp) Debet, 0.00 Kredit, -sum(B.NilaiRp) Saldo, 
       B.KodeVls Valas, B.Kurs KURS, 
       case when B.KodeVLS='IDR' then 0 else sum(B.Nilai) end DebetD, 0.00 KreditD, 
       case when B.KodeVLS='IDR' then 0 else -sum(B.Nilai) end SaldoD, 
       '' KodeSales, 'HT' Tipe, 
       J.Perkiraan PERKIRAAN, '' Catatan, 'DN' NoInvoice, A.NOBUKTI NoPajak,
       isnull(Bl.KodeVls,B.KodeVls) KodeVls_, isnull(Bl.Kurs,B.Kurs) Kurs_,
       A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   DBDebetNote A      
LEft Outer join DBDebetNoteDET B on B.NOBUKTI=A.NOBUKTI
left outer join DBBELI Bl on Bl.NOBUKTI=B.NoInv
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='HT'AND X.Urut=2) J on 1=1
Group by A.NoBukti, A.KodeSupp, A.Tanggal,
	B.KodeVls, B.Kurs, A.NoJurnal, B.NoInv,
	Bl.KODEVLS, Bl.Kurs, A.IsOtorisasi1, A.NoJurnal,J.PERKIRAAN
-- UANG MUKA PENJUALAN
union all
SELECT A.NoBukti NoFaktur, '' NoRetur, 'T' TipeTrans, d.KODECUST KODECUSTSUPP, 
	'' NoBukti, 1 NoMsk, 1 Urut, A.TANGGAL, A.TANGGAL+isnull(D.HARI,0) JatuhTempo, 
	sum(a.DPP+a.PPN) Debet, 0 Kredit, Sum(a.DPP+a.PPN) Saldo, 
	A.Valas, A.KURS, 
	case when A.Valas='IDR' then 0 else sum(a.DPP+a.PPN) end DebetD, 0 KreditD, 
	case when A.Valas='IDR' then 0 else sum(a.DPP+a.PPN) end SaldoD, 
    '' KodeSales, 'PT' Tipe, j.PERKIRAAN PERKIRAAN, '' Catatan, 
    'UMJ' NoInvoice, A.NoBukti NoPajak, A.Valas KodeVls_, A.Kurs Kurs_,
    A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   dbUMJUAL A      
left outer join DBSO D on D.NOBUKTI=a.NOSO
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1 
where pBeli=0
Group by A.NoBukti, d.KodeCust, A.TANGGAL, D.HARI,
	A.Valas, A.Kurs, A.IsOtorisasi1, A.NoJurnal,J.Perkiraan
		
-- Invoice Penjualan

union all
SELECT A.NoBukti NoFaktur, '' NoRetur, 'T' TipeTrans, A.KodeCustSupp KODECUSTSUPP, 
	'' NoBukti, 1 NoMsk, 1 Urut, A.TANGGAL, A.TANGGAL+isnull(D.HARI,0) JatuhTempo,
	case when (a.NoPajak not like '07%') then sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Debet,0 Kredit, 
	case when (a.NoPajak not like '07%') then	Sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Saldo, 
	/*
	case when (isnull(C.BERIKAT,0)=0 or a.NoPajak like '010%') then sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Debet,0 Kredit, 
	case when (isnull(C.BERIKAT,0)=0 or a.NoPajak like '010%') then	Sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Saldo, 
	*/
	A.Valas, A.KURS, 
	case when A.Valas='IDR' then 0 else sum(B.NNet) end DebetD, 0 KreditD, 
	case when A.Valas='IDR' then 0 else sum(B.NNet) end SaldoD, 
    '' KodeSales, 'PT' Tipe, j.PERKIRAAN PERKIRAAN, '' Catatan, 
    'INVC' NoInvoice, A.NoBukti NoPajak, A.Valas KodeVls_, A.Kurs Kurs_,
    A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   dbInvoicePL A      
LEft Outer join dbInvoicePLDet B on B.NOBUKTI=A.NOBUKTI
left outer join DBCUSTSUPP C on c.KODECUSTSUPP=a.KodeCustSupp
left outer join DBSO D on D.NOBUKTI=a.NoSPP
left outer join (select noso,sum(dpp) dpp,sum(ppn) ppn from DBUMJUAL group by NOSO) E on E.NOSO=a.NoSPP and a.NUangMuka<>0
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1 
where a.NoBukti like '%INVC%'and YEAR(a.tanggal)<2026
Group by A.NoBukti, A.KodeCustSupp, A.TANGGAL, D.HARI,E.DPP,e.PPN,c.BERIKAT,a.NoPajak,
	A.Valas, A.Kurs, A.IsOtorisasi1, A.NoJurnal,J.Perkiraan
having 	case when (a.NoPajak not like '07%') then sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end <>0	

union all
SELECT A.NoBukti NoFaktur, '' NoRetur, 'T' TipeTrans, A.KodeCustSupp KODECUSTSUPP, 
	'' NoBukti, 1 NoMsk, 1 Urut, A.TANGGAL, A.TANGGAL+isnull(D.HARI,0) JatuhTempo,
	case when (a.NoPajak not like '07%'and a.NoPajak not like '03%') then sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Debet,0 Kredit, 
	case when (a.NoPajak not like '07%'and a.NoPajak not like '03%') then	Sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Saldo, 
	/*
	case when (isnull(C.BERIKAT,0)=0 or a.NoPajak like '010%') then sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Debet,0 Kredit, 
	case when (isnull(C.BERIKAT,0)=0 or a.NoPajak like '010%') then	Sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Saldo, 
	*/
	A.Valas, A.KURS, 
	case when A.Valas='IDR' then 0 else sum(B.NNet) end DebetD, 0 KreditD, 
	case when A.Valas='IDR' then 0 else sum(B.NNet) end SaldoD, 
    '' KodeSales, 'PT' Tipe, j.PERKIRAAN PERKIRAAN, '' Catatan, 
    'INVC' NoInvoice, A.NoBukti NoPajak, A.Valas KodeVls_, A.Kurs Kurs_,
    A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   dbInvoicePL A      
LEft Outer join dbInvoicePLDet B on B.NOBUKTI=A.NOBUKTI
left outer join DBCUSTSUPP C on c.KODECUSTSUPP=a.KodeCustSupp
left outer join DBSO D on D.NOBUKTI=a.NoSPP
left outer join (select noso,sum(dpp) dpp,sum(ppn) ppn from DBUMJUAL group by NOSO) E on E.NOSO=a.NoSPP and a.NUangMuka<>0
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1 
where a.NoBukti like '%INVC%'and YEAR(a.tanggal)>2025
Group by A.NoBukti, A.KodeCustSupp, A.TANGGAL, D.HARI,E.DPP,e.PPN,c.BERIKAT,a.NoPajak,
	A.Valas, A.Kurs, A.IsOtorisasi1, A.NoJurnal,J.Perkiraan
having 	case when (a.NoPajak not like '07%'and a.NoPajak not like '03%') then sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end <>0	
			
-- Invoice Penjualan Jasa

union all
SELECT A.NoBukti NoFaktur, '' NoRetur, 'T' TipeTrans, A.KodeCustSupp KODECUSTSUPP, 
	'' NoBukti, 1 NoMsk, 1 Urut, A.TANGGAL, A.TANGGAL+isnull(D.HARI,0) JatuhTempo,
	case when (isnull(Cs.BERIKAT,0)=0 or a.NoPajak like '010%') then	sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Debet,0 Kredit, 
	case when (isnull(Cs.BERIKAT,0)=0 or a.NoPajak like '010%') then	Sum(B.NNETRp)-ISNULL(E.dpp,0)-ISNULL(E.PPN,0) else sum(B.NDPPRp)-ISNULL(E.dpp,0) end Saldo, 
	A.Valas, A.KURS, 
	case when A.Valas='IDR' then 0 else sum(B.NNet) end DebetD, 0 KreditD, 
	case when A.Valas='IDR' then 0 else sum(B.NNet) end SaldoD, 
    '' KodeSales, 'PT' Tipe, j.PERKIRAAN PERKIRAAN, '' Catatan, 
    'INVJ' NoInvoice, A.NoBukti NoPajak, A.Valas KodeVls_, A.Kurs Kurs_,
    A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   dbInvoicePL A      
LEft Outer join dbInvoicePLDet B on B.NOBUKTI=A.NOBUKTI
left outer join dbSPP C on c.NoBukti=a.NoSPP
left outer join DBSO D on D.NOBUKTI=c.NoSHIP
left outer join DBUMJUAL E on E.NOSO=a.NoSPP
left outer join DBCUSTSUPP Cs on cs.KODECUSTSUPP=a.KodeCustSupp
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1 
where a.NoBukti like '%INVJ%'
Group by A.NoBukti, A.KodeCustSupp, A.TANGGAL, D.HARI,E.DPP,e.PPN,cs.berikat,a.NoPajak,
	A.Valas, A.Kurs, A.IsOtorisasi1, A.NoJurnal,J.Perkiraan
	
-- Retur Penjualan
union all
SELECT --case when a.noinvoice='-' then A.NOBUKTI else a.NoInvoice end NoFaktur, 
    a.NoBukti nofaktur,
    A.NoBukti NoRetur, 'T' TipeTrans, A.KODECUSTSUPP, 
	'' NoBukti, 1 NoMsk, 1 Urut, A.TANGGAL, A.TANGGAL JatuhTempo, 
	0 Debet, sum(B.NNETRp) Kredit, -1*Sum(B.NNETRp) Saldo, 
	A.KodeVls Valas, A.KURS, 
	0 DebetD, case when A.KODEVLS='IDR' then 0 else sum(B.NNet) end KreditD, 
	-1*case when A.KODEVLS='IDR' then 0 else sum(B.NNet) end SaldoD, 
    '' KodeSales, 'PT' Tipe, J.PERKIRAAN PERKIRAAN, '' Catatan, 
    'RPJ' NoInvoice, A.NOBUKTI NoPajak, ISNULL(Bl.Valas,A.KodeVls) KodeVls_, isnull(Bl.Kurs,A.Kurs) Kurs_,
    A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   DBINVOICERPJ A      
LEft Outer join DBINVOICERPJDet B on B.NOBUKTI=A.NOBUKTI
left outer join dbInvoicePL Bl on Bl.NOBUKTI=A.NoInvoice
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1
Group by A.NoBukti, A.NoInvoice, A.KODECUSTSUPP, A.TANGGAL, A.HARI,
	A.KodeVls, A.Kurs, Bl.Valas, Bl.KURS, A.IsOtorisasi1, A.NoJurnal,J.PERKIRAAN

-- Kredit Note
union all
select B.NoInv NoFaktur, A.NOBUKTI NoRetur, 'T' Tipetrans, A.KodeSupp KODECUSTSUPP, 
       '' NoBukti, 1 NoMsk, 1 Urut, A.TANGGAL, A.TANGGAL JatuhTempo, 
       0.00 Debet, sum(B.NilaiRp) Kredit, -sum(B.NilaiRp) Saldo, 
       B.KodeVls Valas, B.Kurs KURS, 
       case when B.KodeVLS='IDR' then 0 else sum(B.Nilai) end DebetD, 0.00 KreditD, 
       case when B.KodeVLS='IDR' then 0 else -sum(B.Nilai) end SaldoD, 
       '' KodeSales, 'PT' Tipe, 
       J.Perkiraan PERKIRAAN, '' Catatan, 'KN' NoInvoice, A.NOBUKTI NoPajak,
       isnull(Bl.Valas,B.KodeVls) KodeVls_, isnull(Bl.Kurs,B.Kurs) Kurs_,
       A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   DBKreditNote A      
LEft Outer join DBKreditNoteDET B on B.NOBUKTI=A.NOBUKTI
left outer join dbInvoicePL Bl on Bl.NOBUKTI=B.NoInv
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1
Group by A.NoBukti, A.KodeSupp, A.Tanggal,
	B.KodeVls, B.Kurs, A.NoJurnal, B.NoInv,
	Bl.Valas, Bl.Kurs, A.IsOtorisasi1, A.NoJurnal,J.PERKIRAAN
	
---- DPP	
union all
select B.Nofaktur NoFaktur, '' NoRetur, 'T' Tipetrans, --a.KodeCustSupp,
       case when isnull(c.KodeCustSupp,'')='' then a.KodeCustSupp else c.KodeCustSupp end kodecustsupp, 
       a.nobukti NoBukti, b.Urut NoMsk, b.Urut Urut, A.TANGGAL, A.TANGGAL JatuhTempo, 
       0.00 Debet, B.DIBAYAR+b.KL Kredit, -B.DIBAYAR Saldo, 
       B.Valas Valas, B.Kurs KURS, 
       case when B.Valas='IDR' then 0 else B.DIBAYAR+b.KL end DebetD, 0.00 KreditD, 
       case when B.Valas='IDR' then 0 else -B.DIBAYAR+b.KL end SaldoD, 
       '' KodeSales, 'PT' Tipe, 
       '113100' PERKIRAAN, '' Catatan, 'TDP' NoInvoice, A.NOBUKTI NoPajak,
       B.Valas KodeVls_,B.Kurs Kurs_,
       A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   DBTerimaDPP A      
LEft Outer join DBTerimaDPPDET B on B.NOBUKTI=A.NOBUKTI
left outer join dbinvoicepl C on c.NoBukti=b.NOFAKTUR
---- DPH	
union all
select B.Nofaktur NoFaktur, '' NoRetur, 'L' Tipetrans, B.KODECUSTSUPP, 
       c.nobukti NoBukti, C.Urut NoMsk, C.Urut Urut, c.Tanggal, c.Tanggal JatuhTempo, 
       (B.DIBAYAR*c.kurs)+(b.KL*c.kurs) Debet,0.00  Kredit, (-B.DIBAYAR-b.kl)*c.kurs Saldo, 
       A.Valas Valas,c.Kurs KURS, 
       case when A.Valas='IDR' then 0 else B.DIBAYAR+b.KL end DebetD, 0.00 KreditD, 
       case when A.Valas='IDR' then 0 else -B.DIBAYAR+b.KL end SaldoD, 
       '' KodeSales, 'HT' Tipe, 
       '21201' PERKIRAAN, c.Keterangan, 'DPH' NoInvoice, A.NOBUKTI NoPajak,
       A.Valas KodeVls_,c.Kurs Kurs_,
       A.IsOtorisasi1 IsOtorisasi, A.NoJurnal--,b.Urut,c.NoBukti,c.urut
FROM   DBDPH A      
LEft Outer join DBDPHDET B on B.NOBUKTI=A.NOBUKTI
Left Outer join dbTransaksi c on A.NoBukti=C.NODPH and C.CustSuppP=b.KODECUSTSUPP AND C.Perkiraan='21201' and c.Debet=b.DIBAYAR and b.Urut=c.urutDPH 
where A.NoBukti like '%DPH%' and iSNULL(C.CustSuppP,'')<>'' 
--and A.NoBukti='SPL/DPH/00196/1115'


---- DPH	kuRANG BAYAR
union all
select D.Nofaktur NoFaktur, '' NoRetur, 'L' Tipetrans, B.KODECUSTSUPP, 
       c.nobukti NoBukti, C.Urut NoMsk, C.Urut Urut, c.Tanggal, c.Tanggal JatuhTempo, 
       d.NilaiK*c.Kurs Debet,0.00  Kredit, -d.NilaiK Saldo, 
       A.Valas Valas,c.Kurs KURS, 
       case when A.Valas='IDR' then 0 else d.nilaik end DebetD, 0.00 KreditD, 
       case when A.Valas='IDR' then 0 else -d.NilaiK end SaldoD, 
       'KB' KodeSales, 'HT' Tipe, 
       '21201' PERKIRAAN, c.Keterangan, 'DPH' NoInvoice, A.NOBUKTI NoPajak,
       A.Valas KodeVls_,c.Kurs kurs_,
       A.IsOtorisasi1 IsOtorisasi, A.NoJurnal
FROM   DBDPH A      
LEft Outer join DBDPHDET B on B.NOBUKTI=A.NOBUKTI
lEFT OUTER JOIN dbKurangBayar D ON A.NoBukti=D.NoBukti AND B.KODECUSTSUPP=D.KodeCustSupp and B.Urut=d.Urut
Left Outer join dbTransaksi c on A.NoBukti=C.NODPH and C.CustSuppP=b.KODECUSTSUPP AND C.Perkiraan='21201' and c.Debet=D.NilaiK and b.Urut=c.urutDPH 
where A.NoBukti like '%DPH%' and iSNULL(C.CustSuppP,'')<>'' and D.NilaiK>0 and iSNULL(D.KodeCustSupp,'')<>''























































GO











































ALTER  View [dbo].[vwPostJurnalOto]  
as  
--- UANG MUKA PEMBELIAN

SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'UM '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+6000 URUT,  
	'116100' PERKIRAAN, J.Perkiraan LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(a.DPP) DEBET,0 KREDIT, A.VALAS, A.Kurs, 
	Sum(a.DPP)*a.KURS DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	isnull(B.KodeSUpp,'') CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'UMJ' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.dbUMJUAL a 
left outer join dbo.DBPO b on b.NOBUKTI=a.NOSO
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=B.KODESUPP
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='HT'AND X.Urut=2) J on 1=1 
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='UHT'AND X.Urut=2) H on 1=1                  
Where A.pBeli=1  and a.noretur=''and a.TANGGAL<'11-01-2024'
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	A.NOBUKTI, Cs.NAMACUSTSUPP, B.KODESUPP, A.TANGGAL,J.Perkiraan,H.Perkiraan,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 
union all
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Penjualan '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+7000 URUT,  
	H.Perkiraan PERKIRAAN, J.Perkiraan LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(round(a.PPN,0)) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(round(a.PPN,0))*a.KURS DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	B.KODESUPP CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'UMJ' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.dbUMJUAL a 
left outer join dbo.DBPO b on b.NOBUKTI=a.NOSO
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=B.KODESUPP
Left Outer join dbo.DBPOSTHUTPIUT H on H.Kode='PPM'
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='HT'AND X.Urut=2) J on 1=1 
--Where A.noJurnal<>''
Where a.pbeli=1 and a.noretur=''and a.TANGGAL<'11-01-2024'
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	A.NOBUKTI, Cs.NAMACUSTSUPP, B.KODESUPP, A.TANGGAL,J.Perkiraan,H.Perkiraan,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 
UNION ALL
-- BELI 
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, 
--cast(case when ISNULL(pJasa,0)=0 then A.TglQC else A.TANGGAL End as Date) TANGGAL,
A.TANGGAL,
'01' DEVISI, 
	'Pembelian '+isnull(Cs.NamaCustSupp,'')  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1,A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3,A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, min(B.Urut) URUT,
    case when isnull(a.pJasa,0)=0 then '117100' else a.perkiraan end PERKIRAAN,
    case when a.tipebayar='0' then '21203' else'21201' end LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 
	'' KETERANGAN2,sum(B.Ndpp)DEBET,0 KREDIT, A.KODEVLS Valas, A.KURS, sum(B.NdppRp) DEBETRP,
	 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'BPL' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBBELI A 
LEFT OUTER JOIN DBO.DBbeliDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs .KODECUSTSUPP=A.KODESUPP
Left Outer Join dbo.DBBARANG Br on Br.KODEBRG=B.KODEBRG
--where  (ISNULL(pQC,0)=1 Or isnull(a.pJasa,0)<>0) -- and a.TIPEBAYAR='1' 
GROUP BY A.NOBUKTI, A.NOURUT, A.TglQC,a.TANGGAL, Cs.NAMACUSTSUPP, A.KODESUPP,a.pJasa,a.PERKIRAAN,a.TIPEBAYAR,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1,A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3,A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.KODEVLS, A.KURS, A.NOURUT,a.NuangMuka

Union ALL
-- JURNAL BALIk UANG MUKA PEMBELIAN
SELECT A.NOBUKTI NOBUKTI, A.NOURUT,
-- cast(case when ISNULL(pJasa,0)=0 then A.TglQC else A.TANGGAL End as Date) TANGGAL,
A.TANGGAL,
'01' DEVISI, 
	' BALIK UMX '+isnull(Cs.NamaCustSupp,'')  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1,A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,	min(B.Urut)+2400 URUT,
	case when a.tipebayar='0'  then '21203' else'21201' end PERKIRAAN,'116100' LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 	'' KETERANGAN2, 
	a.NuangMuka DEBET,0 KREDIT, A.KODEVLS Valas,
	case when a.kodevls='IDR' then 1 else 
	case when isnull(d.kurs,0)<>0 then d.Kurs else 	c.kurs end end Kurs,
    case when a.kodevls='IDR' then a.NuangMuka else 
       case when ISNULL(D.Kurs,0)=0 then a.nuangmuka*c.Kurs
	   else a.nuangmuka*d.Kurs end
	   end DEBETRP,
	 
	0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'BPL' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBBELI A 
LEFT OUTER JOIN DBO.DBbeliDET B ON B.NOBUKTI=A.NOBUKTI 
left outer join (select a.NOSO,b.nobukti,sum(a.DPP*a.KURS)/case when SUM(a.dpp)=0 then 1 else SUM(a.DPP) end Kurs
                 from dbo.DBUMJUAL a
                 left outer join dbo.DBBELI b on b.NoPOHd=a.NOSO
                 where a.NOSO<>'' and a.TANGGAL<=b.TANGGAL and a.KURS<>1 
                 and a.NOBUKTI not in (select a.NOBUKTI from dbo.DBUMJUAL a
                                       left outer join dbo.DBBELI b on b.NoPOHd=a.NOSO
                                       where a.NOSO<>'' and a.TANGGAL<=b.TANGGAL  and a.dpp=b.nuangmuka and a.KURS<>1 
                                       group by a.NOBUKTI)
                 group by a.NOSO,b.nobukti) 
                 C on C.NOSO=a.NoPOHd and C.NOBUKTI=a.NOBUKTI 
left outer join (select a.NOSO,a.TANGGAL ,b.NOBUKTI,a.kurs
                 from dbo.DBUMJUAL a
                 left outer join dbo.DBBELI b on b.NoPOHd=a.NOSO
                 where a.NOSO<>'' and a.TANGGAL<=b.TANGGAL  and a.dpp=b.nuangmuka and a.KURS<>1 
                 group by a.NOSO,a.tanggal,b.NOBUKTI,a.kurs)
                  D on D.NOSO=a.NoPOHd and D.nobukti=a.NOBUKTI             
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs .KODECUSTSUPP=A.KODESUPP
Left Outer Join dbo.DBBARANG Br on Br.KODEBRG=B.KODEBRG
Left Outer join (Select x.Perkiraan  from dbo.DBPOSTHUTPIUT x where x.Kode='UHT'AND X.Urut=2) H on 1=1
--where a.TIPEBAYAR='1'  
GROUP BY A.NOBUKTI, A.NOURUT, A.TglQC,a.TANGGAL,a.pJasa, Cs.NAMACUSTSUPP, A.KODESUPP,d.KURS,a.TIPEBAYAR,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.KODEVLS, A.KURS, A.NOURUT,a.NuangMuka,h.Perkiraan,c.NOSO,c.Kurs ,d.tanggal
having a.NuangMuka<>0
Union ALL
-- PPN PEMBELIAN KREDIT
SELECT A.NOBUKTI NOBUKTI, A.NOURUT,
--cast(case when ISNULL(pJasa,0)=0 then A.TglQC else A.TANGGAL End as Date) TANGGAL,
A.TANGGAL,
'01' DEVISI, 
	'PPN '+isnull(Cs.NAMACUSTSUPP,'') NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1,A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, min(B.Urut)+100 URUT, 
	isnull(J.Perkiraan,'') PERKIRAAN, case when a.tipebayar='1' then k.Perkiraan else'21203' end LAWAN,
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN, '' KETERANGAN2,
	case when isnull(a.NuangMuka,0)=0 then SUM(B.NPPN) else (SUM(B.ndpprp)-round(isnull(a.NuangMuka*a.KURS,0),0))*max(b.nilaippn)/100 end DEBET,
	 0 KREDIT, A.KODEVLS Valas, A.KURS, 
	case when isnull(a.NuangMuka,0)=0 then SUM(B.NPPN) else (SUM(B.ndpprp)-round(isnull(a.NuangMuka*a.KURS,0),0))*max(b.nilaippn)/100 end DEBETRP,
	 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO,
	'BPL' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBbeli A 
LEFT OUTER JOIN DBO.DBBELIDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs.KODECUSTSUPP=A.KODESUPP
Left Outer join dbo.DBPOSTHUTPIUT J on J.Kode='PPM'
Left Outer join (Select x.Perkiraan from dbo.DBPOSTHUTPIUT x where x.Kode='HT'AND X.Urut=2) K on 1=1
--where  (ISNULL(pQC,0)=1 Or isnull(a.pJasa,0)<>0) -- and  a.TIPEBAYAR='1'
GROUP BY A.NOBUKTI, A.NOURUT, A.TglQC,a.TANGGAL,a.pJasa, Cs.NAMACUSTSUPP, A.KODESUPP,K.Perkiraan,a.TIPEBAYAR,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3,A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.KODEVLS, A.KURS, A.NOURUT,a.NuangMuka,J.Perkiraan
having SUM(B.NPPNRp)<>0
union all

------------------------------ BELI PPH 23 ------------------------------

SELECT A.NOBUKTI NOBUKTI, A.NOURUT,
--cast(case when ISNULL(pJasa,0)=0 then A.TglQC else A.TANGGAL End as Date) TANGGAL,
A.TANGGAL,
'01' DEVISI, 
	'Pembelian '+isnull(Cs.NamaCustSupp,'')  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1,A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3,A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, min(B.Urut)+500 URUT, 
	'21201' PERKIRAAN,'21703' LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 	'' KETERANGAN2, 
	Round(SUM(B.NDPP*isnull(a.NPPH23,0)/100),0) DEBET, 0 KREDIT, A.KODEVLS Valas, A.KURS, 
	Round(SUM(B.NDPPRP*isnull(A.NPPH23,0)/100),0) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'BPL' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBBELI A 
LEFT OUTER JOIN DBO.DBbeliDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs .KODECUSTSUPP=A.KODESUPP
where a.pJasa=1
GROUP BY A.NOBUKTI, A.NOURUT, A.TglQC,a.TANGGAL,a.pJasa, Cs.NAMACUSTSUPP, A.KODESUPP,a.PERKIRAAN,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1,A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3,A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.KODEVLS, A.KURS, A.NOURUT

Union ALL
--RETUR BELI
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Retur Beli '+isnull(Cs.NamaCustSupp,'') NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	MIN(B.URUT)+3200 URUT,
	case when isnull(D.nobukti,'')<>'' and d.TIPEBAYAR=0 then '21203' else '21201' end PERKIRAAN,
	'8230001' LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 
	'' KETERANGAN2, 
	SUM(B.NDPP) DEBET, 0 KREDIT, A.KODEVLS Valas, A.KURS, 
	SUM(B.NDPPRP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'RPB' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBRBELI A 
LEFT OUTER JOIN DBO.DBRBELIDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs.KODECUSTSUPP=A.KODESUPP
Left Outer Join dbo.DBBARANG Br on Br.KODEBRG=B.KODEBRG
LEFT outer join dbo.DBBELI D on d.NOBUKTI=a.NOBELI

--where A.NoJurnal<>''
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, Cs.NAMACUSTSUPP, A.KODESUPP,--SUBSTRING(b.KODEBRG,1,1),
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.KODEVLS, A.KURS, A.NOURUT,D.NOBUKTI,d.TIPEBAYAR

union All
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Retur Beli '+isnull(Cs.NamaCustSupp,'') NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	MIN(B.URUT)+3300 URUT, 
	'8230001' PERKIRAAN,'117100' LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 
	'' KETERANGAN2, 
	SUM(B.Qnt1*isnull(b.HPP,0)) DEBET, 0 KREDIT, A.KODEVLS Valas, A.KURS, 
	SUM(B.Qnt1*isnull(b.HPP,0)) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'RPB' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBRBELI A 
LEFT OUTER JOIN DBO.DBRBELIDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs.KODECUSTSUPP=A.KODESUPP
Left Outer Join dbo.DBBARANG Br on Br.KODEBRG=B.KODEBRG
--where A.NoJurnal<>''
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, Cs.NAMACUSTSUPP, A.KODESUPP,--SUBSTRING(b.KODEBRG,1,1),
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.KODEVLS, A.KURS, A.NOURUT,A.NoJurnal
having SUM(B.Qnt1*isnull(b.HPP,0))<>0

union All
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'PPN '+A.NOBUKTI+' '+isnull(Cs.NAMACUSTSUPP,'') NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	MIN(B.URUT)+100 URUT, 
	'21201' PERKIRAAN, isnull(J.Perkiraan,'') LAWAN,
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN, 
	'' KETERANGAN2, 
	SUM(B.NPPN) DEBET, 0 KREDIT, A.KODEVLS Valas, A.KURS, 
	SUM(B.NPPNRP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'RPB' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBRBELI A 
LEFT OUTER JOIN DBO.DBRBELIDET B ON B.NOBUKTI = A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs.KODECUSTSUPP = A.KODESUPP
Left Outer join dbo.DBPOSTHUTPIUT J on J.Kode='PPM'
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='HT'AND X.Urut=2) K on 1=1
--where A.NoJurnal<>''
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, Cs.NAMACUSTSUPP, A.KODESUPP,K.Perkiraan,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.KODEVLS, A.KURS, A.NOURUT, J.Perkiraan
having SUM(B.NPPNRp)<>0

-- Debet Note
union all
SELECT A.NOBUKTI NOBUKTI, A.NoUrut, A.TANGGAL,'01' DEVISI, 
      'Debet Note : ' + A.NOBUKTI +' TANGGAL : '+ Convert(Varchar(15),A.Tanggal, 105) NOTE,0 LAMPIRAN, 
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       CAST(ROW_NUMBER() Over(PARTITION BY A.Nobukti Order by A.Nobukti) As int) URUT,  
       K.Perkiraan PERKIRAAN, 
       J.PERKIRAAN  LAWAN, 
       'Debet Note : ' + isnull(I.NAMACUSTSUPP,'') + ' (' + Isnull(I.KODECUSTSUPP,'') + ')'+CHAR(13)+ 
       'No. Invoice : '+B.NoInv KETERANGAN, '' KETERANGAN2, 
       Sum(B.Nilai) DEBET, 0 KREDIT, B.KodeVLS VALAS, B.Kurs, 
       Sum(B.NilaiRp) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, '' CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
       '' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
       'DN' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBDebetNote A 
LEFT OUTER JOIN DBO.DBDebetNoteDET B ON B.NOBUKTI = A.NOBUKTI 
Left Outer join (Select x.NoFaktur, Min(x.Tanggal) Tanggal
                 From dbo.vwHutPiut x
                 where TipeTrans='T'
                 Group by x.NoFaktur) C on C.NoFaktur=B.NoInv
Left outer join dbo.vwCUSTSUPP I on I.KODECUSTSUPP=A.KodeSupp 
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='BD') J on 1=1
 Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='HT'AND X.Urut=2) K on 1=1               
--Where A.NoJurnal<>''
Group by A.NOBUKTI, A.TANGGAL, B.kodevls, B.Kurs,B.NoInv, C.Tanggal, 
       A.NoUrut, i.NAMACUSTSUPP, I.KODECUSTSUPP,A.TANGGAL,A.KodeSupp,K.Perkiraan,
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       J.Perkiraan,A.NoUrutJurnal
UNION ALL
-- UANG MUKA PENJUALAN
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'UM '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+6000 URUT,  
	J.Perkiraan PERKIRAAN, H.Perkiraan LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(a.DPP) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(a.DPP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	B.KodeCust CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'UMJ' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.dbUMJUAL a 
left outer join dbo.DBSO b on b.NOBUKTI=a.NOSO
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=B.KodeCust
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1 
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='UPT'AND X.Urut=2) H on 1=1                  
Where a.pbeli=0   ----A.noJurnal<>''
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	 Cs.NAMACUSTSUPP, B.KODECUST, A.TANGGAL,J.Perkiraan,H.Perkiraan,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 
UNION ALL
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Penjualan '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+7000 URUT,  
	J.Perkiraan PERKIRAAN, H.Perkiraan LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(a.PPN) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(a.PPN) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	B.KodeCust CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'UMJ' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.dbUMJUAL a 
left outer join dbo.DBSO b on b.NOBUKTI=a.NOSO
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=B.KodeCust
Left Outer join dbo.DBPOSTHUTPIUT H on H.Kode='PPK'
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1 
--Where A.noJurnal<>''
Where a.pbeli=0
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	Cs.NAMACUSTSUPP, B.KODECUST, A.TANGGAL,J.Perkiraan,H.Perkiraan,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 
UNION ALL

-- SPB PER INVOICE HPP

SELECT A.NoBukti NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Penjualan '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+1000 URUT,  
	'51100' PERKIRAAN,'117100' LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(isnull(b.Qnt,0) * isnull(C.HPP,0)) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(isnull(b.Qnt,0) * isnull(C.HPP,0)) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
LEFT OUTER JOIN DBO.dbInvoicePLDet B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBBARANG Br ON Br.KODEBRG=B.KODEBRG 
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
left outer join dbo.dbSPBDet C on c.NoBukti=b.NoSPB and c.KodeBrg=b.KodeBrg and b.UrutSPB=C.Urut
Where (a.NoBukti like '%INVC%' and ISNULL(a.pJasa,'')='') --and SUBSTRING(b.kodebrg,1,1)='1' 
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,A.KodeCustSupp,--SUBSTRING(b.kodebrg,1,1),--J.Perkiraan,BS.PerkH,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 
having Sum(b.Qnt * isnull(C.HPP,0))<>0
Union ALL
SELECT A.NoBukti NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Penjualan POS '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+2000 URUT,  
	'51100' PERKIRAAN,'117100' LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(b.Qnt * isnull(C.HPP,0)) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(b.Qnt * isnull(C.HPP,0)) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
LEFT OUTER JOIN DBO.dbInvoicePLDet B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBBARANG Br ON Br.KODEBRG=B.KODEBRG 
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
left outer join dbo.dbSPBDet C on c.NoBukti=b.NoSPB and c.KodeBrg=b.KodeBrg and b.UrutSPB=C.Urut
Where a.NoBukti like '%POS%' --and SUBSTRING(b.kodebrg,1,1)<>'1'
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,A.KodeCustSupp,--SUBSTRING(b.kodebrg,1,1),--J.Perkiraan,BS.PerkH,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL
having Sum(b.Qnt * isnull(C.HPP,0))<>0
-- Invoice Penjualan BARANG -> PIUTANG     
Union ALL
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Penjualan '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut URUT,  
	'113100' PERKIRAAN, '41100' LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(B.NDPP)-isnull(c.DPP,0) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(B.NDPPRp)-isnull(c.DPP,0) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
LEFT OUTER JOIN DBO.dbInvoicePLDet B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBBARANG Br ON Br.KODEBRG=B.KODEBRG 
LEFT outer join dbo.DBUMJUAL c on c.NOSO=a.NoSPP and a.NUangMuka<>0
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
Where a.NoBukti like '%INVC%' 
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	A.NOBUKTI, Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,A.KodeCustSupp,c.DPP,--J.Perkiraan,BS.PerkH,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 
Union ALL
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Penjualan '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,a.NoUrut+6600 URUT,  
	'21401' PERKIRAAN, '41100' LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	isnull(c.DPP,0) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	isnull(c.DPP,0) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
LEFT OUTER JOIN DBO.dbInvoicePLDet B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBBARANG Br ON Br.KODEBRG=B.KODEBRG 
LEFT outer join dbo.DBUMJUAL c on c.NOSO=a.NoSPP  and a.NUangMuka<>0
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
Where a.NoBukti like '%INVC%' and ISNULL(c.dpp,0)<>0 and isnull(a.IsBatal,0)=0
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	A.NOBUKTI, Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,A.KodeCustSupp,c.DPP,--J.Perkiraan,BS.PerkH,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 	
Union ALL

-- Invoice Penjualan JASA -> PIUTANG 
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Penjualan '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+5000 URUT,  
	'113100' PERKIRAAN, '41300' LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(B.NDPP)-isnull(c.DPP,0) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(B.NDPPRp)-isnull(c.DPP,0) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
LEFT OUTER JOIN DBO.dbInvoicePLDet B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBBARANG Br ON Br.KODEBRG=B.KODEBRG 
LEFT outer join dbo.DBUMJUAL c on c.NOSO=a.NoSPP and a.NoSPP<>''
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
LEFT outer join dbo.DBSUBgroup D on d.KodeSubGrp=br.KODESUBGRP and d.KodeHDGrp=br.KodeHdGrp
Where a.NoBukti like '%INVJ%' 
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	A.NOBUKTI, Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,A.KodeCustSupp,c.DPP,d.PerkH,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 
Union All
-- PPN
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Faktur Pajak '+' '+isnull(Cs.NAMACUSTSUPP,'') NOTE,0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+3000 URUT,  
	'113100' PERKIRAAN, J.Perkiraan  LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(B.NPPN)-isnull(c.PPN,0) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(B.NPPNRp)-isnull(c.PPN,0) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
LEFT OUTER JOIN DBO.dbInvoicePLDet B ON B.NOBUKTI = A.NOBUKTI
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
LEFT outer join dbo.DBUMJUAL c on c.NOSO=a.NoSPP and a.NUangMuka<>0
Left Outer join dbo.DBPOSTHUTPIUT J on J.Kode='PPK'
Where (a.NoBukti like '%INVC%' OR a.NoBukti like '%INVJ%' )and isnull(a.isbatal,0)=0
      and (a.NoPajak not like '07%') and B.NoBukti is Not Null and YEAR(a.tanggal)<2026
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs,
	A.NOBUKTI, Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,c.PPN,--K.Perkiraan,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	J.Perkiraan
Having SUM(B.NPPNRp)<>0
union all
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Faktur Pajak '+' '+isnull(Cs.NAMACUSTSUPP,'') NOTE,0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+3000 URUT,  
	'113100' PERKIRAAN, J.Perkiraan  LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(B.NPPN)-isnull(c.PPN,0) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(B.NPPNRp)-isnull(c.PPN,0) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
LEFT OUTER JOIN DBO.dbInvoicePLDet B ON B.NOBUKTI = A.NOBUKTI
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
LEFT outer join dbo.DBUMJUAL c on c.NOSO=a.NoSPP and a.NUangMuka<>0
Left Outer join dbo.DBPOSTHUTPIUT J on J.Kode='PPK'
Where (a.NoBukti like '%INVC%' OR a.NoBukti like '%INVJ%' )and isnull(a.isbatal,0)=0
      and (a.NoPajak not like '07%'and a.NoPajak not like '03%') and B.NoBukti is Not Null and YEAR(a.tanggal)>2025
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs,
	A.NOBUKTI, Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,c.PPN,--K.Perkiraan,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	J.Perkiraan
Having SUM(B.NPPNRp)<>0
-- Penjualan POS      
Union ALL
SELECT A.nobukti  NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Penjualan POS '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+4000 URUT,  
	'111105' PERKIRAAN, '41100' LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(B.NDPP) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(B.NDPPRp) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
LEFT OUTER JOIN DBO.dbInvoicePLDet B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBBARANG Br ON Br.KODEBRG=B.KODEBRG 
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
Where a.NoBukti like '%POS%'
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	A.NOBUKTI, Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,A.KodeCustSupp,--J.Perkiraan,BS.PerkH,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 

Union All
SELECT A.nobukti  NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Faktur Pajak POS '+' '+isnull(Cs.NAMACUSTSUPP,'') NOTE,0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+5000 URUT,  
	'111105' PERKIRAAN, '21701'  LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	Sum(B.NPPN) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	Sum(B.NPPNRp) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
LEFT OUTER JOIN DBO.dbInvoicePLDet B ON B.NOBUKTI = A.NOBUKTI
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
Where a.NoBukti like '%POS%'and (isnull(cs.BERIKAT,0)=0 or a.NoPajak like '010%')
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs,
	A.NOBUKTI, Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,--K.Perkiraan,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL
Having SUM(B.NPPNRp)<>0

Union ALL
SELECT A.nobukti  NOBUKTI, A.NOURUT, A.Tanggal TANGGAL,'01' DEVISI, 
	'Penjualan POS '+isnull(Cs.NAMACUSTSUPP,'') NOTE, 0 LAMPIRAN, 
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	a.NoUrut+5500 URUT,  
	'111105' PERKIRAAN, '74300' LAWAN, 
	isnull(Cs.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2, 
	sum(isnull(a.DCChargeRP,0)) DEBET, 0 KREDIT, A.VALAS, A.Kurs, 
	sum(isnull(a.DCChargeRP,0)) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	A.KodeCustSupp CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
	'INVC' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBInvoicePL A 
Left outer join dbo.DBCUSTSUPP Cs on Cs.KODECUSTSUPP=A.KodeCustSupp
Where a.NoBukti like '%POS%'
Group by A.NOBUKTI, A.NOURUT, A.Valas, A.Kurs, 
	A.NOBUKTI, Cs.NAMACUSTSUPP, A.KODECUSTSUPP, A.TANGGAL,A.KodeCustSupp,
	A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL 
having sum(isnull(a.DCChargeRP,0))<>0	
UNION ALL


-- Penerimaan Retur Jual


SELECT A.NOBUKTI NOBUKTI, A.NoUrut, A.Tanggal,'01' DEVISI, 
      'Penerimaan Retur Penjualan : ' + A.NOBUKTI +' TANGGAL : '+ Convert(Varchar(15),A.Tanggal, 105) NOTE,0 LAMPIRAN, 
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       CAST(ROW_NUMBER() Over(PARTITION BY A.Nobukti Order by A.Nobukti) As int) URUT,  
       --case when SUBSTRING(b.kodebrg,1,1)='1' then '117200' else '117100' end PERKIRAAN, 
       '117100' PERKIRAAN,isnull(J.Perkiraan,'') LAWAN, 
       isnull(Cs.NAMACUSTSUPP,'')  KETERANGAN, '' KETERANGAN2, 
       Sum(B.Qnt*b.ISI * B.HPP) DEBET, 0 KREDIT, 'IDR' VALAS, 1 KURS, 
       Sum(B.Qnt*b.ISI * B.HPP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, '' CUSTCUPPP, '' CUSTSUPPL, '' KODEP, 
       '' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
       'SPR' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.dbSPBRJual A 
LEFT OUTER JOIN DBO.dbSPBRJualDet B ON B.NOBUKTI = A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs.KODECUSTSUPP=A.KodeCustSupp
Left Outer Join dbo.DBBARANG Br on Br.KODEBRG=B.KODEBRG
--Left Outer join dbo.dbSubGroup Sg on Sg.KodeGrp=Br.KODEGRP and Sg.KodeSubGrp=Br.KODESUBGRP 
Left Outer Join dbo.DBPOSTHUTPIUT J on J.Kode='HPP'
--Where MONTH(a.tanggal)=11 and YEAR(a.tanggal)=2022
Group by A.NOBUKTI, A.TglJurnal,A.KodeCustSupp, Cs.NAMACUSTSUPP, 
      A.NOBUKTI, A.NoUrut, A.TANGGAL,b.KodeBrg,
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,A.NoUrutJurnal,j.Perkiraan


-- Invoice Retur Jual
union all
SELECT A.NOBUKTI NOBUKTI, A.NoUrut, A.TANGGAL,'01' DEVISI, 
      'Retur Invoice Penjualan : ' + isnull(I.NAMACUSTSUPP,'') + ' (' + Isnull(I.KODECUSTSUPP,'') + ')' NOTE,0 LAMPIRAN, 
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
       min(B.Urut) URUT, '41100' PERKIRAAN,j.PERKIRAAN  LAWAN, 
       A.NoBukti+' '+isnull(I.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2,
       Sum(B.NDPP) DEBET, 0 KREDIT, A.Kodevls VALAS, A.Kurs, 
       Sum(B.NDPPRp) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, '' CUSTSUPPP, A.KODECUSTSUPP CUSTSUPPL, '' KODEP, 
       '' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
       'RPJ' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBINVOICERPJ A 
LEFT OUTER JOIN DBO.DBINVOICERPJDet B ON B.NOBUKTI = A.NOBUKTI 
--LEFT OUTER JOIN DBO.DBBARANG F ON F.KODEBRG = B.KODEBRG 
Left outer join dbo.vwCUSTSUPP I on I.KODECUSTSUPP=A.KodeCustSupp
--LEFT OUTER JOIN dbo.DBSUBGROUP bS on Bs.KodeSubGrp=f.KODESUBGRP AND BS.KodeGrp=F.KODEGRP
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1 

Group by A.NOBUKTI, A.TglJurnal, A.Tanggal, A.KODEVLS, A.Kurs, 
      A.NOBUKTI, A.NoUrut, i.NAMACUSTSUPP, I.KODECUSTSUPP,A.TANGGAL,A.KODECUSTSUPP,--BS.PerkH,
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1,    
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
       J.Perkiraan,A.NoUrutJurnal
Union All
SELECT A.NOBUKTI NOBUKTI, A.NoUrut, A.TANGGAL,'01' DEVISI, 
      'Retur Invoice Penjualan : ' + isnull(I.NAMACUSTSUPP,'') + ' (' + Isnull(I.KODECUSTSUPP,'') + ')' NOTE,0 LAMPIRAN, 
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
       min(B.Urut)+1000 URUT,  
       K.Perkiraan PERKIRAAN, 
       j.PERKIRAAN  LAWAN, 
       A.NoBukti+' '+isnull(I.NAMACUSTSUPP,'') KETERANGAN,'' KETERANGAN2,
       Sum(B.nPPN) DEBET, 0 KREDIT, A.Kodevls VALAS, A.Kurs, 
       Sum(B.NPPNRp) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, '' CUSTSUPPP, A.KODECUSTSUPP CUSTSUPPL, '' KODEP, 
       '' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
       'RPJ' JENIS, A.NoBukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBINVOICERPJ A 
LEFT OUTER JOIN DBO.DBINVOICERPJDet B ON B.NOBUKTI = A.NOBUKTI 
LEFT OUTER JOIN DBO.DBBARANG F ON F.KODEBRG = B.KODEBRG 
Left outer join dbo.vwCUSTSUPP I on I.KODECUSTSUPP=A.KodeCustSupp
Left Outer join dbo.DBPOSTHUTPIUT K on k.Kode='PPK'
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='PT'AND X.Urut=2) J on 1=1 
--Where A.NoJurnal<>''
Group by A.NOBUKTI, A.TglJurnal, A.Tanggal, A.KODEVLS, A.Kurs, 
      A.NOBUKTI, A.NoUrut, i.NAMACUSTSUPP, I.KODECUSTSUPP,A.TANGGAL,A.KODECUSTSUPP,K.Perkiraan,
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1,    
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
       J.Perkiraan,A.NoUrutJurnal


-- Kredit Note       
union all
SELECT A.NOBUKTI NOBUKTI, A.NoUrut, A.TANGGAL,'01' DEVISI, 
      'Kredit Note : ' + A.NOBUKTI +' TANGGAL : '+ Convert(Varchar(15),A.Tanggal, 105) NOTE,0 LAMPIRAN, 
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       CAST(ROW_NUMBER() Over(PARTITION BY A.Nobukti Order by A.Nobukti) As int) URUT,  
       J.Perkiraan PERKIRAAN, 
       isnull(B.PerkHP,'')  LAWAN, 
       'Kredit Note : ' + isnull(I.NAMACUSTSUPP,'') + ' (' + Isnull(I.KODECUSTSUPP,'') + ')'+CHAR(13)+ 
       'No. Invoice : '+B.NoInv KETERANGAN, '' KETERANGAN2, 
       Sum(B.Nilai) DEBET, 0 KREDIT, B.KodeVLS VALAS, B.Kurs, 
       Sum(B.NilaiRp) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, '' CUSTSUPPP, '' CUSTSUPPL, '' KODEP, 
       '' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
       'KN' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBKreditNote A 
LEFT OUTER JOIN DBO.DBKreditNoteDET B ON B.NOBUKTI = A.NOBUKTI 
Left outer join dbo.vwCUSTSUPP I on I.KODECUSTSUPP=A.KodeSupp
Left Outer join (Select x.Perkiraan
                 from dbo.DBPOSTHUTPIUT x
                 where x.Kode='BK') J on 1=1
--Where A.noJurnal<>''
Group by A.NOBUKTI, A.TglJurnal, A.TANGGAL, B.kodevls, B.Kurs, B.NoInv, B.PerkHP, 
      A.NOBUKTI, A.NoUrut, i.NAMACUSTSUPP, I.KODECUSTSUPP, A.TANGGAL,
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       J.Perkiraan,A.NoUrutJurnal

-- Penyerahan Bahan       
union All
SELECT A.NOBUKTI NOBUKTI, A.NoUrut, A.Tanggal,'01' DEVISI, 
      'Penyerahan Bahan : ' + A.NOBUKTI  NOTE,0 LAMPIRAN, 
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       CAST(ROW_NUMBER() Over(PARTITION BY A.Nobukti Order by A.Nobukti) As int) URUT,  
       ISNULL(b.KodePerkiraan,'') PERKIRAAN, 
       '117100' Lawan, 
       a.keterangan KETERANGAN, '' KETERANGAN2, 
       Sum(B.QNT*B.HPP) DEBET, 0 KREDIT, 'IDR' VALAS, 1 KURS, 
       Sum(B.QNT*B.HPP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, '' CUSTCUPPP, '' CUSTSUPPL, '' KODEP, 
       '' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
      'BP' JENIS, A.Nobukti NOBUKTITRANS,B.KODECOST kodecost,B.KODESUBCOST kodesubcost
FROM  DBO.DBPenyerahanBhn A 
LEFT OUTER JOIN DBO.DBPenyerahanBhnDET B ON B.NOBUKTI = A.NOBUKTI 
--Where A.noJurnal<>''
Group by A.NOBUKTI, A.TglJurnal, A.NoBPPB,b.KodePerkiraan,B.KODECOST,B.KODESUBCOST,
      A.NOBUKTI, A.Nourut, A.TANGGAL,a.Keterangan,
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       A.NoUrutJurnal
--- SAMPLE       
union All
SELECT A.Nobukti NOBUKTI, A.NoUrut, A.Tanggal,'01' DEVISI, 
      'Penyerahan Sample : ' + A.NOBUKTI  NOTE,0 LAMPIRAN, 
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       CAST(ROW_NUMBER() Over(PARTITION BY A.Nobukti Order by A.Nobukti) As int) URUT,  
       '64200' PERKIRAAN, 
       '117100' Lawan, 
       C.NAMACUSTSUPP KETERANGAN, '' KETERANGAN2, 
       Sum(B.QNT*B.HPP) DEBET, 0 KREDIT, 'IDR' VALAS, 1 KURS, 
       Sum(B.QNT*B.HPP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, '' CUSTCUPPP, '' CUSTSUPPL, '' KODEP, 
       '' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
      'BP' JENIS, A.Nobukti NOBUKTITRANS,''kodecost,'' kodesubcost
FROM  DBO.DBBEBANSAMPLE A 
LEFT OUTER JOIN DBO.DBBEBANSAMPLEDET B ON B.NOBUKTI = A.NOBUKTI
LEFT OUTER JOIN DBO.DBCUSTSUPP C on C.KODECUSTSUPP=a.KODECUSTSUPP 
--Where A.noJurnal<>''
Group by A.NOBUKTI, A.TglJurnal, 
      A.NOBUKTI, A.Nourut, A.TANGGAL,c.NAMACUSTSUPP,
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       A.NoUrutJurnal     
/*
-- Retur Penyerahan Bahan
union All
SELECT A.NOBUKTI NOBUKTI, A.NoUrut, A.Tanggal,'01' DEVISI, 
      'Penyerahan Bahan : ' + A.NOBUKTI + ' Untuk SPK : ' + A.NoBPPB NOTE,0 LAMPIRAN, 
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       CAST(ROW_NUMBER() Over(PARTITION BY A.NoJurnal Order by A.Nojurnal) As int) URUT,  
       ISNULL(b.KodePerkiraan,'') PERKIRAAN, 
       '117100' Lawan, 
       a.keterangan KETERANGAN, '' KETERANGAN2, 
       Sum(B.QNT*B.HPP) DEBET, 0 KREDIT, 'IDR' VALAS, 1 KURS, 
       Sum(B.QNT*B.HPP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, '' CUSTCUPPP, '' CUSTSUPPL, '' KODEP, 
       '' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
      'BP' JENIS, A.Nobukti NOBUKTITRANS
FROM  DBO.DBRPenyerahanBhn A 
LEFT OUTER JOIN DBO.DBRPenyerahanBhnDET B ON B.NOBUKTI = A.NOBUKTI 
--Where A.noJurnal<>''
Group by A.NOBUKTI, A.TglJurnal, A.NoBPPB,b.KodePerkiraan,
      A.NOBUKTI, A.Nourut, A.TANGGAL,a.Keterangan,
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       A.NoUrutJurnal


-- Ubah Kemasan

Union ALL
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.Tanggal,'01' DEVISI, 
      'Ubah Kemasan : ' + A.NOBUKTI + ' ' + H.NamaSubGrp + ' (' + H.KodeSubGrp + ')' NOTE,0 LAMPIRAN, 
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       CAST(ROW_NUMBER() Over(PARTITION BY A.NoJurnal Order by A.Nojurnal) As int) URUT,  
       --Case when Sum(B.QNTDB)<>0 then J.Perkiraan
       --     when Sum(B.QNTCR)<>0 then H.PerkPers
       --     else ''
       --end  PERKIRAAN, 
       '' PERKIRAAN,
       Case when Sum(B.QNTDB)<>0 then H.PerkPers
            when Sum(B.QNTCR)<>0 then J.Perkiraan
            else ''
       end LAWAN, 
       'Ubah Kemasan : ' + + H.NamaSubGrp + ' (' + H.KodeSubGrp + ')'+CHAR(13)+ 
       'No. Ubah Kemasan : '+A.Nobukti+' TANGGAL : '+ Convert(Varchar(15),A.Tanggal, 105)KETERANGAN, '' KETERANGAN2, 
       Sum(Case when B.QNTDB<>0 then B.QNTDB 
                when B.QNTCR<>0 then B.QNTCR
                else 0
           end * B.HPP) DEBET, 0 KREDIT, 'IDR' VALAS, 1 KURS, 
       Sum(Case when B.QNTDB<>0 then B.QNTDB 
                when B.QNTCR<>0 then B.QNTCR
                else 0
           end * B.HPP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, '' CUSTCUPPP, '' CUSTSUPPL, '' KODEP, 
       '' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '01' KODEBAG, '' STATUSGIRO, 
      'KMS' JENIS, A.NOBUKTI NOBUKTITRANS
FROM  DBO.DBUBAHKEMASAN A 
LEFT OUTER JOIN DBO.DBUBAHKEMASANDET B ON B.NOBUKTI = A.NOBUKTI 
LEFT OUTER JOIN DBO.DBBARANG F ON F.KODEBRG = B.KODEBRG LEFT OUTER JOIN DBO.DBGROUP G ON G.KODEGRP = F.KODEGRP
LEFT OUTER JOIN DBO.dbSubGroup H ON H.KodeGrp = F.KODEGRP and H.KodeSubGrp=F.KODESUBGRP
Left Outer Join (Select Perkiraan 
                 from dbo.DBPOSTHUTPIUT 
                 where Kode='BYO') J on 1=1
--Where A.noJurnal<>''
Group by A.NOBUKTI, A.TglJurnal, H.KodeSubGrp,
      A.NOBUKTI, A.NOURUT, H.NamaSubGrp, H.KodeSubGrp,A.TANGGAL,
       A.IsOtorisasi1, A.OTOUSER1, A.TGLOTO1, 
       A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
       A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
       A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
       A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
       A.NoUrutJurnal,H.PerkPers, J.Perkiraan


*/


--- Opname
union all
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Opname Bahan '  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	min(B.Urut)+1000 URUT, 
	'117100' PERKIRAAN, '8230003' LAWAN,A.NOBUKTI KETERANGAN, '' KETERANGAN2, 
	SUM(b.QNTDB*b.HPP) DEBET, 0 KREDIT, 'IDR' Valas, 1 KURS, 
	SUM(b.QNTDB*b.HPP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'OPN' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBKOREKSI A 
LEFT OUTER JOIN DBO.DBKOREKSIDET B ON B.NOBUKTI=A.NOBUKTI 
where b.QNTDB<>0 --and a.KodeGdg<>'GTC'
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, ---J.Perkiraan,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.NOURUT, A.NoJurnal
union all
SELECT A.NOBUKTI NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Opname Bahan '  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	min(B.Urut)+200 URUT, 
	'8230003' PERKIRAAN, '117100' LAWAN,A.NOBUKTI KETERANGAN, '' KETERANGAN2, 
	SUM(b.QNTCR*b.HPP) DEBET, 0 KREDIT, 'IDR' Valas, 1 KURS, 
	SUM(b.QNTCR*b.HPP) DEBETRP, 0 KREDITRP, 'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'OPN' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBKOREKSI A 
LEFT OUTER JOIN DBO.DBKOREKSIDET B ON B.NOBUKTI=A.NOBUKTI 
where b.QNTCR<>0 --and a.KodeGdg<>'GTC'
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, ---J.Perkiraan,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.NOURUT, A.NoJurnal	
	
-- TERIMA DPP
union all
SELECT case when P.Kode='GTR' then REPLACE(A.NoBukti,'/TDP/','/BGT-TDP/') 
		when P.Kode='BANK' then REPLACE(A.NoBukti,'/TDP/','/BBM-TDP/')
		when P.Kode='KAS' then REPLACE(A.NoBukti,'/TDP/','/BKM-TDP/') 
		else REPLACE(A.NoBukti,'/TDP/','/BMM-TDP/') end NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Penerimaan DPP '+isnull(Cs.NamaCustSupp,'')  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	min(B.Urut)+4000 URUT, 
	case when b.kl=0 then B.KasBank else b.Perkiraan end PERKIRAAN, 
	'113100' LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 
	'' KETERANGAN2, 
	SUM(B.DIBAYAR) DEBET, 0 KREDIT, A.Valas Valas, B.Kurs, 
	SUM((B.DIBAYAR)*B.Kurs) DEBETRP, 0 KREDITRP, 
	case when P.Kode='GTR' then 'BGT' when P.Kode='BANK' then 'BBM' when P.Kode='KAS' then 'BKM' else 'BMM' end TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'TDP' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBTerimaDPP A 
LEFT OUTER JOIN DBO.DBTerimaDPPDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs .KODECUSTSUPP=A.KodeCustSupp
LEFT outer join dbo.DBPOSTHUTPIUT P on P.Perkiraan=B.KasBank
where a.NoJurnal<>'' and isnull(a.pPLD,0)=0 --and a.NoBukti='spl/tdp/00010/0616'
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, Cs.NAMACUSTSUPP, A.KodeCustSupp, B.Perkiraan,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.Valas, B.Kurs, A.NOURUT, P.Kode,case when b.kl=0 then B.KasBank else b.Perkiraan end
	
-- TERIMA DPP LEBIH BAYAR
union all
SELECT case when P.Kode='GTR' then REPLACE(A.NoBukti,'/TDP/','/BGT-TDP/') 
		when P.Kode='BANK' then REPLACE(A.NoBukti,'/TDP/','/BBM-TDP/')
		when P.Kode='KAS' then REPLACE(A.NoBukti,'/TDP/','/BKM-TDP/') 
		else REPLACE(A.NoBukti,'/TDP/','/BMM-TDP/') end NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Penerimaan DPP '+isnull(Cs.NamaCustSupp,'')  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	min(B.Urut)+10000 URUT, 
	B.KasBank PERKIRAAN, 
	 '81600' LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 
	'' KETERANGAN2, 
	SUM(B.LB) DEBET, 0 KREDIT, A.Valas Valas, B.Kurs, 
	SUM(B.LB*B.Kurs) DEBETRP, 0 KREDITRP, 
	case when P.Kode='GTR' then 'BGT' when P.Kode='BANK' then 'BBM' when P.Kode='KAS' then 'BKM' else 'BMM' end TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'TDP' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBTerimaDPP A 
LEFT OUTER JOIN DBO.DBTerimaDPPDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs .KODECUSTSUPP=A.KodeCustSupp
LEFT outer join dbo.DBPOSTHUTPIUT P on P.Perkiraan=B.KasBank
where B.LB<>0 and a.NoJurnal<>''and isnull(a.pPLD,0)=0
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, Cs.NAMACUSTSUPP, A.KodeCustSupp, B.KasBank,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.Valas, B.Kurs, A.NOURUT, P.Kode
union all
-- TERIMA DPP KURANG BAYAR
SELECT case when P.Kode='GTR' then REPLACE(A.NoBukti,'/TDP/','/BGT-TDP/') 
		when P.Kode='BANK' then REPLACE(A.NoBukti,'/TDP/','/BBM-TDP/')
		when P.Kode='KAS' then REPLACE(A.NoBukti,'/TDP/','/BKM-TDP/') 
		else REPLACE(A.NoBukti,'/TDP/','/BMM-TDP/') end NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Penerimaan DPP '+isnull(Cs.NamaCustSupp,'')  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	min(B.Urut)+20000 URUT, 
	 '81600' PERKIRAAN, 
	 '113100' LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 
	'' KETERANGAN2, 
	SUM(B.KL) DEBET, 0 KREDIT, A.Valas Valas, B.Kurs, 
	SUM(B.KL*B.Kurs) DEBETRP, 0 KREDITRP, 
	case when P.Kode='GTR' then 'BGT' when P.Kode='BANK' then 'BBM' when P.Kode='KAS' then 'BKM' else 'BMM' end TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'TDP' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBTerimaDPP A 
LEFT OUTER JOIN DBO.DBTerimaDPPDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs .KODECUSTSUPP=A.KodeCustSupp
LEFT outer join dbo.DBPOSTHUTPIUT P on P.Perkiraan=B.KasBank
where B.KL<>0 and a.NoJurnal<>''and isnull(a.pPLD,0)=0
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, Cs.NAMACUSTSUPP, A.KodeCustSupp, B.KasBank,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.Valas, B.Kurs, A.NOURUT, P.Kode	
------- PELUNASAN DPP	
UNION ALL	
SELECT a.NoBukti NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Penerimaan DPP '+isnull(Cs.NamaCustSupp,'')  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	min(B.Urut) URUT, B.KasBank  PERKIRAAN, '113100' LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 	'' KETERANGAN2, 
	SUM(B.DIBAYAR) DEBET, 0 KREDIT, A.Valas Valas, B.Kurs, 
	SUM((B.DIBAYAR)*B.Kurs) DEBETRP, 0 KREDITRP, 
	'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'TDP' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBTerimaDPP A 
LEFT OUTER JOIN DBO.DBTerimaDPPDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs .KODECUSTSUPP=A.KodeCustSupp
--LEFT outer join dbo.DBPOSTHUTPIUT P on P.Perkiraan=B.KasBank
where B.DIBAYAR<>0  and isnull(a.pPLD,0)=1 
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, Cs.NAMACUSTSUPP, A.KodeCustSupp, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.Valas, B.Kurs, A.NOURUT, B.KasBank 
-- TERIMA DPP KURANG BAYAR	
UNION ALL	
SELECT a.NoBukti NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Penerimaan DPP '+isnull(Cs.NamaCustSupp,'')  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	min(B.Urut)+2000 URUT, 
	 b.Perkiraan  PERKIRAAN, 
	'113100' LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 
	'' KETERANGAN2, 
	SUM(B.KL) DEBET, 0 KREDIT, A.Valas Valas, B.Kurs, 
	SUM(B.KL*B.Kurs) DEBETRP, 0 KREDITRP, 
	'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'TDP' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBTerimaDPP A 
LEFT OUTER JOIN DBO.DBTerimaDPPDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs .KODECUSTSUPP=A.KodeCustSupp
LEFT outer join dbo.DBPOSTHUTPIUT P on P.Perkiraan=B.KasBank
where B.KL<>0  and isnull(a.pPLD,0)=1 --and a.NoJurnal<>''
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, Cs.NAMACUSTSUPP, A.KodeCustSupp, B.Perkiraan,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.Valas, B.Kurs, A.NOURUT, P.Kode
	
-- TERIMA DPP LEBIH BAYAR
union all
SELECT a.NoBukti NOBUKTI, A.NOURUT, A.TANGGAL TANGGAL,'01' DEVISI, 
	'Penerimaan DPP '+isnull(Cs.NamaCustSupp,'')  NOTE,0 LAMPIRAN, 
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL, 
	min(B.Urut)+1000 URUT, 
	B.KasBank PERKIRAAN, 
	B.Perkiraan LAWAN,
	isnull(Cs.NamaCustSupp,'') KETERANGAN, 
	'' KETERANGAN2, 
	SUM(B.LB) DEBET, 0 KREDIT, A.Valas Valas, B.Kurs, 
	SUM(B.LB*B.Kurs) DEBETRP, 0 KREDITRP, 
	'BMM' TIPETRANS, 'C' TPHC, 
	'' CUSTSUPPP,'' CUSTSUPPL, '' KODEP, 
	'' KODEL, '' NOAKTIVAP, '' NOAKTIVAL, '' STATUSAKTIVAP, '' STATUSAKTIVAL, '' NOBON, '' KODEBAG, '' STATUSGIRO, 
	'TDP' JENIS, A.NOBUKTI NOBUKTITRANS,''kodecost,'' kodesubcost
FROM DBO.DBTerimaDPP A 
LEFT OUTER JOIN DBO.DBTerimaDPPDET B ON B.NOBUKTI=A.NOBUKTI 
LEFT OUTER JOIN DBO.DBCUSTSUPP Cs ON Cs .KODECUSTSUPP=A.KodeCustSupp
LEFT outer join dbo.DBPOSTHUTPIUT P on P.Perkiraan=B.KasBank
where B.LB<>0 and isnull(a.pPLD,0)=1
GROUP BY A.NOBUKTI, A.NOURUT, A.TANGGAL, Cs.NAMACUSTSUPP, A.KodeCustSupp, B.KasBank,b.Perkiraan,
	A.ISOTORISASI1, A.OTOUSER1, A.TGLOTO1, 
	A.ISOTORISASI2, A.OTOUSER2, A.TGLOTO2, 
	A.ISOTORISASI3, A.OTOUSER3, A.TGLOTO3, 
	A.ISOTORISASI4, A.OTOUSER4, A.TGLOTO4, 
	A.ISOTORISASI5, A.OTOUSER5, A.TGLOTO5, A.MaxOL,
	A.Valas, B.Kurs, A.NOURUT, P.Kode

































































GO
