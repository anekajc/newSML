# Update Database DBSMLBARU

Semua perubahan database newSML (DBSMLBARU) dicatat di folder ini, dipisah per modul supaya tidak tercampur.

## Struktur folder

| Folder | Isi |
|---|---|
| `umum/` | Script yang berlaku untuk semua modul: `02_menu_web.sql` (menu web dan hak aksesnya), `06_menu_pemakai_acc_nonacc.sql` (href menu Berkas > Pemakai ACC / Non ACC), `15_sp_flmenuweb1_dbflmenuweb.sql` (centang hak akses Berkas > Pemakai disimpan ke DBFLMENUWEB), `17_tabel_otorisasi.sql` (tabel DBREQOTORISASI & DBPUSHSUBSCRIPTION untuk lonceng otorisasi dan otorisasi Kas), `37_menu_wms_kode_21.sql` (kode menu WMS disamakan ke 21xxxx, lihat bagian 37 di bawah). |
| `gudang/` | Script dan catatan modul gudang. Penjelasan lengkapnya ada di `CATATAN-UPDATE-DB-GUDANG.md`. |
| `report/` | Script dan catatan SP/view laporan. Penjelasan lengkapnya ada di `CATATAN-UPDATE-DB-REPORT.md`. |
| `purchasing/` | Script dan catatan modul purchasing. Penjelasan lengkapnya ada di `CATATAN-UPDATE-DB-PURCHASING.md`. |
| `marketing/` | Script dan catatan modul marketing. Penjelasan lengkapnya ada di `CATATAN-UPDATE-DB-MARKETING.md`. |
| `utilitas/` | Script dan catatan modul utilitas. Penjelasan lengkapnya ada di `CATATAN-UPDATE-DB-UTILITAS.md`. |
| `master/` | Script dan catatan modul master data. Penjelasan lengkapnya ada di `CATATAN-UPDATE-DB-MASTER.md`. |
| `accounting/` | Script dan catatan modul accounting. Penjelasan lengkapnya ada di `CATATAN-UPDATE-DB-ACCOUNTING.md`. |

Nomor file tetap urut lintas folder (01, 02, 03, ...), jadi urutan menjalankannya tidak berubah walaupun filenya ada di folder berbeda.

## Urutan menjalankan di server

1. **Backup database DBSMLBARU.**
2. `umum/02_menu_web.sql`. Cukup sekali untuk semua modul.
3. Script di folder modul yang di-update, sesuai urutan di catatan modul itu.
4. `umum/06_menu_pemakai_acc_nonacc.sql`. Butuh baris menu Pemakai ACC / Non ACC yang sudah ada di DBMENUWEB (di bawah menu 0004).
5. `umum/37_menu_wms_kode_21.sql`. Jalankan setelah menu WMS sudah ada di DBMENUWEB/DBFLMENUWEB.

Catatan: tabel pengaturan kolom `DBHEADERTABLE` dan `DBHEADERTABLEALIAS` dibuat di `purchasing/01_vwppl_dan_headertable.sql`. Kalau modul lain (misalnya marketing) di-update lebih dulu dan butuh tabel itu, jalankan file 01 tersebut juga.

## 37 — Kode menu WMS disamakan ke 21xxxx (2026-11-19)

Masalah: di menu WMS, Transfer Gudang Out serta 3 menu Ubah Kemasan (Permintaan UK, Picking UK, Finalisasi UK) tidak tampil. Root WMS di `DBMENUWEB` berkode `10` (`10`, `1001`..`100310`), sedangkan menu yang ditambahkan belakangan (`210311`..`210314`, `2104` Gudang + `210401`..`210407`, `2105` Dashboard + `210501`) berkode `21`. Akibatnya menu 21xxxx tidak punya induk sehingga tidak tampil di navbar. Semua controller WMS (`app/Http/Controllers/Wms`) cek akses dengan kode 21xxxx, jadi menu 10xxxx yang tampil pun gagal di cek akses.

Perbaikan (`umum/37_menu_wms_kode_21.sql`, aman dijalankan ulang, kode PHP tidak berubah):
- `DBMENUWEB`: baris 10xxxx (HeaderMenu 21) diganti jadi 21xxxx.
- `DBFLMENUWEB`: baris 10xxxx dihapus kalau baris 21xxxx untuk user yang sama sudah ada (baris 21xxxx dipertahankan karena berisi setting otorisasi/IsBatal); kalau belum ada, L1-nya diganti jadi 21xxxx.
- `DBREQOTORISASI`: KODEMENU 10xxxx diganti jadi 21xxxx.
- Backup ada di `DBMENUWEB_BAK37` dan `DBFLMENUWEB_BAK37`. Skrip rollback ada di komentar bagian bawah skrip.

Status: **sudah dijalankan di DBSMLBARU pada 2026-11-19.** Hasilnya 25 menu diganti kodenya, 25 baris DBFLMENUWEB 10xxxx (user SA) dihapus karena baris 21xxxx-nya sudah ada, DBREQOTORISASI 0 baris, sisa kode 10 = 0, dan semua 39 menu WMS punya induk.
