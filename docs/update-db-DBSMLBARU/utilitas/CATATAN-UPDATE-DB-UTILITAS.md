# Catatan Update DB - Utilitas (DBSMLBARU)

Menu Utilitas newSML disamakan dengan newProgramAJC (2026-10-14). Controller dan view `Utilitas` sudah identik dengan newProgramAJC; yang kurang ada di route dan database.

## Perubahan kode (bukan database)

- `routes/web.php` sekarang memuat `routes/utilitas.php`. Sebelumnya file itu tidak pernah dimuat, jadi `/hitungulangstock`, `/posting`, dan `/tutupbuku` tidak punya route.

## Script

| No | File | Isi | Status |
|---|---|---|---|
| 12 | `12_menu_utilitas.sql` | Isi `href` + `HeaderMenu` menu 081, 08102 (Hitung Ulang Stock), 08103 (Posting), 08104 (Proses Tutup Buku) di DBMENUWEB. Hanya mengisi kolom yang NULL. | Sudah dijalankan 2026-10-14 |

## Catatan

- Hak akses (DBFLMENUWEB) untuk kode 081xx sudah ada di DBSMLBARU, jadi tidak ditambah.
- 08101 Hitung Ulang HPP sengaja tidak diisi: newProgramAJC memberi href `hitungulanghpp`, tapi route dan halamannya tidak ada (404).
- SP yang dipakai Utilitas sudah ada di DBSMLBARU. `SP_ProsesKursBank` (Tutup Buku > Selisih Kurs) tidak disalin dari DBSMLNEW, karena versi DBSMLNEW berisi baris uji `select @bulan=6,@tahun=2026` yang menimpa periode yang dipilih user.
