-- 11 - Kredit Note: default perkHP & akun posting BK (14 Oktober 2026)
--
-- (a) DBKreditNoteDET.perkHP NOT NULL tanpa default, sedangkan Sp_KreditNote tidak mengisinya.
--     Akibatnya insert item KN gagal diam-diam (hanya header DBKreditNote yang tersimpan, item
--     hilang). Ditambah DEFAULT '' supaya SP berhasil; akun yang benar (akun piutang invoice di
--     DBHUTPIUT, umumnya 113100) diisi oleh KreditNoteController sesudah SP dijalankan.
-- (b) Jurnal otomatis KN (vwPostJurnalOto) mengambil akun DEBET dari DBPOSTHUTPIUT Kode 'BK',
--     tetapi barisnya tidak ada sehingga akun debet jurnal KN kosong. Ditambah 41200 Potongan
--     Penjualan (akun yang dipakai data lama sebagai lawan Piutang Usaha customer; 51300 dipakai
--     untuk potongan dari supplier). Kolom lain disalin dari baris PT.
--
-- Hanya MENAMBAH (constraint default & satu baris konfigurasi). Aman dijalankan ulang.

if not exists (
  select 1 from sys.columns c
  where c.object_id = object_id('dbo.DBKreditNoteDET') and c.name = 'perkHP' and c.default_object_id <> 0
)
  alter table dbo.DBKreditNoteDET add constraint DF_DBKreditNoteDET_perkHP default ('') for perkHP
GO

if not exists (select 1 from dbo.DBPOSTHUTPIUT where Kode = 'BK')
  insert into dbo.DBPOSTHUTPIUT (Perkiraan, Kode, Persen, Tipe, Akumulasi, Biaya1, Biaya2, PersenBiaya1, PersenBiaya2, Urut)
  select top 1 '41200', 'BK', Persen, Tipe, Akumulasi, Biaya1, Biaya2, PersenBiaya1, PersenBiaya2, 2
  from dbo.DBPOSTHUTPIUT where Kode = 'PT' and Urut = 2
GO

select c.name, object_definition(c.default_object_id) dflt
from sys.columns c where c.object_id = object_id('dbo.DBKreditNoteDET') and c.name = 'perkHP'
select * from dbo.DBPOSTHUTPIUT where Kode = 'BK'
