<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Purchasing\PembelianPermintaanNonAgenController;
use App\Http\Controllers\Purchasing\PembelianPermintaanAgenController;
use App\Http\Controllers\Purchasing\PembelianPermintaanNonStockController;
use App\Http\Controllers\Purchasing\PembelianClosingPRController;
use App\Http\Controllers\Purchasing\PembelianPermintaanDebetNoteController;
// use App\Http\Controllers\Purchasing\NewPOBeliAccBiayaKreditController;
// use App\Http\Controllers\Purchasing\NewPOBeliAccBiayaTunaiController;
// use App\Http\Controllers\Purchasing\NewPOBeliAccTunaiController;
use App\Http\Controllers\Purchasing\NewPOBeliAccController;
use App\Http\Controllers\Purchasing\NewPOJasaController;
use App\Http\Controllers\Purchasing\NewPOController;
use App\Http\Controllers\Purchasing\PerintahReturBeliController;
use App\Http\Controllers\Purchasing\POController;
use App\Http\Controllers\Purchasing\PONonStockController;
use App\Http\Controllers\Purchasing\ClosingPOController;
use App\Http\Controllers\Purchasing\InvoiceReturBeliController;
use App\Http\Controllers\Purchasing\ReturPembelianGudangController;
use App\Http\Controllers\Purchasing\InvoicePembelianController;
use App\Http\Controllers\Purchasing\UangMukaBeliController;

