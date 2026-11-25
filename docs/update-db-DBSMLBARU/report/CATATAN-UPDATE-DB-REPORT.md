# Catatan Update DB - Report / Laporan (DBSMLBARU)

Laporan newSML disamakan dengan newProgramAJC (2026-10-14). Keputusan user: report ikut newProgramAJC (versi terbaru), termasuk SP/view-nya.

## Script

| No | File | Isi | Status |
|---|---|---|---|
| 19 | `19_sp_report_ikut_ajc.sql` | 9 view dan 19 SP laporan = isi DBSMLNEW, ditambah 3 SP baru (`sp_DEATFASTSLOW`, `Sp_reportBeligrafik2`, `SP_REPORTHISSOOUTweb`). | Sudah dijalankan 2026-10-14 |
| 39 | `39_menu_report_gudang_cabang.sql` | Menu report Gudang Pemakaian, Opname & Koreksi, Sample, Transfer, Konsinyasi dijadikan cabang (lihat bagian 39). | Sudah dijalankan 2026-11-25 |

## 39 — Menu report Gudang dijadikan cabang (2026-11-25)

Di `DBMENUREPORTWEB`, 5 menu gudang (dulu langsung membuka report) sekarang `href = '#'`, `ACCESS = 0`, dan punya sub-menu (L0 = 3):

| Induk | Sub-menu | href |
|---|---|---|
| 04010 Pemakaian | 040101 Pemakaian | `laporanpemakaianqty` (lama) |
| | 040102 Permintaan Pemakaian | `laporanpermintaanpemakaian` |
| 04030 Opname & Koreksi | 040301 Opname & Koreksi | `laporanopname` (lama) |
| | 040302 Perintah Opname | `laporanperintahopname` |
| 04040 Sample | 040401 Sample | `laporanhistoripenyerahansample` (lama) |
| | 040402 Permintaan Sample | `laporanpermintaansample` |
| 04050 Transfer | 040501 Transfer | `laporantransferkecabangblmditerima` (lama) |
| | 040502 Permintaan Transfer | `laporanpermintaantransfer` |
| 04060 Konsinyasi | 040601 Konsinyasi | `laporangudangoutkonsin` (lama) |
| | 040602 Permintaan Konsinyasi | `laporangudangpermintaankonsinyasi` |

- Hak akses `DBFLMENUREPORTWEB` milik kode induk disalin ke kedua sub-menu, per user (86 user).
- Backup baris induk ada di `DBMENUREPORTWEB_BAK39`. Skrip rollback ada di komentar bagian bawah skrip.
- Sudah dijalankan di DBSMLBARU 2026-11-25. Hasilnya 10 sub-menu dan 10 × 86 baris hak akses. Kode PHP tidak diubah, karena tree menu report dibangun dari prefix KODEMENU.
- href sub-menu "Permintaan/Perintah" sementara menunjuk ke route lama yang sudah ada. Report-nya akan dibuat ulang belakangan.

## Laporan Permintaan Pemakaian (2026-11-25) — tanpa perubahan database

- Menu 040102 → `laporanpermintaanpemakaian`. Halaman ditulis ulang mengikuti Laporan Pemakaian Qty dan memakai `Sp_ReportBpPR` (view `VwreportBPPR`) yang sudah ada.
- Switcher Jenis: Detail (Tanggal, No Bukti, Kode/Nama Barang, Satuan, Qty, HPP = `NilaiHPP`, Otorisasi) dan Rekap (tanpa HPP, Qty dijumlah per No Bukti + Kode Barang + Satuan di controller).

## Laporan Perintah Opname (2026-11-25) — tanpa perubahan database

- Menu 040302 → `laporanperintahopname`. Halaman ditulis ulang mengikuti Laporan Opname dan memakai `Sp_ReportPerintahOP` (view `VwreportPerintahOP`) yang sudah ada.
- Kolom: No Bukti, Tanggal, Kode Barang, Nama Barang, Group, Gudang (`NamaGDG`), Satuan, Qty (`OPNAME`). Nama group (`NamaHdGrp`) diambil controller dari `DBHDGROUP` berdasarkan `KodeHdGrp`.

