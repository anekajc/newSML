# Catatan Update Database DBSMLBARU — Modul Marketing

Catatan ini mendaftar perubahan database DBSMLBARU dari pekerjaan menu **marketing** newSML. Struktur folder dijelaskan di `../README.md`.

## Urutan menjalankan di server

| No | File | Isi | Wajib? |
|---|---|---|---|
| 7 | `07_hak_akses_sa_verifikasibarang.sql` | Hak akses penuh user SA ke menu Verifikasi Barang | Ya, kalau SA perlu tombol Batal Verifikasi |
| 8 | `08_index_dbspbdet_noso.sql` | Index `dbSPBDet (NoSo, UrutSo)` untuk mempercepat menu Sales Order | Disarankan (tanpa index menu SO tetap jalan, tapi lebih lambat) |
| 9 | `09_perbaiki_qnt1_prj_00001_0416.sql` | Perbaiki QNT1 salah ketik PRJ lama `SML/PRJ/00001/0416` | Ya, supaya PRJ itu tidak terus tampil di Outstanding PRJ |
| 10 | `10_perbaiki_qnt_prj_00001_0916.sql` | Isi QNT1/QNT2 yang kosong di PRJ lama `SML/PRJ/00001/0916` | Ya, supaya data PRJ konsisten |
| 11 | `11_kreditnote_perkhp_dan_akun_bk.sql` | Default `''` untuk `DBKreditNoteDET.perkHP` + akun posting `DBPOSTHUTPIUT` Kode `BK` = 41200 | **Wajib**, tanpa ini item Kredit Note tidak tersimpan dan akun debet jurnal KN kosong |
| 22 | `22_penawaranso_disc_header_nilaippn.sql` | Penawaran SO: kolom `DISC` & `NILAIPPN` di `DBPENAWARANSODET`, rumus DPP/PPN/Net ikut Disc header, SP `Sp_UpdatePenawaranSO` | **Wajib** sebelum kode Penawaran SO versi 22 Oktober 2026 dipasang, tanpa ini simpan item gagal (SP tidak ditemukan) |

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

---

## 09 — Perbaiki QNT1 PRJ lama SML/PRJ/00001/0416 (13 Oktober 2026)

- PRJ tahun 2016 ini selalu tampil di tab **Outstanding PRJ** menu Retur Penjualan Gudang, tetapi saat diklik muncul "Data tidak ditemukkan". Penyebabnya data, bukan kode: `dbPRRJualDet.QNT1` urut 2 tersimpan **548,60**, padahal 54,86 MTR × 3,281 = **180,00** FEET (salah ketik 10×). Daftar Outstanding menghitung sisa dari QNT1 (548,60 − 180,00 > 0), sedangkan SP pembuat detail menghitung dari QNT (54,86 − 54,86 = 0).
- Perbaikan: QNT1 diisi 180,00, sama dengan QNT1 SPR `SML/SPR/00001/0416` yang sudah meretur seluruhnya. Hanya mengubah satu nilai data; skrip aman dijalankan ulang.
- Sudah dijalankan langsung di DBSMLBARU (server 36.88.190.218) pada 13 Oktober 2026 atas izin user. PRJ ini sudah tidak tampil lagi di Outstanding PRJ.

---

## 10 — Isi QNT1/QNT2 PRJ lama SML/PRJ/00001/0916 (13 Oktober 2026)

- PRJ tahun 2016, belum diotorisasi, belum pernah dipakai SPR. `QNT` = 8 PAK, tetapi `QNT1` dan `QNT2` tersimpan 0. Barangnya bersatuan 1 (PAK/PAK, ISI 1), jadi yang benar QNT1 = QNT2 = 8. Tanpa perbaikan, kalau PRJ ini diotorisasi ia tidak akan muncul di Outstanding PRJ karena sisa dihitung dari QNT1.
- Sesudah skrip 09 dan 10, tidak ada lagi baris `dbPRRJualDet` dengan QNT1 yang tidak cocok dengan QNT × ISI.
- Hanya mengubah dua nilai data; skrip aman dijalankan ulang.
- Sudah dijalankan langsung di DBSMLBARU (server 36.88.190.218) pada 13 Oktober 2026 atas permintaan user.

