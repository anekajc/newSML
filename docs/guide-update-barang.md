# Guide — Kode Barang Search "Resolve-or-Pick" (Enter / Tombol Plus)

Sub-guide pola pemilih barang. Beda fokus dengan
**[new-cust-supp-modal-guide.md](new-cust-supp-modal-guide.md)**: guide itu soal tampilan/skin
modal picker (`.rt-picker-v2`, klik-baris tanpa kolom Actions). Guide ini soal **apa yang terjadi
sebelum modal itu dibuka** — resolve dulu ke server, isi form langsung kalau kodenya persis cocok,
baru buka modal (dengan pencarian sudah terisi) kalau tidak.

**Referensi implementasi hidup:** `resources/views/gudang/permintaanpemakaian.blade.php`
(form "Add Item", modal `#formAddListItem`) + `app/Http/Controllers/Gudang/PermintaanPemakaianController.php`.

---

## 1. Masalah yang diselesaikan pola ini

Pola lama (search-only, lihat `marketing/invoicejasa.blade.php` fungsi `onKeyPressBarang` versi
lama, atau `marketing/penawaranso.blade.php` `performSearch()`): Enter selalu memicu pencarian LIKE
ke server, lalu:
- kalau hasilnya **persis satu baris** → auto-isi form,
- kalau lebih dari satu → buka modal picker, **tanpa** kotak pencarian modal ikut terisi (user harus
  ketik ulang).

Masalahnya: kode barang sering jadi *prefix* dari kode lain (mis. `A01` vs `A011`), jadi pencarian
LIKE `%A01%` mengembalikan lebih dari satu baris walau user sudah mengetik kode yang **benar dan
lengkap**. User yang hafal kode tetap terlempar ke modal.

Pola baru membedakan dua hal:
1. **Match persis (`KODEBRG` sama, case-insensitive, trim)** → langsung isi form, modal tidak
   pernah terbuka.
2. **Tidak match persis** (kode sebagian, fragmen nama, atau kotak kosong) → modal terbuka, dan
   teks yang tadi diketik user didorong ke kotak pencarian DataTables modal itu supaya user
   melanjutkan dari situ, bukan mengetik ulang dari nol.

Berlaku untuk **dua trigger sekaligus** — tombol Enter di kotak Kode Barang, dan tombol plus (➕) di
sebelahnya — lewat satu fungsi masuk yang sama (`resolveBarang`), supaya keduanya tidak pernah
diam-diam berbeda perilaku.

---

## 2. Peta fungsi (JS, di `@section('js')`)

| Fungsi | Peran |
|---|---|
| `resolveBarang(term)` | **Titik masuk tunggal.** Dipanggil oleh Enter dan tombol plus. Menentukan: isi form langsung, atau buka picker. |
| `applyBarangToForm(item)` | Isi Kode/Nama/Satuan dari satu objek barang, lalu pindah fokus+select ke input Qty. Dipakai oleh `resolveBarang` (match persis) **dan** oleh klik-baris di modal picker — satu tempat, bukan dua salinan logic. |
| `openBarangPicker(term)` | Buka modal `#formAddListItem` memakai katalog lengkap (`barangCacheAll`, di-cache), dengan `term` sudah didorong ke kotak pencarian DataTables. |
| `initBarangTable(list, searchTerm)` | Bangun/rebuild DataTables picker dari `list`, lalu terapkan `searchTerm` ke kotak pencariannya. Menangani kasus modal belum `display:block` lewat `barangTablePending` / `barangSearchPending` (lihat §4). |
| `fetchBarangList(search, callback)` | AJAX GET ke `permintaanpemakaianlistbarang`, set `listBarang` global, lalu panggil `callback(res)`. Dipakai `openBarangPicker` untuk memuat katalog penuh (`search: ''`). |
| `buttonAddAddInsertItem(index)` | Handler klik-baris di modal picker (`onclick` ditempel oleh `createdRow` DataTables). Tutup modal, lalu `applyBarangToForm(listBarang[index])`. |

Variabel state terkait:

| Variabel | Arti |
|---|---|
| `listBarang` | Array barang yang sedang ditampilkan di picker **saat ini**. `buttonAddAddInsertItem(index)` mengambil dari sini — **harus** selalu array yang sama persis yang dioper ke `initBarangTable()` terakhir (lihat §5, gotcha #1). |
| `barangCacheAll` | Katalog penuh (tanpa filter), di-cache sekali per page-load supaya tombol plus tidak selalu roundtrip ke server. |
| `barangTablePending` / `barangSearchPending` | Antrean data + search term saat `initBarangTable()` dipanggil sebelum modal `display:block` (lihat §4). |
| `barangLookupBusy` | Guard sederhana supaya `resolveBarang()` tidak menumpuk beberapa request AJAX kalau Enter ditekan berkali-kali sebelum request pertama selesai. |
| `itemAddObj` | Objek barang yang **sedang terisi di form** — dipakai `submitAddAdd()` untuk validasi ("Barang tidak sesuai dengan pilihan") dan hitung `ISI2`/`ISI3`. Diisi oleh `applyBarangToForm()`. |

---

## 3. Alur lengkap

```
User ketik di #AddAddKodeBrg
        │
        ├─ tekan Enter ──────────┐
        │                        ▼
        └─ klik tombol plus ──▶ resolveBarang(term)
                                   │
                          term kosong? ──yes──▶ openBarangPicker('')
                                   │no
                          barangLookupBusy? ──yes──▶ (abaikan, request lain masih jalan)
                                   │no
                     barangCacheAll sudah ada? ──yes──▶ cari KODEBRG persis di cache (lokal, tanpa AJAX)
                                   │no                          │
                                   ▼                     ketemu?──yes──▶ applyBarangToForm(hit)
                     AJAX GET permintaanpemakaianlistbarang            │no
                     (search = term)                                  ▼
                                   │                          openBarangPicker(term)
                          cari KODEBRG persis di hasil
                                   │
                    ketemu? ──yes──▶ applyBarangToForm(hit)
                          │no
                          ▼
                 openBarangPicker(term)
                          │
                          ▼
        modal #formAddListItem terbuka, DataTables terisi
        barangCacheAll (full), search box = term
                          │
              user klik salah satu baris
                          │
                          ▼
        buttonAddAddInsertItem(index)
                          │
                          ▼
        closeListItemAdd() + applyBarangToForm(listBarang[index])
                          │
                          ▼
        Kode/Nama/Satuan terisi, fokus pindah ke Qty
```

Match persis dicek dengan (di dalam `resolveBarang`):

```js
let findExact = function(list) {
    let needle = term.toLowerCase()
    return list.find(b => String(b.KODEBRG || '').trim().toLowerCase() === needle)
}
```

`term` sendiri sudah di-`trim()` di awal `resolveBarang()`. Sengaja **bukan** "hasil LIKE tinggal
satu baris" (pola lama) — kode yang jadi prefix kode lain tidak boleh auto-pilih baris yang salah.

---

## 4. Kenapa `initBarangTable` butuh parameter `searchTerm` + antrean pending

DataTables menghitung lebar kolom dari ukuran container saat `.DataTable({...})` dipanggil. Bootstrap
4 baru memasang `display:block` ke modal **setelah** animasi fade selesai — kalau init dipanggil
tepat sesudah `.modal('show')` (baris pertama di `openBarangPicker`), modal masih tersembunyi dan
lebar kolom terukur di container 0px.

Solusinya (sudah ada sebelum fitur ini, dipertahankan): `initBarangTable()` cek dulu
`$('#formAddListItem').is(':visible')`.
- **Belum visible** → simpan `list` ke `barangTablePending` dan `searchTerm` ke
  `barangSearchPending`, lalu `return` tanpa membangun tabel.
- **Sudah visible** → bangun DataTables seperti biasa, lalu `barangTableDT.search(searchTerm || '').draw()`.

Event `shown.bs.modal` (didaftarkan sekali di `$(document).ready`) adalah satu-satunya titik yang
dijamin modal sudah `display:block`. Di situ:
- kalau ada `barangTablePending` → jalankan ulang `initBarangTable(barangTablePending, barangSearchPending)` dengan data+term yang sempat diantre,
- kalau tabel sudah pernah dibangun sebelumnya (buka-tutup-buka modal) → cukup `columns.adjust()` +
  terapkan ulang `barangSearchPending` ke kotak pencarian yang sudah ada.

**Kalau menambahkan search-term ke picker lain**, pola dua-variabel (`XxxPending` + `XxxSearchPending`) ini
yang harus ditiru — jangan cuma push search term tanpa menunggu `shown.bs.modal`, karena kotak
pencarian DataTables belum tentu ada saat modal masih hidden.

---

## 5. Controller & route yang terlibat

| Route | Method controller | File |
|---|---|---|
| `GET /permintaanpemakaianlistbarang` | `PermintaanPemakaianController@listBarang` | `app/Http/Controllers/Gudang/PermintaanPemakaianController.php` |

```php
public function listBarang(Request $req)
{
    $search = trim($req->input('search'));
    $query  = "select KODEBRG,NAMABRG,SAT1,SAT2,SAT3, ISI1,ISI2,ISI3 from DBBARANG
           where isnull(ISAKTIF,0)=1 and KODEGRP = 'BJ'";
    $params = [];

    if ($search !== '') {
        $query .= " and ((KodeBrg like :filterKode) or (NamaBrg like :filterNama))";
        $params = ["filterKode" => "%$search%", "filterNama" => "%$search%"];
    }

    $query .= " order by KODEBRG";

    return DB::connection('SML')->select($query, $params);
}
```

Poin penting:
- **Satu endpoint dipakai untuk dua hal**: pencarian match-persis di `resolveBarang()` (`search =
  term`) dan pemuatan katalog penuh untuk `barangCacheAll` di `openBarangPicker()` (`search = ''`).
  Tidak perlu endpoint terpisah.
- `SAT1/SAT2/SAT3` + `ISI1/ISI2/ISI3` ikut dikembalikan karena dipakai langsung oleh
  `applyBarangToForm()` (bangun `<option>` Satuan) dan `submitAddAdd()` (hitung `qnt1` dari `ISI2`/`ISI3`
  kalau satuan bukan Sat 1). Kalau membuat endpoint serupa di halaman lain, field ini wajib ikut —
  tanpa itu `applyBarangToForm()`/`submitAddAdd()`-nya harus AJAX terpisah lagi.
- Filter `KODEGRP = 'BJ'` dan `ISAKTIF = 1` spesifik ke konteks Permintaan Pemakaian (hanya barang
  jadi yang aktif). Sesuaikan `where`-nya untuk kebutuhan modul lain.

---

## 6. Menerapkan pola ini di halaman lain

Langkah kalau mau memindahkan pola ini ke kotak Kode Barang lain (mis. halaman gudang/purchasing
lain yang masih pakai pola search-only lama):

1. **Pastikan endpoint list barang mengembalikan `KODEBRG`** dengan casing/nama field yang konsisten
   — pencocokan persis di `resolveBarang` bergantung pada nama field itu. Kalau field beda nama
   (mis. `KodeBrg` bukan `KODEBRG`), sesuaikan `findExact()`.
2. **Salin tiga fungsi**: `resolveBarang`, `openBarangPicker`, `applyBarangToForm` — ganti ID
   elemen (`#AddAddKodeBrg`, `#AddAddNamaBrg`, `#AddAddSatuan`, `#AddAddInputQty`,
   `#formAddListItem`) sesuai halaman tujuan.
3. **Tambahkan parameter `searchTerm` ke `initBarangTable()` halaman itu** (atau fungsi
   equivalennya) plus variabel `xxxSearchPending`, ikuti pola §4. Kalau modal picker halaman itu
   sudah mengikuti `new-cust-supp-modal-guide.md` (whole-row clickable, `.rt-picker-v2`), tinggal
   tambah baris `table.search(term).draw()` di titik yang sama dengan `resetBarangTableWidths()` /
   `columns.adjust()`.
4. **Pasang `onkeypress="onKeyPressBarang(event)"` di `<input>` dan `onclick="buttonAddListBarang()"`
   di tombol plus** — keduanya cukup satu baris yang memanggil `resolveBarang($('#idKodeBrg').val())`.
5. **Jangan** terapkan ini ke input Kode Barang yang `disabled` (mis. form Edit Item di
   `permintaanpemakaian` — barang baris yang sudah ada sengaja tidak bisa diganti, cuma
   Qty/Satuan-nya).

---

## 7. Gotcha

| Gejala | Penyebab / perbaikan |
|---|---|
| Klik baris di picker mengisi barang yang salah | `listBarang` sempat diisi ulang oleh proses lain (mis. `fetchBarangList` biasa) di antara `initBarangTable(list, ...)` dan klik user. `createdRow` menempelkan **index posisi**, bukan referensi objek — `listBarang` harus tetap menunjuk array yang sama persis yang dioper ke `initBarangTable()` terakhir. |
| Enter dengan kode lengkap tetap membuka modal | Field balikan server bukan `KODEBRG` (casing beda) sehingga `findExact()` selalu `undefined`, atau ada spasi tersembunyi di data — `findExact` sudah `.trim()` di kedua sisi, tapi cek field-nya dulu di response AJAX. |
| Kotak pencarian modal masih kosong walau sudah ketik sebagian kode | `initBarangTable` dipanggil tanpa argumen kedua (lupa oper `term`), atau modal belum `shown.bs.modal` saat `search()` dipanggil langsung tanpa lewat `barangSearchPending`. |
| Menekan Enter cepat berkali-kali memicu beberapa modal/alert bertumpuk | Lupa cek `barangLookupBusy` sebelum melempar AJAX baru di `resolveBarang()`. |
| Lebar kolom picker menyusut tiap dibuka ulang | Bukan masalah pola ini — sudah ditangani `resetBarangTableWidths()`, lihat komentar di fungsi itu (isu lama `_fnDestroy` DataTables 1.10.18). |

---

## 8. Checklist penerapan

- [ ] Endpoint list barang mengembalikan field kode yang namanya cocok dengan `findExact()`
- [ ] `resolveBarang` / `openBarangPicker` / `applyBarangToForm` disalin dan ID elemen disesuaikan
- [ ] `initBarangTable` (atau setara) menerima `searchTerm` + antrean `xxxSearchPending` mengikuti §4
- [ ] Input Kode Barang: `onkeypress="onKeyPressBarang(event)"`; tombol plus: `onclick="buttonAddListBarang()"`
- [ ] Modal picker tidak diterapkan ke input Kode Barang yang `disabled` (baris yang sudah tersimpan)
- [ ] Diuji: kode persis (dengan kode yang jadi prefix kode lain) langsung isi form tanpa modal terbuka
- [ ] Diuji: kode sebagian / fragmen nama / kotak kosong membuka modal dengan search box terisi sesuai
- [ ] Diuji: klik baris di modal mengisi baris yang benar-benar diklik (bukan baris tetangga)
