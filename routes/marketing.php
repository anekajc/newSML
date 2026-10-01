<?php

use App\Http\Controllers\Marketing\BankController;
use App\Http\Controllers\Marketing\CetakTandaTerimaController;
use App\Http\Controllers\Marketing\ClosingSOController;
use App\Http\Controllers\Marketing\FakturPajakController;
use App\Http\Controllers\Marketing\InvoiceJasaController;
use App\Http\Controllers\Marketing\InvoicePenjualanController;
use App\Http\Controllers\Marketing\KreditNoteController;
use App\Http\Controllers\Marketing\NotaReturPenjualanController;
use App\Http\Controllers\Marketing\PembelianPermintaanNonAgenController;
use App\Http\Controllers\Marketing\PenawaranSOController;
use App\Http\Controllers\Marketing\PerformanceController;
use App\Http\Controllers\Marketing\PerintahReturJualController;
use App\Http\Controllers\Marketing\PerintahReturJualMinusController;
use App\Http\Controllers\Marketing\ReturPenjualanGudangController;
use App\Http\Controllers\Marketing\ReturSuratJalanController;
use App\Http\Controllers\Marketing\SOController;
use App\Http\Controllers\Marketing\SuratJalanController;
use App\Http\Controllers\Marketing\TerimaPoCustomerController;
use App\Http\Controllers\Marketing\UangMukaJualController;
use App\Http\Controllers\Marketing\UbahPOCustomerController;
use App\Http\Controllers\Marketing\VerifikasiBarangController;
use App\Http\Controllers\Marketing\VerifikasiPenawaranController;

use Illuminate\Support\Facades\Route;