---

## 11 — Kredit Note: default perkHP & akun posting BK (14 Oktober 2026)

- **Masalah 1 (item KN tidak tersimpan):** kolom `DBKreditNoteDET.perkHP` NOT NULL tanpa default, sedangkan `Sp_KreditNote` tidak mengisinya. Insert item gagal tetapi SP tetap commit, jadi hanya header `DBKreditNote` yang tersimpan, item hilang, dan web tetap menjawab "berhasil". Struktur ini sama di DBSMLBARU dan DBSMLNEW, dan belum ada satu pun data KN.
- **Perbaikan:** ditambah `DEFAULT ('')` pada `perkHP` (constraint `DF_DBKreditNoteDET_perkHP`). `Sp_KreditNote` tidak diubah. Sesudah SP dijalankan, `KreditNoteController@spAdd` memastikan item benar-benar ada lalu mengisi `perkHP` dengan akun piutang invoice dari `DBHUTPIUT` (umumnya 113100; kalau tidak ketemu, pakai `DBPOSTHUTPIUT` PT urut 2). Ini sesuai pilihan user.
- **Masalah 2 (akun debet jurnal KN kosong):** `vwPostJurnalOto` mengambil akun DEBET jurnal KN dari `DBPOSTHUTPIUT` Kode `'BK'`, tetapi barisnya tidak ada.
- **Perbaikan:** ditambah baris `BK` = **41200 Potongan Penjualan**. User minta akunnya ditentukan dari data yang sudah ada. Di data lama, 41200 dipakai sebagai lawan Piutang Usaha 113100 milik customer (jurnal TDP), sedangkan 51300 (Discount/Potongan Pembayaran) dipakai untuk potongan atau credit memo dari supplier (sisi pembelian). Kolom lain disalin dari baris PT.
- Hasil uji (KN uji coba sudah dihapus lagi): saat otorisasi, piutang invoice berkurang (DBHUTPIUT Kredit, akun 113100) dan jurnal tercatat Debet 41200 / Lawan 113100.
- Skrip hanya **menambah** (constraint default + 1 baris konfigurasi) dan aman dijalankan ulang.
- Sudah dijalankan langsung di DBSMLBARU (server 36.88.190.218) pada 14 Oktober 2026.
- Catatan: Debet Note memakai Kode `'BD'` yang juga belum ada di `DBPOSTHUTPIUT`. Ini belum diubah karena di luar menu Kredit Note.

---

## Invoice Penjualan — kolom Piutang (21 Oktober 2026, tanpa perubahan database)

- Tab **Invoice** menu Invoice Penjualan (`invoicepenjualan`) mendapat kolom tetap **Piutang** tepat di kanan kolom Total (kalau Total disembunyikan, di ujung kanan), berisi badge **Lunas** (hijau) / **Belum** (merah). Tampilannya sama dengan kolom Hutang menu Penerimaan ACC (`newpobeliacc`), hanya versi piutang.
- **Tidak ada objek database yang diubah** (tidak ada view/fungsi/SP baru), jadi tidak ada skrip yang perlu dijalankan di server client. Yang berubah hanya query di `InvoicePenjualanController@sqlInvoicePenjualan`. Query ini hanya membaca tabel yang sudah ada (`DBHUTPIUT`).
- Tambahan pada query (dari user):
  - kolom: `case when isnull(h.piutang,0)=0 then 'Lunas' else 'Belum' end Piutang`;
  - join: `left outer join (select nofaktur,SUM(debet-kredit) piutang from DBHUTPIUT group by nofaktur)h on h.nofaktur=a.nobukti`.
- Artinya: sisa piutang per nobukti invoice di `DBHUTPIUT` = 0 → **Lunas**, selain itu **Belum**. Invoice yang belum punya baris di `DBHUTPIUT` (mis. belum diotorisasi) juga tampil **Lunas**. Logika ini sama dengan kolom `hutang` di `fnc_MasterBeli` (lihat `../purchasing/20_fnc_masterbeli_kolom_hutang.sql`).
- Kecepatan diukur di DBSMLBARU (September 2026, 1.011 invoice): 1 halaman tabel 0,2–1 detik.
- Query lengkap tab Invoice sesudah perubahan:

```sql
select  A.NoBukti, A.Tanggal, F.NAMACUSTSUPP NamaCustSupp,
        B.NoSPB, D.Tanggal TglSPB,
        Isnull(A.IsOtorisasi1,0) IsOtorisasi1, A.OtoUser1, A.TglOto1,
        Isnull(A.IsBatal,0) Isbatal, A.userBatal, A.tglBatal, A.NoPajak,
        E.NOBUKTI NOSO, E.TANGGAL TGLSO, E.NoPesanan,
        G.totdpp, G.totppn, G.totnet,
        case when isnull(h.piutang,0)=0 then 'Lunas' else 'Belum' end Piutang
from    dbo.dbInvoicePL A WITH(NOLOCK)
Left Outer join (Select x.NoBukti, x.NoSPB
                 from dbo.dbInvoicePLDet x WITH(NOLOCK)
                 Group by x.NoBukti, x.NoSPB) B on B.NoBukti=A.NoBukti
LEFT OUTER JOIN (SELECT NOBUKTI,NoSO FROM dbo.dbSPBDet WITH(NOLOCK) GROUP BY NoBukti,NoSO) C ON B.NoSPB=c.NoBukti
LEFT OUTER JOIN dbo.dbSPB D WITH(NOLOCK) ON C.NoBukti=D.NoBukti
left Outer join dbo.dbSO E WITH(NOLOCK) on E.Nobukti=C.NoSO
left outer join dbo.DBCUSTSUPP F WITH(NOLOCK) on F.KODECUSTSUPP=A.KodeCustSupp
left outer join vwRpDetInvoicePL G on A.NoBukti=G.NoBukti
left outer join (select nofaktur,SUM(debet-kredit) piutang from DBHUTPIUT group by nofaktur)h on h.nofaktur=a.nobukti
where   A.Tanggal between ? and ?
and isnull(A.FlagKasir,0)=0
And Isnull(A.pJasa,0)=0
and ( (? = 0)
   or (? = 1 and isnull(A.IsOtorisasi1,0) = 0)
   or (? = 2 and isnull(A.IsOtorisasi1,0) <> 0) )
```

---

## 12 — Kredit Note: daftar invoice hanya yang belum lunas (22 Oktober 2026, tanpa perubahan database)

- Sama dengan Debet Note (lihat `../purchasing/CATATAN-UPDATE-DB-PURCHASING.md` bagian 22). Query `KreditNoteController::listInvoice` sebelumnya mengelompokkan per `NoFaktur, Valas, Kurs` dengan `Having SUM(Saldo)<>0 or SUM(SaldoD)<>0`. Akibatnya, invoice IDR yang lunas tapi `SaldoD`-nya tersisa, serta pelunasan valas dengan kurs lain, membuat invoice lunas tetap tampil.
- **Perubahan (hanya di controller):** saldo dijumlah per `NoFaktur` saja (sama dengan `infoInvoice`/`cekNilai` saat simpan). Lunas dinilai per mata uang invoice (IDR: `Saldo`, valas: `SaldoD`). Invoice tampil hanya kalau sisanya >= 1. Sisa di belakang koma dianggap lunas (keputusan user 22 Oktober 2026); di data piutang saat ini tidak ada invoice seperti itu. `KodeVls`/`Kurs` diambil dari baris pertama invoice. Join view (invoice PL/SO/kebun/PIC) yang kolomnya tidak dipakai dilepas.
- **Tidak ada skrip SQL.**
- **Hasil uji langsung** (semua customer): DBSMLNEW 2.962 → 2.953, DBSMLBARU 2.660 → 2.655. Yang tersaring: invoice lunas dengan sisa `SaldoD` serta saldo minus (retur IVRJ, uang muka UMB, lebih bayar kecil). Customer terbesar (CP004) ±0,15–0,25 detik.
- Modal Invoice diberi kolom cari dan judul ganda/`margin-top:-60px` dihapus. Ini hanya perubahan tampilan.

