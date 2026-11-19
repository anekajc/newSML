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
12. Penerimaan ACC gabungan (`newpobeliacc`), gabungan dari `newpobeliacc`, `newpobeliacctunai`, `newpobeliaccbiayatunai`, dan `newpobeliaccbiayakredit` (5 Oktober 2026, tanpa perubahan database). Kolom `fnc_masterbeli`/`fnc_Tampilbeli` di DBSMLBARU sudah sama dengan DBSMLNEW, dan menu web `0304 newpobeliacc` sudah ada di `02_menu_web.sql`. **Tambahan 20 Oktober 2026:** kolom badge Hutang butuh `20_fnc_masterbeli_kolom_hutang.sql`, lihat bagian 20 di bawah.

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
| 7 | `07_debetnote_akun_bd.sql` | Akun posting `DBPOSTHUTPIUT` Kode `BD` (Debet Note) | Ya, kalau menu Debet Note dipakai |
| 20 | `20_fnc_masterbeli_kolom_hutang.sql` | Tambah kolom `hutang` (Lunas/Belum) di fungsi `fnc_MasterBeli` | Ya. Tanpa ini kolom badge Hutang di menu Penerimaan ACC kosong |
| 21 | `21_vwposthutpiut_jurnaloto_tanpa_qc.sql` | Posting hutang (`vwPostHutPiut`) & jurnal (`vwPostJurnalOto`) pembelian barang tidak menunggu QC, disamakan dengan DBSMLNEW | Ya. Tanpa ini pembelian yang di-ACC sebelum QC tidak masuk `DBHUTPIUT`/jurnal dan kolom Hutang tampil Lunas |
| 25 | `25_gabung_menu_pr.sql` | Kosongkan href menu lama PR Non Agen & PR Non Stock di `DBMENUWEB`/`DBMENU` (menu PR digabung) | Ya, bersamaan dengan update kode menu PR gabungan. Tanpa ini menu lama di sidebar mengarah ke halaman 404 |
| 26 | `26_sembunyikan_menu_pr_lama.sql` | Sembunyikan menu PR Non Agen & PR Non Stock dari sidebar (`DBMENUWEB.HeaderMenu` = NULL) | Ya, sesudah file 25 |
| 28 | `28_label_menu_pr.sql` | Label sidebar `DBMENUWEB` `030102` "PR (Keagenan)" diganti "PR" | Ya, sesudah file 26 (hanya label) |
| 31 | `31_dbpodet_perkiraan.sql` | Tambah kolom `DBPODET.PERKIRAAN` (perkiraan per item PO) + isi PO Non Stock lama dari COA Biaya barang | Ya, **sebelum** kode bagian 31 dipasang. Tanpa ini detail PO Stock & PO Non Stock error |
| 32 | `32_sp_cetakpo_perkiraan_item.sql` | `Sp_CetakPO`: kolom `PERKIRAAN`/`ketperk` dari detail item (fallback header). Rollback: `32_sp_cetakpo_ROLLBACK.sql` | Ya, sesudah file 31 |
| 34 | `34_dbpbiaya_kodebiaya_30.sql` | `dbPBiaya.Kodebiaya` + parameter `@KodeBiaya`/`@OldKode` di `sp_PBiaya` jadi `varchar(30)` (dulu KodeBiaya diisi No Bukti BPL; sejak bagian 35 diisi kode master `DBBIAYA`, file ini tetap dipakai). Rollback SP: `34_sp_pbiaya_ROLLBACK.sql` | Ya, **sebelum** kode bagian 34 dipasang. Tanpa ini simpan invoice yang punya biaya gagal (KodeBiaya terpotong/error) |
| 36 | `36_label_menu_pembelian_invoice.sql` | Label sidebar `DBMENUWEB` `0305` "Kelengkapan Dokumen" diganti "Pembelian Invoice" | Ya, kapan saja (hanya label) |

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

---

## 20 — fnc_MasterBeli: kolom hutang untuk menu Penerimaan ACC (20 Oktober 2026)

- Menu Penerimaan ACC gabungan (`newpobeliacc`) punya kolom tetap **Hutang** berisi badge **Lunas** (hijau) / **Belum** (merah). Nilainya diambil dari kolom baru `hutang` di fungsi `dbo.fnc_MasterBeli`.
- Perubahan fungsi (hanya menambah, kolom lama tidak berubah):
  - join `(select nofaktur, sum(kredit-debet) lunas from DBHUTPIUT group by nofaktur) g3 on a.NOBUKTI = g3.NoFaktur`;
  - kolom `case when ISNULL(lunas,0)=0 then 'Lunas' else 'Belum' end hutang` di akhir SELECT;
  - `g3.lunas` ditambahkan di GROUP BY.
