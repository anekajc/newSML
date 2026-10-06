# Catatan Update Database DBSMLBARU — Modul Marketing

Catatan ini mendaftar perubahan database DBSMLBARU dari pekerjaan menu **marketing** newSML. Struktur folder dijelaskan di `../README.md`.

## Urutan menjalankan di server

| No | File | Isi | Wajib? |
|---|---|---|---|
| 7 | `07_hak_akses_sa_verifikasibarang.sql` | Hak akses penuh user SA ke menu Verifikasi Barang | Ya, kalau SA perlu tombol Batal Verifikasi |

**Sebelum mulai:** backup database DBSMLBARU di server.

---

## 07 — Hak akses SA menu Verifikasi Barang (6 Oktober 2026)

- Menu Verifikasi Barang (`verifikasibarang`, kode menu web `04013`) diaktifkan di newSML dari programajcSML. Tidak ada perubahan struktur database untuk menu ini.
- User minta **hanya user SA** diberi akses penuh ke menu ini. Sebelumnya SA sudah punya semua hak kecuali `IsBatal`, jadi yang benar-benar berubah hanya `IsBatal` 0 → 1. User lain tidak diubah.
- Sudah dijalankan langsung di DBSMLBARU (server 36.88.190.218) pada 6 Oktober 2026.
