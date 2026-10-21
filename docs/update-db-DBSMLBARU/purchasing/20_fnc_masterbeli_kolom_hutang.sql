-- =====================================================================
-- 20 - fnc_MasterBeli: tambah kolom hutang ('Lunas' / 'Belum')
-- Menu: Penerimaan ACC gabungan (newpobeliacc), kolom badge Hutang
-- Tanggal: 20 Oktober 2026 (sudah dijalankan di DBSMLBARU & DBSMLNEW)
--
-- Perubahan dibanding versi lama (HANYA MENAMBAH, kolom lama tidak berubah):
--   1. Join baru:
--        left outer join (select nofaktur,sum(kredit-debet) lunas
--                         from DBHUTPIUT group by nofaktur) g3
--          on a.NOBUKTI=g3.NoFaktur
--   2. Kolom baru di akhir SELECT:
--        case when ISNULL(lunas,0)=0 then 'Lunas' else 'Belum' end hutang
--   3. g3.lunas ditambahkan di GROUP BY.
--
-- Tanpa skrip ini menu Penerimaan ACC tetap jalan, tetapi kolom Hutang kosong.
-- Aman dijalankan ulang (ALTER FUNCTION berisi definisi lengkap).
-- =====================================================================
GO
ALTER FUNCTION [dbo].[fnc_MasterBeli] 
(	
	@bulan int,
	@tahun int,
	@pjasa tinyint
)
RETURNS TABLE 
AS
RETURN 
(


Select 	A.NoBukti,cast (A.TANGGAL as DateTIme) TglBeli,
case when A.NOBUKTI like '%SML%' then
		case when a.TANGGAL <='01/31/2024' then A.Tanggal WHEN ISNULL(A.pJasa,0)=1 THEN A.TANGGAL  else ISNULL(a.tglQC,NUll) End  
else A.TANGGAL End TANGGAL
, A.KodeSupp, C.NAMACUSTSUPP, C.Kota,NoPO,A.UserBatal,A.tglbatal,
	A.Handling, A.FakturSupp FAKTURSUPP, isnull(A.IsBatal,0) IsBatal,
        sum(B.SubTotal) TotSubTotal, sum(B.NDiskon) TotDiskon, 
	sum(B.SubTotal)-sum(B.NDiskon) TotTotal, sum(B.NDPP) TotDPP, 
	sum(B.NPPN) TotPPN, sum(B.NNet) TotNet,
	sum(B.SubTotal*B.Kurs) TotSubTotalRp, sum(B.NDiskon*B.Kurs) TotDiskonRp, 
	sum(B.SubTotal*B.Kurs)-sum(B.NDiskon*B.Kurs) TotTotalRp, sum(B.NDPP*B.Kurs) TotDPPRp, 
	sum(B.NPPN*B.Kurs) TotPPNRp, sum(B.NNet*B.Kurs) TotNetRp,
	--a.TipeBayar
		case when a.KODEVLS<>'IDR' then 1 else 
	    case when g2.Bayar is null then a.TIPEBAYAR
			 when  g2.Bayar= 2 then 1
			 when g2.Bayar=1 then 0 end 
	end TipeBayar
	,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,B.KodeGdg,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.MAXOL,A.NoPOHd,D.NOPOCUST,
	F.NAMACUSTSUPP NAMACUSTOMER,ISNULL(A.pjasa,0) pJasa,ISNULL(a.tglQC,NUll) TglQC,ISNULL(UserQC,'') UserQC,ISNULL(pQC,0) pQC,
	A.pWMs,
MONTH(case when A.NOBUKTI like '%SML%' then
		case when a.TANGGAL <='01/31/2024' then A.Tanggal WHEN ISNULL(A.pJasa,0)=1 THEN A.TANGGAL  else ISNULL(a.tglQC,NUll) End  
else A.TANGGAL End) BULAN	,
YEAR(case when A.NOBUKTI like '%SML%' then
		case when a.TANGGAL <='01/31/2024' then A.Tanggal WHEN ISNULL(A.pJasa,0)=1 THEN A.TANGGAL  else ISNULL(a.tglQC,NUll) End  
else A.TANGGAL End) TAHUN,
Cast(Case when Case when A.IsOtorisasi1=1 then 1 else 0 end+
                       Case when A.IsOtorisasi2=1 then 1 else 0 end+
                        Case when A.IsOtorisasi3=1 then 1 else 0 end+
                        Case when A.IsOtorisasi4=1 then 1 else 0 end+
                        Case when A.IsOtorisasi5=1 then 1 else 0 end=A.MaxOL then 0
                   else 1
              end As Bit) NeedOtorisasi,a.KODEVLS,a.KURS,
case when a.PPN=0 then 'NONE' when a.PPN=1 then 'Exclude' when a.PPN=2 then 'Include' End MyPPN ,
case when A.TIPEBAYAR=0 then 'TUNAI' else 'KREDIT' end MyTipeBayar,D.NOSO  ,A.NOUMK,A.NuangMuka,B.NilaiPPN  ,
a.KETERANGAN SOPIR    ,g.NAMA NAMAGUDANG 
,isnull(A.KETERANGAN,'') KETERANGAN 
,CASE WHEN ISNULL(A.NuangMuka,0)=0 THEN 0 ELSE  G2.Bayar eND TipebayarUM
 ,A.TANGGAL + A.HARI JthTempo,A.PPN,F.ALAMAT1 ,A.HARI,A.DISCRP,A.NOURUT,case when ISNULL(lunas,0)=0 then 'Lunas' else 'Belum' end hutang
From dbBeli A
Left Outer Join dbBeliDet B on B.NoBukti=A.NoBukti
Left Outer Join DBCUSTSUPP C on c.KODECUSTSUPP=a.KodeSupp
Left Outer join DBPO D ON A.NoPOHd=D.NOBUKTI
LEFT OUTER JOIN DBSO E ON D.NOSO=E.NOBUKTI
LEFT OUTER JOIN DBCUSTSUPP F ON E.KODECUST=F.KODECUSTSUPP
left outer join DBGUDANG G on b.KodeGdg=g.KODEGDG
left outer join (select noso,Bayar from DBUMJUAL group by NOSO,Bayar) g2 on d.NOBUKTI=g2.NOSO
left outer join (select nofaktur,sum(kredit-debet) lunas from DBHUTPIUT group by nofaktur) g3 on a.NOBUKTI=g3.NoFaktur
--where MOnth(a.tanggal)=@bulan and YEAR(A.TANGGAL)=@tahun and isnull(A.pJasa,0)=@pjasa
Group By A.NoBukti, A.Tanggal, A.KodeSupp, C.NAMACUSTSUPP, C.Kota,
	A.Handling, A.FakturSupp, isnull(A.IsBatal,0), a.Keterangan,a.TipeBayar,NOPO,
	A.IsOtorisasi1, A.OtoUser1, A.TglOto1, A.IsOtorisasi2, A.OtoUser2, A.TglOto2, 
	A.IsOtorisasi3, A.OtoUser3, A.TglOto3, A.IsOtorisasi4, A.OtoUser4, A.TglOto4,
	A.IsOtorisasi5, A.OtoUser5, A.TglOto5, A.MAXOL,A.UserBatal,A.tglbatal,B.KodeGdg,A.NoPOHd,
	D.NOPOCUST,F.NAMACUSTSUPP,ISNULL(A.pjasa,0),ISNULL(a.tglQC,NUll),ISNULL(UserQC,''),ISNULL(pQC,0),
	A.pWMs ,g2.bayar
,a.KODEVLS,a.KURS,D.NOSO  ,A.NOUMK,A.NuangMuka,B.NilaiPPN,a.PPN,G.NAMA,isnull(A.KETERANGAN,'')-- ,A.FAKTURSUPP
 ,A.TANGGAL + A.HARI ,A.PPN,F.ALAMAT1  ,a.TIPEBAYAR,a.HARI,A.DISCRP,A.NOURUT,g3.lunas

)
GO