- Artinya: sisa hutang per nobukti pembelian di `DBHUTPIUT` = 0 → **Lunas**, selain itu **Belum**. Pembelian yang belum punya baris di `DBHUTPIUT` (mis. belum diotorisasi) juga tampil **Lunas**.
- Tanpa skrip ini menu Penerimaan ACC tetap jalan, tetapi kolom Hutang kosong (controller memakai `$r->hutang ?? null`).
- Skrip berisi `ALTER FUNCTION` dengan definisi lengkap, jadi aman dijalankan ulang. Definisi di DBSMLBARU dan DBSMLNEW sudah sama persis.
- Sudah dijalankan langsung di DBSMLBARU dan DBSMLNEW (server 36.88.190.218) pada 20 Oktober 2026. Untuk server client, jalankan `20_fnc_masterbeli_kolom_hutang.sql`.

---

## 21 — vwPostHutPiut & vwPostJurnalOto: posting hutang tidak menunggu QC (21 Oktober 2026)

- **Gejala:** di menu Penerimaan ACC (`newpobeliacc`) kolom **Hutang** tampil **Lunas** untuk pembelian yang sudah diotorisasi, dan pembeliannya tidak ada di `DBHUTPIUT`. Di DBSMLNEW (newProgramAJC) sudah benar.
- **Penyebab:** tombol Otorisasi menjalankan `sp_ProsesPostingHutPiut` dan `sp_ProsesPostingJurnalOto` (prosedurnya sama di kedua DB). Sumber datanya, view `vwPostHutPiut` dan `vwPostJurnalOto`, di DBSMLBARU masih memuat syarat QC:
  - `vwPostHutPiut`: `where ISNULL(pqc,0) = case when ISNULL(pJasa,0)=0 then 1 else 0 End`
  - `vwPostJurnalOto`: `where (ISNULL(pQC,0)=1 Or isnull(a.pJasa,0)<>0)`

  Pembelian barang yang di-ACC sebelum QC tidak ikut diposting, dan `Sp_QC` tidak memposting ulang sesudah QC, jadi hutang & jurnalnya tidak pernah terbentuk.
- **Perubahan** (definisi view disalin persis dari DBSMLNEW, nama & urutan kolom tidak berubah):
  - kedua syarat QC di atas dinonaktifkan (jadi komentar);
  - tanggal hutang/jurnal pembelian = `A.TANGGAL` (tanggal LPB), bukan lagi `TglQC`; jatuh tempo = `A.TANGGAL + A.HARI`.
- **Catatan perilaku:** posting yang sudah ada tidak diubah. Hanya pembelian yang diotorisasi/diposting ulang setelah skrip ini yang memakai tanggal LPB.
- **Posting ulang data:** `SML/LPB/00016/1026` (kredit, B088) dan `SML/LPB/00017/1026` (tunai) diposting ulang dengan `sp_ProsesPostingHutPiut` + `sp_ProsesPostingJurnalOto` ('', 'dbbeli', 10, 2026, nobukti, 1). Hasilnya: baris `DBHUTPIUT` BPL dan `DBJurnalOto` sudah ada, kolom Hutang tampil **Belum**.
- Pembelian tunai September 2026 (LPB/0926, sudah QC) yang belum punya baris `DBHUTPIUT` tidak disentuh, karena penyebabnya bukan syarat QC (dari alur lama).
- View lain yang masih berbeda dengan DBSMLNEW soal QC/tanggal (`vwMasterBeli`, `vwTampilBeli`, `vwKartuStock`, `vwReportBeliGudang`, `vwOutPenerimaanBrg`, `vwOutPOSblmQC`, `vwMasterPenawaran`) **tidak** diubah.
- Sudah dijalankan langsung di DBSMLBARU (server 36.88.190.218) pada 21 Oktober 2026. Untuk server client, jalankan `21_vwposthutpiut_jurnaloto_tanpa_qc.sql`. Aman dijalankan ulang.

---

## 22 — Debet Note: daftar invoice hanya yang belum lunas (22 Oktober 2026, tanpa perubahan database)

