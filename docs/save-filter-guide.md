# Filter laporan tersimpan per user (newSML)

Pilihan filter laporan (Otorisasi, Order By, Mode, Jenis, Divisi, Perkiraan, picker Customer/Supplier/Gudang/Merk, dll.) diingat **per user** di tabel `DBUSERFILTER` (SQL Server). Saat laporan dibuka lagi, termasuk dari komputer lain, filter kembali ke pilihan terakhir user.

- **Tidak disimpan:** tanggal, periode (bulan/tahun), kotak pencarian, dan form "Filter Data" (pilihan baris dari hasil laporan). Semuanya bergantung pada data/tanggal dan cepat basi.
- **Tabel:** `docs/update-db-DBSMLBARU/umum/38_tabel_dbuserfilter.sql` (sudah dijalankan di DBSMLBARU 2026-11-20). Penjelasan lengkap ada di bagian 38 pada `docs/update-db-DBSMLBARU/README.md`.
- **Asal fitur:** newProgramAJC `docs/save-filter-guide.md`. newSML memakai controller, route, dan partial yang sama, dengan helper JS yang diperluas.

## Bagian-bagian

| File | Peran |
|---|---|
| `app/Http/Controllers/UserFilterController.php` | `GET /userfilter_get`, `POST /userfilter_save` (auth). Username diambil dari `Auth::user()`, bukan dari request. |
| `public/js/user-filter.js` | Helper `UserFilter` (API di bawah). |
| `public/js/report-table.js` | Switcher "Tampilan"/"Order By" (`ReportTable.init({ views })`) otomatis disimpan dan dipulihkan untuk **semua** halaman, dengan kunci `rtview<id tabel>`. Matikan per halaman dengan `views.persist: false`. |
| `resources/views/partials/user-filter.blade.php` | Config + memuat helper. Di-include oleh `newmaster`, `report/newmaster2x` (induk `masterreport2`, `masterreport4`, `masterreportGudang`), dan `report/mastercore` (induk `masterreport`). |

## API helper

```js
// 1) Modal berisi select.rt-native (API lama, 55 halaman dari newProgramAJC)
UserFilter.restore('#modalFilter', function (v) { /* sinkronkan global halaman */ });
UserFilter.save('#modalFilter');                 // di tombol Terapkan

// 2) Filter lain: dipulihkan lewat setter halaman, simpan otomatis atau manual
UserFilter.track('toolbar', {
  otorisasi: UserFilter.menu('setOtorisasi', '#dropdownOtorisasi', function () { return globalOtorisasi; }),
  customer:  UserFilter.input('#inputCustomer'),
  divisi:    { get: fn, set: fn(v), valid: fn(v), wrap: 'setDivisi' }
});
UserFilter.commit('toolbar');                    // simpan manual (mis. di Terapkan)
```

- `track()` dipanggil **sekali** di `$(document).ready`, setelah default halaman dipasang dan **sebelum** laporan dimuat / `ReportTable.init`. GET-nya sinkron.
- Nilai tersimpan dipasang lewat `set(v)`, seperti user memilih, hanya kalau `valid(v)`. Nilai yang sudah tidak ada di menu/opsi dilewati, sehingga default halaman tetap dipakai.
- `wrap: 'namaFungsi'` membungkus fungsi global itu: setiap pemanggilan (klik user) ikut menyimpan, digabung jadi 1 request per 300 ms. Selama restore tidak ada penyimpanan balik.
- `UserFilter.input(sel)` menyimpan saat event `change`. Untuk hidden input yang diisi picker, simpan di Terapkan dengan `commit()`.
- Kalau tabel/server error, hanya muncul `console.warn` dan halaman tetap jalan dengan default.
- Kunci per halaman: `HREF` = path URL, `FILTERKEY` = `'toolbar'`, `'modalFilter'`, `'modalPick'` (picker di 21 halaman lama, dipisah supaya tidak tertimpa `save('#modalFilter')`), atau `rtview...`.

## Status halaman report (2026-11-20)

| Kelompok | Halaman | Yang disimpan |
|---|---|---|
| Modal `select.rt-native` (dari newProgramAJC) | 55 | dropdown modal (Valas `modalReportMode` tetap `data-nosave`) |
| ↳ ditambah picker / Jenis (`modalPick`) | 21 dari 55 | Supplier awal–akhir, Customer, Group, PIC, Kategori, Sub Kategori, Merk, Gudang, Grup, Lokasi, Jenis (`inputMode`) |
| Toolbar pola lama (`setReportMode`/`setOtorisasi`/`setOrderBy`/`setAgen`) | 24 | Mode, Otorisasi, Order By, Agen; Invoice & POS: + Customer/Group/PIC/Kategori/Sub Kategori/Merk; Jurnal Computer: + Divisi |
| Jurnal accounting (dropdown Divisi) | 7 | Divisi; Penerimaan Kas: + Jurnal |
| Laporan Arus | 1 | Perkiraan |
| Hutang Outstanding Nota | 1 | Mode IDR/$, Valas, Perkiraan, Supplier awal–akhir, Order |
| Modal picker baru (`PICK_FIELDS`) | 6 | Costing/Sub Costing, Supplier/Lokasi, Gudang/Grup/Kategori/Sub Kategori/Merk/No Satuan |
| Mutasi Stock per Merk / Harian | 2 | Tampilan, Gudang, Merk / Gudang, No Satuan |
| Neraca Lajur, Trial Balance | 2 | filter jenis akun |
| Penerimaan Transfer, Transfer ke Cabang Blm Diterima | 2 | Mode + Otorisasi / Jenis |
| Switcher Tampilan/Order By (`report-table.js`) | 33 (termasuk Close PO, OSP, Penerimaan Gudang, PG Outstanding PO, Ubah Kemasan, Closing Transfer Cabang) | pilihan switcher |