Route::middleware('auth')->group(function () {

    // ── PEMBELIAN PERMINTAAN NON AGEN ─────────────────────────────────────
    Route::get('/purchaseRequestCetak',                         [PembelianPermintaanNonAgenController::class, 'spCetak']);
    Route::get('/pembelianpermintaannonagen',                   [PembelianPermintaanNonAgenController::class, 'index']);
    Route::get('/pembelianpermintaannonagenspnobukti',          [PembelianPermintaanNonAgenController::class, 'getNoBukti']);
    Route::get('/pembelianpermintaannonagenlistbarang',         [PembelianPermintaanNonAgenController::class, 'listBarang']);
    Route::post('/pembelianpermintaannonagenspadd',             [PembelianPermintaanNonAgenController::class, 'spAdd']);
    Route::get('/pembelianpermintaannonagenspdetail',           [PembelianPermintaanNonAgenController::class, 'spDetail']);
    Route::post('/pembelianpermintaannonagenloadall',            [PembelianPermintaanNonAgenController::class, 'loadAll']);
    Route::post('/pembelianpermintaannonagenspdelete',          [PembelianPermintaanNonAgenController::class, 'spDelete']);
    Route::get('/pembelianpermintaannonagenlistdepartemen',     [PembelianPermintaanNonAgenController::class, 'listDepartemen']);
    Route::post('/pembelianpermintaannonagenupdateotorisasi',   [PembelianPermintaanNonAgenController::class, 'updateOtorisasi']);
    Route::post('/pembelianpermintaannonagenupdatebatalotorisasi', [PembelianPermintaanNonAgenController::class, 'updateBatalOtorisasi']);



    // ── PEMBELIAN PERMINTAAN AGEN ─────────────────────────────────────────
    Route::get('/pembelianpermintaanagen',                      [PembelianPermintaanAgenController::class, 'index']);
    Route::get('/pembelianpermintaanagenspnobukti',             [PembelianPermintaanAgenController::class, 'getNoBukti']);
    Route::get('/pembelianpermintaanagenlistbarang',            [PembelianPermintaanAgenController::class, 'listBarang']);
    Route::post('/pembelianpermintaanagenspadd',                [PembelianPermintaanAgenController::class, 'spAdd']);
    Route::get('/pembelianpermintaanagenspdetail',              [PembelianPermintaanAgenController::class, 'spDetail']);
    Route::post('/pembelianpermintaanagenloadall',              [PembelianPermintaanAgenController::class, 'loadAll']);
    Route::post('/pembelianpermintaanagenspdelete',             [PembelianPermintaanAgenController::class, 'spDelete']);
    Route::get('/pembelianpermintaanagenlistdepartemen',        [PembelianPermintaanAgenController::class, 'listDepartemen']);
    Route::post('/pembelianpermintaanagenupdateotorisasi',      [PembelianPermintaanAgenController::class, 'updateOtorisasi']);
    Route::post('/pembelianpermintaanagenupdatebatalotorisasi', [PembelianPermintaanAgenController::class, 'updateBatalOtorisasi']);


    // ── PEMBELIAN NON STOCK ───────────────────────────────────────────────
    Route::get('/pembelianpermintaannonstock',                      [PembelianPermintaanNonStockController::class, 'index']);
    Route::get('/pembelianpermintaannonstockspnobukti',             [PembelianPermintaanNonStockController::class, 'getNoBukti']);
    Route::get('/pembelianpermintaannonstocklistbarang',            [PembelianPermintaanNonStockController::class, 'listBarang']);
    Route::post('/pembelianpermintaannonstockspadd',                [PembelianPermintaanNonStockController::class, 'spAdd']);
    Route::get('/pembelianpermintaannonstockspdetail',              [PembelianPermintaanNonStockController::class, 'spDetail']);
    Route::post('/pembelianpermintaannonstockloadall',              [PembelianPermintaanNonStockController::class, 'loadAll']);
    Route::post('/pembelianpermintaannonstockspdelete',             [PembelianPermintaanNonStockController::class, 'spDelete']);
    Route::get('/pembelianpermintaannonstocklistdepartemen',        [PembelianPermintaanNonStockController::class, 'listDepartemen']);
    Route::post('/pembelianpermintaannonstockupdateotorisasi',      [PembelianPermintaanNonStockController::class, 'updateOtorisasi']);
    Route::post('/pembelianpermintaannonstockupdatebatalotorisasi', [PembelianPermintaanNonStockController::class, 'updateBatalOtorisasi']);

    // ── PEMBELIAN CLOSING PR ──────────────────────────────────────────────
    Route::get('/pembelianclosingpr',       [PembelianClosingPRController::class, 'index']);
    Route::get('/pembelianclosingprloadall',[PembelianClosingPRController::class, 'loadAll']);
    Route::post('/pembelianclosingprlock',  [PembelianClosingPRController::class, 'lock']);
    Route::post('/pembelianclosingprunlock',[PembelianClosingPRController::class, 'unlock']);

    Route::get('/pembelianclosingprdataoutstanding',[PembelianClosingPRController::class, 'dataOutstanding']);
    Route::get('/pembelianclosingprdataclosing',[PembelianClosingPRController::class, 'dataClosing']);
    Route::get('/pembelianclosingprceksisa',[PembelianClosingPRController::class, 'cekSisa']);
    Route::post('/pembelianclosingprheadertable',[PembelianClosingPRController::class, 'headerTable']);


    // ── PEMBELIAN DEBET NOTE ──────────────────────────────────────────────
    Route::get('/pembelianpermintaandebetnote',     [PembelianPermintaanDebetNoteController::class, 'index']);
    Route::get('/debetnotespnobukti',               [PembelianPermintaanDebetNoteController::class, 'getNoBukti']);
    Route::get('/debetnotelistcustomer',            [PembelianPermintaanDebetNoteController::class, 'listCustomer']);
    Route::post('/debetnotelistinvoice',            [PembelianPermintaanDebetNoteController::class, 'listInvoice']);
    Route::post('/debetnotespadd',                  [PembelianPermintaanDebetNoteController::class, 'spAdd']);
    Route::post('/debetnotespdetail',               [PembelianPermintaanDebetNoteController::class, 'getDetail']);
    Route::post('/debetnotespkoreksi',              [PembelianPermintaanDebetNoteController::class, 'spKoreksi']);
    Route::get('/debetnoteloadall',                 [PembelianPermintaanDebetNoteController::class, 'loadAll']);
    Route::post('/debetnotespotorisasi',            [PembelianPermintaanDebetNoteController::class, 'updateOtorisasi']);
    Route::post('/debetnotespbatalotorisasi',       [PembelianPermintaanDebetNoteController::class, 'updateBatalOtorisasi']);

    // // ── NEW PO BELI ACC BIAYA KREDIT ──────────────────────────────────────
    // Route::get('/newpobeliaccbiayakredit',          [NewPOBeliAccBiayaKreditController::class, 'index']);
    // Route::post('/detailPOBelibiayakredit',         [NewPOBeliAccBiayaKreditController::class, 'getDetailPO']);
    // Route::post('/detailPembelianACCbiayakredit',   [NewPOBeliAccBiayaKreditController::class, 'getDetailPembelian']);
    // Route::get('/getNoBuktibiayakredit',            [NewPOBeliAccBiayaKreditController::class, 'getNoBukti']);
    // Route::get('/getAksesNewPOAccbiayakredit',      [NewPOBeliAccBiayaKreditController::class, 'getAkses']);
    // Route::post('/spotorisasiBeliAccbiayakredit',   [NewPOBeliAccBiayaKreditController::class, 'spOtorisasi1']);
    // Route::post('/spUnotorisasiBeliAccbiayakredit', [NewPOBeliAccBiayaKreditController::class, 'spUnOtorisasi1']);
    // Route::get('/getAllPOBeliAccbiayakredit',        [NewPOBeliAccBiayaKreditController::class, 'getAllPO']);
    // Route::get('/getAllPembelianAccbiayakredit',     [NewPOBeliAccBiayaKreditController::class, 'getAllPembelian']);
    // Route::post('/sp_beligudangACCbiayakredit',     [NewPOBeliAccBiayaKreditController::class, 'spBeliGudang']);
    // Route::post('/accbiayakreditonchangeheader',    [NewPOBeliAccBiayaKreditController::class, 'onChangeHeader']);
    // Route::post('/accbiayakreditspupdateso',        [NewPOBeliAccBiayaKreditController::class, 'spUpdateSO']);

    // // ── NEW PO BELI ACC BIAYA TUNAI ───────────────────────────────────────
    // Route::get('/newpobeliaccbiayatunai',           [NewPOBeliAccBiayaTunaiController::class, 'index']);
    // Route::post('/detailPOBelibiayatunai',          [NewPOBeliAccBiayaTunaiController::class, 'getDetailPO']);
    // Route::post('/detailPembelianACCbiayatunai',    [NewPOBeliAccBiayaTunaiController::class, 'getDetailPembelian']);
    // Route::get('/getNoBuktibiayatunai',             [NewPOBeliAccBiayaTunaiController::class, 'getNoBukti']);
    // Route::get('/getAksesNewPOAccbiayatunai',       [NewPOBeliAccBiayaTunaiController::class, 'getAkses']);
    // Route::post('/spotorisasiBeliAccbiayatunai',    [NewPOBeliAccBiayaTunaiController::class, 'spOtorisasi1']);
    // Route::post('/spUnotorisasiBeliAccbiayatunai',  [NewPOBeliAccBiayaTunaiController::class, 'spUnOtorisasi1']);
    // Route::get('/getAllPOBeliAccbiayatunai',         [NewPOBeliAccBiayaTunaiController::class, 'getAllPO']);
    // Route::get('/getAllPembelianAccbiayatunai',      [NewPOBeliAccBiayaTunaiController::class, 'getAllPembelian']);
    // Route::post('/sp_beligudangACCbiayatunai',      [NewPOBeliAccBiayaTunaiController::class, 'spBeliGudang']);
    // Route::post('/accbiayatunaionchangeheader',     [NewPOBeliAccBiayaTunaiController::class, 'onChangeHeader']);
    // Route::post('/accbiayatunaispupdateso',         [NewPOBeliAccBiayaTunaiController::class, 'spUpdateSO']);

    // // ── NEW PO BELI ACC TUNAI ─────────────────────────────────────────────
    // Route::get('/newpobeliacctunai',                [NewPOBeliAccTunaiController::class, 'index']);
    // Route::post('/detailPOBelitunai',               [NewPOBeliAccTunaiController::class, 'getDetailPO']);
    // Route::post('/detailPembelianACCtunai',         [NewPOBeliAccTunaiController::class, 'getDetailPembelian']);
    // Route::get('/getNoBuktibeliacctunai',           [NewPOBeliAccTunaiController::class, 'getNoBukti']);
    // Route::get('/getAksesNewPOAcctunai',            [NewPOBeliAccTunaiController::class, 'getAkses']);
    // Route::post('/spotorisasiBeliAcctunai',         [NewPOBeliAccTunaiController::class, 'spOtorisasi1']);
    // Route::post('/spUnotorisasiBeliAcctunai',       [NewPOBeliAccTunaiController::class, 'spUnOtorisasi1']);
    // Route::get('/getAllPOBeliAcctunai',              [NewPOBeliAccTunaiController::class, 'getAllPO']);
    // Route::get('/getAllPembelianAcctunai',           [NewPOBeliAccTunaiController::class, 'getAllPembelian']);
    // Route::post('/sp_beligudangACCtunai',           [NewPOBeliAccTunaiController::class, 'spBeliGudang']);
    // Route::get('/invoicepembelianprint',            [NewPOBeliAccTunaiController::class, 'spCetak']);

    //NewPOBeliAcc (gabungan: Acc Tunai/Kredit + Jasa Acc Tunai/Kredit)
    Route::get ('/newpobeliacc',            [NewPOBeliAccController::class, 'index'])->middleware('auth');
    Route::get ('/newpobeliaccloadall',     [NewPOBeliAccController::class, 'loadAll'])->middleware('auth');
    Route::get ('/getAksesNewPOAcc',        [NewPOBeliAccController::class, 'getAkses'])->middleware('auth');
    Route::get ('/getNoBukti',              [NewPOBeliAccController::class, 'getNoBukti'])->middleware('auth');
    Route::post('/addDBBeli',               [NewPOBeliAccController::class, 'addDBBeli'])->middleware('auth');
    Route::post('/detailPOBeli',            [NewPOBeliAccController::class, 'getDetailPO'])->middleware('auth');
    Route::post('/detailPembelianACC',      [NewPOBeliAccController::class, 'getDetailPembelian'])->middleware('auth');
    Route::post('/sp_beligudangACC',        [NewPOBeliAccController::class, 'spBeliGudang'])->middleware('auth');
    Route::post('/spotorisasiBeliAcc',      [NewPOBeliAccController::class, 'spOtorisasi1'])->middleware('auth');
    Route::post('/spUnotorisasiBeliAcc',    [NewPOBeliAccController::class, 'spUnOtorisasi1'])->middleware('auth');
    Route::post('/acconchangeheader',       [NewPOBeliAccController::class, 'onChangeHeader'])->middleware('auth');
    Route::post('/accspupdateso',           [NewPOBeliAccController::class, 'spUpdateSO'])->middleware('auth');
    Route::get ('/invoicepembelianprint',   [NewPOBeliAccController::class, 'spCetak'])->middleware('auth');

    // Menu lama - entri di database masih menunjuk ke sini, diarahkan ke halaman gabungan.
    Route::get('/newpobeliacctunai',       function () { return redirect('/newpobeliacc'); })->middleware('auth');
    Route::get('/newpobeliaccbiayatunai',  function () { return redirect('/newpobeliacc'); })->middleware('auth');
    Route::get('/newpobeliaccbiayakredit', function () { return redirect('/newpobeliacc'); })->middleware('auth');

    // ── NEW PO JASA NON STOCK ─────────────────────────────────────────────
    Route::get('/newpojasa',            [NewPOJasaController::class, 'index']);
    Route::post('/addDBBelijasa',       [NewPOJasaController::class, 'addDBBeli']);
    Route::post('/detailPOjasa',        [NewPOJasaController::class, 'getDetailPO']);
    Route::post('/detailPembelianjasa', [NewPOJasaController::class, 'getDetailPembelian']);
    Route::get('/getNoBuktijasa',       [NewPOJasaController::class, 'getNoBukti']);
    Route::get('/getAksesNewPOJasa',    [NewPOJasaController::class, 'getAkses']);
    Route::get('/getAllPOjasa',         [NewPOJasaController::class, 'getAllPO']);
    Route::get('/getAllPembelianjasa',  [NewPOJasaController::class, 'getAllPembelian']);
    Route::post('/sp_beligudangjasa',   [NewPOJasaController::class, 'spBeliGudang']);

    // ── NEW PO ────────────────────────────────────────────────────────────
    Route::get('/newpoCetak',              [NewPOController::class, 'spCetak']);
    Route::get('/newpo',                   [NewPOController::class, 'index']);
    Route::post('/addDBBeli',              [NewPOController::class, 'addDBBeli']);
    Route::post('/detailPO',               [NewPOController::class, 'getDetailPO']);
    Route::post('/detailPembelian',        [NewPOController::class, 'getDetailPembelian']);
    Route::post('/detailCetak',            [NewPOController::class, 'getDetailCetak']);
    Route::get('/getNoBukti',              [NewPOController::class, 'getNoBukti']);
    Route::get('/getAksesNewPO',           [NewPOController::class, 'getAkses']);
    Route::get('/getAllPO',                [NewPOController::class, 'getAllPO']);
    Route::get('/getAllPembelian',         [NewPOController::class, 'getAllPembelian']);
    Route::post('/sp_beligudang',          [NewPOController::class, 'spBeliGudang']);
    Route::get('/getOutstandingPODetail',  [NewPOController::class, 'getOutstandingPODetail']);
    Route::post('/getDetailPembelian',     [NewPOController::class, 'getDetailPembelian']);

    // ── PERINTAH RETUR BELI ───────────────────────────────────────────────
    Route::get('/perintahreturbeliPrint',           [PerintahReturBeliController::class, 'spCetak']);
    Route::get('/perintahreturbeli',                [PerintahReturBeliController::class, 'index']);
    Route::get('/prbloadall',                       [PerintahReturBeliController::class, 'loadAll']);
    Route::get('/prblistnorjual',                   [PerintahReturBeliController::class, 'listNoRJual']);
    Route::get('/prblistnobeli',                    [PerintahReturBeliController::class, 'listNoBeli']);
    Route::get('/prblistsupplier',                  [PerintahReturBeliController::class, 'listSupplier']);
    Route::get('/prblistgudang',                    [PerintahReturBeliController::class, 'listGudang']);
    Route::get('/prblistbarangJualDanBeli',         [PerintahReturBeliController::class, 'listBarangJualDanBeli']);
    Route::get('/prblistbarangBeliTanpaJual',       [PerintahReturBeliController::class, 'listBarangBeliTanpaJual']);
    Route::get('/prblistbarangJualTanpaBeli',       [PerintahReturBeliController::class, 'listBarangJualTanpaBeli']);
    Route::post('/prbspadd',                        [PerintahReturBeliController::class, 'spAdd']);
    Route::post('/prbgetdetail',                    [PerintahReturBeliController::class, 'getDetail']);
    Route::post('/prbcekotorisasi',                 [PerintahReturBeliController::class, 'cekOtorisasi']);
    Route::post('/prbupdateotorisasi',              [PerintahReturBeliController::class, 'updateOtorisasi']);
    Route::post('/prbupdatebatalotorisasi',         [PerintahReturBeliController::class, 'updateBatalOtorisasi']);
    Route::post('/prbonchangeheader',               [PerintahReturBeliController::class, 'onChangeHeader']);

    // ── PURCHASE ORDER ────────────────────────────────────────────────────
    Route::get('/purchaseorder',                    [POController::class, 'index']);
    Route::post('/purchaseordercekhargaoto',        [POController::class, 'cekHargaOto']);
    Route::get('/purchaseorderprint',               [POController::class, 'spCetak']);
    Route::get('/poloadall',                        [POController::class, 'loadAll']);
    Route::get('/polistpelanggan',                  [POController::class, 'listPelanggan']);
    Route::get('/polistbarangfoc',                  [POController::class, 'listBarangFOC']);
    Route::post('/polistbarangnosominus',           [POController::class, 'listBarangNonFOC1']);
    Route::get('/polistbarangnosoplus',             [POController::class, 'listBarangNonFOC2']);
    Route::post('/polistpwo',                       [POController::class, 'listPWO']);
    Route::post('/polistgudang',                    [POController::class, 'listGudang']);
    Route::post('/polistnoso',                      [POController::class, 'listNoSo']);
    Route::post('/polistlokasipenerima',            [POController::class, 'listLokasiPenerima']);
    Route::post('/pospadd',                         [POController::class, 'spAdd']);
    Route::post('/pocekharga',                      [POController::class, 'spCekHarga']);
    Route::post('/pogetdetail',                     [POController::class, 'getDetail']);
    Route::post('/cekPoDet',                        [POController::class, 'cekPoDet']);
    Route::post('/ceksatuanbarang',                 [POController::class, 'cekSatuanBarang']);
    Route::post('/pocekotorisasi',                  [POController::class, 'cekOtorisasi']);
    Route::post('/poupdateotorisasi',               [POController::class, 'updateOtorisasi']);
    Route::post('/poupdatebatalotorisasi',          [POController::class, 'updateBatalOtorisasi']);
    Route::post('/pospupdatepo',                    [POController::class, 'spUpdatePO']);
    Route::post('/poonchangeheader',                [POController::class, 'onChangeHeader']);
    Route::post('/polistbarangnosominusallso',      [POController::class, 'listBarangNonFOC1AllSO']);
    Route::get('/polistvalas',                      [POController::class, 'listValas']);
    Route::get('/checkhargaddd',                    [POController::class, 'CheckHargaAdd']);
    Route::get('/purchaseorderlistrefpr',           [POController::class, 'listRefPr']);
    Route::post('/pospaddtambahso',                 [POController::class, 'spAddTambahSO']);
    // Barang outstanding dari SEMUA SO sekaligus - dipakai dropdown "+ Dari" = SO di form
    // Purchase Order. Route terpisah, bukan menumpang polistbarangnosoplus di atas, karena
    // route itu masih dipakai gudang/transferbarang dan purchasing/perintahreturbeli.
    Route::get('/polistbarangsoall',                [POController::class, 'listBarangSOAll']);
    Route::get('/poloadpurchaseorder',              [POController::class, 'loadPurchaseOrder']);
    Route::get('/podataoutstandingpr',              [POController::class, 'dataOutstandingPR']);
    Route::get('/podataoutstandingso',              [POController::class, 'dataOutstandingSO']);
    Route::post('/purchaseorderspaddpr',            [POController::class, 'spAddPr']);

    // ── PURCHASE ORDER NONSTOCK ───────────────────────────────────────────
    Route::get('/pononstock',              [PONonStockController::class, 'index']);
    Route::get('/polistperkiraan',         [PONonStockController::class, 'listPerkiraan']);
    Route::post('/polistcosting',          [PONonStockController::class, 'listCosting']);
    Route::post('/polistsubcosting',       [PONonStockController::class, 'listSubCosting']);
    Route::get('/polistbarangjasa',        [PONonStockController::class, 'listBarangJasa']);
    Route::get('/polistbarangjasanobukti', [PONonStockController::class, 'listBarangJasaNoBukti']);
    Route::post('/ponsspadd',              [PONonStockController::class, 'spAdd']);
    Route::get('/ponsloadall',             [PONonStockController::class, 'loadAll']);
    Route::get('/ponslistbarangfoc',       [PONonStockController::class, 'listBarangFOC']);
    Route::get('/nonstockcheckhargaddd',   [PONonStockController::class, 'CheckHargaAdd']);
    Route::get('/ponsloadpurchaseorder',   [PONonStockController::class, 'loadPurchaseOrder']);
    Route::get('/ponsdataoutstandingpr',   [PONonStockController::class, 'dataOutstandingPR']);
    Route::post('/ponslistbarangjasaall',  [PONonStockController::class, 'listBarangJasaAll']);

    // ── CLOSING PO ────────────────────────────────────────────────────────
    Route::get('/closingpurchaseorder',             [ClosingPOController::class, 'index']);
    Route::get('/closingpurchaseorderloadall',      [ClosingPOController::class, 'loadAll']);
    Route::get('/closingpodataoutstanding',      [ClosingPOController::class, 'dataOutstanding']);
    Route::get('/closingpodataclosing',      [ClosingPOController::class, 'dataClosing']);
    Route::get('/closingpoceksisa',      [ClosingPOController::class, 'cekSisa']);
    Route::post('/closingpoheadertable',      [ClosingPOController::class, 'headerTable']);
    Route::post('/closingpoheadertable',      [ClosingPOController::class, 'headerTable']);
    Route::post('/closingpospclosebarang',          [ClosingPOController::class, 'updateCloseBarang']);
    Route::post('/closingpospcloseheader',          [ClosingPOController::class, 'updateCloseHeader']);
    Route::post('/closingpospopenbarang',           [ClosingPOController::class, 'updateOpenBarang']);
    Route::post('/closingpospopenheader',           [ClosingPOController::class, 'updateOpenHeader']);
    Route::get('/closingpoprint',                   [ClosingPOController::class, 'spCetak']);

    // ── INVOICE RETUR BELI ────────────────────────────────────────────────
    Route::get('/invoiceReturBeliCetak',            [InvoiceReturBeliController::class, 'spCetak']);
    Route::get('/invoicereturbeli',                 [InvoiceReturBeliController::class, 'index']);
    Route::get('/invoicereturbelispnobukti',        [InvoiceReturBeliController::class, 'getNoBukti']);
    Route::post('/invoicereturbelionchangeheader',  [InvoiceReturBeliController::class, 'onChangeHeader']);
    Route::get('/invoicereturbelilistcustomer',     [InvoiceReturBeliController::class, 'listCustomer']);
    Route::post('/invoicereturbelilistnoinvoice',   [InvoiceReturBeliController::class, 'listNoInvoice']);
    Route::post('/invoicereturbelilistbarang',      [InvoiceReturBeliController::class, 'listBarang']);
    Route::post('/invoicereturbelilistnobeli',      [InvoiceReturBeliController::class, 'listNoBeli']);
    Route::post('/invoicereturbeligetdetail',       [InvoiceReturBeliController::class, 'getDetail']);
    Route::post('/invoicereturbelispadd',           [InvoiceReturBeliController::class, 'spAdd']);
    Route::post('/invoicereturbelispdelete',        [InvoiceReturBeliController::class, 'spDelete']);
    Route::get('/invoicereturbeliloadall',          [InvoiceReturBeliController::class, 'loadAll']);
    Route::post('/invoicereturbelispotorisasi',     [InvoiceReturBeliController::class, 'spOtorisasi']);
    Route::post('/invoicereturbelispbatalotorisasi',[InvoiceReturBeliController::class, 'spBatalOtorisasi']);
    Route::get('/invoicereturbeligetLPBdetail',     [InvoiceReturBeliController::class, 'getLPBDetail']);

    // ── RETUR PEMBELIAN GUDANG ────────────────────────────────────────────
    Route::get('/returbeli',                        [ReturPembelianGudangController::class, 'index']);
    Route::get('/returpembeliangudangprint',        [ReturPembelianGudangController::class, 'spCetak']);
    Route::get('/rpgloadAll',                       [ReturPembelianGudangController::class, 'loadAll']);
    Route::post('/detailoutstandingreturbeli',      [ReturPembelianGudangController::class, 'getDetailOutstanding']);
    Route::get('/getnobuktireturbeli',              [ReturPembelianGudangController::class, 'getNoBukti']);
    Route::post('/addreturbeli',                    [ReturPembelianGudangController::class, 'spAdd']);
    Route::post('/detailpenerimaanreturbeli',       [ReturPembelianGudangController::class, 'getDetailPenerimaan']);
    Route::post('/koreksireturbeli',                [ReturPembelianGudangController::class, 'spKoreksi']);
    Route::post('/returbelikoreksiaddlist',         [ReturPembelianGudangController::class, 'getKoreksiAddList']);

    // ── INVOICE PEMBELIAN ─────────────────────────────────────────────────
    Route::get('/invoicepembelian',                 [InvoicePembelianController::class, 'index']);
    Route::post('/detailbeli',                      [InvoicePembelianController::class, 'getDetailPO']);
    Route::post('/detailBeliDet',                   [InvoicePembelianController::class, 'getBeliDet']);
    Route::post('/detailInvoiceBeli',               [InvoicePembelianController::class, 'getDetailPembelian']);
    Route::get('/getNoBuktiInvoiceBeli',            [InvoicePembelianController::class, 'getNoBukti']);
    Route::get('/getAksesInvoiceBeli',              [InvoicePembelianController::class, 'getAkses']);
    Route::post('/spotorisasiInvoiceBeli',          [InvoicePembelianController::class, 'spOtorisasi1']);
    Route::post('/spUnotorisasiInvoiceBeli',        [InvoicePembelianController::class, 'spUnOtorisasi1']);
    Route::get('/getAllInvoiceBeli',                 [InvoicePembelianController::class, 'getAllPO']);
    Route::get('/getAlloutbeliinv',                  [InvoicePembelianController::class, 'getAllPembelian']);
    Route::post('/sp_beligudangInvoiceBeli',        [InvoicePembelianController::class, 'spBeliGudang']);
    Route::post('/sp_edittransaksi',                [InvoicePembelianController::class, 'spedit']);
    Route::post('/sp_hapusinvoice',                 [InvoicePembelianController::class, 'deleteinvoice']);

    // ── UANG MUKA BELI ────────────────────────────────────────────────────
    Route::get('/uangmukabeli',                  [UangMukaBeliController::class, 'index']);
    Route::get('/uangmukabeliprint',             [UangMukaBeliController::class, 'spCetak']);
    Route::get('/uangmukabelilistpo',            [UangMukaBeliController::class, 'listPO']);
    Route::post('/uangmukabelidetail',           [UangMukaBeliController::class, 'getDetail']);
    Route::post('/uangmukabelispdelete',         [UangMukaBeliController::class, 'spDelete']);
    Route::get('/uangmukabeliloadall',           [UangMukaBeliController::class, 'loadAll']);
    Route::post('/uangmukabelispadd',            [UangMukaBeliController::class, 'spAdd']);
    Route::post('/uangmukabelispotorisasi',      [UangMukaBeliController::class, 'spOtorisasi']);
    Route::post('/uangmukabelispbatalotorisasi', [UangMukaBeliController::class, 'spBatalOtorisasi']);

});