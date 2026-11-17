<?php

// Rute menu WMS (menu 21 "WMS" di DBMENUWEB), dipindah dari ajcwms.
// Controller: App\Http\Controllers\Wms, view: resources/views/wms, akses lewat DBMENUWEB/DBFLMENUWEB (kode 21xxxx).

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Wms\GudangController;
use App\Http\Controllers\Wms\BarangJadiController;
use App\Http\Controllers\Wms\BarcodeBarangController;
use App\Http\Controllers\Wms\NewQCLPBController;
use App\Http\Controllers\Wms\NewPOWmsController;
use App\Http\Controllers\Wms\NewPutAwayController;
use App\Http\Controllers\Wms\NewReturJualController;
use App\Http\Controllers\Wms\NewReturSuratJalanController;
use App\Http\Controllers\Wms\TerimaUbahKemasanController;
use App\Http\Controllers\Wms\TransferLokasiInController;
use App\Http\Controllers\Wms\ReturSerahSampleController;
use App\Http\Controllers\Wms\NewPermintaanPersiapanController;
use App\Http\Controllers\Wms\NewPermintaanPersiapanNonSOController;
use App\Http\Controllers\Wms\NewPermintaanPersiapanBooController;
use App\Http\Controllers\Wms\NewPickingController;
use App\Http\Controllers\Wms\QCPickingController;
use App\Http\Controllers\Wms\PackingController;
use App\Http\Controllers\Wms\PersiapanSpbController;
use App\Http\Controllers\Wms\NewTransferBarangController;
use App\Http\Controllers\Wms\NewPenyerahanSampleController;
use App\Http\Controllers\Wms\NewPenyerahanKonsiController;
use App\Http\Controllers\Wms\TransferLokasiOutController;
use App\Http\Controllers\Wms\PermintaanUbahKemasanController;
use App\Http\Controllers\Wms\UbahKemasanPickingController;
use App\Http\Controllers\Wms\UbahKemasanFinalisasiController;
use App\Http\Controllers\Wms\PermintaanPenyerahanGudangController;
use App\Http\Controllers\Wms\NewPenyerahanGudangController;
use App\Http\Controllers\Wms\NewOpnameGudangController;
use App\Http\Controllers\Wms\TransferLokasiController;
use App\Http\Controllers\Wms\TransferLokasiSiteController;
use App\Http\Controllers\Wms\OpnameGdg1Controller;
use App\Http\Controllers\Wms\OpnameGdgInspeksiController;
use App\Http\Controllers\Wms\DashboardOpnameHarianController;
use App\Http\Controllers\Wms\LockPeriodeController;
use App\Http\Controllers\Wms\UbahKemasanController;

// GudangController
Route::get('/gudang', [GudangController::class, 'index'])->middleware('auth');
Route::get('/loadAllGudang', [GudangController::class, 'loadAll'])->middleware('auth');
Route::post('/showRak', [GudangController::class, 'showRak'])->middleware('auth');
Route::post('/addRak', [GudangController::class, 'addRak'])->middleware('auth');
Route::post('/showLokasi', [GudangController::class, 'showLokasi'])->middleware('auth');
Route::post('/addLokasi', [GudangController::class, 'addLokasi'])->middleware('auth');

// BarangJadiController
Route::get('/barangjadi', [BarangJadiController::class, 'index'])->middleware('auth');
Route::post('/loadAllBarangJadi', [BarangJadiController::class, 'loadAll'])->middleware('auth');
Route::post('/barangjadidata', [BarangJadiController::class, 'dataTabel'])->middleware('auth');
Route::post('/showBarangJadi', [BarangJadiController::class, 'show'])->middleware('auth');
Route::post('/showBarangLokasi', [BarangJadiController::class, 'showBarangLokasi'])->middleware('auth');
Route::post('/addBarangLokasi', [BarangJadiController::class, 'addBarangLokasi'])->middleware('auth');
Route::post('/brgonchangeheader' , [BarangJadiController::class, 'onChangeHeader'])->middleware('auth');
Route::post('/loadkodebrg', [BarangJadiController::class, 'loadkodebrg'])->middleware('auth');