**Tidak ada filter untuk disimpan** (hanya tanggal/periode/cari): Posisi Bank, SKB, Gudang Out Konsinyasi, Histori Penyerahan Sample, Eval SO Lunas, Outstanding UM2, Outstanding UM Beli, Sales Analisa.

**Sengaja tidak disimpan:** panel "Data Pilih" di Surat Jalan (tombol `buttonSelect()` tidak didefinisikan dan nilainya tidak dikirim ke server), Otorisasi di Ubah Kemasan (konstan `"2"`, tanpa kontrol), dan Mode Report di PG Outstanding PO (tidak berfungsi).

**Dilewati:** file report yang tidak punya route/controller (backup, `reportTes*`, dll.; daftar ada di newProgramAJC `docs/file_list/report-files-list.md` lampiran C).

## Halaman transaksi Purchasing / Marketing / Accounting (2026-11-20)

Layout `newmasterTest` sekarang meng-include `partials.user-filter`. Setiap modal filter di halaman-halaman ini mengingat pilihan terakhir user (Status, Otorisasi, Jenis, Penagih, Perkiraan, Prioritas, Transaksi, Tipe Bayar). Tanggal/periode, kotak cari, dan jumlah baris per halaman tidak disimpan. Switcher Tampilan/Order By ikut tersimpan lewat `report-table.js`.

Ada tiga pola:

| Pola | Cara kerja | Restore | Simpan |
|---|---|---|---|
| **F1**: global `xxFilterY` + `xxTerapkanFilter()`/`xxResetFilter()` yang langsung berlaku | modal disinkronkan dari global saat `show.bs.modal` | `UserFilter.restore('#modal', cb)` di level atas script, tepat setelah `xxResetFilter`, sehingga jalan saat script dibaca, sebelum `loadAll()` pertama. `cb` mengisi global + badge. | `UserFilter.save` di Terapkan **dan** Reset |
| **F1 apply**: `applyModalFilter()` mengisi global; Reset hanya mengubah isian | sama | setelah `applyModalFilter` | `UserFilter.save` hanya di Apply |
| **F2**: nilai filter ada di DOM select; Terapkan = `onclick="buttonFilterX(); …hide"` | `loadAll` membaca select langsung | setelah `buttonFilterX` (cb: badge / state halaman) | `UserFilter.save('#modal')` di awal onclick Terapkan |

| Modul | Halaman (modal) |
|---|---|
| Purchasing (F1) | invoicepembelian (`modalFilterIpb`), invoicereturbeli (`modalFilterIRB`), newpobeliacc (`modalFilterPBA`), pembelianpermintaanagen (`modalFilterPR`, Jenis tidak dipulihkan kalau URL membawa `?jenis=`), pembelianpermintaandebetnote (`modalFilterDN`), pembelianpermintaannonagen, pembelianpermintaannonstock (`modalFilterPR`), perintahreturbeli (`modalFilterPRB`), purchaseOrder (`modalFilterPO`, Jenis tidak dipulihkan kalau ada `?jenis=`; `modalFilterOutPR`), purchaseOrderNonStock (`modalFilterPONS`), uangmukabeli (`modalFilterUMB`) |
| Marketing (F2) | invoicejasa, invoicepenjualan, kreditnote, notareturpenjualan, penawaranso, performance, perintahreturjual, perintahreturjualminus, returpenjualangudang, retursuratjalan, so, uangmukajual, suratjalan (`modalFilterTabel1`/`Tabel2` mengisi `sjPageState[..].prioritas`, plus `modalFilterSPB`) |
| Accounting | F1: memorialkoreksi, pelunasanpiutangdpp, pengajuandpp, penerimaandpp (`modalFilterPdpp`, `modalFilterOut`). F1 apply: pengajuandph, pengajuandphtunai, cetakpengajuandph (`modalFilterPenerimaan`), kas & bank (kodenya di `public/js/kas.js` / `bank.js`, modal di `kas/_list` / `bank/_list`). F2: girodibuka, giroditerima. bonsementara: `UserFilter.track('perkiraan', …)` di `ready()` setelah default akun pertama dipasang; satu Perkiraan untuk kedua tab, disimpan saat Terapkan di salah satu modal. |

Catatan: filter `pons*` di `purchasing/partials/poNonStockScript` (yang di-include `purchaseOrder`) tidak punya modal di halaman PO, jadi tidak ada yang perlu disimpan di sana. Modalnya hanya ada di `purchaseOrderNonStock`, dan halaman itu sudah ditangani.

> **Penting saat restore dipanggil di level atas script:** script itu harus berada di `@section('js')`, yang dicetak setelah `user-filter.js`. Kalau berada di `@section('content')`, `UserFilter` belum ada dan sisa script halaman akan berhenti.

## Menambah ke halaman baru

1. Pastikan layout-nya meng-include `partials.user-filter` (lihat tabel di atas).
2. Di `$(document).ready`, setelah default dipasang dan sebelum laporan dimuat: `UserFilter.track('toolbar', {...})`. Pakai setter halaman sendiri di `set` dan `wrap`, supaya label/centang/header ikut diperbarui.
3. Kalau filternya baru berlaku setelah Terapkan, panggil `UserFilter.commit('<key>')` di Terapkan.
4. Jangan masukkan tanggal/periode.
5. Tes manual: pilih nilai non-default → reload → nilai kembali; cek `SELECT * FROM DBUSERFILTER WHERE USERNAME='<user>'`; console tidak boleh ada `UserFilter is not defined`.