## Laporan Permintaan Sample (2026-11-25) — tanpa perubahan database

- Menu 040402 → `laporanpermintaansample`. Halaman ditulis ulang mengikuti Histori Penyerahan Sample, data dari `VWREPORTSERAHSAMPLEPR`.
- Hanya bukti Permintaan Sample: `NOBUKTI like '%/PRS/%'`. View ini juga berisi bukti Permintaan Konsinyasi (PRK). `like '%PRS%'` di query user dikonfirmasi sebagai jenis no bukti.
- Filter Sales (browse `dbKaryawan` IsSales=1 Aktif=1, route baru `laporanpermintaansample_loadsales`). Bila sales dipilih, `KODESLS` dicocokkan persis (bukan LIKE), supaya sales 8 tidak ikut menarik 80/83. Tanpa sales = semua.
- Order By No Bukti (`order by tanggal, nobukti`) / Barang (`order by kodebrg, tanggal, nobukti`).
- Kolom: No Bukti, Tanggal, Kode Barang, Nama Barang, Gudang Asal, Gudang Tujuan, Satuan (`SAT_1`), Qty (`QNT`), Note. Nama gudang kosong diganti kodenya.

## Laporan Permintaan Transfer (2026-11-25) — tanpa perubahan database

- Menu 040502 → `laporanpermintaantransfer`. Halaman ditulis ulang mengikuti Laporan Permintaan Pemakaian (tanpa switcher Jenis), memakai `Sp_ReportTransferDetPR` (view `VwreporttransferPR`) yang sudah ada. Controller tidak diubah.
- Filter Otorisasi (Semua/Belum/Sudah), Order By No Bukti / Barang.
- Kolom: No Bukti, Tanggal, Kode Barang, Nama Barang, Gudang Asal (`NamagdgAsal`), Gudang Tujuan (`NamagdgTujuan`), Satuan (`SAT_1`), Qty (`QNT`).

## Laporan Permintaan Konsinyasi (2026-11-25) — tanpa perubahan database

- Menu 040602 → `laporangudangpermintaankonsinyasi`. Tampilan, filter Sales, Order By, dan kolomnya sama persis dengan Permintaan Sample. Query-nya juga sama (`LaporanPermintaanSampleController::reportSerahSamplePR`), hanya bukti PRK: `NOBUKTI like '%/PRK/%'`.
- Route baru `laporangudangpermintaankonsinyasi_loadsales`.

## Hanya menambah

- `VwreportOutSPB` tetap punya kolom `NNET`.
- `sp_ReportSaldoHutangTO` tetap menerima `@pKpi`, dan `Sp_ReportOUtSPBDet` tetap menerima `@pagen`. Keduanya bernilai default dan tidak dipakai lagi, supaya pemanggil lama tidak error.

## Perhatian

- `SP_TransferBlmTerima` sekarang memakai `@TGLA, @TGLB, @kirim`, sama dengan pemanggilnya di newSML. Versi lama hanya menerima `@TANGGAL`. programajcSML memanggilnya dengan 1 parameter, tapi programajcSML memakai database lain.
- Tidak diubah: `vwOutstandingPO` (dipakai Verifikasi Barang & Uang Muka Beli) dan `CetakInvoicePenjualan` (cetak invoice marketing).

## Perubahan kode terkait

- 17 laporan gudang (transfer, sample, konsinyasi, opname) dulu error 500 karena `view('reportxxx')`/`@include('modalxxx')`/`@extends('masterreport2')` tanpa awalan `report.`. Path-nya sudah diluruskan. Bug ini juga ada di newProgramAJC.
- Yang masih error, sama seperti di newProgramAJC: `laporanmarketinghistoryoutso_loadlokasi`/`_loadcustsupp` dan `reportaccountinglaporanarus_saldoawal` (method-nya tidak ada), `laporanclosingperintahopname` (controller-nya tidak ada), dan `reportaccountingcosting_saldoawal` (error saat dibuka tanpa parameter).