// BarcodeBarangController
Route::get('/barcodebarang', [BarcodeBarangController::class, 'index'])->middleware('auth');
Route::get('/loadAllbarcodebarang', [BarcodeBarangController::class, 'loadAll'])->middleware('auth');
Route::post('/barcodebarangdata', [BarcodeBarangController::class, 'dataTabel'])->middleware('auth');

// NewPOWmsController (Penerimaan Barang WMS, menu 210200). Href '/newpowms' dipisah dari '/newpo' (purchasing, 030301).
Route::get('/newpowms', [NewPOWmsController::class, 'index'])->middleware('auth');
Route::post('/addDBBeliwms', [NewPOWmsController::class, 'addDBBeli'])->middleware('auth');
Route::post('/detailPOwms', [NewPOWmsController::class, 'getDetailPO'])->middleware('auth');
Route::post('/detailPembelianwms', [NewPOWmsController::class, 'getDetailPembelian'])->middleware('auth');
Route::get('/getNoBuktiwms', [NewPOWmsController::class, 'getNoBukti'])->middleware('auth');
Route::get('/getAksesNewPOwms', [NewPOWmsController::class, 'getAkses'])->middleware('auth');
Route::get('/getAllPOwms', [NewPOWmsController::class, 'getAllPO'])->middleware('auth');
Route::get('/getAllPembelianwms', [NewPOWmsController::class, 'getAllPembelian'])->middleware('auth');
Route::post('/sp_beligudangwms', [NewPOWmsController::class, 'spBeliGudang'])->middleware('auth');
// NewQCLPBController
Route::get('/qclpb', [NewQCLPBController::class, 'index'])->middleware('auth');
Route::post('/detailLPB', [NewQCLPBController::class, 'detailLPB'])->middleware('auth');
Route::post('/detailQC', [NewQCLPBController::class, 'detailQC'])->middleware('auth');
Route::post('/submitLPB', [NewQCLPBController::class, 'submitLPB'])->middleware('auth');
Route::get('/getAksesQCLPB' , [NewQCLPBController::class, 'getAkses'])->middleware('auth');
Route::get('/getAllLPB', [NewQCLPBController::class, 'getAllLPB'])->middleware('auth');
Route::get('/getAllQC', [NewQCLPBController::class, 'getAllQC'])->middleware('auth');

