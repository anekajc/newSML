-- 04 - Perbaikan view VWOUTPORP (menu Uang Muka Beli)
-- Dipakai HANYA oleh UangMukaBeliController@spAdd (validasi "Total Uang Muka melebihi PO").
-- Kolom tetap sama (NoBukti, TANGGAL, OUTPORP); hanya rumus OUTPORP untuk PPN tipe 2 (include) yang dibetulkan:
--   1. Bagian DPP memakai A.[Qnt] padahal bagian lain memakai A.[QntOS] (qty outstanding)
--      -> PO yang sudah sebagian diterima nilainya jadi kebesaran. Sekarang memakai QntOS.
--   2. Pembagi DPP ditulis mati 1.11 -> salah untuk PO dengan NilaiPPN selain 11.
--      Sekarang memakai (1 + NilaiPPN/100), sama dengan bagian PPN-nya.
-- Aman dijalankan ulang.

USE DBSMLBARU
GO

ALTER VIEW [dbo].[VWOUTPORP]
AS
select A.NoBukti,C.TANGGAL,
round(SUM(
((case when B.[ppn]=(1) OR B.[ppn]=(0) then A.[QNTOS]*(B.[HARGA]-B.[DISCTOT])-((A.[QntOS]*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100)
      when B.[ppn]=(2) then (A.[QntOS]*(B.[HARGA]-B.[DISCTOT])-((A.[QntOS]*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100))/((1.0)+[nilaippn]/(100.00)) else (0) end+
						case when B.[ppn]=(1) then A.[QntOS]*(B.[HARGA]-B.[DISCTOT])-((A.[QNTOS]*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100) when
						B.[ppn]=(2) then (A.[QntOS]*(B.[HARGA]-B.[DISCTOT])-((A.[QntOS]*(B.[HARGA]-B.[DISCTOT]))*B.[DISC])/(100))/((1.0)+[nilaippn]/(100.00)) else (0) end*([NILAIPPN]/(100.00)))

)),0)OUTPORP

from vwOutstandingPO A
LEFT OUTER JOIN DBPODET B ON A.NoBukti=B.NOBUKTI AND A.Urut=B.URUT
LEFT OUTER JOIN DBPO C ON B.NOBUKTI=C.NOBUKTI
GROUP BY A.NoBukti,C.TANGGAL
GO
