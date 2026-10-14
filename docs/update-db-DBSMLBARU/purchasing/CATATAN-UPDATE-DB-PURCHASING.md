# Catatan Update Database DBSMLBARU — Modul Purchasing

Catatan ini mendaftar semua perubahan database DBSMLBARU dari upgrade menu **purchasing** newSML, supaya update ke server tidak perlu dicari ulang satu per satu. Perubahan untuk modul lain dicatat di folder modulnya sendiri, misalnya `../marketing/`. Lihat `../README.md` untuk struktur foldernya.

**Menu yang di-upgrade:**
1. Permintaan Pembelian (PR) Agen (2 Oktober 2026)
2. Permintaan Pembelian (PR) Non-Agen (2 Oktober 2026)
3. Permintaan Pembelian (PR) Non-Stock (2 Oktober 2026)
4. Closing PR (2 Oktober 2026)
5. Closing PO (2 Oktober 2026)
6. Purchase Order (3 Oktober 2026)
7. Purchase Order Non-Stock (3 Oktober 2026, tanpa perubahan database)
8. Penerimaan Stock (`newpo`) dan Penerimaan Non Stock (`newpojasa`) (5 Oktober 2026, tanpa perubahan database)
9. Invoice Pembelian (`invoicepembelian`) (5 Oktober 2026, tanpa perubahan database)
10. Perintah Retur Beli (`perintahreturbeli`) (5 Oktober 2026)
11. Retur Pembelian Gudang (`returbeli`) dan Invoice Retur Beli (`invoicereturbeli`) (5 Oktober 2026, tanpa perubahan database)
12. Penerimaan ACC gabungan (`newpobeliacc`), gabungan dari `newpobeliacc`, `newpobeliacctunai`, `newpobeliaccbiayatunai`, dan `newpobeliaccbiayakredit` (5 Oktober 2026, tanpa perubahan database). Kolom `fnc_masterbeli`/`fnc_Tampilbeli` di DBSMLBARU sudah sama dengan DBSMLNEW, dan menu web `0304 newpobeliacc` sudah ada di `02_menu_web.sql`.

**Script umum (bukan hanya purchasing):** `../umum/02_menu_web.sql` membuat menu web dan hak aksesnya untuk **semua** menu. Cukup dijalankan sekali. Kalau sudah dijalankan untuk modul lain, tidak perlu diulang.

**Sifat semua perubahan:** hanya menambah (kolom, tabel, dan baris baru). Tidak ada data atau kolom lama yang dihapus. Semua script aman dijalankan ulang.

---

## Urutan menjalankan di server

Buka di SSMS, lalu jalankan berurutan:

| No | File | Isi | Wajib? |
|---|---|---|---|
| 1 | `01_vwppl_dan_headertable.sql` | Tambah 2 kolom di VwPPL, buat tabel pengaturan kolom | Ya |
| 2 | `../umum/02_menu_web.sql` | Buat tabel menu web dan hak aksesnya | Ya. Tanpa ini sidebar kosong dan semua menu error 500 |
| 3 | `03_cek_hasil.sql` | Cek hasil. Isinya hanya SELECT | Disarankan |
| 4 | `04_vwoutporp_uang_muka_beli.sql` | Perbaiki rumus view VWOUTPORP (menu Uang Muka Beli) | Ya, kalau menu Uang Muka Beli dipakai |
| 5 | `05_purchase_order.sql` | Tambah 2 kolom di vwMasterPO, alias kolom tabel Outstanding PR | Ya. Tanpa ini tab Purchase Order error |
| 6 | `06_perintah_retur_beli.sql` | Buat view vwBrowsPrRBeli (kolom Status PRB) | Ya. Tanpa ini tabel Perintah Retur Beli error |

**Sebelum mulai:**
- **Backup database DBSMLBARU di server.**
- Jalankan dulu `EXEC sp_helptext 'dbo.VwPPL'` di server, lalu bandingkan hasilnya dengan definisi di file 01. Lihat poin 1 di bawah.
- Script memakai `USE DBSMLBARU`. Kalau nama database di server berbeda, ganti baris itu.

---

## Daftar perubahan

### 1. View `VwPPL`: ALTER, tambah 2 kolom
- **Dipakai oleh:** 3 menu PR, untuk badge Status (Outstanding/Selesai/Batal).
- **Kolom baru:**
  - `QNTBATAL`, diambil dari `DBPPLDET.Qntbatal`.
  - `qntpo`, yaitu total qty PO per item PR, dihitung dari `DBPODET`.
