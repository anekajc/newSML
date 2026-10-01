<?php

use App\Http\Controllers\Report\ReportTesController;
use App\Http\Controllers\Report\GlobalFunctionsController;
use App\Http\Controllers\Report\FunctionGlobalController;
use App\Http\Controllers\Report\FunctionBrowseController;
use App\Http\Controllers\Report\LaporanPengadaanClosingPRController;
use App\Http\Controllers\Report\LaporanPengadaanPRController;
use App\Http\Controllers\Report\LaporanPurchaseOrderOSPController;
use App\Http\Controllers\Report\LaporanPurchaseOrderPOController;
use App\Http\Controllers\Report\LaporanPurchaseOrderClosePOController;
use App\Http\Controllers\Report\LaporanPenerimaanGudangOSPOController;
use App\Http\Controllers\Report\LaporanPenerimaanGudangController;
use App\Http\Controllers\Report\LaporanRegisterPembelianController;
use App\Http\Controllers\Report\LaporanReturPembelianACCController;
use App\Http\Controllers\Report\LaporanReturPembelianGDGController;
use App\Http\Controllers\Report\LaporanPengadaanOutStandingUMBeliController;
use App\Http\Controllers\Report\LaporanPengadaanOutStandingUM2Controller;
use App\Http\Controllers\Report\LaporanHisPoController;
use App\Http\Controllers\Report\LaporanPengadaanInvoicePembelianController;
use App\Http\Controllers\Report\LaporanPengadaanDebetNoteController;
use App\Http\Controllers\Report\LaporanPengadaanRekapInvoicePembelianController;
use App\Http\Controllers\Report\LaporanPengadaanRegisterRekapOSInvoiceController;
use App\Http\Controllers\Report\LaporanMarketingSOController;
use App\Http\Controllers\Report\LaporanMarketingHistorySOController;
use App\Http\Controllers\Report\LaporanMarketingHistoryOutSOController;
use App\Http\Controllers\Report\LaporanMarketingEvalSoLunasController;
use App\Http\Controllers\Report\LaporanMarketingLaporanOutSoController;
use App\Http\Controllers\Report\LaporanMarketingSPBController;
use App\Http\Controllers\Report\LaporanMarketingSPBLTController;
use App\Http\Controllers\Report\LaporanMarketingSPBACCController;
use App\Http\Controllers\Report\LaporanMarketingSPBHrgSOController;
use App\Http\Controllers\Report\LaporanSuratJalanController;
use App\Http\Controllers\Report\LaporanMarketingReturPenjualanController;
use App\Http\Controllers\Report\LaporanMarketingSalesAnalisaController;
use App\Http\Controllers\Report\LaporanMarketingAnalisaKotorController;
use App\Http\Controllers\Report\LaporanMarketingUangMukaOutController;
use App\Http\Controllers\Report\LaporanMarketingRegUangMukaController;
use App\Http\Controllers\Report\LaporanMarketingOutSPPBController;
use App\Http\Controllers\Report\LaporanMarketingOutSPBHrgSoController;
use App\Http\Controllers\Report\LaporanMarketingOutGudangGTCController;
use App\Http\Controllers\Report\LaporanMarketingInvoiceController;
use App\Http\Controllers\Report\LaporanMarketingPOSController;
use App\Http\Controllers\Report\LaporanMarketingRegSaleInvController;
use App\Http\Controllers\Report\LaporanPermintaanPemakaianController;
use App\Http\Controllers\Report\LaporanPemakaianController;
use App\Http\Controllers\Report\LaporanPermintaanTransferController;
use App\Http\Controllers\Report\LaporanClosingPermintaanTransferController;
use App\Http\Controllers\Report\LaporanTransferController;
use App\Http\Controllers\Report\LaporanPenerimaanTransferController;
use App\Http\Controllers\Report\LaporanOutstandingTransferController;
use App\Http\Controllers\Report\LaporanPerintahOpnameController;
use App\Http\Controllers\Report\LaporanClosingPerintahOpnameController;
use App\Http\Controllers\Report\LaporanOpnameController;
use App\Http\Controllers\Report\LaporanClosingOpnameController;
use App\Http\Controllers\Report\LaporanSelisihPerintahOpnameController;
use App\Http\Controllers\Report\LaporanUbahKemasanController;
use App\Http\Controllers\Report\LaporanPermintaanSampleController;
use App\Http\Controllers\Report\LaporanOutstandingPermintaanSampleController;
use App\Http\Controllers\Report\LaporanPenyerahanSampleController;
use App\Http\Controllers\Report\LaporanGudangPembebananSampleController;
use App\Http\Controllers\Report\LaporanGudangReturSampleController;
use App\Http\Controllers\Report\LaporanHistoriPenyerahanSampleController;
use App\Http\Controllers\Report\LaporanKoreksiStockController;
use App\Http\Controllers\Report\LaporanTransferKeCabangBlmDiterimaController;
use App\Http\Controllers\Report\LaporanClosingTransferCabangController;
use App\Http\Controllers\Report\LaporanGudangOutstandingSoPoController;
use App\Http\Controllers\Report\LaporanGudangPermintaanKonsinyasiController;
use App\Http\Controllers\Report\LaporanGudangOutPRKonsinController;
use App\Http\Controllers\Report\LaporanGudangKonsinController;
use App\Http\Controllers\Report\LaporanGudangOutKonsinsController;
use App\Http\Controllers\Report\LaporanStockMutasiStockController;
use App\Http\Controllers\Report\LaporanStockMutasiStockPerMerkController;
use App\Http\Controllers\Report\LaporanStockMutasiStockHarianController;
use App\Http\Controllers\Report\LaporanStockSaldoStockController;
use App\Http\Controllers\Report\LaporanStockStockFisikGudangController;
use App\Http\Controllers\Report\LaporanStockStockKartuDanOpnameController;
use App\Http\Controllers\Report\LaporanStockFastSlowDeadMovingController;
use App\Http\Controllers\Report\LaporanStockKartuStockController;
use App\Http\Controllers\Report\LaporanAccountingKasHarianController;
use App\Http\Controllers\Report\LaporanAccountingBankHarianController;
use App\Http\Controllers\Report\LaporanAccountingPosisiBankController;
use App\Http\Controllers\Report\LaporanAccountingBonSementaraController;
use App\Http\Controllers\Report\LaporanAccountingLaporanArusController;
use App\Http\Controllers\Report\LaporanAccountingCostingController;
use App\Http\Controllers\Report\LaporanAccountingHisBonController;
use App\Http\Controllers\Report\LaporanAccountingHutangKartuController;
use App\Http\Controllers\Report\LaporanAccountingHutangOutstandingJTController;
use App\Http\Controllers\Report\LaporanAccountingHutangPelunasanController;
use App\Http\Controllers\Report\LaporanAccountingHutangLPHController;
use App\Http\Controllers\Report\LaporanAccountingHutangUmurController;
use App\Http\Controllers\Report\LaporanAccountingHutangOutstandingNotaController;
use App\Http\Controllers\Report\LaporanAccountingHutangLHPJTController;
use App\Http\Controllers\Report\LaporanAccountingHutangLPHTOController;
use App\Http\Controllers\Report\LaporanAccountingPiutangKartuController;
use App\Http\Controllers\Report\LaporanAccountingPiutangOutstandingJTController;
use App\Http\Controllers\Report\LaporanAccountingPiutangPelunasanController;
use App\Http\Controllers\Report\LaporanAccountingPiutangLPPController;
use App\Http\Controllers\Report\LaporanAccountingPiutangUmurController;
use App\Http\Controllers\Report\LaporanAccountingPiutangSPJTController;
use App\Http\Controllers\Report\LaporanAccountingPiutangLPPTOController;
use App\Http\Controllers\Report\LaporanAccountingJurnalPenerimaanKasController;
use App\Http\Controllers\Report\LaporanAccountingJurnalPengeluaranKasController;
use App\Http\Controllers\Report\LaporanAccountingJurnalPenerimaanBankController;
use App\Http\Controllers\Report\LaporanAccountingJurnalPengeluaranBankController;
use App\Http\Controllers\Report\LaporanAccountingJurnalMemorialController;
use App\Http\Controllers\Report\LaporanAccountingJurnalKoreksiController;
use App\Http\Controllers\Report\LaporanAccountingJurnalComputerController;
use App\Http\Controllers\Report\LaporanAccountingJurnalPenutupController;
use App\Http\Controllers\Report\LaporanAccountingBukuBesarController;
use App\Http\Controllers\Report\LaporanAccountingTrialBalanceController;
use App\Http\Controllers\Report\LaporanAccountingBiayaController;
use App\Http\Controllers\Report\LaporanAccountingAktivaController;
use App\Http\Controllers\Report\LaporanAccountingBiayaPenyusutanController;
use App\Http\Controllers\Report\LaporanAccountingSKBController;
use App\Http\Controllers\Report\LaporanAccountingNeracaLajurController;
use App\Http\Controllers\Report\LaporanAccountingLabaRugiController;
use App\Http\Controllers\Report\LaporanAccountingLabaRugiTahunanController;
use App\Http\Controllers\Report\LaporanAccountingNeracaController;
use App\Http\Controllers\Report\LaporanAccountingNeracaPenunjangController;
use App\Http\Controllers\Report\LaporanAccountingHPPController;

// REPORT
Route::get('/reportTes', [ReportTesController::class, 'index'])->middleware('auth');
Route::get('/reportTesTable', [ReportTesController::class, 'spReport'])->middleware('auth');
Route::get('/takeDataFormCustomize', [ReportTesController::class, 'takeDataFormCustomizeTable'])->middleware('auth');

// ========== GLOBAL FUNCTIONS ========== //
Route::get('/globalfunctions_doLoadHeader', [GlobalFunctionsController::class, 'doLoadHeader']);
Route::get('/globalfunctions_doSimpanHeader', [GlobalFunctionsController::class, 'doSimpanHeader']);

// BERKAS
Route::get('/functionglobal_doUpdateDBMENUREPORT', [FunctionGlobalController::class, 'doUpdateDBMENUREPORT']);

// REPORT
Route::get('/functionglobal_doLoadHeader', [FunctionGlobalController::class, 'doLoadHeader']);
Route::get('/functionglobal_doSimpanHeader', [FunctionGlobalController::class, 'doSimpanHeader']);

