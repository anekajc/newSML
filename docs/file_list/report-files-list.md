# Report Pages — Saved Filter Checklist

Tracks which pages in `resources/views/report/` have the **saved filter per user** feature.
How the feature works and how to add it to a page: **[docs/save-filter-guide.md](../save-filter-guide.md)** — a session
asked to migrate a page should read that guide first, pick an unticked page below, and tick it when done
(also add the page to the status table in the guide, §9).

- Analyzed: 174 Blade files in `resources/views/report/` (top level only; `partials/` not included), on 2026-11-17.
- **Progress: 55 / 55 ready pages done.** Update this line when you tick a page.
- Note: "ready" means the page has a filter modal (`rt-filter`) containing at least one `select.rt-native` with an `id`
  and is used by a controller or another view. It does **not** guarantee the page migrates cleanly; do the per-page
  check in the guide (§6 step 0 and §7) first.

---

## 1. Summary — how many files

| Group | Files | Checkbox? |
|---|---:|---|
| **Ready to migrate** (filter modal with dropdowns, in use) | 0 | ✅ section 2 |
| **Done** | 55 | ✅ section 2 (ticked) |
| **Skipped on purpose** (a select exists but saving it would do nothing) | 1 | — end of section 2, with the reason |
| **Blocked** — filter modal has only picker boxes (`rt-combo`), needs helper phase 2 | 7 | ✅ section 3 |
| In use, but **no filter modal** (nothing to save) | 35 | — appendix A |
| Shared **layouts** (in use) | 8 | — appendix B |
| Layout backups, not referenced | 12 | — appendix B |
| Modal partials / templates (in use) | 5 | — appendix B |
| Modal partials / templates, not referenced | 4 | — appendix B |
| **Not referenced anywhere** (likely dead) | 47 | — appendix C |
| **Total** | **174** | |

How "in use" was decided: the file name appears as `report.<name>` (or `report/<name>`) in any PHP file under `app/` or
`routes/`, or in another Blade view (`@extends`/`@include`). No controller builds view names dynamically, so this is reliable,
but it is a text search: before **deleting** anything in appendix C, open the file and search for it once more.

---

## 2. Checklist — pages that can be migrated now (55)

All of these pages already sit on a layout that loads the helper (`report/masterreport2`, `masterreport4` or
`masterreportGudang` → `report/newmaster2x`), so each one only needs the **two code edits** from the guide (restore on load, save on Terapkan).
Every page has one filter modal; in most it is `#modalFilter`.

Reading a row: dropdown ids that will be saved. A trailing `*` means the options are filled by script (not in the markup), so restore must run after that fill.
The rows say nothing about *how* each page loads its data or how its Reset button behaves — check that per page (guide §7).

### Accounting (27/27)

