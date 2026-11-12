# Catatan Update DB - Master (DBSMLBARU)

Menu Master newSML disamakan dengan newProgramAJC (2026-10-14). Controller dan view `Master` sudah identik dengan newProgramAJC; yang kurang hanya satu tabel.

## Script

| No | File | Isi | Status |
|---|---|---|---|
| 13 | `13_dbhislokasibrg.sql` | Tabel baru `DBHISLOKASIBRG` (riwayat lokasi barang) untuk Master > Set Lokasi Barang. | Sudah dijalankan 2026-10-14 |
| 24 | `24_dbcustsupp_tipebayar.sql` | Kolom baru `DBCUSTSUPP.TipeBayar` (0 = Tunai/CBD, 1 = Kredit) untuk Master Customer + isi otomatis Pembayaran di SO. Data lama: customer dengan `HARI` > 0 diisi Kredit. | Sudah dijalankan 2026-11-10 |
| 27 | `27_dbbadanusaha.sql` | Tabel baru `DBBADANUSAHA` (KodeUsaha, PPH21, PPH23; minimal satu PPh > 0) + 9 data awal (PT, CV = PPh23 2; PD, UD, TOKO, BAPAK, IBU, EXP, - = PPh23 2.5; PPh21 0). Menu `DBMENUWEB` 01003014 "Badan Usaha" di Supplier & Customer; hak akses `DBFLMENUWEB` hanya SA & SYAIDI (user lain baris 0). Dropdown Bentuk Usaha di Master Customer/Supplier/Ekspedisi diambil dari tabel ini, PPh21/PPh23 dikunci mengikuti master. Data lama `DBCUSTSUPP` tidak diubah. | Sudah dijalankan 2026-11-12 |
