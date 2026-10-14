-- 09 - Perbaiki QNT1 PRJ lama SML/PRJ/00001/0416 urut 2 (13 Oktober 2026)
-- QNT1 tersimpan 548.60 (salah ketik 10x). Yang benar 54.86 MTR x 3.281 = 180.00 FEET,
-- sama dengan QNT1 SPR SML/SPR/00001/0416 yang sudah meretur seluruhnya. Akibat salah
-- ketik itu PRJ ini selalu tampil di tab Outstanding PRJ menu Retur Penjualan Gudang
-- padahal tidak bisa diproses ("Data tidak ditemukkan").
-- Aman dijalankan ulang: hanya mengubah baris yang masih bernilai 548.60.

update dbPRRJualDet
set QNT1 = 180.00
where NoBukti = 'SML/PRJ/00001/0416' and Urut = 2 and QNT1 = 548.60

select NoBukti, Urut, QNT, QNT1, QNT2 from dbPRRJualDet where NoBukti = 'SML/PRJ/00001/0416'