// NewPutAwayController
Route::get('/putaway', [NewPutAwayController::class, 'index'])->middleware('auth');
Route::post('/addputaway', [NewPutAwayController::class, 'spAdd'])->middleware('auth');
Route::post('/checkaddputaway', [NewPutAwayController::class, 'spCheckAdd'])->middleware('auth');
Route::post('/detailpenerimaanputaway' , [NewPutAwayController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/koreksiputaway',[NewPutAwayController::class, 'spKoreksi'])->middleware('auth');
Route::get('/loadallputaway', [NewPutAwayController::class, 'loadAll'])->middleware('auth');

// NewReturJualController
Route::get('/newreturjual' , [NewReturJualController::class, 'index'])->middleware('auth');
Route::get('/loadallreturjual' , [NewReturJualController::class, 'loadall'])->middleware('auth');
Route::post('/detailoutstandingreturjual' , [NewReturJualController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/detailreturgudang' , [NewReturJualController::class, 'getDetailReturGudang'])->middleware('auth');
Route::get('/getNoBuktiReturJual', [NewReturJualController::class, 'getNoBukti'])->middleware('auth');
Route::post('/addReturJual' , [NewReturJualController::class, 'addReturJual'])->middleware('auth');
Route::post('/koreksiReturJual' , [NewReturJualController::class, 'spKoreksi'])->middleware('auth');
Route::post('/returjualkoreksiaddlist' , [NewReturJualController::class, 'getKoreksiAddList'])->middleware('auth');

// NewReturSuratJalanController
Route::get('/newretursuratjalan' , [NewReturSuratJalanController::class, 'index'])->middleware('auth');
Route::get('/getnobuktiretursuratjalan' , [NewReturSuratJalanController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingretursuratjalan' , [NewReturSuratJalanController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/addSuratJalan' , [NewReturSuratJalanController::class, 'addSuratJalan'])->middleware('auth');
Route::post('/detailpenerimaanretursuratjalan' , [NewReturSuratJalanController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/suratjalankoreksiaddlist', [NewReturSuratJalanController::class, 'getKoreksiAddList'])->middleware('auth');
Route::post('/koreksiSuratJalan' , [NewReturSuratJalanController::class, 'spKoreksi'])->middleware('auth');
Route::get('/loadallsuratjalan' , [NewReturSuratJalanController::class, 'loadAll'])->middleware('auth');

// TerimaUbahKemasanController
Route::get('/terimaubahkemasan', [TerimaUbahKemasanController::class, 'index'])->middleware('auth');
Route::post('/detailoutstandingterimaubahkemasan' , [TerimaUbahKemasanController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/detailpenerimaanterimaubahkemasan', [TerimaUbahKemasanController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::get('/loadallterimaubahkemasan', [TerimaUbahKemasanController::class, 'loadAll'])->middleware('auth');
Route::post('/addterimaubahkemasan' , [TerimaUbahKemasanController::class, 'spAdd'])->middleware('auth');
Route::post('/koreksiterimaubahkemasan' , [TerimaUbahKemasanController::class, 'spKoreksi'])->middleware('auth');
Route::get('/getnobuktiterimaubahkemasan' , [TerimaUbahKemasanController::class, 'getNoBuktiTerima'])->middleware('auth');

// TransferLokasiInController
Route::get('/transferlokasiin', [TransferLokasiInController::class, 'index'])->middleware('auth');
Route::get('/loadalltransferlokasiin', [TransferLokasiInController::class, 'loadAll'])->middleware('auth');
Route::post('/detailoutstandingtransferlokasiin' , [TransferLokasiInController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/detailpenerimaantransferlokasiin' , [TransferLokasiInController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/addtransferlokasiin' , [TransferLokasiInController::class, 'spAdd'])->middleware('auth');
Route::get('/getnobuktitransferlokasiin' , [TransferLokasiInController::class, 'getNoBukti'])->middleware('auth');
Route::post('/koreksitransferlokasiin' , [TransferLokasiInController::class, 'spKoreksi'])->middleware('auth');

// ReturSerahSampleController
Route::get('/returserahsample' , [ReturSerahSampleController::class, 'index'])->middleware('auth');
Route::get('/getnobuktireturserahsample' , [ReturSerahSampleController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingreturserahsample' , [ReturSerahSampleController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/addreturserahsample' , [ReturSerahSampleController::class, 'addSuratJalan'])->middleware('auth');
Route::post('/detailpenerimaanreturserahsample' , [ReturSerahSampleController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/returserahsamplekoreksiaddlist', [ReturSerahSampleController::class, 'getKoreksiAddList'])->middleware('auth');
Route::post('/koreksireturserahsample' , [ReturSerahSampleController::class, 'spKoreksi'])->middleware('auth');
Route::get('/loadallreturserahsample' , [ReturSerahSampleController::class, 'loadAll'])->middleware('auth');

// NewPermintaanPersiapanController
Route::get('/newpermintaanpersiapan' , [NewPermintaanPersiapanController::class, 'index'])->middleware('auth');
Route::get('/newpermintaanpersiapanloadall' , [NewPermintaanPersiapanController::class, 'loadAll'])->middleware('auth');
Route::get('/newpermintaanpersiapangetnobukti' , [NewPermintaanPersiapanController::class, 'getNoBukti'])->middleware('auth');
Route::post('/newpermintaanpersiapanspadd' , [NewPermintaanPersiapanController::class, 'spAdd'])->middleware('auth');
Route::get('/newpermintaanpersiapandetailpenerimaan' , [NewPermintaanPersiapanController::class, 'detailPenerimaan'])->middleware('auth');
Route::post('/newpermintaanpersiapanspdelete' , [NewPermintaanPersiapanController::class, 'spDelete'])->middleware('auth');
Route::get('/newpermintaanpersiapandetailpenerimaanperiod' , [NewPermintaanPersiapanController::class, 'detailPenerimaanperiod'])->middleware('auth');
Route::post('/newpermintaanpersiapanspcheckreguler' , [NewPermintaanPersiapanController::class, 'spCheckReguler'])->middleware('auth');

// NewPermintaanPersiapanNonSOController
Route::get('/newpermintaanpersiapannonso' ,[NewPermintaanPersiapanNonSOController::class, 'index'])->middleware('auth');
Route::get('/newpermintaanpersiapannonsoloadall' ,[NewPermintaanPersiapanNonSOController::class, 'loadAll'])->middleware('auth');
Route::get('/newpermintaanpersiapannonsogetnobukti' , [NewPermintaanPersiapanNonSOController::class, 'getNoBukti'])->middleware('auth');
Route::get('/newpermintaanpersiapannonsodetailpenerimaan' , [NewPermintaanPersiapanNonSOController::class, 'detailPenerimaan'])->middleware('auth');
Route::post('/newpermintaanpersiapannonsospadd' , [NewPermintaanPersiapanNonSOController::class, 'spAdd'])->middleware('auth');

// NewPermintaanPersiapanBooController
Route::get('/newpermintaanpersiapanboo' , [NewPermintaanPersiapanBooController::class, 'index'])->middleware('auth');
Route::get('/newpermintaanpersiapanbooloadall' , [NewPermintaanPersiapanBooController::class, 'loadAll'])->middleware('auth');
Route::get('/newpermintaanpersiapanboogetnobukti' , [NewPermintaanPersiapanBooController::class, 'getNoBukti'])->middleware('auth');
Route::post('/newpermintaanpersiapanboospadd' , [NewPermintaanPersiapanBooController::class, 'spAdd'])->middleware('auth');
Route::get('/newpermintaanpersiapanboodetailpenerimaan' , [NewPermintaanPersiapanBooController::class, 'detailPenerimaan'])->middleware('auth');
Route::post('/newpermintaanpersiapanboospdelete' , [NewPermintaanPersiapanBooController::class, 'spDelete'])->middleware('auth');
Route::get('/newpermintaanpersiapanboodetailpenerimaanperiod' , [NewPermintaanPersiapanBooController::class, 'detailPenerimaanperiod'])->middleware('auth');
Route::post('/newpermintaanpersiapanboospcheckreguler' , [NewPermintaanPersiapanBooController::class, 'spCheckReguler'])->middleware('auth');

// NewPickingController
Route::get('/newpicking', [NewPickingController::class, 'index'])->middleware('auth');
Route::post('/detailpenerimaanpicking' , [NewPickingController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/addpicking', [NewPickingController::class, 'spAdd'])->middleware('auth');
Route::post('/addpickinglpb', [NewPickingController::class, 'spAddLPB'])->middleware('auth');
Route::post('/checkaddpicking', [NewPickingController::class, 'spCheckAdd'])->middleware('auth');
Route::post('/checkaddpickinglpb', [NewPickingController::class, 'spCheckAddLPB'])->middleware('auth');
Route::post('/koreksipicking',[NewPickingController::class, 'spKoreksi'])->middleware('auth');
Route::get('/loadallpicking', [NewPickingController::class, 'loadAll'])->middleware('auth');

// QCPickingController
Route::get('/qcpicking', [QCPickingController::class, 'index'])->middleware('auth');
Route::post('/qcpickingdetailoutstanding', [QCPickingController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/qcpickingdetailpenerimaan', [QCPickingController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::get('/qcpickingloadall', [QCPickingController::class, 'loadAll'])->middleware('auth');
Route::post('/qcpickingspadd', [QCPickingController::class, 'spAdd'])->middleware('auth');

// PackingController
Route::get('/packing', [PackingController::class, 'index'])->middleware('auth');
Route::get('/loadallpacking' , [PackingController::class, 'loadAll'])->middleware('auth');
Route::post('/detailoutpacking' , [PackingController::class, 'getDetailOutstanding'])->middleware('auth');
Route::get('/getnobuktipacking' , [PackingController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingpacking' , [PackingController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/packingcheckadd' , [PackingController::class, 'checkAdd'])->middleware('auth');
Route::post('/addpacking' , [PackingController::class, 'addPacking'])->middleware('auth');
Route::post('/detailpacking' , [PackingController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/koreksiPacking' , [PackingController::class, 'spKoreksi'])->middleware('auth');

// PersiapanSpbController
Route::get('/persiapanspb', [PersiapanSpbController::class, 'index'])->middleware('auth');
Route::get('/loadallpersiapanspb' , [PersiapanSpbController::class, 'loadAll'])->middleware('auth');
Route::post('/detailoutpersiapanspb' , [PersiapanSpbController::class, 'getDetailOutstanding'])->middleware('auth');
Route::get('/getnobuktipersiapanspb' , [PersiapanSpbController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingpersiapanspb' , [PersiapanSpbController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/addpersiapanspb' , [PersiapanSpbController::class, 'addSuratJalan'])->middleware('auth');
Route::post('/detailpersiapanspb' , [PersiapanSpbController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/detailtransaksispb' , [PersiapanSpbController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/spbkoreksiaddlist', [PersiapanSpbController::class, 'getKoreksiAddList'])->middleware('auth');
Route::post('/koreksiPersiapanSPB' , [PersiapanSpbController::class, 'spKoreksi'])->middleware('auth');
Route::post('/persiapanspbspotorisasi' , [PersiapanSpbController::class, 'spOtorisasi1'])->middleware('auth');
Route::post('/PrintSPB' , [PersiapanSpbController::class, 'getDetailPenerimaancetak'])->middleware('auth');
Route::post('/PrintKolli' , [PersiapanSpbController::class, 'getDetailOutstandingkolli'])->middleware('auth');
Route::post('/persiapanspbupdatecetakkolli' , [PersiapanSpbController::class, 'updateCetakKolli'])->middleware('auth');
Route::post('/persiapanspbupdatecetakspb' , [PersiapanSpbController::class, 'updateCetakspb'])->middleware('auth');

// NewTransferBarangController
Route::get('/newtransferbarang' , [NewTransferBarangController::class, 'index'])->middleware('auth');
Route::get('/getnobuktitransferbarang' , [NewTransferBarangController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingtransferbarang', [NewTransferBarangController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/newAddTransferBarang', [NewTransferBarangController::class, 'addTransferBarang'])->middleware('auth');
Route::post('/detailpenerimaantransferbarang', [NewTransferBarangController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/transferbarangkoreksiaddlist' , [NewTransferBarangController::class, 'getKoreksiAddList'])->middleware('auth');
Route::post('/koreksiTransferBarang' , [NewTransferBarangController::class, 'spKoreksi'])->middleware('auth');
Route::get('/loadalltransferbarang' , [NewTransferBarangController::class, 'loadAll'])->middleware('auth');

// NewPenyerahanSampleController
Route::get('/penyerahansample', [NewPenyerahanSampleController::class, 'index'])->middleware('auth');
Route::get('/getnobuktipenyerahansample' , [NewPenyerahanSampleController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingpenyerahansample' , [NewPenyerahanSampleController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/addpenyerahansample' , [NewPenyerahanSampleController::class, 'spAdd'])->middleware('auth');
Route::get('/loadallpenyerahansample', [NewPenyerahanSampleController::class, 'loadAll'])->middleware('auth');
Route::post('/detailpenerimaanpenyerahansample', [NewPenyerahanSampleController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/penyerahansamplekoreksiaddlist', [NewPenyerahanSampleController::class, 'getKoreksiAddList'])->middleware('auth');
Route::post('/koreksipenyerahansample', [NewPenyerahanSampleController::class, 'spKoreksi'])->middleware('auth');
Route::post('/PrintSample' , [NewPenyerahanSampleController::class, 'getDetailPenerimaan'])->middleware('auth');

// NewPenyerahanKonsiController
Route::get('/penyerahankonsi', [NewPenyerahanKonsiController::class, 'index'])->middleware('auth');
Route::get('/getnobuktipenyerahankonsi' , [NewPenyerahanKonsiController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingpenyerahankonsi' , [NewPenyerahanKonsiController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/addpenyerahankonsi' , [NewPenyerahanKonsiController::class, 'spAdd'])->middleware('auth');
Route::get('/loadallpenyerahankonsi', [NewPenyerahanKonsiController::class, 'loadAll'])->middleware('auth');
Route::post('/detailpenerimaanpenyerahankonsi', [NewPenyerahanKonsiController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/penyerahankonsikoreksiaddlist', [NewPenyerahanKonsiController::class, 'getKoreksiAddList'])->middleware('auth');
Route::post('/koreksipenyerahankonsi', [NewPenyerahanKonsiController::class, 'spKoreksi'])->middleware('auth');
Route::post('/PrintKonsi' , [NewPenyerahanKonsiController::class, 'getDetailPenerimaan'])->middleware('auth');

// TransferLokasiOutController
Route::get('/transferlokasiout', [TransferLokasiOutController::class, 'index'])->middleware('auth');
Route::get('/loadalltransferlokasiout', [TransferLokasiOutController::class, 'loadAll'])->middleware('auth');
Route::get('/getnobuktitransferlokasiout' , [TransferLokasiOutController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingtransferlokasiout' , [TransferLokasiOutController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/addtransferlokasiout' , [TransferLokasiOutController::class, 'spAdd'])->middleware('auth');
Route::post('/detailpenerimaantransferlokasiout' , [TransferLokasiOutController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/koreksitransferlokasiout' , [TransferLokasiOutController::class, 'spKoreksi'])->middleware('auth');

// PermintaanUbahKemasanController
Route::get('/permintaanubahkemasan' , [PermintaanUbahKemasanController::class, 'index'])->middleware('auth');
Route::get('/getnobuktipermintaanubahkemasan' , [PermintaanUbahKemasanController::class, 'getNoBukti'])->middleware('auth');
Route::post('/permintaanubahkemasandetailpenerimaan' , [PermintaanUbahKemasanController::class, 'detailPenerimaan'])->middleware('auth');
Route::post('/permintaanubahkemasandetailpenerimaanprint' , [PermintaanUbahKemasanController::class, 'detailPenerimaanPrint'])->middleware('auth');
Route::get('/loadallpermintaanubahkemasan' , [PermintaanUbahKemasanController::class, 'loadAll'])->middleware('auth');
Route::post('/permintaanubahkemasanspadd' , [PermintaanUbahKemasanController::class, 'spAdd'])->middleware('auth');

// UbahKemasanPickingController
Route::get('/pickinguk', [UbahKemasanPickingController::class, 'index'])->middleware('auth');
Route::post('/detailoutstandingubahkemasanpicking' , [UbahKemasanPickingController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/detailpenerimaanubahkemasanpicking' , [UbahKemasanPickingController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/detailoutstandingubahkemasanpickinginfo' , [UbahKemasanPickingController::class, 'getDetailOutstandingInfo'])->middleware('auth');
Route::get('/loadallubahkemasanpicking', [UbahKemasanPickingController::class, 'loadAll'])->middleware('auth');
Route::post('/ubahkemasanpickingspcekadd', [UbahKemasanPickingController::class, 'spCheckAdd'])->middleware('auth');
Route::post('/ubahkemasanpickingspadd', [UbahKemasanPickingController::class, 'spAdd'])->middleware('auth');

// UbahKemasanFinalisasiController
Route::get('/ubahkemasanfinalisasi', [UbahKemasanFinalisasiController::class, 'index'])->middleware('auth');
Route::get('/getnobuktiubahkemasanfinalisasi' , [UbahKemasanFinalisasiController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingubahkemasanfinalisasi' , [UbahKemasanFinalisasiController::class, 'getDetailOutstanding'])->middleware('auth');
Route::get('/loadallubahkemasanfinalisasi', [UbahKemasanFinalisasiController::class, 'loadAll'])->middleware('auth');
Route::post('/addubahkemasanfinalisasi' , [UbahKemasanFinalisasiController::class, 'spAdd'])->middleware('auth');

// PermintaanPenyerahanGudangController
Route::get('/permintaanpenyerahangudang' , [PermintaanPenyerahanGudangController::class, 'index'])->middleware('auth');
Route::get('/getnobuktipermintaanpenyerahangudang' , [PermintaanPenyerahanGudangController::class, 'getNoBukti'])->middleware('auth');
Route::get('/permintaanpenyerahangudanglistbarang' , [PermintaanPenyerahanGudangController::class, 'listBarang'])->middleware('auth');
Route::get('/permintaanpenyerahangudanglistgudang' , [PermintaanPenyerahanGudangController::class, 'listGudang'])->middleware('auth');
Route::post('/permintaanpenyerahangudangdetailpenerimaan' , [PermintaanPenyerahanGudangController::class, 'detailPenerimaan'])->middleware('auth');
Route::get('/loadallpermintaanpenyerahangudang' , [PermintaanPenyerahanGudangController::class, 'loadAll'])->middleware('auth');
Route::post('/permintaanpenyerahangudangspadd' , [PermintaanPenyerahanGudangController::class, 'spAdd'])->middleware('auth');

// NewPenyerahanGudangController
Route::get('/penyerahangudang' , [NewPenyerahanGudangController::class, 'index'])->middleware('auth');
Route::get('/getnobuktipenyerahangudang', [NewPenyerahanGudangController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailoutstandingpenyerahangudang', [NewPenyerahanGudangController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/addpenyerahangudang' ,[NewPenyerahanGudangController::class, 'addPenyerahanGudang'])->middleware('auth');
Route::post('/detailpenerimaanpenyerahangudang' , [NewPenyerahanGudangController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::get('/loadallpenyerahangudang', [NewPenyerahanGudangController::class, 'loadAll'])->middleware('auth');
Route::post('/penyerahangudangkoreksiaddlist' , [NewPenyerahanGudangController::class, 'getKoreksiAddList'])->middleware('auth');
Route::post('/koreksipenyerahangudang', [NewPenyerahanGudangController::class, 'spKoreksi'])->middleware('auth');

// NewOpnameGudangController
Route::get('/opnamegudang', [NewOpnameGudangController::class, 'index'])->middleware('auth');
Route::post('/opnamegudangspcheck', [NewOpnameGudangController::class, 'spcheck'])->middleware('auth');
Route::post('/detailitemopnamegudang', [NewOpnameGudangController::class, 'getDetailItem'])->middleware('auth');
Route::get('/getnobuktiopnamegudang', [NewOpnameGudangController::class, 'getNoBukti'])->middleware('auth');
Route::post('/addformopnamegudang', [NewOpnameGudangController::class, 'addForm'])->middleware('auth');
Route::get('/loadallopnamegudang', [NewOpnameGudangController::class, 'loadAll'])->middleware('auth');
Route::post('/getdetailpenerimaanopnamegudang', [NewOpnameGudangController::class, 'getDetailPenerimaan'])->middleware('auth');

// TransferLokasiController
Route::get('/transferlokasi', [TransferLokasiController::class, 'index'])->middleware('auth');
Route::get('/transferlokasilistbarang' , [TransferLokasiController::class, 'listBarang'])->middleware('auth');
Route::get('/transferlokasilistlokasi' , [TransferLokasiController::class, 'listLokasi'])->middleware('auth');
Route::get('/getnobuktitransferlokasi' , [TransferLokasiController::class, 'getNoBukti'])->middleware('auth');
Route::post('/transferlokasispadd' , [TransferLokasiController::class, 'spAdd'])->middleware('auth');
Route::post('/transferlokasidetailpenerimaan' , [TransferLokasiController::class, 'detailPenerimaan'])->middleware('auth');
Route::get('/loadalltransferlokasi' , [TransferLokasiController::class, 'loadAll'])->middleware('auth');

// TransferLokasiSiteController
Route::get('/transferlokasisite' , [TransferLokasiSiteController::class, 'index'])->middleware('auth');
Route::post('/transferlokasisiteceklokasiawal' , [TransferLokasiSiteController::class, 'cekLokasiAwal'])->middleware('auth');
Route::post('/transferlokasisiteceklokasitujuan' , [TransferLokasiSiteController::class, 'cekLokasiTujuan'])->middleware('auth');
Route::post('/transferlokasisitecekbarang' , [TransferLokasiSiteController::class, 'cekBarang'])->middleware('auth');
Route::post('/transferlokasisitecekkodegdg' , [TransferLokasiSiteController::class, 'cekKodeGdg'])->middleware('auth');
Route::get('/getnobuktitransferlokasisite' , [TransferLokasiSiteController::class, 'getNoBukti'])->middleware('auth');
Route::post('/transferlokasisitespadd' , [TransferLokasiSiteController::class, 'spAdd'])->middleware('auth');

// OpnameGdg1Controller
Route::get('/opnamegdg', [OpnameGdg1Controller::class, 'index'])->middleware('auth');
Route::post('/opnamegdgcekbarang' , [OpnameGdg1Controller::class, 'cekBarang'])->middleware('auth');
Route::post('/opnamegdgceklokasi' , [OpnameGdg1Controller::class, 'cekLokasi'])->middleware('auth');
Route::get('/getnobuktiopnamegdg' , [OpnameGdg1Controller::class, 'getNoBukti'])->middleware('auth');
Route::post('/opnamegdgspadd', [OpnameGdg1Controller::class, 'spAdd'])->middleware('auth');
Route::get('/opnamegdgloadall', [OpnameGdg1Controller::class, 'loadAll'])->middleware('auth');
Route::post('/opnamegdgdetailoutstanding' , [OpnameGdg1Controller::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/opnamegdgspkoreksi', [OpnameGdg1Controller::class, 'spKoreksi'])->middleware('auth');
Route::post('/opnamegdgspdelete', [OpnameGdg1Controller::class, 'spDelete'])->middleware('auth');
Route::post('/opnamegdgspverifikasi', [OpnameGdg1Controller::class, 'spVerifikasi'])->middleware('auth');

// OpnameGdgInspeksiController
Route::get('/opnamegdginspeksi', [OpnameGdgInspeksiController::class, 'index'])->middleware('auth');
Route::get('/opnamegdginspeksiloadall', [OpnameGdgInspeksiController::class, 'loadAll'])->middleware('auth');
Route::post('/opnamegdginspeksidetailoutstanding' , [OpnameGdgInspeksiController::class, 'getDetailOutstanding'])->middleware('auth');
Route::post('/opnamegdginspeksispkoreksi', [OpnameGdgInspeksiController::class, 'spKoreksi'])->middleware('auth');
Route::post('/opnamegdginspeksispdelete', [OpnameGdgInspeksiController::class, 'spDelete'])->middleware('auth');
Route::post('/opnamegdginspeksispverifikasiaudit', [OpnameGdgInspeksiController::class, 'spVerifikasiAudit'])->middleware('auth');

// DashboardOpnameHarianController
Route::get('/dashboardopnameharian', [DashboardOpnameHarianController::class, 'index'])->middleware('auth');
Route::get('/dashboardopnamehariansp', [DashboardOpnameHarianController::class, 'spDashboard'])->middleware('auth');
Route::post('/dashboardopnamehariansplistbarang', [DashboardOpnameHarianController::class, 'spListBarang'])->middleware('auth');

// LockPeriodeController (dipakai 6 halaman WMS)
Route::post('/lockperiodecheck', [LockPeriodeController::class, 'check'])->middleware('auth');

// UbahKemasanController (hanya endpoint yang dipakai Finalisasi UK dan Picking UK)
Route::get('/getnobuktiubahkemasan', [UbahKemasanController::class, 'getNoBukti'])->middleware('auth');
Route::post('/detailpenerimaanubahkemasan', [UbahKemasanController::class, 'getDetailPenerimaan'])->middleware('auth');
Route::post('/koreksiubahkemasan', [UbahKemasanController::class, 'spKoreksi'])->middleware('auth');