- [x] [reportaccountingaktiva](../../resources/views/report/reportaccountingaktiva.blade.php) — `modalDivisi`* — LaporanAccountingAktivaController — ✅ done
- [x] [reportaccountingbankharian](../../resources/views/report/reportaccountingbankharian.blade.php) — `modalPerkiraan`* — LaporanAccountingBankHarianController — ✅ done
- [x] [reportaccountingbiaya](../../resources/views/report/reportaccountingbiaya.blade.php) — `modalDivisi`* — LaporanAccountingBiayaController — ✅ done
- [x] [reportaccountingbiayapenyusutan](../../resources/views/report/reportaccountingbiayapenyusutan.blade.php) — `modalDivisi`* — LaporanAccountingBiayaPenyusutanController — ✅ done
- [x] [reportaccountingbonsementara](../../resources/views/report/reportaccountingbonsementara.blade.php) — `modalPerkiraan`* — LaporanAccountingBonSementaraController — ✅ done
- [x] [reportaccountingbukubesar](../../resources/views/report/reportaccountingbukubesar.blade.php) — `modalDivisi`* — LaporanAccountingBukuBesarController — ✅ done
- [x] [reportaccountinghisbon](../../resources/views/report/reportaccountinghisbon.blade.php) — `modalPerkiraan`* — LaporanAccountingHisBonController — ✅ done
- [x] [reportaccountinghpp](../../resources/views/report/reportaccountinghpp.blade.php) — `modalDivisi`* — LaporanAccountingHPPController — ✅ done
- [x] [reportaccountinghutangkartu](../../resources/views/report/reportaccountinghutangkartu.blade.php) — `modalPerkiraan`*, `modalOrder`, ~~`modalReportMode`~~ (not saved: `data-nosave`) — LaporanAccountingHutangKartuController — ✅ done
- [x] [reportaccountinghutanglhpjt](../../resources/views/report/reportaccountinghutanglhpjt.blade.php) — ~~`modalReportMode`~~ (not saved: `data-nosave`), `modalOrder`, `modalPerkiraan`* — LaporanAccountingHutangLHPJTController — ✅ done
- [x] [reportaccountinghutanglph](../../resources/views/report/reportaccountinghutanglph.blade.php) — ~~`modalReportMode`~~ (not saved: `data-nosave`), `modalPerkiraan`* — LaporanAccountingHutangLPHController — ✅ done
- [x] [reportaccountinghutanglphto](../../resources/views/report/reportaccountinghutanglphto.blade.php) — `modalPerkiraan`* — LaporanAccountingHutangLPHTOController — ✅ done
- [x] [reportaccountinghutangoutstandingJT](../../resources/views/report/reportaccountinghutangoutstandingJT.blade.php) — ~~`modalReportMode`~~ (not saved: `data-nosave`), `modalOrder`, `modalPerkiraan`* — LaporanAccountingHutangOutstandingJTController — ✅ done
- [x] [reportaccountinghutangpelunasan](../../resources/views/report/reportaccountinghutangpelunasan.blade.php) — ~~`modalReportMode`~~ (not saved: `data-nosave`), `modalOrder`, `modalPerkiraan`* — LaporanAccountingHutangPelunasanController — ✅ done
- [x] [reportaccountinghutangumur](../../resources/views/report/reportaccountinghutangumur.blade.php) — `modalPerkiraan`*, ~~`modalReportMode`~~ (not saved: `data-nosave`) — LaporanAccountingHutangUmurController — ✅ done
- [x] [reportaccountingkasharian](../../resources/views/report/reportaccountingkasharian.blade.php) — `modalPerkiraan`* — LaporanAccountingKasHarianController — ✅ done
- [x] [reportaccountinglabarugi](../../resources/views/report/reportaccountinglabarugi.blade.php) — `modalDivisi`* — LaporanAccountingLabaRugiController — ✅ done
- [x] [reportaccountinglabarugitahunan](../../resources/views/report/reportaccountinglabarugitahunan.blade.php) — `modalDivisi`* — LaporanAccountingLabaRugiTahunanController — ✅ done
- [x] [reportaccountingneraca](../../resources/views/report/reportaccountingneraca.blade.php) — `modalDivisi`* — LaporanAccountingNeracaController — ✅ done
- [x] [reportaccountingneracapenunjang](../../resources/views/report/reportaccountingneracapenunjang.blade.php) — `modalDivisi`* — LaporanAccountingNeracaPenunjangController — ✅ done
- [x] [reportaccountingpiutangkartu](../../resources/views/report/reportaccountingpiutangkartu.blade.php) — `modalPerkiraan`*, `modalOrder`, ~~`modalReportMode`~~ (not saved: `data-nosave`) — LaporanAccountingPiutangKartuController — ✅ done
- [x] [reportaccountingpiutanglpp](../../resources/views/report/reportaccountingpiutanglpp.blade.php) — `modalPerkiraan`*, ~~`modalReportMode`~~ (not saved: `data-nosave`) — LaporanAccountingPiutangLPPController — ✅ done
- [x] [reportaccountingpiutanglppto](../../resources/views/report/reportaccountingpiutanglppto.blade.php) — `modalPerkiraan`*, ~~`modalReportMode`~~ (not saved: `data-nosave`) — LaporanAccountingPiutangLPPTOController — ✅ done
- [x] [reportaccountingpiutangoutstandingJT](../../resources/views/report/reportaccountingpiutangoutstandingJT.blade.php) — `modalPerkiraan`*, `modalOrder`, `modalGroup` — LaporanAccountingPiutangOutstandingJTController — ✅ done
- [x] [reportaccountingpiutangpelunasan](../../resources/views/report/reportaccountingpiutangpelunasan.blade.php) — `modalPerkiraan`*, `modalOrder`, ~~`modalReportMode`~~ (not saved: `data-nosave`) — LaporanAccountingPiutangPelunasanController — ✅ done
- [x] [reportaccountingpiutangspjt](../../resources/views/report/reportaccountingpiutangspjt.blade.php) — `modalPerkiraan`* — LaporanAccountingPiutangSPJTController — ✅ done
- [x] [reportaccountingpiutangumur](../../resources/views/report/reportaccountingpiutangumur.blade.php) — `modalPerkiraan`*, ~~`modalReportMode`~~ (not saved: `data-nosave`) — LaporanAccountingPiutangUmurController — ✅ done

