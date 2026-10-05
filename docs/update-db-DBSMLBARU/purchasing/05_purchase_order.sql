-- 05 - Menu Purchase Order (upgrade tampilan dari newProgramAJC)
-- Sifat: hanya menambah. Aman dijalankan ulang.
--
-- 1. View vwMasterPO: tambah 2 kolom di akhir
--      qnt     = SUM(DBPODET.QNT - DBPODET.QntBatal) per PO
--      qntbeli = total qty yang sudah diterima (DBBELIDET per NoPO)
--    Dipakai badge status penerimaan PO (Batal/Outstanding/Sebagian/Selesai) di tab Purchase Order.
--    Kolom lama tetap utuh, jumlah baris tidak berubah. Definisi disamakan dengan DBSMLNEW.
--    Pemakai lain view ini di DBSMLBARU: SP CetakRevisiPO (hanya ambil kolom TotDiskon/TotDPP/TotPPN/TotNet - tidak terpengaruh).
--
-- 2. DBHEADERTABLEALIAS: alias kolom tabel Outstanding PR (href purchaseorder, urut 1).

USE DBSMLBARU
GO

ALTER View [dbo].[vwMasterPO]
as

Select 	A.NoBukti, A.Tanggal, A.KodeSupp, C.NAMACUSTSUPP, C.Kota,
	A.Handling, A.FakturSupp, isnull(A.IsBatal,0) IsBatal,
        sum(B.SubTotal) TotSubTotal, sum(B.NDiskon) TotDiskon,
	sum(B.SubTotal)-sum(B.NDiskon) TotTotal, sum(B.NDPP) TotDPP,
	sum(B.NPPN) TotPPN, sum(B.NNet) TotNet,
	sum(B.SubTotal*A.Kurs) TotSubTotalRp, sum(B.NDiskon*A.Kurs) TotDiskonRp,
	sum(B.SubTotal*A.Kurs)-sum(B.NDiskon*A.Kurs) TotTotalRp, sum(B.NDPP*A.Kurs) TotDPPRp,
	sum(B.NPPN*A.Kurs) TotPPNRp, sum(B.NNet*A.Kurs) TotNetRp,ISNULL(A.pJasa,0) pJasa,
	SUM(B.QNT-B.QntBatal) qnt, D.qntbeli
From dbPO A
Left Outer Join dbPODet B on B.NoBukti=A.NoBukti
Left Outer Join  dbCustSupp C on c.KodeCustSupp=a.KodeSupp
left outer join (select nopo,sum(qnt)qntbeli from DBBELIDET group by nopo) D on D.NoPO=A.NOBUKTI
Group By A.NoBukti, A.Tanggal, A.KodeSupp, C.NAMACUSTSUPP, C.Kota,
	A.Handling, A.FakturSupp, isnull(A.IsBatal,0),ISNULL(A.pJasa,0),D.qntbeli
GO

IF NOT EXISTS (SELECT 1 FROM DBHEADERTABLEALIAS WHERE href = 'purchaseorder' AND urut = 1)
  INSERT INTO DBHEADERTABLEALIAS (value, href, urut)
  VALUES ('[{"value":"Nobukti","alias":"Nobukti"},{"value":"Tanggal","alias":"Tanggal"},{"value":"sat","alias":"sat"},{"value":"kodebrg","alias":"kodebrg"},{"value":"NamaBrg","alias":"NamaBrg"},{"value":"Qnt","alias":"Qnt"},{"value":"QNTPO","alias":"QNTPO"},{"value":"SisaPPL","alias":"SisaPR"},{"value":"Keterangan","alias":"Keterangan"},{"value":"QntoutSO","alias":"QntoutSO"},{"value":"QntStock","alias":"QntStock"}]',
          'purchaseorder', 1)
GO
