-- 10 - Perbaiki QNT1/QNT2 PRJ lama SML/PRJ/00001/0916 urut 1 (13 Oktober 2026)
-- QNT 8 PAK tetapi QNT1 dan QNT2 tersimpan 0. Barang bersatuan 1 (PAK/PAK, ISI 1), jadi
-- yang benar QNT1 = QNT2 = 8. Belum pernah dipakai SPR. Tanpa perbaikan ini, kalau PRJ ini
-- diotorisasi ia tidak akan muncul di Outstanding PRJ (sisa dihitung dari QNT1 = 0).
-- Aman dijalankan ulang: hanya mengubah baris yang masih bernilai 0.

update dbPRRJualDet
set QNT1 = 8.00, QNT2 = 8.00
where NoBukti = 'SML/PRJ/00001/0916' and Urut = 1 and QNT = 8.00 and QNT1 = 0 and QNT2 = 0

select NoBukti, Urut, QNT, QNT1, QNT2 from dbPRRJualDet where NoBukti = 'SML/PRJ/00001/0916'