- **Gejala:** modal Invoice di menu Debet Note masih menampilkan invoice yang sudah lunas. Dua penyebab:
  1. **Invoice IDR dengan sisa `SaldoD`.** Contoh di DBSMLNEW: `123123` (supplier 165) sudah dilunasi (baris LNS -1.000.000), tapi baris LNS mengisi `SaldoD` = 0 sehingga `SaldoD` tersisa 1.000.000. Filter lama `Having SUM(Saldo)<>0 or SUM(SaldoD)<>0` tetap menampilkannya.
  2. **Invoice valas dilunasi dengan kurs lain.** Query lama mengelompokkan per `NoFaktur, Valas, Kurs`. Contoh supplier K015 (KINGOYA ENTERPRISE SDN BHD, RM): `SML/LPB/00004/0521` dicatat kurs 3527,29 (+3.750 RM), pelunasannya kurs 3545 (-3.750 RM), ditambah baris selisih kurs (IDR). Ketiganya jadi kelompok terpisah, sehingga invoice lunas tampil sebagai belum lunas. Di DBSMLNEW K015 tampil 72 invoice, padahal yang belum lunas hanya 1 (`SML/LPB/00005/1026`, IDR 9.200.000).
- **Perubahan (hanya di controller `PembelianPermintaanDebetNoteController::listInvoice`):**
  - Saldo dijumlah per `NoFaktur` saja, langsung dari `DBHUTPIUT` (sebelumnya `vwHutPiut`; kolom view yang dipakai sama, dan filter `KodeCustSupp` di tabel dasar bisa memakai index).
  - Invoice dianggap valas kalau ada baris ber-valas selain IDR. Lunas dinilai per mata uang itu (IDR: `Saldo`, valas: `SaldoD`), dan hanya sisa > 0 yang tampil (saldo minus/lebih bayar ikut tersaring).
  - `KodeVls`/`Kurs` diambil dari baris pertama invoice dengan valas tersebut (`outer apply ... order by Tanggal, Urut`).
- **Tidak ada skrip SQL.**
- **Hasil uji langsung** (jumlah invoice hutang yang tampil, semua supplier): DBSMLNEW 828 → 516, DBSMLBARU 1.004 → 678. Supplier 165: `123123` tidak tampil lagi, `SML/LPB/00050–00053/0526` tetap tampil di DBSMLNEW (belum ada pelunasan) dan tidak tampil di DBSMLBARU (sudah lunas). Supplier K015 di DBSMLNEW tinggal 1 invoice.
- **Sisa pembulatan dianggap lunas** (keputusan user 22 Oktober 2026): invoice tampil hanya kalau sisanya **>= 1** (IDR atau valas). Sisa di belakang koma tidak ditampilkan, contohnya `PI201500842.` (M037, 0,03), `SML/LPB/00216/0824` (0,07), dan `SML/LPB/00224/0724` (0,04) milik C023. Data `DBHUTPIUT`-nya tidak diubah. Dengan batas ini, Debet Note tampil 513 invoice di DBSMLNEW dan 675 di DBSMLBARU.
- Modal Invoice juga dirapikan (kolom cari terpotong header). Ini hanya perubahan tampilan.

---

## 23 — Debet Note: Otorisasi memposting ke DBHUTPIUT & jurnal + separator angka (22 Oktober 2026, tanpa perubahan database)

