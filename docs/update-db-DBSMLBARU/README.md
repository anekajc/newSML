# Update Database DBSMLBARU

Semua perubahan database newSML (DBSMLBARU) dicatat di folder ini, dipisah per modul supaya tidak tercampur.

## Struktur folder

| Folder | Isi |
|---|---|
| `umum/` | Script yang berlaku untuk semua modul: `02_menu_web.sql` (menu web dan hak aksesnya), `06_menu_pemakai_acc_nonacc.sql` (href menu Berkas > Pemakai ACC / Non ACC). |
| `purchasing/` | Script dan catatan modul purchasing. Penjelasan lengkapnya ada di `CATATAN-UPDATE-DB-PURCHASING.md`. |
| `marketing/` | Script dan catatan modul marketing. Penjelasan lengkapnya ada di `CATATAN-UPDATE-DB-MARKETING.md`. |

Nomor file tetap urut lintas folder (01, 02, 03, ...), jadi urutan menjalankannya tidak berubah walaupun filenya ada di folder berbeda.

## Urutan menjalankan di server

1. **Backup database DBSMLBARU.**
2. `umum/02_menu_web.sql`. Cukup sekali untuk semua modul.
3. Script di folder modul yang di-update, sesuai urutan di catatan modul itu.
4. `umum/06_menu_pemakai_acc_nonacc.sql`. Butuh baris menu Pemakai ACC / Non ACC yang sudah ada di DBMENUWEB (di bawah menu 0004).

Catatan: tabel pengaturan kolom `DBHEADERTABLE` dan `DBHEADERTABLEALIAS` dibuat di `purchasing/01_vwppl_dan_headertable.sql`. Kalau modul lain (misalnya marketing) di-update lebih dulu dan butuh tabel itu, jalankan file 01 tersebut juga.