- **Join baru:** `left outer join (select noppl, urutppl, sum(qnt) qnt from DBPODET group by NoPPL, urutppl) C`.
- Kolom lama tetap utuh. Jumlah baris view tidak berubah (di dev 17.902 baris, sebelum dan sesudah).
- ⚠️ `ALTER VIEW` mengganti seluruh definisi view. Kalau `VwPPL` di server tidak sama dengan versi dev, misalnya pernah diubah orang lain, jangan langsung jalankan file 01. Cukup tambahkan 2 kolom dan join C di atas ke definisi milik server.

### 2. Tabel baru `DBHEADERTABLE`
- **Fungsi:** menyimpan pengaturan kolom tabel per user (kolom tampil/sembunyi dan urutan).
- **Dipakai oleh:** 3 menu PR dan Closing PR.
- Dibuat kosong. Isinya bertambah sendiri saat user mengatur kolom.

### 3. Tabel baru `DBHEADERTABLEALIAS`
- **Fungsi:** alias nama kolom per menu.
- **Isi awal:** 3 baris, untuk `pembelianpermintaanagen`, `pembelianpermintaannonagen`, dan `pembelianpermintaannonstock`.

### 4. Tabel `DBMENU`: tambah 2 kolom dan 2 baris
- **Kolom baru (nullable):** `icon varchar(50)`, `tableCount int`.
- Kolom baru diisi dari DBSMLNEW, hanya untuk menu yang kode dan href-nya sama. Menu yang isinya beda tetap versi DBSMLBARU.
- **Menu baru:** 2 menu yang ada di DBSMLNEW tapi belum ada di DBSMLBARU, yaitu `Set Header` dan `Retur Surat Jalan`. Di dev totalnya jadi 170, dari sebelumnya 168.
- Efek samping: trigger lama `Tri_ADD_DBMENU` otomatis menambah akses SA di `DBFLMENU` untuk menu baru. Di dev bertambah 2 baris. Ini perilaku trigger yang memang sudah ada.

### 5. Tabel baru `DBMENUWEB`
- **Fungsi:** daftar menu sidebar versi web, sama seperti newProgramAJC.
- **Isi:** disalin dari `DBSMLNEW.dbo.DBMENUWEB` (di dev 174 menu).

### 6. Tabel baru `DBMENUREPORTWEB`
- **Fungsi:** daftar menu laporan versi web.
- **Isi:** disalin dari `DBSMLNEW.dbo.DBMENUREPORTWEB` (di dev 125 menu).

### 7. Tabel baru `DBFLMENUWEB`
- **Fungsi:** hak akses user per menu web (akses, tambah, koreksi, hapus, cetak, export, otorisasi 1–5, batal).
- **Isi:** diturunkan dari `DBFLMENU` milik server itu sendiri, bukan disalin dari DBSMLNEW. Jadi hak akses user di server tetap sesuai data server.
  - Menu web dicocokkan ke menu SML lewat `href`. Kalau tidak ada yang cocok, lewat kode menu yang namanya sama.
  - Kalau satu href dipakai lebih dari satu menu, dipakai kode lama. Contohnya Retur Surat Jalan: 041015 (lama) dan 041016 (dari DBSMLNEW).
  - Menu induk (href `#` atau kosong) mendapat akses kalau user punya akses ke salah satu submenunya.
- Di dev: 15.486 baris, 4.923 di antaranya diberi akses.

### 8. Tabel baru `DBFLMENUREPORTWEB`
- **Fungsi:** hak akses user per menu laporan web.
- **Isi:** diturunkan dari `DBFLMENUREPORT` milik server dengan aturan yang sama (di dev 10.750 baris).

---

### 4. View `VWOUTPORP`: ALTER, perbaiki rumus (menu Uang Muka Beli)
- **Dipakai oleh:** hanya `UangMukaBeliController@spAdd`, untuk validasi "Total Uang Muka melebihi PO". Tidak ada view/SP lain yang memakainya.
- **Kolom tetap sama** (`NoBukti`, `TANGGAL`, `OUTPORP`). Hanya rumus OUTPORP untuk PPN tipe 2 (include) yang dibetulkan:
  - Bagian DPP sebelumnya memakai `Qnt` (qty PO penuh), padahal bagian PPN memakai `QntOS`. Akibatnya, PO yang sudah sebagian diterima nilainya kebesaran. Sekarang keduanya memakai `QntOS`.
  - Pembagi DPP sebelumnya ditulis mati `1.11`. Sekarang memakai `1 + NilaiPPN/100`, karena di dev ada 7.738 baris PO PPN include yang NilaiPPN-nya bukan 11.
- **Ikut diubah di kode (tanpa perubahan DB):** validasi uang muka di controller sekarang dibandingkan per rupiah penuh, jadi selisih sen akibat pembulatan tidak lagi memunculkan "melebihi PO".