- **Gejala:** Debet Note tidak pernah masuk `DBHUTPIUT`/`DBJurnalOto`.
- **Penyebab:** `PembelianPermintaanDebetNoteController::updateOtorisasi` hanya mengubah flag `IsOtorisasi1`, tanpa memanggil `sp_ProsesPostingHutPiut` / `sp_ProsesPostingJurnalOto`. Kredit Note sudah memanggilnya. SP dan `vwPostHutPiut` bagian DN sudah benar: tabel `DBDEBETNOTE` dipetakan ke jenis `DN`, dan baris yang diambil hanya DN dengan `IsOtorisasi1 = 1`.
- **Perubahan (controller):** Otorisasi menjalankan kedua SP dengan TipeProses 1 (`'', 'DBDEBETNOTE', bulan, tahun, nobukti, 1`). Batal Otorisasi menjalankan kedua SP dengan TipeProses 0 untuk menghapus posting. DN tetap masuk `DBHUTPIUT` saat **diotorisasi**, bukan saat dibuat, sama seperti KN, pembelian, dan desktop.
- **Uji (di-ROLLBACK) di DBSMLNEW** dengan `SML/DN/00001/1026` (M028, invoice `SML/LPB/00133/0326`, 2 USD × 18.000). Hasilnya 1 baris `DBHUTPIUT` HT 21201 dengan Debet 36.000 dan DebetD 2, sehingga saldo invoice turun dari 1.046.932 menjadi 1.010.932. Jurnal yang terbentuk: 21201 / lawan 51300 sebesar 36.000.
- **Data lama:** saat dicek, DN di DBSMLNEW (`SML/DN/00001/0926`, `SML/DN/00001/1026`) belum diotorisasi, dan DBSMLBARU belum punya DN. Jadi tidak ada data yang perlu diposting ulang.
- **Separator:** tabel Add/Edit/Detail, modal Invoice, dan form Edit Item diberi separator ribuan. Input Nilai/Kurs diketik dengan separator, lalu dikirim sebagai angka bersih. Controller juga membuang koma (`angka()`). Kolom **Nilai Inv** untuk invoice valas sekarang menampilkan `SaldoD` (dulu `Saldo` Rp, mis. 1,046,932.00 untuk invoice USD 60). Hapus item memakai data item itu sendiri, bukan isi form Edit. `formatAngka` lama yang salah untuk angka minus (`-,360,000.00`) juga diperbaiki.

---

## 25 — Menu PR Agen, PR Non Agen, PR Non Stock digabung jadi satu (11 November 2026)

- **Perubahan kode:** ketiga menu PR digabung di halaman `/pembelianpermintaanagen` (`PembelianPermintaanAgenController` + `purchasing/pembelianpermintaanagen.blade.php`).
  - Tabel memuat ketiga jenis sekaligus, dengan kolom baru **Jenis** (Agen / Non Agen / Non Stock) di sebelah No Bukti dan filter **Jenis** di modal Filter. `?jenis=agen|nonagen|nonstock` di URL dipakai sebagai filter awal.
  - Form Add punya dropdown **Jenis PR** di sebelah No Bukti, defaultnya Agen. Ganti jenis membuat no bukti di-generate ulang dengan prefix jenis itu (`DBNOMOR.PRA` / `PR` / `PNS`, sama seperti dulu). Dropdown dikunci setelah item pertama tersimpan dan saat edit.
  - Browse barang per jenis sama seperti dulu: Agen `KODEGRP 'BJ'` + `pAgen = 1`, Non Agen `KODEGRP 'BJ'` + `pAgen = 0`, Non Stock `KODEGRP 'JS'` + `IsJasa = 1`. Untuk Non Stock satuan tidak dipilih (selalu SAT1).
  - Nilai `isjasa`/`pagen`/`pjasa` ke `sp_PPL` ditentukan server dari jenis PR (PR yang sudah ada mengikuti header `DBPPL.pAgen`/`pJasa`), bukan dari kiriman browser.
  - **Hak akses per jenis:** tetap memakai `DBFLMENUWEB` kode menu lama, yaitu Agen `030102`, Non Agen `030101`, Non Stock `030103`. Halaman bisa dibuka kalau user punya `HASACCESS` di salah satunya. Data, pilihan Jenis PR, serta Tambah/Koreksi/Hapus/Otorisasi/Batal mengikuti hak jenis masing-masing.
  - Route `pembelianpermintaannonagen*` dan `pembelianpermintaannonstock*` di `routes/purchasing.php` di-comment. Controller dan blade lamanya dibiarkan sebagai cadangan. Route `purchaseRequestCetak` (cetak PR) dipindah ke `PembelianPermintaanAgenController::spCetak`.
- **Perubahan database (`25_gabung_menu_pr.sql`):** href menu lama dikosongkan (NULL):
  - `DBMENUWEB` `030101` (pembelianpermintaannonagen) dan `030103` (pembelianpermintaannonstock).
  - `DBMENU` `03001` (pembelianpermintaannonagen) dan `030013` (pembelianpermintaannonstock).
  - Baris menu dan hak akses `DBFLMENUWEB` `030101`/`030103` **tidak** dihapus, karena dipakai sebagai hak akses jenis Non Agen dan Non Stock.
  - Rollback ada di bagian bawah file skrip.