// KAS
Route::middleware('auth')->group(function () {

  //PENAWARAN SO
Route::get('/penawaranso', [PenawaranSOController::class, 'index']);
Route::post('/penawaransocekhargaoto', [PenawaranSOController::class, 'cekHargaOto']);
Route::get('/penawaransoprint', [PenawaranSOController::class, 'spCetak']);
Route::get('/penawaransoloadall', [PenawaranSOController::class, 'loadAll']);
Route::get('/penawaransoloadheader', [PenawaranSOController::class, 'loadHeader']);
Route::get('/penawaransosimpanheader', [PenawaranSOController::class, 'simpanHeader']);

Route::get('/penawaransolistpelanggan', [PenawaranSOController::class, 'listPelanggan']); //
Route::get('/penawaransolistttd', [PenawaranSOController::class, 'listttd']);  //

Route::get('/penawaransolistbarangfoc', [PenawaranSOController::class, 'listBarangFOC']);
Route::post('/penawaransolistbarangnosominus', [PenawaranSOController::class, 'listBarangNonFOC1']); //
Route::get('/penawaransolistbarangnosoplus', [PenawaranSOController::class, 'listBarangNonFOC2']);
Route::post('/penawaransolistpwo', [PenawaranSOController::class, 'listPWO']);
Route::post('/penawaransolistgudang', [PenawaranSOController::class, 'listGudang']);
Route::post('/penawaransolistnoso', [PenawaranSOController::class, 'listNoSo']);
Route::post('/penawaransolistlokasipenerima', [PenawaranSOController::class, 'listLokasiPenerima']);
Route::post('/penawaransospadd', [PenawaranSOController::class, 'spAdd']);
Route::post('/penawaransocekharga', [PenawaranSOController::class, 'spCekHarga']);
Route::post('/penawaransogetdetail', [PenawaranSOController::class, 'getDetail']);
Route::post('/cekpenawaransoDet', [PenawaranSOController::class, 'cekPoDet']);
Route::post('/penawaransoceksatuanbarang', [PenawaranSOController::class, 'cekSatuanBarang']);
Route::post('/penawaransocekotorisasi', [PenawaranSOController::class, 'cekOtorisasi']);
Route::post('/penawaransoupdateotorisasi', [PenawaranSOController::class, 'updateOtorisasi']);
Route::post('/penawaransoupdatebatalotorisasi', [PenawaranSOController::class, 'updateBatalOtorisasi']);
Route::post('/penawaransospupdatepo', [PenawaranSOController::class, 'spUpdatePO']);
Route::post('/penawaransoonchangeheader', [PenawaranSOController::class, 'onChangeHeader']);
Route::post('/penawaransolistbarangnosominusallso', [PenawaranSOController::class, 'listBarangNonFOC1AllSO']);
Route::get('/penawaransolistvalas', [PenawaranSOController::class, 'listValas']);
Route::get('/penawaransocheckhargaddd', [PenawaranSOController::class, 'CheckHargaAdd']);
Route::get('/penawaransoprint', [PenawaranSOController::class, 'CheckHargaAdd']);


  // VERIF PENAWARAN
  Route::get('/verifikasipenawaran', [VerifikasiPenawaranController::class, 'index']);
  Route::post('/verifikasipenawaranotorisasi', [VerifikasiPenawaranController::class, 'spOtorisasi']);
  Route::post('/verifikasipenawaranbatalotorisasi', [VerifikasiPenawaranController::class, 'spBatalOtorisasi']);
  Route::get('/verifikasipenawaranloadall', [VerifikasiPenawaranController::class, 'loadAll']);
  Route::get('/verifikasipenawaranloadallinfo', [VerifikasiPenawaranController::class, 'loadAllInfo']);
  Route::get('/verifikasipenawaranloadheader', [VerifikasiPenawaranController::class, 'loadHeader']);
  Route::get('/verifikasipenawaransimpanheader', [VerifikasiPenawaranController::class, 'simpanHeader']);
  Route::post('/verifikasipenawarandetailbarang', [VerifikasiPenawaranController::class, 'detailBarang']);
  Route::post('/get-barang', [VerifikasiPenawaranController::class, 'getBarang']);
  Route::post('/get-customer', [VerifikasiPenawaranController::class, 'getCustomer']);

//   // VERIF BARANG
//   Route::get('/verifikasibarang', [VerifikasiBarangController::class, 'index']);
//   Route::post('/verifikasibarangotorisasi', [VerifikasiBarangController::class, 'spOtorisasi']);
//   Route::post('/verifikasibarangbatalotorisasi', [VerifikasiBarangController::class, 'spBatalOtorisasi']);
//   Route::get('/verifikasibarangloadall', [VerifikasiBarangController::class, 'loadAll']);
//   Route::get('/verifikasibarangloadallinfo', [VerifikasiBarangController::class, 'loadAllInfo']);
//   Route::post('/verifikasibarangdetailbarang', [VerifikasiBarangController::class, 'detailBarang']);
//   Route::post('/verifikasibarangdetailbarangx', [VerifikasiBarangController::class, 'detailBarangx']);
//   Route::get('/verifikasibaranglistbarang', [VerifikasiBarangController::class, 'listBarang']);
//   Route::post('/verifikasibarangspadd', [VerifikasiBarangController::class, 'spOtorisasi']);

//   // Terima PO Cust
//   Route::get('/terimapocustomer', [TerimaPoCustomerController::class, 'index']);
//   Route::post('/terimapocustomerspadd', [TerimaPoCustomerController::class, 'spAdd']);
//   Route::get('/terimapocustomerloadall', [TerimaPoCustomerController::class, 'loadAll']);
//   Route::get('/terimapocustomerpelanggan', [TerimaPoCustomerController::class, 'getCustomer']);
//   Route::post('/terimapocustomerspclose', [TerimaPoCustomerController::class, 'spClose']);

//   // Ubah PO Cust
//   Route::get('/ubahpocust', [UbahPOCustomerController::class, 'index']);
//   Route::get('/ubahPoCustomerListData', [UbahPOCustomerController::class, 'listData']);

  // SO
  Route::get('/so', [SOController::class, 'index']);
  Route::post('/soloadall', [SOController::class, 'loadAll']);
  Route::post('/socekhargaoto', [SOController::class, 'cekHargaOto']);
Route::get('/socheckhargaddd', [SOController::class, 'SOCheckHargaAdd']);

  Route::get('/sospnobukti', [SOController::class, 'getNoBukti']);
  Route::get('/solistpelanggan', [SOController::class, 'listPelanggan']);
  Route::get('/solistsales', [SOController::class, 'listSales']);
  Route::get('/solistvalas', [SOController::class, 'listValas']);
  Route::get('/solistbackoffice', [SOController::class, 'listBackOffice']);
  Route::get('/solistbarang', [SOController::class, 'listBarang']);
  Route::post('/solistnopo', [SOController::class, 'listNoPo']);

  Route::post('/solistnopotambahso', [SOController::class, 'listNoPoTambahSO']);


  Route::post('/solistalamatkirim', [SOController::class, 'listAlamatKirim']);
  Route::post('/solistpic', [SOController::class, 'listPIC']);
  Route::post('/solistrefpr', [SOController::class, 'listRefPR']);
  Route::post('/solistnopenyerahan', [SOController::class, 'listNoPenyerahan']);
  Route::post('/solistbarangrefpr', [SOController::class, 'listBarangRefPR']);
  Route::post('/sospaddtambahsoall', [SOController::class, 'spAddTambahSOAll']);
  Route::post('/sogetdetailtambahsoall', [SOController::class, 'getDetailTambahSOAll']);

  Route::post('/solistlokasipenerima', [SOController::class, 'listLokasiPenerima']);
  Route::post('/socekkredithari', [SOController::class, 'cekKreditHari']);
  Route::post('/sospadd', [SOController::class, 'spAdd']);

  Route::post('/sospaddtambahso', [SOController::class, 'spAddTambahSO']);


  Route::post('/socekharga', [SOController::class, 'spCekHarga']);
  Route::get('/socheckhargaddd', [SOController::class, 'SOCheckHargaAdd']);
  Route::post('/sogetdetail', [SOController::class, 'getDetail']);

  Route::post('/sogetdetailtambahso', [SOController::class, 'getDetailTambahSO']);


  Route::post('/sogetsatuanbarang', [SOController::class, 'getSatuanBarang']);
  Route::post('/soonchangeheader', [SOController::class, 'onChangeHeader']);
  Route::post('/sospupdateso', [SOController::class, 'spUpdateSO']);
  Route::post('/soupdateotorisasi', [SOController::class, 'updateOtorisasi']);
  Route::post('/soupdatebatalotorisasi', [SOController::class, 'updateBatalOtorisasi']);
  Route::post('/socekotorisasi', [SOController::class, 'cekOtorisasi']);
  Route::post('/sodetailbarangall', [SOController::class, 'detailBarangAll']);
  Route::post('/sodetailCetak', [SOController::class, 'getDetailCetak']);
  Route::post('/soupdatecbd', [SOController::class, 'updateCBD']);
  Route::post('/soloadsofilter', [SOController::class, 'loadSOFilter']);
  Route::get('/soloadheader', [SOController::class, 'loadHeader']);
  Route::get('/sosimpanheader', [SOController::class, 'simpanHeader']);


  // closing so
  Route::get('/closingso', [ClosingSOController::class, 'index']);
  Route::post('/closingsospclosingso', [ClosingSOController::class, 'spClosingSO']);
  Route::post('/closingsospopenso', [ClosingSOController::class, 'spOpenSO']);
  Route::get('/closingsoloadall', [ClosingSOController::class, 'loadAll']);

  // performance
  Route::get('/performance', [PerformanceController::class, 'index']);
  Route::post('/performancegetdetail', [PerformanceController::class, 'getDetail']);
  Route::get('/performanceloadall', [PerformanceController::class, 'loadAll']);
  Route::post('/performancespotoperf', [PerformanceController::class, 'spOtoPerf']);
  Route::post('/performancespbatalotoperf', [PerformanceController::class, 'spBatalOtoPerf']);
  Route::post('/performancedetailCetak', [PerformanceController::class, 'getDetailCetak']);

  // UANG MUKA JUAL
  Route::get('/uangmukajualprint', [UangMukaJualController::class, 'spCetak']);
  Route::get('/uangmukajual', [UangMukaJualController::class, 'index']);
  Route::post('/uangmukajualgetdetail', [UangMukaJualController::class, 'getDetail']);
  Route::post('/uangmukajualspnobukti', [UangMukaJualController::class, 'getNoBukti']);
  Route::post('/uangmukajualspadd', [UangMukaJualController::class, 'spAdd']);
  Route::post('/uangmukajualgetdetailumj', [UangMukaJualController::class, 'getDetailUMJ']);
  Route::get('/uangmukajualloadall', [UangMukaJualController::class, 'loadAll']);
  Route::post('/uangmukajualspoto', [UangMukaJualController::class, 'spOto']);
  Route::post('/uangmukajualspbataloto', [UangMukaJualController::class, 'spBatalOto']);

  // suratjalan
  Route::get('/suratjalan', [SuratJalanController::class, 'index']);
  Route::post('/suratjalangetdetail', [SuratJalanController::class, 'getDetail']);
  Route::get('/suratjalanspnobukti', [SuratJalanController::class, 'getNoBukti']);
  Route::get('/suratjalanlistgudang', [SuratJalanController::class, 'listGudang']);
  Route::get('/suratjalanlistekspedisi', [SuratJalanController::class, 'listEkspedisi']);
  Route::post('/suratjalanspadd', [SuratJalanController::class, 'spAdd']);
  Route::get('/suratjalanloadall', [SuratJalanController::class, 'loadAll']);
  Route::get('/suratjalanpaginate', [SuratJalanController::class, 'paginateOutstanding']);
  Route::post('/suratjalanspotorisasi', [SuratJalanController::class, 'spOtorisasi']);
  Route::post('/suratjalanspbatalotorisasi', [SuratJalanController::class, 'spBatalOtorisasi']);
  Route::post('/suratjalangetdetailkoreksi', [SuratJalanController::class, 'getDetailKoreksi']);
  Route::post('/suratjalanspkoreksi', [SuratJalanController::class, 'spKoreksi']);
  Route::post('/suratjalanlistbarang', [SuratJalanController::class, 'listBarang']);
  Route::post('/suratJalanAddKirimTerima', [SuratJalanController::class, 'spAddKirimTerima']);
  Route::post('/suratJalanAddKirimTerimaAcc', [SuratJalanController::class, 'spAddKirimTerimaAcc']);
  Route::get('/suratjalanloadheader', [SuratJalanController::class, 'loadHeader']);
  Route::get('/suratjalansimpanheader', [SuratJalanController::class, 'simpanHeader']);

  // retursuratjalan
  Route::get('/retursuratjalan', [ReturSuratJalanController::class, 'index']);
  Route::get('/retursuratjalanspnobukti', [ReturSuratJalanController::class, 'getNoBukti']);
  Route::post('/retursuratjalanlistsj', [ReturSuratJalanController::class, 'listSJ']);
  Route::get('/retursuratjalanlistcustsuppbaru', [ReturSuratJalanController::class, 'listCustSuppBaru']);
  Route::post('/retursuratjalanlistbarang', [ReturSuratJalanController::class, 'listBarang']);
  Route::post('/retursuratjalanspadd', [ReturSuratJalanController::class, 'spAdd']);
  Route::post('/retursuratjalanspdetail', [ReturSuratJalanController::class, 'getDetail']);
  Route::get('/retursuratjalanloadall', [ReturSuratJalanController::class, 'loadAll']);
  Route::post('/retursuratjalanspoto', [ReturSuratJalanController::class, 'spOtorisasi']);
  Route::post('/retursuratjalanspbataloto', [ReturSuratJalanController::class, 'spBatalOtorisasi']);
  Route::get('/retursuratjalanloadheader', [ReturSuratJalanController::class, 'loadHeader']);
  Route::get('/retursuratjalansimpanheader', [ReturSuratJalanController::class, 'simpanHeader']);

  // Invoice Penjualan
  Route::get('/invoicepenjualan', [InvoicePenjualanController::class, 'index']);
  Route::post('/invoicepenjualanlistso', [InvoicePenjualanController::class, 'getListSO']);
  Route::post('/invoicepenjualanspadd', [InvoicePenjualanController::class, 'spAdd']);
  Route::post('/invoicepenjualanspdetailkoreksi', [InvoicePenjualanController::class, 'spDetailKoreksi']);
  Route::post('/invoicepenjualanspdelete', [InvoicePenjualanController::class, 'spDelete']);
  Route::get('/invoicepenjualanloadall', [InvoicePenjualanController::class, 'loadAll']);
  Route::post('/invoicepenjualanspotorisasi', [InvoicePenjualanController::class, 'spOtorisasi']);
  Route::post('/invoicepenjualanspbatalotorisasi', [InvoicePenjualanController::class, 'spBatalOtorisasi']);
  Route::post('/invoicepenjualanonchangeheader', [InvoicePenjualanController::class, 'onChangeHeader']);
  Route::post('/invoicepenjualanonchangedetail', [InvoicePenjualanController::class, 'onChangeDetail']);
  Route::post('/invoicepenjualandetailCetak', [InvoicePenjualanController::class, 'getDetailCetak']);
  Route::post('/invoicepenjualandetailCetakJBG', [InvoicePenjualanController::class, 'getDetailCetak']);
  Route::post('/invoicepenjualandetailCetak3', [InvoicePenjualanController::class, 'getDetailCetak']);
  Route::post('/ambilNomorSPB', [InvoicePenjualanController::class, 'getDetailCetakSPB']);
  Route::post('/invoicePenjualanPrintSPB', [InvoicePenjualanController::class, 'getDetailPenerimaancetak']);
  Route::post('/invoicepenjualangetdetail', [InvoicePenjualanController::class, 'spDetailKoreksi']);
  Route::post('/invoicepenjualangetspbdetail', [InvoicePenjualanController::class, 'spAddDetailKoreksi']);
  Route::get('/invoicepenjualangetlistinvoicecetak', [InvoicePenjualanController::class, 'getListInvoiceCetak']);
  Route::post('/invoicepenjualandetailcetakall', [InvoicePenjualanController::class, 'getDetailCetakAll']);

  // Invoice Jasa
  Route::get('/invoicejasa', [InvoiceJasaController::class, 'index']);
  Route::get('/invoicejasaspnobukti', [InvoiceJasaController::class, 'getNoBukti']);
  Route::get('/invoicejasalistcustomer', [InvoiceJasaController::class, 'listCustomer']);
  Route::get('/invoicejasalistsales', [InvoiceJasaController::class, 'listSales']);
  Route::post('/invoicejasalistlokasipenerima', [InvoiceJasaController::class, 'listLokasiPenerima']);
  Route::post('/invoicejasaspadd', [InvoiceJasaController::class, 'spAdd']);
  Route::post('/invoicejasaspdetail', [InvoiceJasaController::class, 'spDetail']);
  Route::post('/invoicejasaonchangeheader', [InvoiceJasaController::class, 'onChangeHeader']);
  Route::post('/invoicejasaonchangedetail', [InvoiceJasaController::class, 'onChangeDetail']);
  Route::get('/invoicejasaloadall', [InvoiceJasaController::class, 'loadAll']);

  // Faktur Pajak

  Route::get('/fakturpajak', [FakturPajakController::class, 'index']);
  Route::post('/fakturpajakspadd', [FakturPajakController::class, 'spAdd']);
  Route::post('/fakturpajakspdelete', [FakturPajakController::class, 'spDelete']);
  Route::post('/fakturpajakloadall', [FakturPajakController::class, 'loadAll']);
  Route::post('/fakturpajakimportexcel', [FakturPajakController::class, 'importExcel']);
  Route::get('/fakturpajakexportexcel', [FakturPajakController::class, 'spExport']);

  // Perintah Retur Jual
  Route::get('/perintahreturjualcetak', [PerintahReturJualController::class, 'spCetak']);
  Route::get('/perintahreturjual', [PerintahReturJualController::class, 'index']);
  Route::get('/perintahreturjualspnobukti', [PerintahReturJualController::class, 'getNoBukti']);
  // Route::post('/newsetupperiodekerjaupdate', [PerintahReturJualMinusController::class, 'updatePeriodeKerja']);
  Route::get('/perintahreturjuallistcustomer', [PerintahReturJualController::class, 'listCustomer']);
  Route::post('/perintahreturjuallistnoinvoice', [PerintahReturJualController::class, 'listNoInvoice']);
  Route::post('/perintahreturjuallistbarang', [PerintahReturJualController::class, 'listBarang']);
  Route::post('/perintahreturjuallistnobeli', [PerintahReturJualController::class, 'listNoBeli']);
  Route::post('/perintahreturjualgetdetail', [PerintahReturJualController::class, 'getDetail']);
  Route::post('/perintahreturjualspadd', [PerintahReturJualController::class, 'spAdd']);
  Route::get('/perintahreturjualloadall', [PerintahReturJualController::class, 'loadAll']);
  Route::post('/perintahreturjualspotorisasi', [PerintahReturJualController::class, 'spOtorisasi']);
  Route::post('/perintahreturjualspbatalotorisasi', [PerintahReturJualController::class, 'spBatalOtorisasi']);



  // Perintah Retur Jual
  Route::get('/perintahreturjualminus', [PerintahReturJualMinusController::class, 'index']);
  Route::get('/perintahreturjualminusspnobukti', [PerintahReturJualMinusController::class, 'getNoBukti']);
  // Route::post('/newsetupperiodekerjaupdate', [PerintahReturJualMinusController::class, 'updatePeriodeKerja']);
  Route::get('/perintahreturjualminuslistcustomer', [PerintahReturJualMinusController::class, 'listCustomer']);
  Route::get('/perintahreturjualminuslistgudang', [PerintahReturJualMinusController::class, 'listGudang']);
  Route::post('/perintahreturjualminuslistnoinvoice', [PerintahReturJualMinusController::class, 'listNoInvoice']);
  Route::get('/perintahreturjualminuslistbarang', [PerintahReturJualMinusController::class, 'listBarang']);
  Route::post('/perintahreturjualminuslistnobeli', [PerintahReturJualMinusController::class, 'listNoBeli']);
  Route::post('/perintahreturjualminusgetdetail', [PerintahReturJualMinusController::class, 'getDetail']);
  Route::post('/perintahreturjualminusspadd', [PerintahReturJualMinusController::class, 'spAdd']);
  Route::get('/perintahreturjualminusloadall', [PerintahReturJualMinusController::class, 'loadAll']);
  Route::post('/perintahreturjualminusspotorisasi', [PerintahReturJualMinusController::class, 'spOtorisasi']);
  Route::post('/perintahreturjualminusspbatalotorisasi', [PerintahReturJualMinusController::class, 'spBatalOtorisasi']);

  // Retur Penjualan Gudang
  Route::get('/returpenjualangudang', [ReturPenjualanGudangController::class, 'index']);
  Route::get('/returpenjualangudangspnobukti', [ReturPenjualanGudangController::class, 'getNoBukti']);
  Route::post('/returpenjualangudanggetdetail', [ReturPenjualanGudangController::class, 'getDetail']);
  Route::post('/returpenjualangudangspadd', [ReturPenjualanGudangController::class, 'spAdd']);
  Route::post('/returpenjualangudanggetdetailpenerimaan', [ReturPenjualanGudangController::class, 'getDetailPenerimaan']);
  Route::post('/returpenjualangudangspkoreksi', [ReturPenjualanGudangController::class, 'spKoreksi']);
  Route::get('/returpenjualangudangloadall', [ReturPenjualanGudangController::class, 'loadAll']);
  Route::post('/returpenjualangudangspotorisasi', [ReturPenjualanGudangController::class, 'spOtorisasi']);
  Route::post('/returpenjualangudangspbatalotorisasi', [ReturPenjualanGudangController::class, 'spBatalOtorisasi']);
  Route::post('/returpenjualangudangonchangeheader', [ReturPenjualanGudangController::class, 'onChangeHeader']);


  // NOTA RETUR Penjualan
  Route::get('/notareturpenjualan', [NotaReturPenjualanController::class, 'index']);
  Route::post('/notareturpenjualangetdetail', [NotaReturPenjualanController::class, 'getDetail']);
  Route::post('/notareturpenjualangetdetailnew', [NotaReturPenjualanController::class, 'getDetailNew']);
  Route::get('/notareturpenjualanspnobukti', [NotaReturPenjualanController::class, 'getNoBukti']);
  Route::post('/notareturpenjualanlistbarang', [NotaReturPenjualanController::class, 'listBarang']);
  Route::get('/notareturpenjualanlistvalas', [NotaReturPenjualanController::class, 'listValas']);
  Route::post('/notareturpenjualanspadd', [NotaReturPenjualanController::class, 'spAdd']);
  Route::post('/notareturpenjualanspaddall', [NotaReturPenjualanController::class, 'spAddAll']);
  Route::post('/notareturpenjualanspaddallnew', [NotaReturPenjualanController::class, 'spAddAllNew']);
  Route::post('/notareturpenjualancekkredithari', [NotaReturPenjualanController::class, 'cekKreditHari']);
  Route::post('/notareturpenjualanspdeleteall', [NotaReturPenjualanController::class, 'spDeleteAll']);
  Route::post('/notareturpenjualangetdetailpenerimaan', [NotaReturPenjualanController::class, 'getDetailPenerimaan']);
  Route::get('/notareturpenjualanloadall', [NotaReturPenjualanController::class, 'loadAll']);
  Route::post('/notareturpenjualanspotorisasi', [NotaReturPenjualanController::class, 'spOtorisasi']);
  Route::post('/notareturpenjualanspbatalotorisasi', [NotaReturPenjualanController::class, 'spBatalOtorisasi']);

  // KREDITNOTE
  Route::get('/kreditnote', [KreditNoteController::class, 'index']);
  Route::get('/kreditnotespnobukti', [KreditNoteController::class, 'getNoBukti']);
  Route::get('/kreditnotelistcustomer', [KreditNoteController::class, 'listCustomer']);
  Route::post('/kreditnotelistinvoice', [KreditNoteController::class, 'listInvoice']);
  Route::post('/kreditnotespadd', [KreditNoteController::class, 'spAdd']);
  Route::post('/kreditnotespdetail', [KreditNoteController::class, 'getDetail']);
  Route::post('/kreditnotespkoreksi', [KreditNoteController::class, 'spKoreksi']);
  Route::get('/kreditnoteloadall', [KreditNoteController::class, 'loadAll']);
  Route::post('/kreditnotespotorisasi', [KreditNoteController::class, 'spOtorisasi']);
  Route::post('/kreditnotespbatalotorisasi', [KreditNoteController::class, 'spBatalOtorisasi']);




//   // // PEMBELIAN PERMINTAAN NON AGEN
//   // Route::get('/pembelianpermintaannonagen', [PembelianPermintaanNonAgenController::class, 'index']);
//   // Route::get('/pembelianpermintaannonagenspnobukti', [PembelianPermintaanNonAgenController::class, 'getNoBukti']);
//   // Route::get('/pembelianpermintaannonagenlistbarang', [PembelianPermintaanNonAgenController::class, 'listBarang']);
//   // Route::post('/pembelianpermintaannonagenspadd', [PembelianPermintaanNonAgenController::class, 'spAdd']);
//   // Route::get('/pembelianpermintaannonagenspdetail', [PembelianPermintaanNonAgenController::class, 'spDetail']);
//   // Route::get('/pembelianpermintaannonagenloadall', [PembelianPermintaanNonAgenController::class, 'loadAll']);
//   // Route::post('/pembelianpermintaannonagenspdelete', [PembelianPermintaanNonAgenController::class, 'spDelete']);
//   // Route::get('/pembelianpermintaannonagenlistdepartemen', [PembelianPermintaanNonAgenController::class, 'listDepartemen']);
//   // Route::post('/pembelianpermintaannonagenupdateotorisasi', [PembelianPermintaanNonAgenController::class, 'updateOtorisasi']);




// cetak tanda terima

// Route::post('/cetakpengajuandphspnobukti', [BankController::class, 'getNoBukti']);
Route::get('/cetaktandaterima', [CetakTandaTerimaController::class, 'index']);
Route::post('/cetaktandaterimadetailkoreksi', [CetakTandaTerimaController::class, 'detailKoreksi']);
Route::post('/cetaktandaterimadetailoutstanding', [CetakTandaTerimaController::class, 'getDetailOutstanding']);
Route::post('/cetaktandaterimalistproses', [CetakTandaTerimaController::class, 'listProses']);

Route::post('/cetaktandaterimaspadd', [CetakTandaTerimaController::class, 'spAdd']);
Route::post('/cetaktandaterimaspkoreksi', [CetakTandaTerimaController::class, 'spKoreksi']);
Route::post('/cetaktandaterimaspproses', [CetakTandaTerimaController::class, 'spProses']);
Route::get('/cetaktandaterimaloadall' , 'CetakTandaTerimaController@loadAll' )->middleware('auth');
Route::post('/cetaktandaterimadetailCetak', [CetakTandaTerimaController::class, 'getDetailCetak']);


});