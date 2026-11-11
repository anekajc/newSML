# Catatan Update DB - Master (DBSMLBARU)

Menu Master newSML disamakan dengan newProgramAJC (2026-10-14). Controller dan view `Master` sudah identik dengan newProgramAJC; yang kurang hanya satu tabel.

## Script

| No | File | Isi | Status |
|---|---|---|---|
| 13 | `13_dbhislokasibrg.sql` | Tabel baru `DBHISLOKASIBRG` (riwayat lokasi barang) untuk Master > Set Lokasi Barang. | Sudah dijalankan 2026-10-14 |
| 24 | `24_dbcustsupp_tipebayar.sql` | Kolom baru `DBCUSTSUPP.TipeBayar` (0 = Tunai/CBD, 1 = Kredit) untuk Master Customer + isi otomatis Pembayaran di SO. Data lama: customer dengan `HARI` > 0 diisi Kredit. | Sudah dijalankan 2026-11-10 |
