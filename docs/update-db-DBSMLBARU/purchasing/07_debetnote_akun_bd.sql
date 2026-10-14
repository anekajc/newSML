-- 07 - Debet Note: akun posting BD (14 Oktober 2026)
--
-- Jurnal otomatis Debet Note (vwPostJurnalOto, Jenis 'DN') = Debet Hutang Usaha (DBPOSTHUTPIUT
-- HT urut 2) / Lawan akun DBPOSTHUTPIUT Kode 'BD'. Baris 'BD' tidak ada, sehingga akun lawan
-- jurnal DN kosong. Ditambah 51300 Discount/Potongan Pembayaran: di data lama akun ini yang
-- dikreditkan untuk credit memo / potongan dari supplier (146 jurnal, 2019-2026), sedangkan
-- 81300 Discount / Bonus Pembelian hanya sekali dipakai. Kolom lain disalin dari baris HT.
-- Pasangannya di sisi penjualan: Kode 'BK' (Kredit Note) = 41200, lihat
-- ../marketing/11_kreditnote_perkhp_dan_akun_bk.sql.
--
-- Hanya MENAMBAH satu baris konfigurasi. Aman dijalankan ulang.

if not exists (select 1 from dbo.DBPOSTHUTPIUT where Kode = 'BD')
  insert into dbo.DBPOSTHUTPIUT (Perkiraan, Kode, Persen, Tipe, Akumulasi, Biaya1, Biaya2, PersenBiaya1, PersenBiaya2, Urut)
  select top 1 '51300', 'BD', Persen, Tipe, Akumulasi, Biaya1, Biaya2, PersenBiaya1, PersenBiaya2, 2
  from dbo.DBPOSTHUTPIUT where Kode = 'HT' and Urut = 2
GO

select * from dbo.DBPOSTHUTPIUT where Kode in ('BD', 'BK')