### Marketing (14/14)

- [x] [reportlaporanmarketingspb](../../resources/views/report/reportlaporanmarketingspb.blade.php) — `modalOtorisasi`, `modalStatus`, `modalTerima` — LaporanMarketingSPBController — ✅ done
- [x] [reportlaporanmarketingspbhrgso](../../resources/views/report/reportlaporanmarketingspbhrgso.blade.php) — `modalOtorisasi`, `modalTerima`, `modalOutstanding` — LaporanMarketingSPBHrgSOController — ✅ done
- [x] [reportlaporanmarketingspbLT](../../resources/views/report/reportlaporanmarketingspbLT.blade.php) — `modalOtorisasi`, `modalTerima` — LaporanMarketingSPBLTController — ✅ done
- [x] [reportmarketinganalisakotor](../../resources/views/report/reportmarketinganalisakotor.blade.php) — `modalOtorisasi`, `modalAgen` — LaporanMarketingAnalisaKotorController — ✅ done
- [x] [reportmarketinghistoryoutso](../../resources/views/report/reportmarketinghistoryoutso.blade.php) — `inputOutstanding` — LaporanMarketingHistoryOutSOController — ✅ done
- [x] [reportmarketinghistoryso](../../resources/views/report/reportmarketinghistoryso.blade.php) — `modalOtorisasi` — LaporanMarketingHistorySOController — ✅ done
- [x] [reportmarketinglaporanoutso](../../resources/views/report/reportmarketinglaporanoutso.blade.php) — `modalOtorisasi`, `modalOrder` — LaporanMarketingLaporanOutSoController — ✅ done
- [x] [reportmarketingoutgudanggtc](../../resources/views/report/reportmarketingoutgudanggtc.blade.php) — `modalOtorisasi` — LaporanMarketingOutGudangGTCController — ✅ done
- [x] [reportmarketingoutspbhrgso](../../resources/views/report/reportmarketingoutspbhrgso.blade.php) — `modalOtorisasi` — LaporanMarketingOutSPBHrgSoController — ✅ done
- [x] [reportmarketingoutsppb](../../resources/views/report/reportmarketingoutsppb.blade.php) — `modalOtorisasi`, `modalOrder` — LaporanMarketingOutSPPBController — ✅ done
- [x] [reportmarketingregsaleinv](../../resources/views/report/reportmarketingregsaleinv.blade.php) — `modalOtorisasi`, `modalAgen` — LaporanMarketingRegSaleInvController — ✅ done
- [x] [reportmarketingreturpenjualan](../../resources/views/report/reportmarketingreturpenjualan.blade.php) — `modalOtorisasi` — LaporanMarketingReturPenjualanController — ✅ done
- [x] [reportmarketingso](../../resources/views/report/reportmarketingso.blade.php) — `modalOtorisasi`, `modalOutstanding`, `modalAgen` — LaporanMarketingSOController — ✅ done
- [x] [reportmarketinguangmukaout](../../resources/views/report/reportmarketinguangmukaout.blade.php) — `modalOtorisasi` — LaporanMarketingUangMukaOutController — ✅ done

### Pengadaan (Purchasing) (9/9)

- [x] [reportpengadaandebetnote](../../resources/views/report/reportpengadaandebetnote.blade.php) — `modalOtorisasi` — LaporanPengadaanDebetNoteController — ✅ done
- [x] [reportpengadaaninvoicepembelian](../../resources/views/report/reportpengadaaninvoicepembelian.blade.php) — `modalOtorisasi` — LaporanPengadaanInvoicePembelianController — ✅ done
- [x] [reportpengadaanpopo](../../resources/views/report/reportpengadaanpopo.blade.php) — `modalOtorisasi`, `modalDiterima` — LaporanPurchaseOrderPOController — ✅ done
- [x] [reportpengadaanpr](../../resources/views/report/reportpengadaanpr.blade.php) — `modalOtorisasi`, `modalStatusPO` — LaporanPengadaanPRController — ✅ done
- [x] [reportpengadaanregisterosinvoice](../../resources/views/report/reportpengadaanregisterosinvoice.blade.php) — `modalPPN` — LaporanPengadaanRegisterRekapOSInvoiceController — ✅ done
- [x] [reportpengadaanregisterpembelian](../../resources/views/report/reportpengadaanregisterpembelian.blade.php) — `modalPPN`, `modalPjasa`, `modalTipe`, `modalOtorisasi` — LaporanRegisterPembelianController — ✅ done
- [x] [reportpengadaanrekapinvoicepembelian](../../resources/views/report/reportpengadaanrekapinvoicepembelian.blade.php) — `modalOtorisasi` — LaporanPengadaanRekapInvoicePembelianController — ✅ done
- [x] [reportpengadaanreturpembelianacc](../../resources/views/report/reportpengadaanreturpembelianacc.blade.php) — `modalOtorisasi` — LaporanReturPembelianACCController — ✅ done
- [x] [reportpengadaanreturpembeliangdg](../../resources/views/report/reportpengadaanreturpembeliangdg.blade.php) — `modalOtorisasi` — LaporanReturPembelianGDGController — ✅ done