- **Sudah dijalankan** langsung di DBSMLBARU (server 36.88.190.218) pada 11 November 2026, dengan hasil sesuai. Untuk server client, jalankan `25_gabung_menu_pr.sql` bersamaan dengan update kodenya.
- **Catatan:** menu `030101`/`030103` disembunyikan dari sidebar lewat file 26 (lihat bagian 26). Pintu masuk PR gabungan di sidebar hanya menu `030102`, yang tampil bagi user dengan `HASACCESS` di `030102`. User yang **hanya** punya akses Non Agen/Non Stock (di DBSMLBARU: `sax`) tidak melihat menu PR, kecuali diberi akses `030102` (yang berarti akses PR Agen juga).

---

## 26 — Sembunyikan menu PR Non Agen & PR Non Stock dari sidebar (11 November 2026)

- **Gejala:** sesudah file 25, menu `030101` (PR Stock (Non Keagenan)) dan `030103` (PR Non Stock) masih tampil di sidebar, tapi tidak bisa diklik.
- **Perubahan (`26_sembunyikan_menu_pr_lama.sql`):** `DBMENUWEB.HeaderMenu` kedua menu diubah ke NULL. Sidebar web (`HomeController`, `NewMenuController@getMenuL0`) hanya menampilkan baris yang `HeaderMenu`-nya tidak NULL. Pola ini sama dengan menu tersembunyi lain, misalnya `030303` List Reject.
- **Hasil sidebar:** Pembelian > PR > PR (Keagenan), Closing PR. Struktur PR tetap punya turunan karena Closing PR ada di bawahnya.
- Hak akses `DBFLMENUWEB` `030101`/`030103` tidak diubah. Hak akses ini tetap dipakai sebagai hak akses jenis Non Agen dan Non Stock di halaman PR gabungan.
- **Sudah dijalankan** di DBSMLBARU (server 36.88.190.218) pada 11 November 2026. Rollback ada di bagian bawah file skrip.

---

## 28 — Label menu "PR (Keagenan)" diganti "PR" (12 November 2026)

- **Alasan:** sejak file 25, halaman `/pembelianpermintaanagen` sudah menjadi PR gabungan (Agen / Non Agen / Non Stock), jadi label "(Keagenan)" di sidebar menyesatkan.
- **Perubahan (`28_label_menu_pr.sql`):** `DBMENUWEB.Keterangan` untuk `030102` diubah dari `PR (Keagenan)` menjadi `PR`. Hak akses `DBFLMENUWEB` `030102` tidak diubah.
- **Hasil sidebar:** Pembelian > PR > PR, Closing PR.
- **Sudah dijalankan** di DBSMLBARU (server 36.88.190.218) pada 12 November 2026. Rollback ada di bagian bawah file skrip.

---

## 29 — Menu PO Stock & PO Non Stock digabung jadi satu (12 November 2026)

- **Perubahan kode:** kedua menu PO digabung di halaman `/purchaseorder` (`POController` + `purchasing/purchaseOrder.blade.php`).
  - Tab **Purchase Order** memuat PO Stock dan PO Non Stock sekaligus. Ada kolom baru **Jenis** (PO Stock / PO Non Stock) tepat sesudah No Bukti, dan filter **Jenis** di modal Filter. `?jenis=stock|nonstock` di URL dipakai sebagai filter awal.
  - Kolom Jenis dipasang langsung di JS, **tidak** lewat konfigurasi kolom `dbheadertable`, supaya selalu tampil apa pun pengaturan kolom user (di PR dulu kolom Jenis bisa hilang karena itu).
  - Tombol **Tambah** membuka modal pilihan PO Stock / PO Non Stock. Kalau user hanya boleh menambah satu jenis, form jenis itu langsung terbuka. Tombol **Tambah PR** tetap berdiri sendiri (PO Stock).
  - Edit / Detail / Otorisasi / Batal / Print di tiap baris memakai fungsi sesuai jenis PO-nya.
  - Form PO Non Stock dipindah ke `purchasing/partials/poNonStockForm|Modal|Script|Css.blade.php`. Semua id, fungsi, variabel global, `name`, dan class show/hide yang bentrok dengan form PO Stock diberi awalan `ns_`. **Perubahan form PO Non Stock dilakukan di partial ini**, bukan di `purchaseOrderNonStock.blade.php` (blade lama dibiarkan sebagai cadangan).
  - Tab **Outstanding PR** menampilkan semua PR (Agen / Non Agen / Non Stock) dengan kolom **Jenis PR** sesudah No Bukti dan tombol Filter Jenis PR. Jenis = `vwOutPPL.pjasa` + `DBPPL.pAgen`, sama dengan menu PR.
  - Supplier di form PO Stock dan PO Non Stock bisa diketik. Saran 10 teratas (kode/nama) tampil seperti Pelanggan di menu SO. Enter / tombol cari tetap membuka modal Supplier.
  - **Hak akses per jenis:** memakai `DBFLMENUWEB` kode menu lama, PO Stock `030201` dan PO Non Stock `030202` (dibaca per KODEMENU). Halaman bisa dibuka kalau punya `HASACCESS` di salah satunya. Daftar PO hanya memuat jenis yang boleh diakses.
  - Route `/pononstock` diarahkan ke `/purchaseorder?jenis=nonstock`. Route AJAX `pons*`/`polist*` tetap dipakai form Non Stock.
