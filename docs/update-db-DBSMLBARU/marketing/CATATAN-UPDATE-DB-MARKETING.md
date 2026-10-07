# Catatan Update Database DBSMLBARU — Modul Marketing

Catatan ini mendaftar perubahan database DBSMLBARU dari pekerjaan menu **marketing** newSML. Struktur folder dijelaskan di `../README.md`.

## Urutan menjalankan di server

| No | File | Isi | Wajib? |
|---|---|---|---|
| 7 | `07_hak_akses_sa_verifikasibarang.sql` | Hak akses penuh user SA ke menu Verifikasi Barang | Ya, kalau SA perlu tombol Batal Verifikasi |
| 8 | `08_index_dbspbdet_noso.sql` | Index `dbSPBDet (NoSo, UrutSo)` untuk mempercepat menu Sales Order | Disarankan (tanpa index menu SO tetap jalan, tapi lebih lambat) |

**Sebelum mulai:** backup database DBSMLBARU di server.

---

## 07 — Hak akses SA menu Verifikasi Barang (6 Oktober 2026)

- Menu Verifikasi Barang (`verifikasibarang`, kode menu web `04013`) diaktifkan di newSML dari programajcSML. Tidak ada perubahan struktur database untuk menu ini.
- User minta **hanya user SA** diberi akses penuh ke menu ini. Sebelumnya SA sudah punya semua hak kecuali `IsBatal`, jadi yang benar-benar berubah hanya `IsBatal` 0 → 1. User lain tidak diubah.
- Sudah dijalankan langsung di DBSMLBARU (server 36.88.190.218) pada 6 Oktober 2026.

---

## 08 — Index dbSPBDet untuk menu Sales Order (7 Oktober 2026)

- Menu Sales Order (`so`) menampilkan kolom **Status** (Belum proses / Proses Sebagian / Full supply / Terclose) dan punya filter status. Keduanya menjumlah Qty SPB per baris SO dari `dbSPBDet`.
- `dbSPBDet` (±137 ribu baris) sebelumnya tidak punya index di `NoSo`, sehingga pencarian SPB untuk satu SO memindai seluruh tabel. User minta menu SO dipercepat ("boleh cepetin lagi").
- Ditambah index `IX_SPB_NoSo_UrutSo` pada `(NoSo, UrutSo)` dengan `INCLUDE (QNT)`. Hanya **menambah** index, tidak mengubah tabel/data. Skrip aman dijalankan ulang.
- Sudah dijalankan langsung di DBSMLBARU (server 36.88.190.218) pada 7 Oktober 2026.