### Stock (2/2)

- [x] [reportstockkartustock](../../resources/views/report/reportstockkartustock.blade.php) — `inputGudang` — LaporanStockKartuStockController — ✅ done
- [x] [reportstockmutasistock](../../resources/views/report/reportstockmutasistock.blade.php) — `inputAgenSelect` — LaporanStockMutasiStockController — ✅ done

### Gudang & others (3/3)

- [x] [reportclosingopname](../../resources/views/report/reportclosingopname.blade.php) — `modalOtorisasi` — LaporanClosingOpnameController — ✅ done
- [x] [reportopname](../../resources/views/report/reportopname.blade.php) — `modalOtorisasi` — LaporanOpnameController — ✅ done
- [x] [reportpemakaian](../../resources/views/report/reportpemakaian.blade.php) — `modalOtorisasi` — LaporanPemakaianController — ✅ done

### Skipped on purpose (1)

No checkbox; not counted in the progress above.

- [reportpengadaanpgoutstandingpo](../../resources/views/report/reportpengadaanpgoutstandingpo.blade.php) — its only select is the "Mode Report" (Detail/Rekap) toggle `inputReportMode`: the page reads it once when the script loads (`let DetOrRekap = Number($('#inputReportMode').val())`) and the page itself documents the toggle as non-functional, so a saved value would change nothing. Revisit if that toggle is fixed or the page gets a real filter.

---

## 3. Blocked — filter modal has only picker boxes (7)

These pages have a filter modal but **no `select.rt-native`** (they use `rt-combo` picker boxes or other controls), so the current helper
would save nothing. They wait for the helper extension described in guide §11 (pickers hold a code **and** a label and may point at deleted master data).
Do not start these until that extension exists.

- [ ] [reportaccountingcosting](../../resources/views/report/reportaccountingcosting.blade.php) — picker boxes (`rt-combo`) — LaporanAccountingCostingController
- [ ] [reportpengadaanhispo](../../resources/views/report/reportpengadaanhispo.blade.php) — picker boxes (`rt-combo`) — LaporanHisPoController
- [ ] [reportstockfastslowdeadmoving](../../resources/views/report/reportstockfastslowdeadmoving.blade.php) — picker boxes (`rt-combo`) — LaporanStockFastSlowDeadMovingController
- [ ] [reportstockmutasistockpermerk](../../resources/views/report/reportstockmutasistockpermerk.blade.php) — no `rt-native` select (check what the modal uses) — LaporanStockMutasiStockPerMerkController
- [ ] [reportstocksaldostock](../../resources/views/report/reportstocksaldostock.blade.php) — picker boxes (`rt-combo`) — LaporanStockSaldoStockController
- [ ] [reportstockstockfisikgudang](../../resources/views/report/reportstockstockfisikgudang.blade.php) — picker boxes (`rt-combo`) — LaporanStockStockFisikGudangController
- [ ] [reportstockstockkartudanopname](../../resources/views/report/reportstockstockkartudanopname.blade.php) — picker boxes (`rt-combo`) — LaporanStockStockKartuDanOpnameController

---

## Appendix A — In use, but no filter modal (35)

Nothing to save on these pages today (no `rt-filter` modal). If one of them later gets a filter modal built with the new design
(`docs/new-filter-modal-ui-guide.md`), move it to section 2.