- **Perubahan database (`29_gabung_menu_po.sql`):**
  - `DBMENUWEB` `030202` (PO Non Stock): `href` dan `HeaderMenu` dikosongkan (NULL), sehingga hilang dari sidebar.
  - `DBMENU` `030022` (PO Jasa, `pononstock`): `href` dikosongkan.
  - `DBMENUWEB` `030201`: label `PO Stock` diganti `PO`.
  - Hak akses `DBFLMENUWEB` `030201`/`030202` **tidak** diubah.
  - Rollback ada di bagian bawah file skrip.
- **Sudah dijalankan** di DBSMLBARU (server 36.88.190.218) pada 12 November 2026, dengan hasil sesuai. Untuk server client, jalankan `29_gabung_menu_po.sql` bersamaan dengan update kodenya.

---

## 30 — Form PO: dropdown Jenis PO, PPH dari supplier, Perkiraan otomatis dari COA barang (13 November 2026, tanpa perubahan database)

- **Tombol Tambah** langsung membuka form Add (modal pilihan PO Stock / PO Non Stock dihapus). Default PO Stock, atau PO Non Stock kalau user tidak punya hak tambah PO Stock.
- **Dropdown Jenis PO** ada di atas Supplier pada kedua form (`#input_add_jenispo`, `#ns_input_add_jenispo`). Isinya hanya jenis yang boleh ditambah user. Kalau diganti, form direset total dan no bukti dibuat sesuai jenisnya: PO Stock `PO`/`PON`, PO Non Stock `POB`. Dropdown terkunci saat Edit/Detail, setelah item pertama tersimpan, dan saat form dibuka dari Buat PO (Outstanding PR).
- **Buat PO dari Outstanding PR:** PR Non Stock sekarang membuka form PO Non Stock. PR Agen/Non Agen tetap membuka form PO Stock. Dulu semua PR membuka form PO Stock.
- **PO Non Stock, PPH23 & PPH21:** hanya informatif dan selalu disabled. Waktu supplier dipilih, nilainya diisi dari `DBCUSTSUPP.NPPH23` / `NPPH22`. Waktu Edit/Detail, nilainya dari yang tersimpan di DBPO. Nilai yang disimpan ditentukan server (`PONonStockController@nilaiHeaderNonStock`), nilai kiriman browser diabaikan.
- **PO Non Stock, Perkiraan header:** input dikomentari. `DBPO.PERKIRAAN` pada PO baru diisi otomatis dari COA Biaya (`dbSubGroup.PerkPers`, Master Headgroup) barang item pertama. PO yang sudah ada mempertahankan nilai yang tersimpan, karena `sp_PO` Choice `U` menimpa `NPPH23`/`PERKIRAAN` header. Perkiraan per item tetap dibaca dari master barang masing-masing.
- **PO Non Stock, Costing:** daftar Costing (`polistcosting`) disaring dari COA Biaya barang yang dipilih (`kodebrg`), bukan lagi dari Perkiraan header. Costing/Sub-Costing dikosongkan setiap kali barang dipilih ulang.
- **Belum dikerjakan:** cetak PO Non Stock masih memakai `DBPO.PERKIRAAN` header (`Sp_CetakPO`). Rencananya nanti diganti ke perkiraan per barang.

---

## 31 — PO Non Stock: Perkiraan disimpan per item di DBPODET (16 November 2026)

- **Alasan:** 1 No Bukti PO bisa berisi banyak barang dengan COA Biaya berbeda, jadi perkiraan disimpan per baris detail, tidak cukup di header.
- **Perubahan database (`31_dbpodet_perkiraan.sql`):**
  - Kolom baru `DBPODET.PERKIRAAN varchar(25) NULL`.
  - Baris PO Non Stock lama (`DBPO.pJasa = 1`) diisi dari COA Biaya barang (`DBBARANG` -> `dbSubGroup.PerkPers`). Di akhir skrip ada SELECT untuk melihat baris yang masih kosong (COA Biaya barangnya belum diatur).
  - `sp_PO` **tidak** diubah.