// BROWSE MASTER
Route::get('/functionbrowse_doBrowseGudang', [FunctionBrowseController::class, 'doBrowseGudang']);
Route::get('/functionbrowse_doBrowseHdGroup', [FunctionBrowseController::class, 'doBrowseHdGroup']);
Route::get('/functionbrowse_doBrowseSubGroup', [FunctionBrowseController::class, 'doBrowseSubGroup']);
Route::get('/functionbrowse_doBrowseSubGroupJnsTambah', [FunctionBrowseController::class, 'doBrowseSubGroupJnsTambah']);
Route::get('/functionbrowse_doBrowseMerk', [FunctionBrowseController::class, 'doBrowseMerk']);
Route::get('/functionbrowse_doBrowseBarang', [FunctionBrowseController::class, 'doBrowseBarang']);

// LOAD MASTER
Route::get('/functionbrowse_doLoadGudang', [FunctionBrowseController::class, 'doLoadGudang']);
Route::get('/functionbrowse_doLoadBarang', [FunctionBrowseController::class, 'doLoadBarang']);
Route::get('/functionbrowse_doLoadMLokasi', [FunctionBrowseController::class, 'doLoadMLokasi']);


// =========================    PENGADAAN    ============================ //

// CLOSING PR
Route::get('/laporanpengadaanclosingpr', [LaporanPengadaanClosingPRController::class, 'index']);
Route::get('/laporanpengadaanclosingpr_doReport', [LaporanPengadaanClosingPRController::class, 'doReport']);
Route::get('/laporanpengadaanclosingpr_doFilter', [LaporanPengadaanClosingPRController::class, 'doFilter']);
Route::get('/laporanpengadaanclosingpr_doReportFilter', [LaporanPengadaanClosingPRController::class, 'doReportFilter']);

// ========== GLOBAL FUNCTIONS ========== //
Route::get('/globalfunctions_doLoadHeader', [GlobalFunctionsController::class, 'doLoadHeader']);
Route::get('/globalfunctions_doSimpanHeader', [GlobalFunctionsController::class, 'doSimpanHeader']);

// PR
Route::get('/laporanpengadaanpr', [LaporanPengadaanPRController::class, 'index']);
Route::get('/laporanpengadaanpr_doReport', [LaporanPengadaanPRController::class, 'doReport']);

// Purchase Order OSP
Route::get('/laporanpurchaseorderosp', [LaporanPurchaseOrderOSPController::class, 'index']);
Route::get('/laporanpurchaseorderosp_doReport', [LaporanPurchaseOrderOSPController::class, 'doReport']);

// Purchase Order PO
Route::get('/laporanpurchaseorderpo', [LaporanPurchaseOrderPOController::class, 'index']);
Route::get('/laporanpurchaseorderpo_doReport', [LaporanPurchaseOrderPOController::class, 'doReport']);

// Purchase Order Close PO
Route::get('/laporanpurchaseorderclosepo', [LaporanPurchaseOrderClosePOController::class, 'index']);
Route::get('/laporanpurchaseorderclosepo_doReport', [LaporanPurchaseOrderClosePOController::class, 'doReport']);
Route::get('/laporanpurchaseorderclosepo_doFilter', [LaporanPurchaseOrderClosePOController::class, 'doFilter']);
Route::get('/laporanpurchaseorderclosepo_doReportFilter', [LaporanPurchaseOrderClosePOController::class, 'doReportFilter']);

// Penerimaan Gudang Out Standing PO
Route::get('/laporanpenerimaangudangospo', [LaporanPenerimaanGudangOSPOController::class, 'index']);
Route::get('/laporanpenerimaangudangospo_doReport', [LaporanPenerimaanGudangOSPOController::class, 'doReport']);

// Penerimaan Gudang
Route::get('/laporanpenerimaangudang', [LaporanPenerimaanGudangController::class, 'index']);
Route::get('/laporanpenerimaangudang_doReport', [LaporanPenerimaanGudangController::class, 'doReport']);

// Register Pembelian
Route::get('/laporanregisterpembelian', [LaporanRegisterPembelianController::class, 'index']);
Route::get('/laporanregisterpembelian_doReport', [LaporanRegisterPembelianController::class, 'doReport']);
Route::get('/laporanregisterpembelian_doGrafik', [LaporanRegisterPembelianController::class, 'doGrafik']);

// Retur Pembelian ACC
Route::get('/laporanreturpembelianacc', [LaporanReturPembelianACCController::class, 'index']);
Route::get('/laporanreturpembelianacc_doReport', [LaporanReturPembelianACCController::class, 'doReport']);

// Retur Pembelian GDG
Route::get('/laporanreturpembeliangdg', [LaporanReturPembelianGDGController::class, 'index']);
Route::get('/laporanreturpembeliangdg_doReport', [LaporanReturPembelianGDGController::class, 'doReport']);

// Oustanding UM BELI
Route::get('/laporanpengadaanoutstandingumbeli', [LaporanPengadaanOutStandingUMBeliController::class, 'index']);
Route::get('/laporanpengadaanoutstandingumbeli_doReport', [LaporanPengadaanOutStandingUMBeliController::class, 'doReport']);
Route::get('/laporanpengadaanoutstandingumbeli_doFilter', [LaporanPengadaanOutStandingUMBeliController::class, 'doFilter']);
Route::get('/laporanpengadaanoutstandingumbeli_doReportFilter', [LaporanPengadaanOutStandingUMBeliController::class, 'doReportFilter']);

// Oustanding UM BELI 2
Route::get('/laporanpengadaanoutstandingum2', [LaporanPengadaanOutStandingUM2Controller::class, 'index']);
Route::get('/laporanpengadaanoutstandingum2_doReport', [LaporanPengadaanOutStandingUM2Controller::class, 'doReport']);
Route::get('/laporanpengadaanoutstandingum2_doKasharian', [LaporanPengadaanOutStandingUM2Controller::class, 'doKasharian']);
Route::get('/laporanpengadaanoutstandingum2_doFilter', [LaporanPengadaanOutStandingUM2Controller::class, 'doFilter']);
Route::get('/laporanpengadaanoutstandingum2_doReportFilter', [LaporanPengadaanOutStandingUM2Controller::class, 'doReportFilter']);

// HIS PO
Route::get('/laporanhispo', [LaporanHisPoController::class, 'index']);
Route::get('/laporanhispo_loadcustomer', [LaporanHisPoController::class, 'loadCustomer']);
Route::get('/laporanhispo_loadlokasi', [LaporanHisPoController::class, 'loadLokasi']);
Route::get('/laporanhispo_doReport', [LaporanHisPoController::class, 'doReport']);

// Invoice Pembelian
Route::get('/laporanpengadaaninvoicepembelian', [LaporanPengadaanInvoicePembelianController::class, 'index']);
Route::get('/laporanpengadaaninvoicepembelian_doReport', [LaporanPengadaanInvoicePembelianController::class, 'doReport']);

// Debet Note
Route::get('/laporanpengadaandebetnote', [LaporanPengadaanDebetNoteController::class, 'index']);
Route::get('/laporanpengadaandebetnote_doReport', [LaporanPengadaanDebetNoteController::class, 'doReport']);

// Rekap Invoice Pembelian
Route::get('/laporanpengadaanrekapinvoicepembelian', [LaporanPengadaanRekapInvoicePembelianController::class, 'index']);
Route::get('/laporanpengadaanrekapinvoicepembelian_doReport', [LaporanPengadaanRekapInvoicePembelianController::class, 'doReport']);

// Register Rekap Oustanding Invoice
Route::get('/laporanpengadaanregisteroutstandinginvoice', [LaporanPengadaanRegisterRekapOSInvoiceController::class, 'index']);
Route::get('/laporanpengadaanregisteroutstandinginvoice_doReport', [LaporanPengadaanRegisterRekapOSInvoiceController::class, 'doReport']);

// ======================= MARKETING =================== //

//LAPORAN SO LAPORAN SO
Route::get('/laporanmarketingso', [LaporanMarketingSOController::class, 'index']);
Route::get('/laporanmarketingso_doReport', [LaporanMarketingSOController::class, 'doReport']);
Route::get('/laporanmarketingso_loadcustomer', [LaporanMarketingSOController::class, 'loadCustomer']);
Route::get('/laporanmarketingso_loadgroup', [LaporanMarketingSOController::class, 'loadGroup']);
Route::get('/laporanmarketingso_loadpic', [LaporanMarketingSOController::class, 'loadPIC']);
Route::get('/laporanmarketingso_loadkategori', [LaporanMarketingSOController::class, 'loadKategori']);
Route::get('/laporanmarketingso_loadsubkategori', [LaporanMarketingSOController::class, 'loadSubKategori']);
Route::get('/laporanmarketingso_loadmerk', [LaporanMarketingSOController::class, 'loadMerk']);

// LAPORAN HISTORY SO
// SP_REPORTHISSO
Route::get('/laporanmarketinghistoryso', [LaporanMarketingHistorySOController::class, 'index']);
Route::get('/laporanmarketinghistoryso_doReport', [LaporanMarketingHistorySOController::class, 'doReport']);
Route::get('/laporanmarketinghistoryso_doFilter', [LaporanMarketingHistorySOController::class, 'doFilter']);
Route::get('/laporanmarketinghistoryso_doReportFilter', [LaporanMarketingHistorySOController::class, 'doReportFilter']);
Route::get('/laporanmarketinghistoryso_loadlokasi', [LaporanMarketingHistorySOController::class, 'loadLokasi']);
Route::get('/laporanmarketinghistoryso_loadcustsupp', [LaporanMarketingHistorySOController::class, 'loadCustSupp']);

//LAPORAN HISTORY OUSTANDING SO
//SP_REPORTHISSOOUT
Route::get('/laporanmarketinghistoryoutso', [LaporanMarketingHistoryOutSOController::class, 'index']);
Route::get('/laporanmarketinghistoryoutso_doReport', [LaporanMarketingHistoryOutSOController::class, 'doReport']);
Route::get('/laporanmarketinghistoryoutso_doFilter', [LaporanMarketingHistoryOutSOController::class, 'doFilter']);
Route::get('/laporanmarketinghistoryoutso_doReportFilter', [LaporanMarketingHistoryOutSOController::class, 'doReportFilter']);
Route::get('/laporanmarketinghistoryoutso_loadlokasi', [LaporanMarketingHistoryOutSOController::class, 'loadLokasi']);
Route::get('/laporanmarketinghistoryoutso_loadcustsupp', [LaporanMarketingHistoryOutSOController::class, 'loadCustSupp']);