- [reportaccountinghutangoutstandingnota](../../resources/views/report/reportaccountinghutangoutstandingnota.blade.php) — LaporanAccountingHutangOutstandingNotaController
- [reportaccountingjurnalcomputer](../../resources/views/report/reportaccountingjurnalcomputer.blade.php) — LaporanAccountingJurnalComputerController
- [reportaccountingjurnalkoreksi](../../resources/views/report/reportaccountingjurnalkoreksi.blade.php) — LaporanAccountingJurnalKoreksiController
- [reportaccountingjurnalmemorial](../../resources/views/report/reportaccountingjurnalmemorial.blade.php) — LaporanAccountingJurnalMemorialController
- [reportaccountingjurnalpenerimaanbank](../../resources/views/report/reportaccountingjurnalpenerimaanbank.blade.php) — LaporanAccountingJurnalPenerimaanBankController
- [reportaccountingjurnalpenerimaankas](../../resources/views/report/reportaccountingjurnalpenerimaankas.blade.php) — LaporanAccountingJurnalPenerimaanKasController
- [reportaccountingjurnalpengeluaranbank](../../resources/views/report/reportaccountingjurnalpengeluaranbank.blade.php) — LaporanAccountingJurnalPengeluaranBankController
- [reportaccountingjurnalpengeluarankas](../../resources/views/report/reportaccountingjurnalpengeluarankas.blade.php) — LaporanAccountingJurnalPengeluaranKasController
- [reportaccountingjurnalpenutup](../../resources/views/report/reportaccountingjurnalpenutup.blade.php) — LaporanAccountingJurnalPenutupController
- [reportaccountinglaporanarus](../../resources/views/report/reportaccountinglaporanarus.blade.php) — LaporanAccountingLaporanArusController
- [reportaccountingneracalajur](../../resources/views/report/reportaccountingneracalajur.blade.php) — LaporanAccountingNeracaLajurController
- [reportaccountingposisibank](../../resources/views/report/reportaccountingposisibank.blade.php) — LaporanAccountingPosisiBankController
- [reportaccountingskb](../../resources/views/report/reportaccountingskb.blade.php) — LaporanAccountingSKBController
- [reportaccountingtrialbalance](../../resources/views/report/reportaccountingtrialbalance.blade.php) — LaporanAccountingTrialBalanceController
- [reportclosingtransfercabang](../../resources/views/report/reportclosingtransfercabang.blade.php) — LaporanClosingTransferCabangController
- [reportgudangoutkonsin](../../resources/views/report/reportgudangoutkonsin.blade.php) — LaporanGudangOutKonsinsController
- [reporthistoripenyerahansample](../../resources/views/report/reporthistoripenyerahansample.blade.php) — LaporanHistoriPenyerahanSampleController
- [reportkoreksistock](../../resources/views/report/reportkoreksistock.blade.php) — LaporanKoreksiStockController
- [reportlaporanmarketingspbacc](../../resources/views/report/reportlaporanmarketingspbacc.blade.php) — LaporanMarketingSPBACCController
- [reportlaporansuratjalan](../../resources/views/report/reportlaporansuratjalan.blade.php) — LaporanSuratJalanController
- [reportmarketingevalsolunas](../../resources/views/report/reportmarketingevalsolunas.blade.php) — LaporanMarketingEvalSoLunasController
- [reportmarketinginvoice](../../resources/views/report/reportmarketinginvoice.blade.php) — LaporanMarketingInvoiceController
- [reportmarketingpos](../../resources/views/report/reportmarketingpos.blade.php) — LaporanMarketingPOSController
- [reportmarketingreguangmuka](../../resources/views/report/reportmarketingreguangmuka.blade.php) — LaporanMarketingRegUangMukaController
- [reportmarketingsalesanalisa](../../resources/views/report/reportmarketingsalesanalisa.blade.php) — LaporanMarketingSalesAnalisaController
- [reportpengadaanclosepo](../../resources/views/report/reportpengadaanclosepo.blade.php) — LaporanPurchaseOrderClosePOController
- [reportpengadaanclosingpr](../../resources/views/report/reportpengadaanclosingpr.blade.php) — LaporanPengadaanClosingPRController
- [reportpengadaanosp](../../resources/views/report/reportpengadaanosp.blade.php) — LaporanPurchaseOrderOSPController
- [reportpengadaanoutstandingum2](../../resources/views/report/reportpengadaanoutstandingum2.blade.php) — LaporanPengadaanOutStandingUM2Controller
- [reportpengadaanoutstandingumbeli](../../resources/views/report/reportpengadaanoutstandingumbeli.blade.php) — LaporanPengadaanOutStandingUMBeliController
- [reportpengadaanpenerimaangudang](../../resources/views/report/reportpengadaanpenerimaangudang.blade.php) — LaporanPenerimaanGudangController
- [reportpermintaanpemakaian](../../resources/views/report/reportpermintaanpemakaian.blade.php) — LaporanPermintaanPemakaianController
- [reportstockmutasistockharian](../../resources/views/report/reportstockmutasistockharian.blade.php) — LaporanStockMutasiStockHarianController
- [reporttransferkecabangblmditerima](../../resources/views/report/reporttransferkecabangblmditerima.blade.php) — LaporanTransferKeCabangBlmDiterimaController
- [reportubahkemasan](../../resources/views/report/reportubahkemasan.blade.php) — LaporanUbahKemasanController