- **Perubahan kode:**
  - `PONonStockController@spAdd`: sesudah `exec sp_PO`, `DBPODET.PERKIRAAN` diisi dari COA Biaya barang untuk baris PO itu yang masih kosong (item baru). Barang item tidak bisa diganti waktu edit, jadi baris lama tetap benar.
  - `POController@getDetail` (`pogetdetail`): tambah kolom `PerkiraanBrg` (= `B.PERKIRAAN`) dan `NmPerkiraanBrg`.
  - Form PO Non Stock: tabel detail barang dapat kolom **Perkiraan** (kode - nama). Form input item **tidak** punya field perkiraan, karena perkiraan otomatis mengikuti barang.
- **Header `DBPO.PERKIRAAN` tidak dipakai lagi** (menggantikan aturan bagian 30). `PONonStockController@nilaiHeaderNonStock` selalu mengirim perkiraan kosong ke `sp_PO`, jadi PO Non Stock baru dan PO lama yang item-nya diedit akan punya header perkiraan kosong. PO lama yang tidak disentuh tetap menyimpan nilai lamanya.
- **Cetak:** sudah diganti ke perkiraan per item, lihat bagian 32.
- **Sudah dijalankan** di DBSMLBARU (server 36.88.190.218) pada 16 November 2026. Hasil backfill: dari 2.203 baris detail PO Non Stock, 444 terisi dan 1.759 masih kosong. Hampir semuanya (1.757 baris) barang HeadGroup `20` / SubGroup `03` yang `dbSubGroup.PerkPers`-nya kosong. COA Biaya subgroup itu perlu diisi di Master Headgroup, lalu jalankan ulang `31_dbpodet_perkiraan.sql` untuk mengisi baris lama.

---

## 32 — Cetak PO: Perkiraan per item (16 November 2026)

- **Perubahan database (`32_sp_cetakpo_perkiraan_item.sql`):** `Sp_CetakPO` mengambil `PERKIRAAN` dan `ketperk` dari `DBPODET.PERKIRAAN` tiap baris. Kalau kosong, memakai `DBPO.PERKIRAAN` header (PO lama / PO Stock). Nama dan urutan kolom hasil tidak berubah, jadi cetak PO Stock dan Closing PO yang memakai SP yang sama tidak terpengaruh. Definisi lama disimpan di `32_sp_cetakpo_ROLLBACK.sql`.
- **Perubahan kode:** cetak PO Non Stock (`submitPrintcopy` di `partials/poNonStockScript.blade.php`) dapat kolom **PERKIRAAN** (kode perkiraan per baris; nama perkiraan muncul sebagai tooltip) di antara MERK dan QTY.
- **Sudah dijalankan** di DBSMLBARU (server 36.88.190.218) pada 16 November 2026, dan sudah dicoba `exec Sp_CetakPO` pada PO Non Stock: berjalan normal.
- **Urutan pasang:** jalankan `31_dbpodet_perkiraan.sql` dulu, baru update kode.

## 34 — Pembelian Invoice: modal Add per LPB + biaya per LPB (17 November 2026)

- **Perubahan database (`34_dbpbiaya_kodebiaya_30.sql`):** kolom `dbPBiaya.Kodebiaya` dan parameter `@KodeBiaya`/`@OldKode` di `sp_PBiaya` diperbesar dari `varchar(15)` ke `varchar(30)`, karena KodeBiaya sekarang diisi No Bukti invoice (mis. `SML/BPL/00004/1126`, 18 karakter). Isi SP lain tidak berubah. Data lama tidak berubah. Rollback SP: `34_sp_pbiaya_ROLLBACK.sql` (kolom dibiarkan 30).
- **Perubahan kode (`invoicepembelian`, tanpa perubahan database lain):**
  - Tab Outstanding Pembelian dan halaman Detail tetap seperti semula (satu baris per LPB).
  - Tombol **+** membuka modal berisi **semua LPB outstanding** dengan supplier, tipe PPN dan valuta yang sama dengan LPB yang diklik, dalam rentang PERIODE tab Outstanding. LPB yang dicentang masuk invoice beserta semua item outstanding-nya (`DBINVOICE`/`DBInvoiceDET`, kolom sama dengan `SP_INVB`; `SP_INVB` tidak dipanggil lagi dari halaman ini). No/Tgl Invoice, Faktur Pajak, Bukti Potong disimpan kosong (diisi lewat tab Pembelian Invoice > Edit).
  - Kolom **Action** (tombol + biru) per LPB membuka modal Biaya: KodeBiaya = No Bukti BPL, Keterangan, Nilai, Valas (`DBVALAS`, IDR kurs 1 terkunci), Kurs, Ekspedisi (browse `DBCUSTSUPP` JENIS 3). Biaya baru disimpan lewat `exec sp_PBiaya 'I'` (NoBuktiInv = No LPB, NoBiayaPO = PO LPB, UrutBiayaPO = 0) **saat invoice disimpan**, dalam transaksi yang sama.
  - LPB yang PO-nya memakai ekspedisi (`DBPO.KODEEXP` bukan kosong/`-`) wajib punya biaya. LPB yang punya biaya wajib dicentang.