### 5. Menu Purchase Order (file `05_purchase_order.sql`, 3 Oktober 2026)
- **View `vwMasterPO`: ALTER, tambah 2 kolom di akhir.**
  - `qnt` = `SUM(DBPODET.QNT - DBPODET.QntBatal)` per PO.
  - `qntbeli` = total qty yang sudah diterima, dari `DBBELIDET` per `NoPO`.
  - **Join baru:** `left outer join (select nopo, sum(qnt) qntbeli from DBBELIDET group by nopo) D on D.NoPO = A.NOBUKTI`.
  - **Dipakai oleh:** badge status penerimaan PO (Batal/Outstanding/Sebagian/Selesai) di tab Purchase Order.
  - Kolom lama tetap utuh. Jumlah baris tidak berubah (di dev 36.916 baris, sebelum dan sesudah). Definisinya disamakan dengan DBSMLNEW.
  - Pemakai lain view ini di DBSMLBARU hanya SP `CetakRevisiPO`. SP itu hanya mengambil `TotDiskon`, `TotDPP`, `TotPPN`, dan `TotNet`, jadi tidak terpengaruh.
  - ⚠️ `ALTER VIEW` mengganti seluruh definisi view. Jalankan dulu `EXEC sp_helptext 'dbo.vwMasterPO'` di server. Kalau isinya beda dengan versi dev, cukup tambahkan 2 kolom dan join D di atas ke definisi milik server (`qntbeli` juga ikut ke `GROUP BY`).
- **Tabel `DBHEADERTABLEALIAS`: tambah 1 baris** untuk `href = 'purchaseorder'`, `urut = 1`, berisi alias kolom tabel Outstanding PR (`SisaPPL` tampil sebagai "SisaPR"). Baris hanya ditambahkan kalau belum ada.

### 6. Menu Perintah Retur Beli (file `06_perintah_retur_beli.sql`, 5 Oktober 2026)
- **View baru `vwBrowsPrRBeli`**, disalin dari DBSMLNEW. Dibuat hanya kalau belum ada.
  - `status` = `Belum` / `Sebagian` / `Sudah` per PRB, dari qty `DBPRRBELIDET` dibanding qty yang sudah diretur di `DBRBELIDET` (lewat `NOPRB`).
  - **Dipakai oleh:** kolom Status di tabel Perintah Retur Beli (`PerintahReturBeliController@loadAll`). Tanpa view ini tabel PRB error.
  - Hanya membaca `DBPRRBELI`, `DBPRRBELIDET`, dan `DBRBELIDET`. Tidak mengubah data.
- Menu **Retur Pembelian Gudang** (`returbeli`) dan **Invoice Retur Beli** (`invoicereturbeli`) yang di-upgrade bersamaan tidak butuh perubahan DB. Pengaturan kolom tabelnya memakai `DBHEADERTABLE` dari file 01.

---

## Yang TIDAK mengubah database

Bagian ini tidak perlu dijalankan apa pun di server:
- **Closing PR dan Closing PO:** hanya kode program. View `vwOutPPL` tetap versi DBSMLBARU, termasuk batas `Tanggal >= 06/01/2026`. Percepatan query Closing PR memakai hint `OPTION (HASH JOIN, MERGE JOIN)` di kode, bukan di database.
- **Tanggal form Add 3 menu PR:** sekarang diambil dari jam server SQL (`select getdate()`) lewat route `/gettanggalserver` di `GlobalController`. Ini hanya query baca, tidak ada objek database baru.
- Validasi tanggal filter (error tahun `0202`) dan perbaikan namespace `App\Models`: hanya kode program.
- **Purchase Order, selain file 05:** route baru `/polistnosotambahso` (daftar SO per barang untuk Tambah SO) dan contoh cadangan untuk pengaturan kolom tabel PO di `HeaderTableController`. Keduanya hanya query baca.
- **Purchase Order Non-Stock:** hanya kode program. Butuh kolom `qnt`/`qntbeli` di `vwMasterPO` dari file 05 (badge status), tidak ada objek baru lain.
- **Percepatan buka menu PO dan PO Non-Stock:** query ke `vwOutPPL` (tab Outstanding PR, daftar barang PR, contoh kolom tabel PO) diberi hint `OPTION (HASH JOIN, MERGE JOIN)` di kode, sama seperti Closing PR. Hasilnya identik, waktunya turun dari ±4–5 detik menjadi <1,5 detik per query. Tidak ada perubahan di database.
- **Debet Note** (`pembelianpermintaandebetnote`): hanya kode program. Hak akses sekarang dibaca dari `DBMENUWEB`/`DBFLMENUWEB` (menu web kode `0307`), jadi butuh file `../umum/02_menu_web.sql` sudah dijalankan. Pengaturan kolom tabel memakai `DBHEADERTABLE` dari file 01. Tidak ada tabel, view, atau SP baru.
- **Penerimaan Stock dan Penerimaan Non Stock** (`newpo`, `newpojasa`): hanya kode program. Butuh `../umum/02_menu_web.sql` (hak akses lewat `DBMENUWEB`/`DBFLMENUWEB`) dan `DBHEADERTABLE` dari file 01 (pengaturan kolom tabel). Tidak ada tabel, view, atau SP baru.
  - SP `sp_BeliGudang` **tidak diubah**. SP itu hanya punya cabang I/U/D, sehingga simpan Faktur & Keterangan dari modal Edit (`choice = 'F'`) sebelumnya tidak pernah tersimpan. Sekarang controller menjalankan `UPDATE dbBeli SET FAKTURSUPP, KETERANGAN` langsung untuk NoBukti itu.
  - Data lama tidak disentuh. Di dev ada 111 header `dbBeli` tanpa detail (LPB kosong yang tampil bertanggal 1970/01/01). Form Add sekarang menolak item yang dicentang dengan qty 0, supaya header kosong baru tidak terbentuk. Header yang sudah ada dibiarkan.
