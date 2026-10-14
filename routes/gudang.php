<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gudang\PemakaianBarangController;
use App\Http\Controllers\Gudang\PembebananPemakaianController;
use App\Http\Controllers\Gudang\ClosingTransferController;
use App\Http\Controllers\Gudang\ClosingPerintahOpnameController;
use App\Http\Controllers\Gudang\TerimaTransferBarangController;
use App\Http\Controllers\Gudang\TransferBarangController;
use App\Http\Controllers\Gudang\PermintaanPemakaianController;
use App\Http\Controllers\Gudang\GudangPermintaanSampleController;
use App\Http\Controllers\Gudang\GudangPenyerahanSampleController;
use App\Http\Controllers\Gudang\GudangReturPenyerahanSampleController;
use App\Http\Controllers\Gudang\GudangClosingPenyerahanSampleController;
use App\Http\Controllers\Gudang\GudangPermintaanKonsinyasiController;
use App\Http\Controllers\Gudang\GudangPenyerahanKonsinyasiController;
use App\Http\Controllers\Gudang\GudangReturPenyerahanKonsinyasiController;
use App\Http\Controllers\Gudang\PerintahOpnameController;
use App\Http\Controllers\Gudang\BeritaAcaraOpnameController;
use App\Http\Controllers\Gudang\OpnameBarangController;
use App\Http\Controllers\Gudang\KoreksiStockController;
use App\Http\Controllers\Gudang\UbahKemasanBarangController;
use App\Http\Controllers\Gudang\PermintaanTransferBarangController;
use App\Http\Controllers\Gudang\PembebananSampleController;