---

## Appendix B — Layouts, modal partials and templates

Not pages; listed so every file is accounted for.

**Shared layouts in use (8):** `mastercore.blade.php`, `masterreport2.blade.php`, `masterreport2x.blade.php`, `masterreport4.blade.php`, `masterreportGudang.blade.php`, `masterreportNeraca.blade.php`, `newmaster2.blade.php`, `newmaster2x.blade.php`

**Layout files not referenced (12):** `master.blade.php`, `masterpdf.blade.php`, `masterreport.blade.php`, `masterreport3.blade.php`, `masterreport5.blade.php`, `masterreportBackup2310.blade.php`, `masterreportGudangbackup.blade.php`, `newmaster.blade.php`, `newmaster2backup.blade.php`, `newmasterTransaksi.blade.php`, `newmasterxreport.blade.php`, `newtes.blade.php`

**Modal partials / templates in use (5):** `modalAccountingJurnal.blade.php`, `modalMarketingHistorySO.blade.php`, `modalMarketingInvoice.blade.php`, `modalMarketingSO.blade.php`, `modalbrowsemaster.blade.php`

**Modal partials / templates not referenced (4):** `modalMarketingRegUangMuka.blade.php`, `modalStock.blade.php`, `templateexcel.blade.php`, `templatepdf.blade.php`

---

## Appendix C — Not referenced anywhere (47, likely dead)

No controller or view uses these. Many look like backups (`*backup*`, `*2310`), tests (`reportTes*`, `testes`), old layouts/dashboards and pages of
other modules that ended up in this folder. **Review before deleting; this list does not delete or change anything.** They are
excluded from the checklist above.

- `alokasibarang.blade.php`
- `barangjadi.blade.php`
- `dashboard.blade.php`
- `dashforecast.blade.php`
- `dashsales.blade.php`
- `gudang.blade.php`
- `home.blade.php`
- `koreksistockMySql.blade.php`
- `laporan.blade.php`
- `model.blade.php`
- `newmenu.blade.php`
- `newmenu1.blade.php`
- `opnamebrg.blade.php`
- `outbarang.blade.php`
- `pembelianpermintaanagen.blade.php`
- `pembelianpermintaannonagen.blade.php`
- `pembelianpermintaannonstock.blade.php`
- `permintaanoutbarang.blade.php`
- `persiapanspb.blade.php`
- `purchaseorderstock.blade.php`
- `reportclosingpermintaantransfer.blade.php`
- `reportgudangkonsin.blade.php`
- `reportgudangoutprkonsin.blade.php`
- `reportgudangoutstandingsopo.blade.php`
- `reportgudangpembebanansample.blade.php`
- `reportgudangpermintaankonsinyasi.blade.php`
- `reportgudangretursample.blade.php`
- `reportoutstandingpermintaansample.blade.php`
- `reportoutstandingtransfer.blade.php`
- `reportpenerimaantransfer.blade.php`
- `reportpenyerahansample.blade.php`
- `reportperintahopname.blade.php`
- `reportpermintaansample.blade.php`
- `reportpermintaantransfer.blade.php`
- `reportselisihperintahopname.blade.php`
- `reportstockkartustock2310.blade.php`
- `reportstockmutasistockbackup.blade.php`
- `reportTes.blade.php`
- `reportTes2.blade.php`
- `reportTes3.blade.php`
- `reportTes4.blade.php`
- `reporttransfer.blade.php`
- `search.blade.php`
- `setperiode.blade.php`
- `settingmenu.blade.php`
- `settransaksiperusahaan.blade.php`
- `testes.blade.php`