- **Invoice Pembelian** (`invoicepembelian`): hanya kode program. Butuh `../umum/02_menu_web.sql` (hak akses lewat `DBMENUWEB`/`DBFLMENUWEB`, menu web kode `0305`) dan `DBHEADERTABLE` dari file 01 (pengaturan kolom tabel). Tidak ada tabel, view, atau SP baru.
  - Route `getAksesInvoiceBeli` dulu memakai model `DBFLMENU` dengan kode `03007` dan selalu error 500 (class tidak ditemukan). Sekarang hak aksesnya dibaca dari menu web. Di dev, daftar user yang punya akses di `DBFLMENU` 03007 dan `DBFLMENUWEB` 0305 sama persis (26 user).
  - Batal otorisasi sekarang wajib diisi keterangan dan dicatat lewat `LoggingData` (`btloto`), memakai tabel log yang sudah ada.

---

## Kalau DBSMLNEW tidak ada di server

File `../umum/02_menu_web.sql` menyalin data dari database **DBSMLNEW** di server yang sama. Kalau server tujuan tidak punya DBSMLNEW, script berhenti di awal dengan pesan error dan tidak mengubah apa pun.

Jalan keluarnya, pilih salah satu:
1. Salin dulu tabel `DBMENU`, `DBMENUWEB`, dan `DBMENUREPORTWEB` dari DBSMLNEW ke server tujuan dalam database sementara bernama `DBSMLNEW`. Caranya lewat SSMS: klik kanan database, lalu Tasks > Generate Scripts > Types of data: *Schema and data*. Setelah itu jalankan `../umum/02_menu_web.sql`, lalu hapus database sementara itu.
2. Atau minta dibuatkan versi `02` yang datanya sudah ditulis langsung di dalam script, sehingga tidak perlu DBSMLNEW.

---

## Setelah update

- Jalankan `03_cek_hasil.sql`. Semua kolom dan tabel harus ada, dan jumlah barisnya tidak boleh 0.
- Ada 5 menu web dan 5 menu laporan yang tidak punya pasangan di menu SML lama, misalnya Analisa Laba Kotor dan laporan Closing Opname. Belum ada user yang punya akses ke menu-menu itu. Kalau dibutuhkan, beri akses lewat **Berkas > Set Pemakai**.
- Login ke newSML, cek sidebar terisi, lalu buka kelima menu di atas.

---

## 07 — Debet Note: akun posting BD (14 Oktober 2026)

- Jurnal otomatis Debet Note (`vwPostJurnalOto`, Jenis `DN`) = Debet Hutang Usaha (`DBPOSTHUTPIUT` HT urut 2) / Lawan akun `DBPOSTHUTPIUT` Kode `'BD'`. Baris `BD` tidak ada (di DBSMLBARU maupun DBSMLNEW), sehingga akun lawan jurnal DN kosong.
- Ditambah `BD` = **51300 Discount/Potongan Pembayaran**. Di data lama, akun ini yang dikreditkan untuk credit memo atau potongan dari supplier (146 jurnal, 2019–2026, mis. credit memo Kimberly Clark). 81300 Discount / Bonus Pembelian hanya pernah dipakai sekali. Akunnya dipilih dari data yang ada, atas permintaan user (14 Oktober 2026).
- Pasangannya di sisi penjualan adalah Kode `BK` (Kredit Note) = 41200, lihat `../marketing/11_kreditnote_perkhp_dan_akun_bk.sql`.
- Detail Debet Note tidak punya kolom `perkHP`, jadi tidak ada masalah simpan item seperti di Kredit Note.
- Hanya **menambah** satu baris konfigurasi; aman dijalankan ulang.
- Sudah dijalankan langsung di DBSMLBARU (server 36.88.190.218) pada 14 Oktober 2026 atas izin user.