- **Sudah dijalankan** di DBSMLBARU (server 36.88.190.218) pada 17 November 2026: `COL_LENGTH('dbPBiaya','Kodebiaya')` = 30, parameter SP = 30. Dicoba `exec sp_PBiaya 'I'` dengan KodeBiaya 18 karakter di dalam transaksi lalu ROLLBACK: tersimpan utuh, urut 1, 2 per LPB.
- **Urutan pasang:** jalankan `34_dbpbiaya_kodebiaya_30.sql` dulu, baru update kode.

## 35 — Pembelian Invoice: Kode Biaya di-browse dari master Daftar Biaya (19 November 2026, tanpa perubahan database)

- **Koreksi bagian 34:** KodeBiaya di modal Biaya **bukan lagi No Bukti BPL**. Sekarang field Kode Biaya dipilih lewat browse master Daftar Biaya (`DBBIAYA`, menu Master > Daftar Biaya), sama seperti data lama di `dbPBiaya` (mis. `BY1`). Keterangan otomatis diisi nama biaya kalau masih kosong (boleh diubah).
- Simpan tetap lewat `exec sp_PBiaya` (`I`/`U`/`D`) dengan `@KodeBiaya` = kode master, `NoBuktiInv` = No LPB. Saat Edit, `@OldKode` = KodeBiaya lama baris itu, jadi kode biaya boleh diganti.
- Biaya milik satu invoice sekarang dicari lewat LPB di `DBInvoiceDET` invoice itu (`dbPBiaya.NoBuktiInv in (select NoBeli from DBInvoiceDET where NOBUKTI = <invoice>)`), bukan lewat `Kodebiaya = No Bukti invoice`.
- Server menolak kode biaya yang tidak ada di `DBBIAYA`.
- **Tidak ada perubahan database.** Kolom `dbPBiaya.Kodebiaya` dan parameter SP tetap `varchar(30)` dari bagian 34 (tetap cukup untuk kode `DBBIAYA` yang `varchar(15)`), jadi file 34 tidak perlu di-rollback.
- **Data uji dari bagian 34:** per 19 November 2026 di DBSMLBARU ada 2 baris `dbPBiaya` dengan Kodebiaya berisi No Bukti BPL (`SML/BPL/00004/1126`, `SML/BPL/00005/1126`). Sudah **dihapus** di DBSMLBARU (server 36.88.190.218) pada 19 November 2026 (`delete from dbPBiaya where Kodebiaya in ('SML/BPL/00004/1126','SML/BPL/00005/1126')`, 2 baris; LPB `SML/LPB/00092/0926` dan `SML/LPB/00011/0926`). Ini hanya data uji di DBSMLBARU, **tidak perlu dijalankan di server client**.

## 36 — Label menu "Kelengkapan Dokumen" diganti "Pembelian Invoice" (19 November 2026)

- **Alasan:** judul halaman `/invoicepembelian` sudah "Pembelian Invoice", tapi label sidebar masih "Kelengkapan Dokumen".
- **Perubahan (`36_label_menu_pembelian_invoice.sql`):** `DBMENUWEB.Keterangan` untuk `0305` diubah dari `Kelengkapan Dokumen` menjadi `Pembelian Invoice`. Hak akses `DBFLMENUWEB` `0305` dan href tidak diubah.
- **Sudah dijalankan** di DBSMLBARU (server 36.88.190.218) pada 19 November 2026: 1 baris, `0305` = `Pembelian Invoice`. Rollback ada di bagian bawah file skrip.
