# Catatan Update DB - Report / Laporan (DBSMLBARU)

Laporan newSML disamakan dengan newProgramAJC (2026-10-14). Keputusan user: report ikut newProgramAJC (versi terbaru), termasuk SP/view-nya.

## Script

| No | File | Isi | Status |
|---|---|---|---|
| 19 | `19_sp_report_ikut_ajc.sql` | 9 view dan 19 SP laporan = isi DBSMLNEW, ditambah 3 SP baru (`sp_DEATFASTSLOW`, `Sp_reportBeligrafik2`, `SP_REPORTHISSOOUTweb`). | Sudah dijalankan 2026-10-14 |

## Hanya menambah

- `VwreportOutSPB` tetap punya kolom `NNET`.
- `sp_ReportSaldoHutangTO` tetap menerima `@pKpi`, dan `Sp_ReportOUtSPBDet` tetap menerima `@pagen`. Keduanya bernilai default dan tidak dipakai lagi, supaya pemanggil lama tidak error.

## Perhatian

- `SP_TransferBlmTerima` sekarang memakai `@TGLA, @TGLB, @kirim`, sama dengan pemanggilnya di newSML. Versi lama hanya menerima `@TANGGAL`. programajcSML memanggilnya dengan 1 parameter, tapi programajcSML memakai database lain.
- Tidak diubah: `vwOutstandingPO` (dipakai Verifikasi Barang & Uang Muka Beli) dan `CetakInvoicePenjualan` (cetak invoice marketing).

## Perubahan kode terkait

- 17 laporan gudang (transfer, sample, konsinyasi, opname) dulu error 500 karena `view('reportxxx')`/`@include('modalxxx')`/`@extends('masterreport2')` tanpa awalan `report.`. Path-nya sudah diluruskan. Bug ini juga ada di newProgramAJC.
- Yang masih error, sama seperti di newProgramAJC: `laporanmarketinghistoryoutso_loadlokasi`/`_loadcustsupp` dan `reportaccountinglaporanarus_saldoawal` (method-nya tidak ada), `laporanclosingperintahopname` (controller-nya tidak ada), dan `reportaccountingcosting_saldoawal` (error saat dibuka tanpa parameter).