//LAPORAN EVALUASI SO LUNAS
//REPOSRTEVALUASISOLUNAS
Route::get('/laporanmarketingevalsolunas', [LaporanMarketingEvalSoLunasController::class, 'index']);
Route::get('/laporanmarketingevalsolunas_doReport', [LaporanMarketingEvalSoLunasController::class, 'doReport']);
Route::get('/laporanmarketingevalsolunas_doKasharian', [LaporanMarketingEvalSoLunasController::class, 'doKasharian']);
Route::get('/laporanmarketingevalsolunas_doInvoice', [LaporanMarketingEvalSoLunasController::class, 'doInvoice']);
Route::get('/laporanmarketingevalsolunas_doLpb', [LaporanMarketingEvalSoLunasController::class, 'doLpb']);

//LAPORAN SO OUTSTANDING SO
Route::get('/laporanmarketinglaporanoutso', [LaporanMarketingLaporanOutSoController::class, 'index']);
Route::get('/laporanmarketinglaporanoutso_doReport', [LaporanMarketingLaporanOutSoController::class, 'doReport']);

// Marketing Laporan SPB
Route::get('/laporanmarketingspb', [LaporanMarketingSPBController::class, 'index']);
Route::get('/laporanmarketingspb_doReport', [LaporanMarketingSPBController::class, 'doReport']);

// Marketing Laporan SPB LT
Route::get('/laporanmarketingspbLT', [LaporanMarketingSPBLTController::class, 'index']);
Route::get('/laporanmarketingspbLT_doReport', [LaporanMarketingSPBLTController::class, 'doReport']);

// Marketing Laporan SPB ACC
Route::get('/laporanmarketingspbacc', [LaporanMarketingSPBACCController::class, 'index']);
Route::get('/laporanmarketingspbacc_doReport', [LaporanMarketingSPBACCController::class, 'doReport']);
Route::get('/laporanmarketingspbacc_doFilter', [LaporanMarketingSPBACCController::class, 'doFilter']);
Route::get('/laporanmarketingspbacc_doReportFilter',[LaporanMarketingSPBACCController::class, 'doReportFilter']);

// Marketing Laporan SPB HRG SO
Route::get('/laporanmarketingspbhrgso', [LaporanMarketingSPBHrgSOController::class, 'index']);
Route::get('/laporanmarketingspbhrgso_doReport', [LaporanMarketingSPBHrgSOController::class, 'doReport']);
Route::get('/laporanmarketingspbhrgso_doFilter', [LaporanMarketingSPBHrgSOController::class, 'doFilter']);
Route::get('/laporanmarketingspbhrgso_doReportFilter',[LaporanMarketingSPBHrgSOController::class, 'doReportFilter']);

// Laporan Surat Jalan
Route::get('/laporansuratjalan', [LaporanSuratJalanController::class, 'index']);
Route::get('/laporansuratjalan_doReport', [LaporanSuratJalanController::class, 'doReport']);
Route::get('/laporansuratjalan_doFilter', [LaporanSuratJalanController::class, 'doFilter']);
Route::get('/laporansuratjalan_doReportFilter',[LaporanSuratJalanController::class, 'doReportFilter']);

// Laporan Marketing Invoice Retur Penjualan
Route::get('/laporanmarketingreturpenjualan', [LaporanMarketingReturPenjualanController::class, 'index']);
Route::get('/laporanmarketingreturpenjualan_doReport', [LaporanMarketingReturPenjualanController::class, 'doReport']);

// Laporan Marketing Sales Analisa
Route::get('/laporanmarketingsalesanalisa', [LaporanMarketingSalesAnalisaController::class, 'index']);
Route::get('/laporanmarketingsalesanalisa_doReport', [LaporanMarketingSalesAnalisaController::class, 'doReport']);

//Laporan Analisa Laba Kotor
Route::get('/laporanmarketinganalisakotor', [LaporanMarketingAnalisaKotorController::class, 'index']);
Route::get('/laporanmarketinganalisakotor_doReport', [LaporanMarketingAnalisaKotorController::class, 'doReport']);
Route::get('/laporanmarketinganalisakotor_doFilter', [LaporanMarketingAnalisaKotorController::class, 'doFilter']);
Route::get('/laporanmarketinganalisakotor_doReportFilter',[LaporanMarketingAnalisaKotorController::class, 'doReportFilter']);

// Laporan Marketing Oustanding Uang Muka
Route::get('/laporanmarketinguangmukaout', [LaporanMarketingUangMukaOutController::class, 'index']);
Route::get('/laporanmarketinguangmukaout_doReport', [LaporanMarketingUangMukaOutController::class, 'doReport']);
Route::get('/laporanmarketinguangmukaout_loadsales', [LaporanMarketingUangMukaOutController::class, 'loadSales']);

//Laporan Marketing Register Uang Muka
Route::get('/laporanmarketingreguangmuka', [LaporanMarketingRegUangMukaController::class, 'index']);
Route::get('/laporanmarketingreguangmuka_doReport', [LaporanMarketingRegUangMukaController::class, 'doReport']);
Route::get('/laporanmarketingreguangmuka_doFilter', [LaporanMarketingRegUangMukaController::class, 'doFilter']);
Route::get('/laporanmarketingreguangmuka_doReportFilter',[LaporanMarketingRegUangMukaController::class, 'doReportFilter']);

//Laporan Marketing Oustanding SPPB
Route::get('/laporanmarketingoutsppb', [LaporanMarketingOutSPPBController::class, 'index']);
Route::get('/laporanmarketingoutsppb_doReport', [LaporanMarketingOutSPPBController::class, 'doReport']);
Route::get('/laporanmarketingoutsppb_doFilter', [LaporanMarketingOutSPPBController::class, 'doFilter']);
Route::get('/laporanmarketingoutsppb_doReportFilter',[LaporanMarketingOutSPPBController::class, 'doReportFilter']);

//Laporan Marketing Oustanding harga SO
Route::get('/laporanmarketingoutspbhrgso', [LaporanMarketingOutSPBHrgSoController::class, 'index']);
Route::get('/laporanmarketingoutspbhrgso_doReport', [LaporanMarketingOutSPBHrgSoController::class, 'doReport']);
Route::get('/laporanmarketingoutspbhrgso_doFilter', [LaporanMarketingOutSPBHrgSoController::class, 'doFilter']);
Route::get('/laporanmarketingoutspbhrgso_doReportFilter',[LaporanMarketingOutSPBHrgSoController::class, 'doReportFilter']);

//Laporan Marketing Outstanding Gudang GTC
Route::get('/laporanmarketingoutgudanggtc', [LaporanMarketingOutGudangGTCController::class, 'index']);
Route::get('/laporanmarketingoutgudanggtc_doReport', [LaporanMarketingOutGudangGTCController::class, 'doReport']);
Route::get('/laporanmarketingoutgudanggtc_doFilter', [LaporanMarketingOutGudangGTCController::class, 'doFilter']);
Route::get('/laporanmarketingoutgudanggtc_doReportFilter',[LaporanMarketingOutGudangGTCController::class, 'doReportFilter']);

//Laporan Marketing Laporan Invoice
Route::get('/laporanmarketinginvoice', [LaporanMarketingInvoiceController::class, 'index']);
Route::get('/laporanmarketinginvoice_doReport', [LaporanMarketingInvoiceController::class, 'doReport']);
Route::get('/laporanmarketinginvoice_doFilter', [LaporanMarketingInvoiceController::class, 'doFilter']);
Route::get('/laporanmarketinginvoice_doReportFilter',[LaporanMarketingInvoiceController::class, 'doReportFilter']);
Route::get('/laporanmarketinginvoice_loadjenis', [LaporanMarketingInvoiceController::class, 'loadJenis']);
Route::get('/laporanmarketinginvoice_loadgroup', [LaporanMarketingInvoiceController::class, 'loadGroup2']);

//Laporan Marketing Laporan POS
Route::get('/laporanmarketingpos', [LaporanMarketingPOSController::class, 'index']);
Route::get('/laporanmarketingpos_doReport', [LaporanMarketingPOSController::class, 'doReport']);
Route::get('/laporanmarketingpos_doFilter', [LaporanMarketingPOSController::class, 'doFilter']);
Route::get('/laporanmarketingpos_doReportFilter',[LaporanMarketingPOSController::class, 'doReportFilter']);

//Laporan Marketing Register Sales Invoice
Route::get('/laporanmarketingregsaleinv', [LaporanMarketingRegSaleInvController::class, 'index']);
Route::get('/laporanmarketingregsaleinv_doReport', [LaporanMarketingRegSaleInvController::class, 'doReport']);


// ========== GUDANG ========== //

// PERMINTAAN PEMAKAIAN
Route::get('/laporanpermintaanpemakaian', [LaporanPermintaanPemakaianController::class, 'index']);
Route::get('/laporanpermintaanpemakaian_doReport', [LaporanPermintaanPemakaianController::class, 'doReport']);
Route::get('/laporanpermintaanpemakaian_doFilter', [LaporanPermintaanPemakaianController::class, 'doFilter']);
Route::get('/laporanpermintaanpemakaian_doReportFilter', [LaporanPermintaanPemakaianController::class, 'doReportFilter']);

// PEMAKAIAN
Route::get('/laporanpemakaianqty', [LaporanPemakaianController::class, 'indexQty']);
Route::get('/laporanpemakaianrp', [LaporanPemakaianController::class, 'indexRp']);
Route::get('/laporanpemakaian_doReport', [LaporanPemakaianController::class, 'doReport']);
Route::get('/laporanpemakaian_doFilter', [LaporanPemakaianController::class, 'doFilter']);
Route::get('/laporanpemakaian_doReportFilter', [LaporanPemakaianController::class, 'doReportFilter']);

// PERMINTAAN TRANSFER
Route::get('/laporanpermintaantransfer', [LaporanPermintaanTransferController::class, 'index']);
Route::get('/laporanpermintaantransfer_doReport', [LaporanPermintaanTransferController::class, 'doReport']);
// Route::get('/laporanpermintaantransfer_doFilter', [LaporanPermintaanTransferController::class, 'doFilter']);
// Route::get('/laporanpermintaantransfer_doReportFilter', [LaporanPermintaanTransferController::class, 'doReportFilter']);

// CLOSING PERMINTAAN TRANSFER
Route::get('/laporanclosingpermintaantransfer', [LaporanClosingPermintaanTransferController::class, 'index']);
Route::get('/laporanclosingpermintaantransfer_doReport', [LaporanClosingPermintaanTransferController::class, 'doReport']);
Route::get('/laporanclosingpermintaantransfer_doFilter', [LaporanClosingPermintaanTransferController::class, 'doFilter']);
Route::get('/laporanclosingpermintaantransfer_doReportFilter', [LaporanClosingPermintaanTransferController::class, 'doReportFilter']);