Route::middleware('auth')->group(function () {

    // PENYERAHAN GUDANG
    Route::get('/pemakaianbarang', [PemakaianBarangController::class, 'index']);
    Route::post('/pemakaianbarangdetailoutstanding', [PemakaianBarangController::class, 'getDetailOutstanding']);
    Route::post('/pemakaianbarangdetailadd', [PemakaianBarangController::class, 'getDetailAdd']);
    Route::post('/pemakaianbarangspadd', [PemakaianBarangController::class, 'addPenyerahanGudang']);
    Route::post('/pemakaianbarangdetailpenerimaan', [PemakaianBarangController::class, 'getDetailPenerimaan']);
    Route::get('/pemakaianbarangloadall', [PemakaianBarangController::class, 'loadAll']);
    Route::post('/pemakaianbarangkoreksiaddlist', [PemakaianBarangController::class, 'getKoreksiAddList']);
    Route::post('/pemakaianbarangspkoreksi', [PemakaianBarangController::class, 'spKoreksi']);
    Route::post('/pemakaianbarangdetailCetak', [PemakaianBarangController::class, 'getDetailCetak']);

    // PEMBEBANAN PEMAKAIAN
    Route::get('/pembebananpemakaian', [PembebananPemakaianController::class, 'index']);
    Route::post('/pembebananpemakaiangetdetailpenerimaan', [PembebananPemakaianController::class, 'getDetailPenerimaan']);
    Route::get('/pembebananpemakaianlistperkiraan', [PembebananPemakaianController::class, 'Perkiraan']);
    Route::get('/pembebananpemakaianlistcosting', [PembebananPemakaianController::class, 'Costing']);
    Route::get('/pembebananpemakaianlistsubcosting', [PembebananPemakaianController::class, 'SubCosting']);
    Route::get('/pembebananpemakaianloadall', [PembebananPemakaianController::class, 'loadAll']);
    Route::post('/pembebananpemakaianspkoreksi', [PembebananPemakaianController::class, 'spKoreksi']);
    Route::post('/pembebananpemakaianspotorisasi', [PembebananPemakaianController::class, 'spOtorisasi']);
    Route::post('/pembebananpemakaianspbatalotorisasi', [PembebananPemakaianController::class, 'spBatalOtorisasi']);
    Route::post('/pembebananpemakaiandetailCetak', [PembebananPemakaianController::class, 'getDetailCetak']);

    // CLOSING TRANSFER
    Route::get('/closingtransfer', [ClosingTransferController::class, 'index']);
    Route::get('/closingtransferloadall', [ClosingTransferController::class, 'loadAll']);
    Route::post('/closingtransferlock', [ClosingTransferController::class, 'lock']);
    Route::post('/closingtransferunlock', [ClosingTransferController::class, 'unlock']);

    // CLOSING PERINTAH OPNAME
    Route::get('/closingperintahopname', [ClosingPerintahOpnameController::class, 'index']);
    Route::get('/closingperintahopnameloadall', [ClosingPerintahOpnameController::class, 'loadAll']);
    Route::post('/closingperintahopnamelock', [ClosingPerintahOpnameController::class, 'lock']);
    Route::post('/closingperintahopnameunlock', [ClosingPerintahOpnameController::class, 'unlock']);

    // TERIMA TRANSFER BARANG
    Route::get('/terimatransferbarang', [TerimaTransferBarangController::class, 'index']);
    Route::get('/terimatransferbarangspnobukti', [TerimaTransferBarangController::class, 'getNoBukti']);
    Route::post('/terimatransferbaranggetdetail', [TerimaTransferBarangController::class, 'getDetail']);
    Route::post('/terimatransferbarangspadd', [TerimaTransferBarangController::class, 'spAdd']);
    Route::post('/terimatransferbaranggetdetailtransferbarang', [TerimaTransferBarangController::class, 'getDetail']);
    Route::post('/terimatransferbaranggetdetailpenerimaan', [TerimaTransferBarangController::class, 'getDetailPenerimaan']);
    Route::post('/terimatransferbarangspkoreksi', [TerimaTransferBarangController::class, 'spKoreksi']);
    Route::post('/terimatransferbarangonchangeheader', [TerimaTransferBarangController::class, 'onChangeHeader']);
    Route::get('/terimatransferbarangloadall', [TerimaTransferBarangController::class, 'loadAll']);
    Route::post('/terimatransferbarangspotorisasi', [TerimaTransferBarangController::class, 'spOtorisasi']);
    Route::post('/terimatransferbarangspbatalotorisasi', [TerimaTransferBarangController::class, 'spBatalOtorisasi']);
    Route::post('/trfbrgCekQntStock', [TransferBarangController::class, 'cekQntStock']);
    Route::post('/trfbrgsimpandetail', [TransferBarangController::class, 'simpanDetail']);

    // PERMINTAAN PENYERAHAN GUDANG
    Route::get('/permintaanpemakaian', [PermintaanPemakaianController::class, 'index']);
    Route::post('/permintaanpemakaianbatalotorisasi', [PermintaanPemakaianController::class, 'updateBatalOtorisasi']);
    Route::post('/permintaanpemakaianotorisasi', [PermintaanPemakaianController::class, 'updateOtorisasi']);
    Route::get('/permintaanpemakaianlistbarang', [PermintaanPemakaianController::class, 'listBarang']);
    Route::get('/permintaanpemakaianlistgudang', [PermintaanPemakaianController::class, 'listGudang']);
    Route::post('/permintaanpemakaiandetailpenerimaan', [PermintaanPemakaianController::class, 'detailPenerimaan']);
    Route::get('/permintaanpemakaianloadall', [PermintaanPemakaianController::class, 'loadAll']);
    Route::post('/permintaanpemakaianspadd', [PermintaanPemakaianController::class, 'spAdd']);
    Route::post('/permintaanpemakaiandetailCetak', [PermintaanPemakaianController::class, 'getDetailCetak']);

    // GUDANG PERMINTAAN SAMPLE
    Route::get('/gudangpermintaansample', [GudangPermintaanSampleController::class, 'index']);
    Route::get('/permintaansamplespnobukti', [GudangPermintaanSampleController::class, 'getNoBukti']);
    Route::get('/permintaansamplelistcustomer', [GudangPermintaanSampleController::class, 'Customer']);
    Route::get('/permintaansamplecekrefpr', [GudangPermintaanSampleController::class, 'cekRefPR']);
    Route::get('/permintaansamplelistsales', [GudangPermintaanSampleController::class, 'Sales']);
    Route::get('/permintaansamplelistgudang', [GudangPermintaanSampleController::class, 'Gudang']);
    Route::get('/permintaansamplelistbarang', [GudangPermintaanSampleController::class, 'listBarang']);
    Route::post('/permintaansamplespadd', [GudangPermintaanSampleController::class, 'spAdd']);
    Route::get('/permintaansamplespdetail', [GudangPermintaanSampleController::class, 'spDetail']);
    Route::get('/permintaansampleloadall', [GudangPermintaanSampleController::class, 'loadAll']);
    Route::post('/permintaansamplespdelete', [GudangPermintaanSampleController::class, 'spDelete']);
    Route::post('/permintaansampleonchangeheader', [GudangPermintaanSampleController::class, 'onChangeHeader']);
    Route::post('/permintaansampleupdateotorisasi', [GudangPermintaanSampleController::class, 'updateOtorisasi']);
    Route::post('/permintaansampleupdatebatalotorisasi', [GudangPermintaanSampleController::class, 'updateBatalOtorisasi']);
    Route::post('/permintaansampledetailCetak', [GudangPermintaanSampleController::class, 'getDetailCetak']);

    // PENYERAHAN SAMPLE GUDANG
    Route::get('/gudangpenyerahansample', [GudangPenyerahanSampleController::class, 'index']);
    Route::get('/penyerahansamplespnobukti', [GudangPenyerahanSampleController::class, 'getNoBukti']);
    Route::post('/penyerahansamplegetdetail', [GudangPenyerahanSampleController::class, 'getDetail']);
    Route::post('/penyerahansamplespadd', [GudangPenyerahanSampleController::class, 'spAdd']);
    Route::post('/penyerahansamplegetdetailpenerimaan', [GudangPenyerahanSampleController::class, 'getDetailPenerimaan']);
    Route::post('/penyerahansamplespkoreksi', [GudangPenyerahanSampleController::class, 'spKoreksi']);
    Route::post('/penyerahansamplegetdetailpenerimaanadd', [GudangPenyerahanSampleController::class, 'getDetail']);
    Route::post('/penyerahansampleonchangeheader', [GudangPenyerahanSampleController::class, 'onChangeHeader']);
    Route::get('/penyerahansampleloadall', [GudangPenyerahanSampleController::class, 'loadAll']);
    Route::post('/penyerahansamplespotorisasi', [GudangPenyerahanSampleController::class, 'spOtorisasi']);
    Route::post('/penyerahansamplespbatalotorisasi', [GudangPenyerahanSampleController::class, 'spBatalOtorisasi']);
    Route::post('/penyerahansampledetailCetak', [GudangPenyerahanSampleController::class, 'getDetailCetak']);

    // RETUR PENYERAHAN SAMPLE
    Route::get('/gudangreturpenyerahansample', [GudangReturPenyerahanSampleController::class, 'index']);
    Route::get('/returpenyerahansamplespnobukti', [GudangReturPenyerahanSampleController::class, 'getNoBukti']);
    Route::get('/returpenyerahansamplenosample', [GudangReturPenyerahanSampleController::class, 'SerahSample']);
    Route::get('/returpenyerahansamplelistsales', [GudangReturPenyerahanSampleController::class, 'Sales']);
    Route::get('/returpenyerahansamplelistbarang', [GudangReturPenyerahanSampleController::class, 'listBarang']);
    Route::post('/returpenyerahansamplespadd', [GudangReturPenyerahanSampleController::class, 'spAdd']);
    Route::get('/returpenyerahansamplespdetail', [GudangReturPenyerahanSampleController::class, 'spDetail']);
    Route::get('/returpenyerahansampleloadall', [GudangReturPenyerahanSampleController::class, 'loadAll']);
    Route::post('/returpenyerahansamplespdelete', [GudangReturPenyerahanSampleController::class, 'spDelete']);
    Route::post('/returpenyerahansampleupdateotorisasi', [GudangReturPenyerahanSampleController::class, 'updateOtorisasi']);
    Route::post('/returpenyerahansampleupdatebatalotorisasi', [GudangReturPenyerahanSampleController::class, 'updateBatalOtorisasi']);
    Route::post('/returpenyerahansampledetailCetak', [GudangReturPenyerahanSampleController::class, 'getDetailCetak']);

    // GUDANG CLOSING PENYERAHAN SAMPLE
    Route::get('/gudangclosingpenyerahansample', [GudangClosingPenyerahanSampleController::class, 'index']);
    Route::get('/closingpenyerahansampleloadall', [GudangClosingPenyerahanSampleController::class, 'loadAll']);
    Route::post('/closingpenyerahansamplelock', [GudangClosingPenyerahanSampleController::class, 'lock']);
    Route::post('/closingpenyerahansampleunlock', [GudangClosingPenyerahanSampleController::class, 'unlock']);

    // GUDANG PERMINTAAN KONSINYASI
    Route::get('/gudangpermintaankonsinyasi', [GudangPermintaanKonsinyasiController::class, 'index']);
    Route::get('/permintaankonsinyasispnobukti', [GudangPermintaanKonsinyasiController::class, 'getNoBukti']);
    Route::get('/permintaankonsinyasilistcustomer', [GudangPermintaanKonsinyasiController::class, 'Customer']);
    Route::get('/permintaankonsinyasicekrefpr', [GudangPermintaanKonsinyasiController::class, 'cekRefPR']);
    Route::get('/permintaankonsinyasilistsales', [GudangPermintaanKonsinyasiController::class, 'Sales']);
    Route::get('/permintaankonsinyasilistgudang', [GudangPermintaanKonsinyasiController::class, 'Gudang']);
    Route::get('/permintaankonsinyasilistbarang', [GudangPermintaanKonsinyasiController::class, 'listBarang']);
    Route::post('/permintaankonsinyasispadd', [GudangPermintaanKonsinyasiController::class, 'spAdd']);
    Route::get('/permintaankonsinyasispdetail', [GudangPermintaanKonsinyasiController::class, 'spDetail']);
    Route::get('/permintaankonsinyasiloadall', [GudangPermintaanKonsinyasiController::class, 'loadAll']);
    Route::post('/permintaankonsinyasispdelete', [GudangPermintaanKonsinyasiController::class, 'spDelete']);
    Route::post('/permintaankonsinyasionchangeheader', [GudangPermintaanKonsinyasiController::class, 'onChangeHeader']);
    Route::post('/permintaankonsinyasiupdateotorisasi', [GudangPermintaanKonsinyasiController::class, 'updateOtorisasi']);
    Route::post('/permintaankonsinyasiupdatebatalotorisasi', [GudangPermintaanKonsinyasiController::class, 'updateBatalOtorisasi']);
    Route::post('/permintaankonsinyasidetailCetak', [GudangPermintaanKonsinyasiController::class, 'getDetailCetak']);
    Route::get('/permintaankonsinyasilistlokasi', [GudangPermintaanKonsinyasiController::class, 'listLokasi']);

    // PENYERAHAN KONSINYASI GUDANG
    Route::get('/gudangpenyerahankonsinyasi', [GudangPenyerahanKonsinyasiController::class, 'index']);
    Route::get('/penyerahankonsinyasispnobukti', [GudangPenyerahanKonsinyasiController::class, 'getNoBukti']);
    Route::post('/penyerahankonsinyasigetdetail', [GudangPenyerahanKonsinyasiController::class, 'getDetail']);
    Route::post('/penyerahankonsinyasispadd', [GudangPenyerahanKonsinyasiController::class, 'spAdd']);
    Route::post('/penyerahankonsinyasigetdetailpenerimaan', [GudangPenyerahanKonsinyasiController::class, 'getDetailPenerimaan']);
    Route::post('/penyerahankonsinyasispkoreksi', [GudangPenyerahanKonsinyasiController::class, 'spKoreksi']);
    Route::post('/penyerahankonsinyasionchangeheader', [GudangPenyerahanKonsinyasiController::class, 'onChangeHeader']);
    Route::get('/penyerahankonsinyasiloadall', [GudangPenyerahanKonsinyasiController::class, 'loadAll']);
    Route::post('/penyerahankonsinyasispotorisasi', [GudangPenyerahanKonsinyasiController::class, 'spOtorisasi']);
    Route::post('/penyerahankonsinyasispbatalotorisasi', [GudangPenyerahanKonsinyasiController::class, 'spBatalOtorisasi']);
    Route::post('/penyerahankonsinyasigetdetailpenerimaanadd', [GudangPenyerahanKonsinyasiController::class, 'getDetail']);
    Route::post('/penyerahankonsinyasidetailCetak', [GudangPenyerahanKonsinyasiController::class, 'getDetailCetak']);

    // RETUR PENYERAHAN KONSINYASI
    Route::get('/gudangreturpenyerahankonsinyasi', [GudangReturPenyerahanKonsinyasiController::class, 'index']);
    Route::get('/returpenyerahankonsinyasispnobukti', [GudangReturPenyerahanKonsinyasiController::class, 'getNoBukti']);
    Route::get('/returpenyerahankonsinyasinokonsinyasi', [GudangReturPenyerahanKonsinyasiController::class, 'SerahKonsinyasi']);
    Route::get('/returpenyerahankonsinyasilistsales', [GudangReturPenyerahanKonsinyasiController::class, 'Sales']);
    Route::get('/returpenyerahankonsinyasilistbarang', [GudangReturPenyerahanKonsinyasiController::class, 'listBarang']);
    Route::post('/returpenyerahankonsinyasispadd', [GudangReturPenyerahanKonsinyasiController::class, 'spAdd']);
    Route::get('/returpenyerahankonsinyasispdetail', [GudangReturPenyerahanKonsinyasiController::class, 'spDetail']);
    Route::get('/returpenyerahankonsinyasiloadall', [GudangReturPenyerahanKonsinyasiController::class, 'loadAll']);
    Route::post('/returpenyerahankonsinyasispdelete', [GudangReturPenyerahanKonsinyasiController::class, 'spDelete']);
    Route::post('/returpenyerahankonsinyasiupdateotorisasi', [GudangReturPenyerahanKonsinyasiController::class, 'updateOtorisasi']);
    Route::post('/returpenyerahankonsinyasiupdatebatalotorisasi', [GudangReturPenyerahanKonsinyasiController::class, 'updateBatalOtorisasi']);
    Route::post('/returpenyerahankonsinyasidetailCetak', [GudangReturPenyerahanKonsinyasiController::class, 'getDetailCetak']);

    // PERINTAH OPNAME
    Route::get('/perintahopname', [PerintahOpnameController::class, 'index']);
    Route::post('/perintahopnamespdetail', [PerintahOpnameController::class, 'getDetail']);
    Route::get('/perintahopnamelistgudang', [PerintahOpnameController::class, 'listGudang']);
    Route::get('/perintahopnamelistmerk', [PerintahOpnameController::class, 'listMerk']);
    Route::get('/perintahopnamelistheadgroup', [PerintahOpnameController::class, 'listHeadGroup']);
    Route::post('/perintahopnamelistkategori', [PerintahOpnameController::class, 'listKategori']);
    Route::post('/perintahopnamelistsubkategori', [PerintahOpnameController::class, 'listSubKategori']);
    Route::post('/perintahopnamelistbarang', [PerintahOpnameController::class, 'listBarang']);
    Route::post('/perintahopnamespadd', [PerintahOpnameController::class, 'spAdd']);
    Route::get('/perintahopnameloadall', [PerintahOpnameController::class, 'loadAll']);
    Route::post('/perintahopnamespkoreksi', [PerintahOpnameController::class, 'spKoreksi']);
    Route::post('/perintahopnamespotorisasi', [PerintahOpnameController::class, 'spOtorisasi']);
    Route::post('/perintahopnamespbatalotorisasi', [PerintahOpnameController::class, 'spBatalOtorisasi']);
    Route::post('/perintahopnamedetailCetak', [PerintahOpnameController::class, 'getDetailCetak']);

    // BERITA ACARA OPNAME
    Route::get('/beritaacaraopname', [BeritaAcaraOpnameController::class, 'index']);
    Route::post('/beritaacaraopnamedetailkoreksi', [BeritaAcaraOpnameController::class, 'getDetailKoreksi']);
    Route::post('/beritaacaraopnamelistadd', [BeritaAcaraOpnameController::class, 'listAdd']);
    Route::post('/beritaacaraopnamelistdetailadd', [BeritaAcaraOpnameController::class, 'listAdd']);
    Route::post('/beritaacaraopnamespdelete', [BeritaAcaraOpnameController::class, 'spDelete']);
    Route::get('/beritaacaraopnameloadall', [BeritaAcaraOpnameController::class, 'loadAll']);
    Route::post('/beritaacaraopnamespadd', [BeritaAcaraOpnameController::class, 'spAdd']);
    Route::post('/beritaacaraopnamespkoreksi', [BeritaAcaraOpnameController::class, 'spKoreksi']);
    Route::post('/beritaacaraopnamespotorisasi', [BeritaAcaraOpnameController::class, 'spOtorisasi']);
    Route::post('/beritaacaraopnamespbatalotorisasi', [BeritaAcaraOpnameController::class, 'spBatalOtorisasi']);
    Route::post('/beritaacaraopnamespupdateheader', [BeritaAcaraOpnameController::class, 'spUpdateHeader']);
    Route::post('/beritaacaraopnamedetailCetak', [BeritaAcaraOpnameController::class, 'getDetailCetak']);

    // OPNAME BARANG
    Route::get('/opnamebarang', [OpnameBarangController::class, 'index']);
    Route::post('/opnamebarangdetailkoreksi', [OpnameBarangController::class, 'getDetailKoreksi']);
    Route::post('/opnamebaranglistadd', [OpnameBarangController::class, 'listAdd']);
    Route::post('/opnamebarangspdelete', [OpnameBarangController::class, 'spDelete']);
    Route::get('/opnamebarangloadall', [OpnameBarangController::class, 'loadAll']);
    Route::post('/opnamebarangspadd', [OpnameBarangController::class, 'spAdd']);
    Route::post('/opnamebarangspkoreksi', [OpnameBarangController::class, 'spKoreksi']);
    Route::post('/opnamebarangspotorisasi', [OpnameBarangController::class, 'spOtorisasi']);
    Route::post('/opnamebarangspbatalotorisasi', [OpnameBarangController::class, 'spBatalOtorisasi']);
    Route::post('/opnamebarangspupdateheader', [OpnameBarangController::class, 'spUpdateHeader']);
    Route::get('/opnamebaranglistpropname', [OpnameBarangController::class, 'listPROpname']);
    Route::post('/opnamebarangspaddpropname', [OpnameBarangController::class, 'spAddPROpname']);
    Route::post('/opnamebarangspkoreksinonbap', [OpnameBarangController::class, 'spKoreksiNonBap']);
    Route::post('/opnamebarangdetailCetak', [OpnameBarangController::class, 'getDetailCetak']);

    // KOREKSI STOCK
    Route::get('/koreksistock', [KoreksiStockController::class, 'index']);
    Route::post('/koreksistockspdetail', [KoreksiStockController::class, 'getDetail']);
    Route::get('/koreksistocklistgudang', [KoreksiStockController::class, 'listGudang']);
    Route::post('/koreksistocklistbarang', [KoreksiStockController::class, 'listBarang']);
    Route::post('/koreksistockspadd', [KoreksiStockController::class, 'spAdd']);
    Route::get('/koreksistockloadall', [KoreksiStockController::class, 'loadAll']);
    Route::post('/koreksistockspotorisasi', [KoreksiStockController::class, 'spOtorisasi']);
    Route::post('/koreksistockspbatalotorisasi', [KoreksiStockController::class, 'spBatalOtorisasi']);
    Route::post('/koreksistockspupdateheader', [KoreksiStockController::class, 'spUpdateHeader']);
    Route::post('/koreksistockdetailCetak', [KoreksiStockController::class, 'getDetailCetak']);

    // UBAH KEMASAN BARANG
    Route::get('/ubahkemasanbarang', [UbahKemasanBarangController::class, 'index']);
    Route::get('/kmbjloadall', [UbahKemasanBarangController::class, 'loadAll']);
    Route::get('/kmbjlistgudang', [UbahKemasanBarangController::class, 'listGudang']);
    Route::get('/kmbjlistbarang', [UbahKemasanBarangController::class, 'listBarang']);
    Route::post('/kmbjspadd', [UbahKemasanBarangController::class, 'spAdd']);
    Route::post('/kmbjonchangeheader', [UbahKemasanBarangController::class, 'onChangeHeader']);
    Route::post('/kmbjgetdetail', [UbahKemasanBarangController::class, 'getDetail']);
    Route::post('/kmbjcekotorisasi', [UbahKemasanBarangController::class, 'cekOtorisasi']);
    Route::post('/kmbjupdateotorisasi', [UbahKemasanBarangController::class, 'updateOtorisasi']);
    Route::post('/kmbjupdatebatalotorisasi', [UbahKemasanBarangController::class, 'updateBatalOtorisasi']);
    Route::post('/kmbjdetailCetak', [UbahKemasanBarangController::class, 'getDetailCetak']);

    // PERMINTAAN TRANSFER BARANG
    Route::get('/gudangpermintaantrfbrg', [PermintaanTransferBarangController::class, 'index']);
    Route::get('/prtlistgudangasal', [PermintaanTransferBarangController::class, 'listGudangAsal']);
    Route::get('/prtlistgudangtujuan', [PermintaanTransferBarangController::class, 'listGudangTujuan']);
    Route::get('/prtlistbarang', [PermintaanTransferBarangController::class, 'listBarang']);
    Route::get('/prtloadall', [PermintaanTransferBarangController::class, 'loadAll']);
    Route::post('/ceksatuanbarang', [PermintaanTransferBarangController::class, 'cekSatuanBarang']);
    Route::post('/prtspadd', [PermintaanTransferBarangController::class, 'spAdd']);
    Route::post('/prtgetdetail', [PermintaanTransferBarangController::class, 'getDetail']);
    Route::post('/prtcekotorisasi', [PermintaanTransferBarangController::class, 'cekOtorisasi']);
    Route::post('/prtupdateotorisasi', [PermintaanTransferBarangController::class, 'updateOtorisasi']);
    Route::post('/prtupdatebatalotorisasi', [PermintaanTransferBarangController::class, 'updateBatalOtorisasi']);
    Route::post('/prtonchangeheader', [PermintaanTransferBarangController::class, 'onChangeHeader']);
    Route::post('/prtonchangegudang', [PermintaanTransferBarangController::class, 'onChangeGudang']);
    Route::post('/prtdetailCetak', [PermintaanTransferBarangController::class, 'getDetailCetak']);

    // TRANSFER BARANG
    Route::get('/gudangtrfbrg', [TransferBarangController::class, 'index']);
    Route::get('/trfbrgcekotorisasi', [TransferBarangController::class, 'cekOtorisasi']);
    Route::get('/trfbrgloadall', [TransferBarangController::class, 'loadAll']);
    Route::post('/trfbrggetdetail', [TransferBarangController::class, 'getDetail']);
    Route::post('/trfbrggetdetailedit', [TransferBarangController::class, 'getDetailEdit']);
    Route::post('/trfbrgspadd', [TransferBarangController::class, 'spAdd']);
    Route::post('/trfbrgspaddmultiple', [TransferBarangController::class, 'spAddMultiple']);
    Route::post('/trfbrgdeletetransfer', [TransferBarangController::class, 'deleteTransfer']);
    Route::post('/trfbrgonchangeqnt', [TransferBarangController::class, 'onChangeQnt']);
    Route::post('/trfbrgupdateotorisasi', [TransferBarangController::class, 'updateOtorisasi']);
    Route::post('/trfbrgupdatebatalotorisasi', [TransferBarangController::class, 'updateBatalOtorisasi']);
    Route::post('/trfbrggetdetaileditAdd', [TransferBarangController::class, 'getDetail']);
    Route::post('/trfbrgCekQntStock', [TransferBarangController::class, 'cekQntStock']);
    Route::post('/trfbrgdetailCetak', [TransferBarangController::class, 'getDetailCetak']);

    // PEMBEBANAN SAMPLE
    Route::get('/pembebanansample', [PembebananSampleController::class, 'index']);
    Route::get('/bbsloadall', [PembebananSampleController::class, 'loadAll']);
    Route::get('/bbslistnoserahsample', [PembebananSampleController::class, 'listNoSerahSample']);
    Route::get('/bbslistsales', [PembebananSampleController::class, 'listSales']);
    Route::get('/bbslistcustomer', [PembebananSampleController::class, 'listCustomer']);
    Route::get('/bbslistbarang', [PembebananSampleController::class, 'listBarang']);
    Route::get('/bbslistgudang', [PembebananSampleController::class, 'listGudang']);
    Route::get('/bbsgetstockserahsample', [PembebananSampleController::class, 'getStockSerahSample']);
    Route::post('/bbsspadd', [PembebananSampleController::class, 'spAdd']);
    Route::post('/bbsgetdetail', [PembebananSampleController::class, 'getDetail']);
    Route::post('/bbscekotorisasi', [PembebananSampleController::class, 'cekOtorisasi']);
    Route::post('/bbsupdateotorisasi', [PembebananSampleController::class, 'updateOtorisasi']);
    Route::post('/bbsupdatebatalotorisasi', [PembebananSampleController::class, 'updateBatalOtorisasi']);
    Route::post('/bbsdetailCetak', [PembebananSampleController::class, 'getDetailCetak']);

});
