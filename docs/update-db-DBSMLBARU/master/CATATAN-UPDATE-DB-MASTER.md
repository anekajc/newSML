# Catatan Update DB - Master (DBSMLBARU)

Menu Master newSML disamakan dengan newProgramAJC (2026-10-14). Controller dan view `Master` sudah identik dengan newProgramAJC; yang kurang hanya satu tabel.

## Script

| No | File | Isi | Status |
|---|---|---|---|
| 13 | `13_dbhislokasibrg.sql` | Tabel baru `DBHISLOKASIBRG` (riwayat lokasi barang) untuk Master > Set Lokasi Barang. | Sudah dijalankan 2026-10-14 |
| 24 | `24_dbcustsupp_tipebayar.sql` | Kolom baru `DBCUSTSUPP.TipeBayar` (0 = Tunai/CBD, 1 = Kredit) untuk Master Customer + isi otomatis Pembayaran di SO. Data lama: customer dengan `HARI` > 0 diisi Kredit. | Sudah dijalankan 2026-11-10 |
| 27 | `27_dbbadanusaha.sql` | Tabel baru `DBBADANUSAHA` (KodeUsaha, PPH21, PPH23; minimal satu PPh > 0) + 8 data awal (PT, CV = PPh23 2; PD, UD, TOKO, BAPAK, IBU, EXP = PPh23 2.5; PPh21 0). Menu `DBMENUWEB` 01003014 "Badan Usaha" di Supplier & Customer; hak akses `DBFLMENUWEB` hanya SA & SYAIDI (user lain baris 0). Dropdown Bentuk Usaha di Master Customer/Supplier/Ekspedisi diambil dari tabel ini, PPh21/PPh23 dikunci mengikuti master. Data lama `DBCUSTSUPP` tidak diubah. (Data awal `-` sudah dikeluarkan dari script ini, lihat 29.) | Sudah dijalankan 2026-11-12 |
| 29 | `29_dbbadanusaha_tanpa_strip.sql` | Hapus `KodeUsaha` tanpa huruf/angka (`-`) dari `DBBADANUSAHA` + CHECK `CK_DBBADANUSAHA_KODE` (KodeUsaha wajib berisi huruf/angka). Bentuk Usaha tidak boleh `-` / kosong di Master Badan Usaha maupun Customer/Supplier/Ekspedisi. Data lama `DBCUSTSUPP` dengan USAHA `-` / kosong tidak diubah, wajib dipilih ulang saat edit. | Sudah dijalankan 2026-11-12 (DBSMLBARU) |

## Deploy ke server client

Jalankan berurutan di database client (ganti `USE DBSMLBARU` di baris atas script bila nama DB client berbeda). Semua script aman dijalankan ulang.

1. `27_dbbadanusaha.sql` - tabel + data awal + menu + hak akses Badan Usaha. Versi terbaru sudah tanpa data `-`.
2. `29_dbbadanusaha_tanpa_strip.sql` - wajib tetap dijalankan setelah 27 (menghapus `-` bila sempat ter-insert oleh versi lama script 27, dan memasang CHECK `CK_DBBADANUSAHA_KODE`).
3. Upload kode aplikasi bersamaan: `app/Traits/BadanUsahaTrait.php`, `app/Http/Controllers/Master/MasterBadanUsahaController.php`, `resources/views/master/masterbadanusaha.blade.php`, `resources/views/master/partials/badanUsaha.blade.php`.

Cek setelah deploy:
- `SELECT * FROM DBBADANUSAHA` -> tidak ada kode `-`.
- `SELECT name FROM sys.check_constraints WHERE parent_object_id = OBJECT_ID('dbo.DBBADANUSAHA')` -> `CK_DBBADANUSAHA_PPH` dan `CK_DBBADANUSAHA_KODE`.
- Query terakhir script 29 menampilkan jumlah Customer/Supplier/Ekspedisi yang Bentuk Usaha-nya masih `-` / kosong. Data ini tidak diubah otomatis; user wajib memilih ulang Bentuk Usaha saat edit. Di DBSMLBARU (2026-11-12): 397 data (`-` 16, kosong 380, `.` 1).