// TRANSFER
Route::get('/laporantransfer', [LaporanTransferController::class, 'index']);
Route::get('/laporantransfer_doReport', [LaporanTransferController::class, 'doReport']);
Route::get('/laporantransfer_doFilter', [LaporanTransferController::class, 'doFilter']);
Route::get('/laporantransfer_doReportFilter', [LaporanTransferController::class, 'doReportFilter']);

// PENERIMAAN TRANSFER
Route::get('/laporanpenerimaantransfer', [LaporanPenerimaanTransferController::class, 'index']);
Route::get('/laporanpenerimaantransfer_doReport', [LaporanPenerimaanTransferController::class, 'doReport']);
Route::get('/laporanpenerimaantransfer_doFilter', [LaporanPenerimaanTransferController::class, 'doFilter']);
Route::get('/laporanpenerimaantransfer_doReportFilter', [LaporanPenerimaanTransferController::class, 'doReportFilter']);

// OUTSTANDING TRANSFER
Route::get('/laporanoutstandingtransfer', [LaporanOutstandingTransferController::class, 'index']);
Route::get('/laporanoutstandingtransfer_doReport', [LaporanOutstandingTransferController::class, 'doReport']);
Route::get('/laporanoutstandingtransfer_doFilter', [LaporanOutstandingTransferController::class, 'doFilter']);
Route::get('/laporanoutstandingtransfer_doReportFilter', [LaporanOutstandingTransferController::class, 'doReportFilter']);

// PERINTAH OPNAME
Route::get('/laporanperintahopname', [LaporanPerintahOpnameController::class, 'index']);
Route::get('/laporanperintahopname_doReport', [LaporanPerintahOpnameController::class, 'doReport']);
Route::get('/laporanperintahopname_doFilter', [LaporanPerintahOpnameController::class, 'doFilter']);
Route::get('/laporanperintahopname_doReportFilter', [LaporanPerintahOpnameController::class, 'doReportFilter']);

// CLOSING PERINTAH OPNAME
Route::get('/laporanclosingperintahopname', [LaporanClosingPerintahOpnameController::class, 'index']);
Route::get('/laporanclosingperintahopname_doReport', [LaporanClosingPerintahOpnameController::class, 'doReport']);
Route::get('/laporanclosingperintahopname_doFilter', [LaporanClosingPerintahOpnameController::class, 'doFilter']);
Route::get('/laporanclosingperintahopname_doReportFilter', [LaporanClosingPerintahOpnameController::class, 'doReportFilter']);

// OPNAME
Route::get('/laporanopname', [LaporanOpnameController::class, 'index']);
Route::get('/laporanopname_doReport', [LaporanOpnameController::class, 'doReport']);

// CLOSING OPNAME
Route::get('/laporanclosingopname', [LaporanClosingOpnameController::class, 'index']);
Route::get('/laporanclosingopname_doReport', [LaporanClosingOpnameController::class, 'doReport']);


// SELISIH PERINTAH OPNAME
Route::get('/laporanselisihperintahopname', [LaporanSelisihPerintahOpnameController::class, 'index']);
Route::get('/laporanselisihperintahopname_doReport', [LaporanSelisihPerintahOpnameController::class, 'doReport']);
Route::get('/laporanselisihperintahopname_doFilter', [LaporanSelisihPerintahOpnameController::class, 'doFilter']);
Route::get('/laporanselisihperintahopname_doReportFilter', [LaporanSelisihPerintahOpnameController::class, 'doReportFilter']);

// UBAH KEMASAN
Route::get('/laporanubahkemasan', [LaporanUbahKemasanController::class, 'index']);
Route::get('/laporanubahkemasan_doReport', [LaporanUbahKemasanController::class, 'doReport']);

// PERMINTAAN SAMPLE
Route::get('/laporanpermintaansample', [LaporanPermintaanSampleController::class, 'index']);
Route::get('/laporanpermintaansample_doReport', [LaporanPermintaanSampleController::class, 'doReport']);
Route::get('/laporanpermintaansample_doFilter', [LaporanPermintaanSampleController::class, 'doFilter']);
Route::get('/laporanpermintaansample_doReportFilter', [LaporanPermintaanSampleController::class, 'doReportFilter']);

// OUTSTANDING PERMINTAAN SAMPLE
Route::get('/laporanoutstandingpermintaansample', [LaporanOutstandingPermintaanSampleController::class, 'index']);
Route::get('/laporanoutstandingpermintaansample_doReport', [LaporanOutstandingPermintaanSampleController::class, 'doReport']);
Route::get('/laporanoutstandingpermintaansample_doFilter', [LaporanOutstandingPermintaanSampleController::class, 'doFilter']);
Route::get('/laporanoutstandingpermintaansample_doReportFilter', [LaporanOutstandingPermintaanSampleController::class, 'doReportFilter']);

// PENYERAHAN SAMPLE
Route::get('/laporanpenyerahansample', [LaporanPenyerahanSampleController::class, 'index']);
Route::get('/laporanpenyerahansample_doReport', [LaporanPenyerahanSampleController::class, 'doReport']);
Route::get('/laporanpenyerahansample_doFilter', [LaporanPenyerahanSampleController::class, 'doFilter']);
Route::get('/laporanpenyerahansample_doReportFilter', [LaporanPenyerahanSampleController::class, 'doReportFilter']);

// PEMBEBANAN SAMPLE
Route::get('/laporangudangpembebanansample', [LaporanGudangPembebananSampleController::class, 'index']);
Route::get('/laporangudangpembebanansample_doReport', [LaporanGudangPembebananSampleController::class, 'doReport']);

// RETUR SAMPLE
Route::get('/laporangudangretursample', [LaporanGudangReturSampleController::class, 'index']);
Route::get('/laporangudangretursample_doReport', [LaporanGudangReturSampleController::class, 'doReport']);

// HISTORI PENEYERAHAN SAMPLE
Route::get('/laporanhistoripenyerahansample', [LaporanHistoriPenyerahanSampleController::class, 'index']);
Route::get('/laporanhistoripenyerahansample_doReport', [LaporanHistoriPenyerahanSampleController::class, 'doReport']);

// KOREKSI STOCK
Route::get('/laporankoreksistock', [LaporanKoreksiStockController::class, 'index']);
Route::get('/laporankoreksistock_doReport', [LaporanKoreksiStockController::class, 'doReport']);
Route::get('/laporankoreksistock_doFilter', [LaporanKoreksiStockController::class, 'doFilter']);
Route::get('/laporankoreksistock_doReportFilter', [LaporanKoreksiStockController::class, 'doReportFilter']);

// TRANSFER KE CABANG BELUM DITERIMA
Route::get('/laporantransferkecabangblmditerima', [LaporanTransferKeCabangBlmDiterimaController::class, 'index']);
Route::get('/laporantransferkecabangblmditerima_doReport', [LaporanTransferKeCabangBlmDiterimaController::class, 'doReport']);
Route::get('/laporantransferkecabangblmditerima_doFilter', [LaporanTransferKeCabangBlmDiterimaController::class, 'doFilter']);
Route::get('/laporantransferkecabangblmditerima_doReportFilter', [LaporanTransferKeCabangBlmDiterimaController::class, 'doReportFilter']);

// CLOSING TRANSFER CABANG
Route::get('/laporanclosingtransfercabang', [LaporanClosingTransferCabangController::class, 'index']);
Route::get('/laporanclosingtransfercabang_doReport', [LaporanClosingTransferCabangController::class, 'doReport']);

// OUTSTANDING SO PO
Route::get('/laporangudangoutstandingsopo', [LaporanGudangOutstandingSoPoController::class, 'index']);
Route::get('/laporangudangoutstandingsopo_doReport', [LaporanGudangOutstandingSoPoController::class, 'doReport']);
Route::get('/laporangudangoutstandingsopo_doFilter', [LaporanGudangOutstandingSoPoController::class, 'doFilter']);
Route::get('/laporangudangoutstandingsopo_doReportFilter',[LaporanGudangOutstandingSoPoController::class, 'doReportFilter']);

// GUDANG PERMINTAAN KONSINYASI
Route::get('/laporangudangpermintaankonsinyasi', [LaporanGudangPermintaanKonsinyasiController::class, 'index']);
Route::get('/laporangudangpermintaankonsinyasi_doReport', [LaporanGudangPermintaanKonsinyasiController::class, 'doReport']);
Route::get('/laporangudangpermintaankonsinyasi_doFilter', [LaporanGudangPermintaanKonsinyasiController::class, 'doFilter']);
Route::get('/laporangudangpermintaankonsinyasi_doReportFilter', [LaporanGudangPermintaanKonsinyasiController::class, 'doReportFilter']);

// GUDANG OUTSTANDING PR KONSINYASI
Route::get('/laporangudangoutprkonsin', [LaporanGudangOutPRKonsinController::class, 'index']);
Route::get('/laporangudangoutprkonsin_doReport', [LaporanGudangOutPRKonsinController::class, 'doReport']);
Route::get('/laporangudangoutprkonsin_doFilter', [LaporanGudangOutPRKonsinController::class, 'doFilter']);
Route::get('/laporangudangoutprkonsin_doReportFilter', [LaporanGudangOutPRKonsinController::class, 'doReportFilter']);

// GUDANG KONSINYASI
Route::get('/laporangudangkonsin', [LaporanGudangKonsinController::class, 'index']);
Route::get('/laporangudangkonsin_doReport', [LaporanGudangKonsinController::class, 'doReport']);
Route::get('/laporangudangkonsin_doFilter', [LaporanGudangKonsinController::class, 'doFilter']);
Route::get('/laporangudangkonsin_doReportFilter', [LaporanGudangKonsinController::class, 'doReportFilter']);

// GUDANG OUTSTANDING KONSINYASI
Route::get('/laporangudangoutkonsin', [LaporanGudangOutKonsinsController::class, 'index']);
Route::get('/laporangudangoutkonsin_doReport', [LaporanGudangOutKonsinsController::class, 'doReport']);

// ========== STOCK ========== //

// MUTASI STOCK
Route::get('/laporanstockmutasistockqty', [LaporanStockMutasiStockController::class, 'indexqty']);
Route::get('/laporanstockmutasistockrp', [LaporanStockMutasiStockController::class, 'indexrp']);
Route::get('/laporanstockmutasistockqtyrp', [LaporanStockMutasiStockController::class, 'indexqtyrp']);
Route::get('/laporanstockmutasistockperiode', [LaporanStockMutasiStockController::class, 'indexperiode']);
Route::get('/laporanstockmutasistock_doReport', [LaporanStockMutasiStockController::class, 'doReport']);
Route::get('/laporanstockmutasistock_doMonthlyGraphics', [LaporanStockMutasiStockController::class, 'doMonthlyGraphics']);
Route::get('/laporanstockmutasistock_doDeadFastSlow', [LaporanStockMutasiStockController::class, 'doDeadFastSlow']);