---

## 22 — Penawaran SO: Disc header ikut DPP/PPN/Grand Total, PPN pakai NILAIPPN (22 Oktober 2026)

- **Masalah:** computed column `NDISKON/NDPP/NDPPRP/NPPN/NPPNRP/NNET/NNETRP` di `DBPENAWARANSODET` menulis mati diskon header = 0 dan PPN = 11% (`NDPPRP` malah membagi 1.1, tidak sama dengan `NDPP` yang membagi 1.11). Disc %/DiscRp di form Penawaran SO tidak pernah mengurangi DPP, PPN, dan Grand Total. Selain itu form memanggil `pospupdatepo` (`Sp_UpdatePO`), yang hanya bekerja di `DBPO`, jadi tidak ada yang menghitung ulang.
- **Perubahan (disamakan dengan `DBPODET` + `Sp_UpdatePO` di Purchase Order):**
  - `DBPENAWARANSODET` tambah `DISC float NOT NULL default 0` (Disc % header) dan `NILAIPPN int NOT NULL default 11` (tarif PPN). Default 11 = tarif lama, jadi nilai data lama tidak berubah.
  - Computed column di atas dibuat ulang memakai `DISC` dan `NILAIPPN`, rumusnya persis `DBPODET`. `HRGNETTO`, `SUBTOTAL`, `SUBTOTALRP` tidak diubah.
  - SP baru `Sp_UpdatePenawaranSO @NOBUKTI` menyalin `PPN`, `DISC`, `KURS` header ke semua baris detail. Dipanggil di `PenawaranSOController::spAdd` (setiap item disimpan) dan route `penawaransospupdate` (setiap Disc %/DiscRp/PPN/Kurs/header diubah, lewat `refreshUpdateHeader()`).
  - Data lama diselaraskan sekali; `DBPENAWARANSO.DISCRP` disamakan dengan total `NDISKON` untuk penawaran yang punya Disc header. View yang membaca tabel ini di-`sp_refreshview`.
- **Uji coba (dry run, di-ROLLBACK):** dijalankan di DBSMLBARU dalam satu transaksi lalu di-rollback pada 22 Oktober 2026, tanpa error. Contoh `0013/SML/PNW/X/2026` (PPN exclude, Disc 5%): Total 364.550.000 → Diskon 18.227.500, DPP 346.322.500, PPN 11% 38.095.475, Grand Total 384.417.975. Penawaran tanpa Disc header nilainya tetap sama dengan sebelumnya.
- **Belum dijalankan permanen di server.** Jalankan skrip ini (setelah backup) sebelum kode Penawaran SO versi ini dipakai.
- DBSMLNEW (newProgramAJC) memakai skrip yang sama dengan default `NILAIPPN` **10** (rumus lama di sana 10%): `newProgramAJC/docs/update-db-DBSMLNEW/marketing/22_penawaranso_disc_header_nilaippn.sql` (juga sudah di-dry-run + rollback).
- **Revisi 22 Oktober 2026 (sore):** `NILAIPPN` detail kini diisi dari `DBNILAIPPN` sesuai tanggal dokumen, baik oleh `Sp_UpdatePenawaranSO` maupun penyelarasan data lama (semua dokumen). Alasannya DBSMLNEW selama ini menghitung PPN 10% padahal `DBNILAIPPN` 2025–2050 = 11%. Form Penawaran SO juga menghitung DPP/PPN/Grand Total sendiri dengan rumus yang sama (tarif dari `getDetail` → `NilaiPPNHeader`), jadi tampilan form sudah benar walau skrip belum dijalankan; daftar & cetakan baru benar setelah skrip dijalankan.
- Controller kini tidak lagi gagal kalau `Sp_UpdatePenawaranSO` belum ada (dibungkus try/catch + `report()`), karena sebelumnya simpan/koreksi/hapus item tampil "Terjadi kesalahan" walau itemnya sudah tersimpan.
- Belum disentuh: `sp_cetakpenawaranso` masih menulis tarif 11 untuk harga satuan include PPN di cetakan; total di cetakan memakai `NDPP/NNET` sehingga ikut rumus baru.