// MUTASI STOCK PER MERK
Route::get('/laporanstockmutasistockpermerk', [LaporanStockMutasiStockPerMerkController::class, 'index']);
Route::get('/laporanstockmutasistockpermerk_doReport', [LaporanStockMutasiStockPerMerkController::class, 'doReport']);

// MUTASI STOCK HARIAN
Route::get('/laporanstockmutasistockharian', [LaporanStockMutasiStockHarianController::class, 'index']);
Route::get('/laporanstockmutasistockharian_doReport', [LaporanStockMutasiStockHarianController::class, 'doReport']);

// SALDO STOCK
Route::get('/laporanstocksaldostock', [LaporanStockSaldoStockController::class, 'index']);
Route::get('/laporanstocksaldostock_doReport', [LaporanStockSaldoStockController::class, 'doReport']);

// STOCK FISIK GUDANG
Route::get('/laporanstockstockfisikgudang', [LaporanStockStockFisikGudangController::class, 'index']);
Route::get('/laporanstockstockfisikgudang_doReport', [LaporanStockStockFisikGudangController::class, 'doReport']);

// STOCK KARTU DAN OPNAME
Route::get('/laporanstockstockkartudanopname', [LaporanStockStockKartuDanOpnameController::class, 'index']);
Route::get('/laporanstockstockkartudanopname_doReport', [LaporanStockStockKartuDanOpnameController::class, 'doReport']);

// FAST SLOW DEAD MOVING
Route::get('/laporanstockfastslowdeadmoving', [LaporanStockFastSlowDeadMovingController::class, 'index']);
Route::get('/laporanstockfastslowdeadmoving_doReport', [LaporanStockFastSlowDeadMovingController::class, 'doReport']);

// KARTU STOCK
Route::get('/laporanstockkartustockqty', [LaporanStockKartuStockController::class, 'indexqty']);
Route::get('/laporanstockkartustockqtyrp', [LaporanStockKartuStockController::class, 'indexqtyrp']);
Route::get('/laporanstockkartustock_doReport', [LaporanStockKartuStockController::class, 'doReport']);

// ======================= ACCOUNTING =================== //
// Kas Harian
Route::get('/reportaccountingkasharian', [LaporanAccountingKasHarianController::class, 'index']);
Route::get('/reportaccountingkasharian_loadperkiraan', [LaporanAccountingKasHarianController::class, 'loadPerkiraan']);
Route::get('/reportaccountingkasharian_doReport', [LaporanAccountingKasHarianController::class, 'doReport']);
Route::get('/reportaccountingkasharian_saldoawal',[LaporanAccountingKasHarianController::class, 'doReportSaldoAwal']);

// Bank Harian
Route::get('/reportaccountingbankharian', [LaporanAccountingBankHarianController::class, 'index']);
Route::get('/reportaccountingbankharian_loadperkiraan', [LaporanAccountingBankHarianController::class, 'loadPerkiraan']);
Route::get('/reportaccountingbankharian_doReport', [LaporanAccountingBankHarianController::class, 'doReport']);
Route::get('/reportaccountingbankharian_doFilter', [LaporanAccountingBankHarianController::class, 'doFilter']);
Route::get('/reportaccountingbankharian_doReportFilter', [LaporanAccountingBankHarianController::class, 'doReportFilter']);
Route::get('/reportaccountingbankharian_saldoawal',[LaporanAccountingBankHarianController::class, 'doReportSaldoAwal']);
Route::get('/reportaccountingbankharian_saldoakhir',[LaporanAccountingBankHarianController::class, 'doReportSaldoAkhir']);

// Posisi Bank
Route::get('/reportaccountingposisibank', [LaporanAccountingPosisiBankController::class, 'index']);
Route::get('/reportaccountingposisibank_doReport', [LaporanAccountingPosisiBankController::class, 'doReport']);
Route::get('/reportaccountingposisibank_doFilter', [LaporanAccountingPosisiBankController::class, 'doFilter']);
Route::get('/reportaccountingposisibank_doReportFilter', [LaporanAccountingPosisiBankController::class, 'doReportFilter']);

// Bon Sementara
Route::get('/reportaccountingbonsementara', [LaporanAccountingBonSementaraController::class, 'index']);
Route::get('/reportaccountingbonsementara_doReport', [LaporanAccountingBonSementaraController::class, 'doReport']);
Route::get('/reportaccountingbonsementara_loadperkiraan', [LaporanAccountingBonSementaraController::class, 'loadPerkiraan']);
Route::get('/reportaccountingbonsementara_doFilter', [LaporanAccountingBonSementaraController::class, 'doFilter']);
Route::get('/reportaccountingbonsementara_doReportFilter', [LaporanAccountingBonSementaraController::class, 'doReportFilter']);

// Laporan Arus
Route::get('/reportaccountinglaporanarus', [LaporanAccountingLaporanArusController::class, 'index']);
Route::get('/reportaccountinglaporanarus_loadperkiraan', [LaporanAccountingLaporanArusController::class, 'loadPerkiraan']);
Route::get('/reportaccountinglaporanarus_doReport', [LaporanAccountingLaporanArusController::class, 'doReport']);
Route::get('/reportaccountinglaporanarus_doFilter', [LaporanAccountingLaporanArusController::class, 'doFilter']);
Route::get('/reportaccountinglaporanarus_doReportFilter', [LaporanAccountingLaporanArusController::class, 'doReportFilter']);
Route::get('/reportaccountinglaporanarus_saldoawal',[LaporanAccountingLaporanArusController::class, 'doReportSaldoAwal']);

// Costing
Route::get('/reportaccountingcosting', [LaporanAccountingCostingController::class, 'index']);
Route::get('/reportaccountingcosting_loadperkiraan', [LaporanAccountingCostingController::class, 'loadPerkiraan']);
Route::get('/reportaccountingcosting_loadsubcosting', [LaporanAccountingCostingController::class, 'loadSubCosting']);
Route::get('/reportaccountingcosting_doReport', [LaporanAccountingCostingController::class, 'doReport']);
Route::get('/reportaccountingcosting_doFilter', [LaporanAccountingCostingController::class, 'doFilter']);
Route::get('/reportaccountingcosting_doReportFilter', [LaporanAccountingCostingController::class, 'doReportFilter']);
Route::get('/reportaccountingcosting_saldoawal',[LaporanAccountingCostingController::class, 'doReportSaldoAwal']);

// His Bon
Route::get('/reportaccountinghisbon', [LaporanAccountingHisBonController::class, 'index']);
Route::get('/reportaccountinghisbon_loadperkiraan', [LaporanAccountingHisBonController::class, 'loadPerkiraan']);
Route::get('/reportaccountinghisbon_doReport', [LaporanAccountingHisBonController::class, 'doReport']);

// Hutang - Kartu
Route::get('/reportaccountinghutangkartu', [LaporanAccountingHutangKartuController::class, 'index']);
Route::get('/reportaccountinghutangkartu_loadvalas', [LaporanAccountingHutangKartuController::class, 'loadValas']);
Route::get('/reportaccountinghutangkartu_loadsuppawal', [LaporanAccountingHutangKartuController::class, 'loadSuppAwal']);
Route::get('/reportaccountinghutangkartu_loadperkiraan', [LaporanAccountingHutangKartuController::class, 'loadPerkiraan']);
Route::get('/reportaccountinghutangkartu_doReport', [LaporanAccountingHutangKartuController::class, 'doReport']);
Route::get('/reportaccountinghutangkartu_doKasharian', [LaporanAccountingHutangKartuController::class, 'doKasharian']);
Route::get('/reportaccountinghutangkartu_doInvoice', [LaporanAccountingHutangKartuController::class, 'doInvoice']);
Route::get('/reportaccountinghutangkartu_doLpb', [LaporanAccountingHutangKartuController::class, 'doLpb']);
Route::get('/reportaccountinghutangkartu_doBp', [LaporanAccountingHutangKartuController::class, 'doBp']);

// Hutang - Outstanding JT
Route::get('/reportaccountinghutangoutstandingJT', [LaporanAccountingHutangOutstandingJTController::class, 'index']);
Route::get('/reportaccountinghutangoutstandingJT_loadvalas', [LaporanAccountingHutangOutstandingJTController::class, 'loadValas']);
Route::get('/reportaccountinghutangoutstandingJT_loadsuppawal', [LaporanAccountingHutangOutstandingJTController::class, 'loadSuppAwal']);
Route::get('/reportaccountinghutangoutstandingJT_loadperkiraan', [LaporanAccountingHutangOutstandingJTController::class, 'loadPerkiraan']);
Route::get('/reportaccountinghutangoutstandingJT_doReport', [LaporanAccountingHutangOutstandingJTController::class, 'doReport']);
Route::get('/reportaccountinghutangoutstandingJT_doKasharian', [LaporanAccountingHutangOutstandingJTController::class, 'doKasharian']);
Route::get('/reportaccountinghutangoutstandingJT_doInvoice', [LaporanAccountingHutangOutstandingJTController::class, 'doInvoice']);
Route::get('/reportaccountinghutangoutstandingJT_doLpb', [LaporanAccountingHutangOutstandingJTController::class, 'doLpb']);
Route::get('/reportaccountinghutangoutstandingJT_doBp', [LaporanAccountingHutangOutstandingJTController::class, 'doBp']);

// Hutang - Pelunasan
Route::get('/reportaccountinghutangpelunasan', [LaporanAccountingHutangPelunasanController::class, 'index']);
Route::get('/reportaccountinghutangpelunasan_loadvalas', [LaporanAccountingHutangPelunasanController::class, 'loadValas']);
Route::get('/reportaccountinghutangpelunasan_loadsuppawal', [LaporanAccountingHutangPelunasanController::class, 'loadSuppAwal']);
Route::get('/reportaccountinghutangpelunasan_loadperkiraan', [LaporanAccountingHutangPelunasanController::class, 'loadPerkiraan']);
Route::get('/reportaccountinghutangpelunasan_doReport', [LaporanAccountingHutangPelunasanController::class, 'doReport']);
Route::get('/reportaccountinghutangpelunasan_doKasharian', [LaporanAccountingHutangPelunasanController::class, 'doKasharian']);
Route::get('/reportaccountinghutangpelunasan_doInvoice', [LaporanAccountingHutangPelunasanController::class, 'doInvoice']);
Route::get('/reportaccountinghutangpelunasan_doLpb', [LaporanAccountingHutangPelunasanController::class, 'doLpb']);
Route::get('/reportaccountinghutangpelunasan_doBp', [LaporanAccountingHutangPelunasanController::class, 'doBp']);

// Hutang - LPH
Route::get('/reportaccountinghutanglph', [LaporanAccountingHutangLPHController::class, 'index']);
Route::get('/reportaccountinghutanglph_loadvalas', [LaporanAccountingHutangLPHController::class, 'loadValas']);
Route::get('/reportaccountinghutanglph_loadsuppawal', [LaporanAccountingHutangLPHController::class, 'loadSuppAwal']);
Route::get('/reportaccountinghutanglph_loadperkiraan', [LaporanAccountingHutangLPHController::class, 'loadPerkiraan']);
Route::get('/reportaccountinghutanglph_doReport', [LaporanAccountingHutangLPHController::class, 'doReport']);
Route::get('/reportaccountinghutanglph_doKasharian', [LaporanAccountingHutangLPHController::class, 'doKasharian']);
Route::get('/reportaccountinghutanglph_doInvoice', [LaporanAccountingHutangLPHController::class, 'doInvoice']);
Route::get('/reportaccountinghutanglph_doLpb', [LaporanAccountingHutangLPHController::class, 'doLpb']);
Route::get('/reportaccountinghutanglph_doBp', [LaporanAccountingHutangLPHController::class, 'doBp']);

// Hutang - UMUR
Route::get('/reportaccountinghutangumur', [LaporanAccountingHutangUmurController::class, 'index']);
Route::get('/reportaccountinghutangumur_loadvalas', [LaporanAccountingHutangUmurController::class, 'loadValas']);
Route::get('/reportaccountinghutangumur_loadsuppawal', [LaporanAccountingHutangUmurController::class, 'loadSuppAwal']);
Route::get('/reportaccountinghutangumur_loadperkiraan', [LaporanAccountingHutangUmurController::class, 'loadPerkiraan']);
Route::get('/reportaccountinghutangumur_doReport', [LaporanAccountingHutangUmurController::class, 'doReport']);
Route::get('/reportaccountinghutangumur_doKasharian', [LaporanAccountingHutangUmurController::class, 'doKasharian']);
Route::get('/reportaccountinghutangumur_doInvoice', [LaporanAccountingHutangUmurController::class, 'doInvoice']);
Route::get('/reportaccountinghutangumur_doLpb', [LaporanAccountingHutangUmurController::class, 'doLpb']);
Route::get('/reportaccountinghutangumur_doBp', [LaporanAccountingHutangUmurController::class, 'doBp']);


// Hutang - Outstanding Nota
Route::get('/reportaccountinghutangoutstandingnota', [LaporanAccountingHutangOutstandingNotaController::class, 'index']);
Route::get('/reportaccountinghutangoutstandingnota_loadvalas', [LaporanAccountingHutangOutstandingNotaController::class, 'loadValas']);
Route::get('/reportaccountinghutangoutstandingnota_loadsuppawal', [LaporanAccountingHutangOutstandingNotaController::class, 'loadSuppAwal']);
Route::get('/reportaccountinghutangoutstandingnota_loadperkiraan', [LaporanAccountingHutangOutstandingNotaController::class, 'loadPerkiraan']);
Route::get('/reportaccountinghutangoutstandingnota_doReport', [LaporanAccountingHutangOutstandingNotaController::class, 'doReport']);
Route::get('/reportaccountinghutangoutstandingnota_doKasharian', [LaporanAccountingHutangOutstandingNotaController::class, 'doKasharian']);
Route::get('/reportaccountinghutangoutstandingnota_doInvoice', [LaporanAccountingHutangOutstandingNotaController::class, 'doInvoice']);
Route::get('/reportaccountinghutangoutstandingnota_doLpb', [LaporanAccountingHutangOutstandingNotaController::class, 'doLpb']);
Route::get('/reportaccountinghutangoutstandingnota_doBp', [LaporanAccountingHutangOutstandingNotaController::class, 'doBp']);


// Hutang - LHPJT
Route::get('/reportaccountinghutanglhpjt', [LaporanAccountingHutangLHPJTController::class, 'index']);
Route::get('/reportaccountinghutanglhpjt_loadvalas', [LaporanAccountingHutangLHPJTController::class, 'loadValas']);
Route::get('/reportaccountinghutanglhpjt_loadsuppawal', [LaporanAccountingHutangLHPJTController::class, 'loadSuppAwal']);
Route::get('/reportaccountinghutanglhpjt_loadperkiraan', [LaporanAccountingHutangLHPJTController::class, 'loadPerkiraan']);
Route::get('/reportaccountinghutanglhpjt_doReport', [LaporanAccountingHutangLHPJTController::class, 'doReport']);
Route::get('/reportaccountinghutanglhpjt_doKasharian', [LaporanAccountingHutangLHPJTController::class, 'doKasharian']);
Route::get('/reportaccountinghutanglhpjt_doInvoice', [LaporanAccountingHutangLHPJTController::class, 'doInvoice']);
Route::get('/reportaccountinghutanglhpjt_doLpb', [LaporanAccountingHutangLHPJTController::class, 'doLpb']);
Route::get('/reportaccountinghutanglhpjt_doBp', [LaporanAccountingHutangLHPJTController::class, 'doBp']);


// Hutang - LPH TO
Route::get('/reportaccountinghutanglphto', [LaporanAccountingHutangLPHTOController::class, 'index']);
Route::get('/reportaccountinghutanglphto_loadvalas', [LaporanAccountingHutangLPHTOController::class, 'loadValas']);
Route::get('/reportaccountinghutanglphto_loadsuppawal', [LaporanAccountingHutangLPHTOController::class, 'loadSuppAwal']);
Route::get('/reportaccountinghutanglphto_loadperkiraan', [LaporanAccountingHutangLPHTOController::class, 'loadPerkiraan']);
Route::get('/reportaccountinghutanglphto_doReport', [LaporanAccountingHutangLPHTOController::class, 'doReport']);
Route::get('/reportaccountinghutanglphto_doKasharian', [LaporanAccountingHutangLPHTOController::class, 'doKasharian']);
Route::get('/reportaccountinghutanglphto_doInvoice', [LaporanAccountingHutangLPHTOController::class, 'doInvoice']);
Route::get('/reportaccountinghutanglphto_doLpb', [LaporanAccountingHutangLPHTOController::class, 'doLpb']);
Route::get('/reportaccountinghutanglphto_doBp', [LaporanAccountingHutangLPHTOController::class, 'doBp']);


// Piutang - Kartu
Route::get('/reportaccountingpiutangkartu', [LaporanAccountingPiutangKartuController::class, 'index']);
Route::get('/reportaccountingpiutangkartu_loadvalas', [LaporanAccountingPiutangKartuController::class, 'loadValas']);
Route::get('/reportaccountingpiutangkartu_loadsuppawal', [LaporanAccountingPiutangKartuController::class, 'loadSuppAwal']);
Route::get('/reportaccountingpiutangkartu_loadperkiraan', [LaporanAccountingPiutangKartuController::class, 'loadPerkiraan']);
Route::get('/reportaccountingpiutangkartu_doReport', [LaporanAccountingPiutangKartuController::class, 'doReport']);
Route::get('/reportaccountingpiutangkartu_doKasharian', [LaporanAccountingPiutangKartuController::class, 'doKasharian']);
Route::get('/reportaccountingpiutangkartu_doInvoice', [LaporanAccountingPiutangKartuController::class, 'doInvoice']);
Route::get('/reportaccountingpiutangkartu_doLpb', [LaporanAccountingPiutangKartuController::class, 'doLpb']);

// Piutang - Outstanding JT
Route::get('/reportaccountingpiutangoutstandingJT', [LaporanAccountingPiutangOutstandingJTController::class, 'index']);
Route::get('/reportaccountingpiutangoutstandingJT_loadvalas', [LaporanAccountingPiutangOutstandingJTController::class, 'loadValas']);
Route::get('/reportaccountingpiutangoutstandingJT_loadsuppawal', [LaporanAccountingPiutangOutstandingJTController::class, 'loadSuppAwal']);
Route::get('/reportaccountingpiutangoutstandingJT_loadlokasi', [LaporanAccountingPiutangOutstandingJTController::class, 'loadLokasi']);
Route::get('/reportaccountingpiutangoutstandingJT_loadcustomer', [LaporanAccountingPiutangOutstandingJTController::class, 'loadCustomer']);
Route::get('/reportaccountingpiutangoutstandingJT_loadperkiraan', [LaporanAccountingPiutangOutstandingJTController::class, 'loadPerkiraan']);
Route::get('/reportaccountingpiutangoutstandingJT_doReport', [LaporanAccountingPiutangOutstandingJTController::class, 'doReport']);
Route::get('/reportaccountingpiutangoutstandingJT_doKasharian', [LaporanAccountingPiutangOutstandingJTController::class, 'doKasharian']);
Route::get('/reportaccountingpiutangoutstandingJT_doInvoice', [LaporanAccountingPiutangOutstandingJTController::class, 'doInvoice']);
Route::get('/reportaccountingpiutangoutstandingJT_doLpb', [LaporanAccountingPiutangOutstandingJTController::class, 'doLpb']);
Route::get('/reportaccountingpiutangoutstandingJT_doBp', [LaporanAccountingPiutangOutstandingJTController::class, 'doBp']);

// Piutang - Pelunasan
Route::get('/reportaccountingpiutangpelunasan', [LaporanAccountingPiutangPelunasanController::class, 'index']);
Route::get('/reportaccountingpiutangpelunasan_loadvalas', [LaporanAccountingPiutangPelunasanController::class, 'loadValas']);
Route::get('/reportaccountingpiutangpelunasan_loadsuppawal', [LaporanAccountingPiutangPelunasanController::class, 'loadSuppAwal']);
Route::get('/reportaccountingpiutangpelunasan_loadperkiraan', [LaporanAccountingPiutangPelunasanController::class, 'loadPerkiraan']);
Route::get('/reportaccountingpiutangpelunasan_doReport', [LaporanAccountingPiutangPelunasanController::class, 'doReport']);
Route::get('/reportaccountingpiutangpelunasan_doKasharian', [LaporanAccountingPiutangPelunasanController::class, 'doKasharian']);
Route::get('/reportaccountingpiutangpelunasan_doInvoice', [LaporanAccountingPiutangPelunasanController::class, 'doInvoice']);
Route::get('/reportaccountingpiutangpelunasan_doLpb', [LaporanAccountingPiutangPelunasanController::class, 'doLpb']);

// Piutang - LPP
Route::get('/reportaccountingpiutanglpp', [LaporanAccountingPiutangLPPController::class, 'index']);
Route::get('/reportaccountingpiutanglpp_loadvalas', [LaporanAccountingPiutangLPPController::class, 'loadValas']);
Route::get('/reportaccountingpiutanglpp_loadsuppawal', [LaporanAccountingPiutangLPPController::class, 'loadSuppAwal']);
Route::get('/reportaccountingpiutanglpp_loadperkiraan', [LaporanAccountingPiutangLPPController::class, 'loadPerkiraan']);
Route::get('/reportaccountingpiutanglpp_doReport', [LaporanAccountingPiutangLPPController::class, 'doReport']);
Route::get('/reportaccountingpiutanglpp_doKartu', [LaporanAccountingPiutangLPPController::class, 'doKartu']);
Route::get('/reportaccountingpiutanglpp_doKasharian', [LaporanAccountingPiutangLPPController::class, 'doKasharian']);
Route::get('/reportaccountingpiutanglpp_doInvoice', [LaporanAccountingPiutangLPPController::class, 'doInvoice']);
Route::get('/reportaccountingpiutanglpp_doLpb', [LaporanAccountingPiutangLPPController::class, 'doLpb']);


// Piutang - UMUR
Route::get('/reportaccountingpiutangumur', [LaporanAccountingPiutangUmurController::class, 'index']);
Route::get('/reportaccountingpiutangumur_loadvalas', [LaporanAccountingPiutangUmurController::class, 'loadValas']);
Route::get('/reportaccountingpiutangumur_loadsuppawal', [LaporanAccountingPiutangUmurController::class, 'loadSuppAwal']);
Route::get('/reportaccountingpiutangumur_loadperkiraan', [LaporanAccountingPiutangUmurController::class, 'loadPerkiraan']);
Route::get('/reportaccountingpiutangumur_doReport', [LaporanAccountingPiutangUmurController::class, 'doReport']);
Route::get('/reportaccountingpiutangumur_doKasharian', [LaporanAccountingPiutangUmurController::class, 'doKasharian']);
Route::get('/reportaccountingpiutangumur_doInvoice', [LaporanAccountingPiutangUmurController::class, 'doInvoice']);
Route::get('/reportaccountingpiutangumur_doLpb', [LaporanAccountingPiutangUmurController::class, 'doLpb']);

// Piutang - SPJT
Route::get('/reportaccountingpiutangspjt', [LaporanAccountingPiutangSPJTController::class, 'index']);
Route::get('/reportaccountingpiutangspjt_loadvalas', [LaporanAccountingPiutangSPJTController::class, 'loadValas']);
Route::get('/reportaccountingpiutangspjt_loadsuppawal', [LaporanAccountingPiutangSPJTController::class, 'loadSuppAwal']);
Route::get('/reportaccountingpiutangspjt_loadperkiraan', [LaporanAccountingPiutangSPJTController::class, 'loadPerkiraan']);
Route::get('/reportaccountingpiutangspjt_doReport', [LaporanAccountingPiutangSPJTController::class, 'doReport']);
Route::get('/reportaccountingpiutangspjt_doKasharian', [LaporanAccountingPiutangSPJTController::class, 'doKasharian']);
Route::get('/reportaccountingpiutangspjt_doInvoice', [LaporanAccountingPiutangSPJTController::class, 'doInvoice']);
Route::get('/reportaccountingpiutangspjt_doLpb', [LaporanAccountingPiutangSPJTController::class, 'doLpb']);

// Piutang - LPP TO
Route::get('/reportaccountingpiutanglppto', [LaporanAccountingPiutangLPPTOController::class, 'index']);
Route::get('/reportaccountingpiutanglppto_loadvalas', [LaporanAccountingPiutangLPPTOController::class, 'loadValas']);
Route::get('/reportaccountingpiutanglppto_loadsuppawal', [LaporanAccountingPiutangLPPTOController::class, 'loadSuppAwal']);
Route::get('/reportaccountingpiutanglppto_loadperkiraan', [LaporanAccountingPiutangLPPTOController::class, 'loadPerkiraan']);
Route::get('/reportaccountingpiutanglppto_doReport', [LaporanAccountingPiutangLPPTOController::class, 'doReport']);


// DIVISI JURNAL DAN KAWAN KAWAN
Route::get('/laporanaccountingjurnal_loaddivisi',[LaporanAccountingJurnalPenerimaanKasController::class, 'loadDivisi']);
Route::get('/laporanaccounting_loadperkiraan1',[LaporanAccountingJurnalPenerimaanKasController::class, 'loadPerkiraan1']);

// JURNAL - PENERIMAAN KAS
Route::get('/laporanaccountingjurnalpenerimaankas', [LaporanAccountingJurnalPenerimaanKasController::class, 'index']);
Route::get('/laporanaccountingjurnalpenerimaankas_doReport', [LaporanAccountingJurnalPenerimaanKasController::class, 'doReport']);
Route::get('/laporanaccountingjurnalpenerimaankas_doKasharian', [LaporanAccountingJurnalPenerimaanKasController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalpenerimaankas_doInvoice', [LaporanAccountingJurnalPenerimaanKasController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalpenerimaankas_doLpb', [LaporanAccountingJurnalPenerimaanKasController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalpenerimaankas_doKasharian', [LaporanAccountingJurnalPenerimaanKasController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalpenerimaankas_doInvoice', [LaporanAccountingJurnalPenerimaanKasController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalpenerimaankas_doLpb', [LaporanAccountingJurnalPenerimaanKasController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalpenerimaankas_doBp', [LaporanAccountingJurnalPenerimaanKasController::class, 'doBp']);

// JURNAL - PENGELUARAN KAS
Route::get('/laporanaccountingjurnalpengeluarankas', [LaporanAccountingJurnalPengeluaranKasController::class, 'index']);
Route::get('/laporanaccountingjurnalpengeluarankas_doReport', [LaporanAccountingJurnalPengeluaranKasController::class, 'doReport']);
Route::get('/laporanaccountingjurnalpengeluarankas_doKasharian', [LaporanAccountingJurnalPengeluaranKasController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalpengeluarankas_doInvoice', [LaporanAccountingJurnalPengeluaranKasController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalpengeluarankas_doLpb', [LaporanAccountingJurnalPengeluaranKasController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalpengeluarankas_doKasharian', [LaporanAccountingJurnalPengeluaranKasController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalpengeluarankas_doInvoice', [LaporanAccountingJurnalPengeluaranKasController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalpengeluarankas_doLpb', [LaporanAccountingJurnalPengeluaranKasController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalpengeluarankas_doBp', [LaporanAccountingJurnalPengeluaranKasController::class, 'doBp']);

// JURNAL - PENERIMAAN BANK
Route::get('/laporanaccountingjurnalpenerimaanbank', [LaporanAccountingJurnalPenerimaanBankController::class, 'index']);
Route::get('/laporanaccountingjurnalpenerimaanbank_doReport', [LaporanAccountingJurnalPenerimaanBankController::class, 'doReport']);
Route::get('/laporanaccountingjurnalpenerimaanbank_doKasharian', [LaporanAccountingJurnalPenerimaanBankController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalpenerimaanbank_doInvoice', [LaporanAccountingJurnalPenerimaanBankController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalpenerimaanbank_doLpb', [LaporanAccountingJurnalPenerimaanBankController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalpenerimaanbank_doKasharian', [LaporanAccountingJurnalPenerimaanBankController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalpenerimaanbank_doInvoice', [LaporanAccountingJurnalPenerimaanBankController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalpenerimaanbank_doLpb', [LaporanAccountingJurnalPenerimaanBankController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalpenerimaanbank_doBp', [LaporanAccountingJurnalPenerimaanBankController::class, 'doBp']);

// JURNAL - PENGELUARAN BANK
Route::get('/laporanaccountingjurnalpengeluaranbank', [LaporanAccountingJurnalPengeluaranBankController::class, 'index']);
Route::get('/laporanaccountingjurnalpengeluaranbank_doReport', [LaporanAccountingJurnalPengeluaranBankController::class, 'doReport']);
Route::get('/laporanaccountingjurnalpengeluaranbank_doKasharian', [LaporanAccountingJurnalPengeluaranBankController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalpengeluaranbank_doInvoice', [LaporanAccountingJurnalPengeluaranBankController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalpengeluaranbank_doLpb', [LaporanAccountingJurnalPengeluaranBankController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalpengeluaranbank_doKasharian', [LaporanAccountingJurnalPengeluaranBankController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalpengeluaranbank_doInvoice', [LaporanAccountingJurnalPengeluaranBankController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalpengeluaranbank_doLpb', [LaporanAccountingJurnalPengeluaranBankController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalpengeluaranbank_doBp', [LaporanAccountingJurnalPengeluaranBankController::class, 'doBp']);

// JURNAL - MEMORIAL
Route::get('/laporanaccountingjurnalmemorial', [LaporanAccountingJurnalMemorialController::class, 'index']);
Route::get('/laporanaccountingjurnalmemorial_doReport', [LaporanAccountingJurnalMemorialController::class, 'doReport']);
Route::get('/laporanaccountingjurnalmemorial_doKasharian', [LaporanAccountingJurnalMemorialController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalmemorial_doKasharian', [LaporanAccountingJurnalMemorialController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalmemorial_doInvoice', [LaporanAccountingJurnalMemorialController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalmemorial_doLpb', [LaporanAccountingJurnalMemorialController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalmemorial_doBp', [LaporanAccountingJurnalMemorialController::class, 'doBp']);

// JURNAL - KOREKSI
Route::get('/laporanaccountingjurnalkoreksi', [LaporanAccountingJurnalKoreksiController::class, 'index']);
Route::get('/laporanaccountingjurnalkoreksi_doReport', [LaporanAccountingJurnalKoreksiController::class, 'doReport']);
Route::get('/laporanaccountingjurnalkoreksi_doKasharian', [LaporanAccountingJurnalKoreksiController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalkoreksi_doInvoice', [LaporanAccountingJurnalKoreksiController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalkoreksi_doLpb', [LaporanAccountingJurnalKoreksiController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalkoreksi_doBp', [LaporanAccountingJurnalKoreksiController::class, 'doBp']);

// JURNAL - COMPUTER
Route::get('/laporanaccountingjurnalcomputer', [LaporanAccountingJurnalComputerController::class, 'index']);
Route::get('/laporanaccountingjurnalcomputer_doReport', [LaporanAccountingJurnalComputerController::class, 'doReport']);
Route::get('/laporanaccountingjurnalcomputer_doFilter', [LaporanAccountingJurnalComputerController::class, 'doFilter']);
Route::get('/laporanaccountingjurnalcomputer_doReportFilter',[LaporanAccountingJurnalComputerController::class, 'doReportFilter']);

// JURNAL - PENUTUP
Route::get('/laporanaccountingjurnalpenutup', [LaporanAccountingJurnalPenutupController::class, 'index']);
Route::get('/laporanaccountingjurnalpenutup_doReport', [LaporanAccountingJurnalPenutupController::class, 'doReport']);
Route::get('/laporanaccountingjurnalpenutup_doKasharian', [LaporanAccountingJurnalPenutupController::class, 'doKasharian']);
Route::get('/laporanaccountingjurnalpenutup_doInvoice', [LaporanAccountingJurnalPenutupController::class, 'doInvoice']);
Route::get('/laporanaccountingjurnalpenutup_doLpb', [LaporanAccountingJurnalPenutupController::class, 'doLpb']);
Route::get('/laporanaccountingjurnalpenutup_doBp', [LaporanAccountingJurnalPenutupController::class, 'doBp']);

// BUKU BESAR
Route::get('/laporanaccountingbukubesar', [LaporanAccountingBukuBesarController::class, 'index']);
Route::get('/laporanaccountingbukubesar_doReport', [LaporanAccountingBukuBesarController::class, 'doReport']);
Route::get('/laporanaccountingbukubesar_loaddivisi', [LaporanAccountingBukuBesarController::class, 'loadDivisi']);
Route::get('/laporanaccountingbukubesar_loadperkiraan', [LaporanAccountingBukuBesarController::class, 'loadPerkiraan']);
Route::get('/laporanaccountingbukubesar_doKasharian', [LaporanAccountingBukuBesarController::class, 'doKasharian']);
Route::get('/laporanaccountingbukubesar_doInvoice', [LaporanAccountingBukuBesarController::class, 'doInvoice']);
Route::get('/laporanaccountingbukubesar_doLpb', [LaporanAccountingBukuBesarController::class, 'doLpb']);
Route::get('/laporanaccountingbukubesar_doBp', [LaporanAccountingBukuBesarController::class, 'doBp']);

// TRIAL BALANCE
Route::get('/laporanaccountingtrialbalance', [LaporanAccountingTrialBalanceController::class, 'index']);
Route::get('/laporanaccountingtrialbalance_doReport', [LaporanAccountingTrialBalanceController::class, 'doReport']);
Route::get('/laporanaccountingtrialbalance_doLedger', [LaporanAccountingTrialBalanceController::class, 'doLedger']);
Route::get('/laporanaccountingtrialbalance_doKasharian', [LaporanAccountingTrialBalanceController::class, 'doKasharian']);
Route::get('/laporanaccountingtrialbalance_doInvoice', [LaporanAccountingTrialBalanceController::class, 'doInvoice']);
Route::get('/laporanaccountingtrialbalance_doLpb', [LaporanAccountingTrialBalanceController::class, 'doLpb']);
Route::get('/laporanaccountingtrialbalance_doBp', [LaporanAccountingTrialBalanceController::class, 'doBp']);

// ACCOUNTING BIAYA
Route::get('/laporanaccountingbiaya', [LaporanAccountingBiayaController::class, 'index']);
Route::get('/laporanaccountingbiaya_doReport', [LaporanAccountingBiayaController::class, 'doReport']);
Route::get('/laporanaccountingbiaya_doFilter', [LaporanAccountingBiayaController::class, 'doFilter']);
Route::get('/laporanaccountingbiaya_doReportFilter',[LaporanAccountingBiayaController::class, 'doReportFilter']);
Route::get('/laporanaccountingbiaya_loaddivisi',[LaporanAccountingBiayaController::class, 'loadDivisi']);

// ACCOUNTING AKTIVA
Route::get('/laporanaccountingaktiva', [LaporanAccountingAktivaController::class, 'index']);
Route::get('/laporanaccountingaktiva_doReport', [LaporanAccountingAktivaController::class, 'doReport']);
Route::get('/laporanaccountingaktiva_doFilter', [LaporanAccountingAktivaController::class, 'doFilter']);
Route::get('/laporanaccountingaktiva_doReportFilter',[LaporanAccountingAktivaController::class, 'doReportFilter']);
Route::get('/laporanaccountingaktiva_loaddivisi', [LaporanAccountingAktivaController::class, 'loadDivisi']);

// BIAYA PENYUSUTAN
Route::get('/laporanaccountingbiayapenyusutan', [LaporanAccountingBiayaPenyusutanController::class, 'index']);
Route::get('/laporanaccountingbiayapenyusutan_doReport', [LaporanAccountingBiayaPenyusutanController::class, 'doReport']);
Route::get('/laporanaccountingbiayapenyusutan_loaddivisi', [LaporanAccountingBiayaPenyusutanController::class, 'loadDivisi']);

// SKB
Route::get('/laporanaccountingskb', [LaporanAccountingSKBController::class, 'index']);
Route::get('/laporanaccountingskb_doReport', [LaporanAccountingSKBController::class, 'doReport']);
Route::get('/laporanaccountingskb_doKasharian', [LaporanAccountingSKBController::class, 'doKasharian']);
Route::get('/laporanaccountingskb_doInvoice', [LaporanAccountingSKBController::class, 'doInvoice']);
Route::get('/laporanaccountingskb_doLpb', [LaporanAccountingSKBController::class, 'doLpb']);
Route::get('/laporanaccountingskb_doBp', [LaporanAccountingSKBController::class, 'doBp']);

// NERACA LAJUR
Route::get('/laporanaccountingneracalajur', [LaporanAccountingNeracaLajurController::class, 'index']);
Route::get('/laporanaccountingneracalajur_doReport', [LaporanAccountingNeracaLajurController::class, 'doReport']);
Route::get('/laporanaccountingneracalajur_doLedger', [LaporanAccountingNeracaLajurController::class, 'doLedger']);
Route::get('/laporanaccountingneracalajur_doKasharian', [LaporanAccountingNeracaLajurController::class, 'doKasharian']);
Route::get('/laporanaccountingneracalajur_doInvoice', [LaporanAccountingNeracaLajurController::class, 'doInvoice']);
Route::get('/laporanaccountingneracalajur_doLpb', [LaporanAccountingNeracaLajurController::class, 'doLpb']);
Route::get('/laporanaccountingneracalajur_doBp', [LaporanAccountingNeracaLajurController::class, 'doBp']);

// LABA RUGI
Route::get('/laporanaccountinglabarugi', [LaporanAccountingLabaRugiController::class, 'index']);
Route::get('/laporanaccountinglabarugi_doReport', [LaporanAccountingLabaRugiController::class, 'doReport']);
Route::get('/laporanaccountinglabarugi_triggerSp',[LaporanAccountingLabaRugiController::class, 'triggerSp']);
Route::get('/laporanaccountinglabarugi_loaddivisi',[LaporanAccountingLabaRugiController::class, 'loadDivisi']);
Route::get('/laporanaccountinglabarugi_doKasharian', [LaporanAccountingLabaRugiController::class, 'doKasharian']);
Route::get('/laporanaccountinglabarugi_doInvoice', [LaporanAccountingLabaRugiController::class, 'doInvoice']);
Route::get('/laporanaccountinglabarugi_doLpb', [LaporanAccountingLabaRugiController::class, 'doLpb']);
Route::get('/laporanaccountinglabarugi_doBp', [laporanaccountinglabarugiLaporanAccountingLabaRugiController::class, 'doBp']);

// LABA RUGI TAHUNAN
Route::get('/laporanaccountinglabarugitahunan', [LaporanAccountingLabaRugiTahunanController::class, 'index']);
Route::get('/laporanaccountinglabarugitahunan_doReport', [LaporanAccountingLabaRugiTahunanController::class, 'doReport']);
Route::get('/laporanaccountinglabarugitahunan_loaddivisi',[LaporanAccountingLabaRugiTahunanController::class, 'loadDivisi']);

// NERACA
Route::get('/laporanaccountingneraca', [LaporanAccountingNeracaController::class, 'index']);
Route::get('/laporanaccountingneraca_doReport', [LaporanAccountingNeracaController::class, 'doReport']);
Route::get('/laporanaccountingneraca_doFilter', [LaporanAccountingNeracaController::class, 'doFilter']);
Route::get('/laporanaccountingneraca_doReportFilter',[LaporanAccountingNeracaController::class, 'doReportFilter']);
Route::get('/laporanaccountingneraca_saldoawal',[LaporanAccountingNeracaController::class, 'doReportSaldoAwal']);
Route::get('/laporanaccountingneraca_loaddivisi',[LaporanAccountingNeracaController::class, 'loadDivisi']);

// NERACA PENUNJANG
Route::get('/laporanaccountingneracapenunjang', [LaporanAccountingNeracaPenunjangController::class, 'index']);
Route::get('/laporanaccountingneracapenunjang_doReport', [LaporanAccountingNeracaPenunjangController::class, 'doReport']);
Route::get('/laporanaccountingneracapenunjang_doFilter', [LaporanAccountingNeracaPenunjangController::class, 'doFilter']);
Route::get('/laporanaccountingneracapenunjang_doReportFilter',[LaporanAccountingNeracaPenunjangController::class, 'doReportFilter']);
Route::get('/laporanaccountingneracapenunjang_loaddivisi',[LaporanAccountingNeracaPenunjangController::class, 'loadDivisi']);

// HPP
Route::get('/laporanaccountinghpp', [LaporanAccountingHPPController::class, 'index']);
Route::get('/laporanaccountinghpp_doReport', [LaporanAccountingHPPController::class, 'doReport']);
Route::get('/laporanaccountinghpp_doFilter', [LaporanAccountingHPPController::class, 'doFilter']);
Route::get('/laporanaccountinghpp_doReportFilter',[LaporanAccountingHPPController::class, 'doReportFilter']);
Route::get('/laporanaccountinghpp_triggerSp',[LaporanAccountingHPPController::class, 'triggerSp']);
Route::get('/laporanaccountinghpp_loaddivisi',[LaporanAccountingHPPController::class, 'loadDivisi']);
