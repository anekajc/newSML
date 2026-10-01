<?php

use App\Http\Controllers\Master\MasterAktivaController;
use App\Http\Controllers\Master\MasterAreaController;
use App\Http\Controllers\Master\MasterBarangController;
use App\Http\Controllers\Master\MasterBarangJasaController;
use App\Http\Controllers\Master\MasterCostingController;
use App\Http\Controllers\Master\MasterCustomerController;
use App\Http\Controllers\Master\MasterDaftarBiayaController;
use App\Http\Controllers\Master\MasterDaftarDevisiController;
use App\Http\Controllers\Master\MasterDaftarKaryawanController;
use App\Http\Controllers\Master\MasterDepartemenController;
use App\Http\Controllers\Master\MasterEkspedisiController;
use App\Http\Controllers\Master\MasterGiroController;
use App\Http\Controllers\Master\MasterGroupController;
use App\Http\Controllers\Master\MasterGroupCustomerController;
use App\Http\Controllers\Master\MasterGudangController;
use App\Http\Controllers\Master\MasterHeadGroupController;
use App\Http\Controllers\Master\MasterHutangController;
use App\Http\Controllers\Master\MasterJabatanController;
use App\Http\Controllers\Master\MasterJenisCustomerController;
use App\Http\Controllers\Master\MasterKotaController;
use App\Http\Controllers\Master\MasterLabaRugiController;
use App\Http\Controllers\Master\MasterLokasiBarangController;
use App\Http\Controllers\Master\MasterMerkController;
use App\Http\Controllers\Master\MasterNeracaController;
use App\Http\Controllers\Master\MasterNilaiPPNController;
use App\Http\Controllers\Master\MasterNoPolController;
use App\Http\Controllers\Master\MasterNomorFakturPajakController;
use App\Http\Controllers\Master\MasterPiutangController;
use App\Http\Controllers\Master\MasterSalesController;
use App\Http\Controllers\Master\MasterSatuanController;
use App\Http\Controllers\Master\MasterSetLokasiBarangController;
use App\Http\Controllers\Master\MasterSetPostingController;
use App\Http\Controllers\Master\MasterSopirController;
use App\Http\Controllers\Master\MasterSupplierBankController;
use App\Http\Controllers\Master\MasterSupplierController;
use App\Http\Controllers\Master\MasterValasController;
use App\Http\Controllers\Master\NewMasterPerkiraanController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingAktivaController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingAkumulasiController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingBankController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingDepositoController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingGiroBukaController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingGiroTerimaController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingHargaPokokController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingHutangController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingHutangSementaraController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingKasController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingPPHKeluaranController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingPPHMasukanController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingPPNKeluaranController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingPPNMasukanController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingPiutangController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingPiutangSementaraController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingRLBulanLaluController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingRLTahunIniController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingRLTahunLaluController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingSelisihController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingUMHutangController;
use App\Http\Controllers\Master\MasterSetPosting\MasterSetPostingUMPiutangController;

// Master

// ACCOUNTING ================================================================================================================================================
//============================================================================================================================================================

// MASTER SATUAN ==========================================================
Route::get('/mastersatuan', [MasterSatuanController::class, 'index'])->middleware('auth');
Route::get('/mastersatuanloadall', [MasterSatuanController::class, 'loadAll'])->middleware('auth');
Route::post('/mastersatuanspadd', [MasterSatuanController::class, 'spAdd'])->middleware('auth');
Route::post('/mastersatuanspedit', [MasterSatuanController::class, 'spEdit'])->middleware('auth');
Route::post('/mastersatuanspdelete', [MasterSatuanController::class, 'spDelete'])->middleware('auth');
Route::get('/mastersatuanspdetail', [MasterSatuanController::class, 'spDetail'])->middleware('auth');
Route::get('/masterSatuanAmbilSatTax', [MasterSatuanController::class, 'AmbilSatTax'])->middleware('auth');

// MASTER VALAS ==========================================================
Route::get('/mastervalas', [MasterValasController::class, 'index'])->middleware('auth');
Route::get('/mastervalasloadall', [MasterValasController::class, 'loadAll'])->middleware('auth');
Route::post('/mastervalasspadd', [MasterValasController::class, 'spAdd'])->middleware('auth');
Route::post('/mastervalasspedit', [MasterValasController::class, 'spEdit'])->middleware('auth');
Route::post('/mastervalasspdelete', [MasterValasController::class, 'spDelete'])->middleware('auth');
Route::get('/mastervalasspdetail', [MasterValasController::class, 'spDetail'])->middleware('auth');

// MASTER DAFTAR DEVISI ==========================================================
Route::get('/masterdaftardevisi', [MasterDaftarDevisiController::class, 'index'])->middleware('auth');
Route::get('/masterdaftardevisiloadall', [MasterDaftarDevisiController::class, 'loadAll'])->middleware('auth');
Route::post('/masterdaftardevisispadd', [MasterDaftarDevisiController::class, 'spAdd'])->middleware('auth');
Route::post('/masterdaftardevisispedit', [MasterDaftarDevisiController::class, 'spEdit'])->middleware('auth');
Route::post('/masterdaftardevisispdelete', [MasterDaftarDevisiController::class, 'spDelete'])->middleware('auth');
Route::get('/masterdaftardevisispdetail', [MasterDaftarDevisiController::class, 'spDetail'])->middleware('auth');

// MASTER PERKIRAAN ======================================================
Route::get('/newperkiraan', [NewMasterPerkiraanController::class, 'index'])->middleware('auth');
Route::post('/newaddperkiraan', [NewMasterPerkiraanController::class, 'spAdd'])->middleware('auth');
Route::post('/newdetailperkiraan' , [NewMasterPerkiraanController::class, 'detail'])->middleware('auth');
Route::get('/newperkiraanloadall' , [NewMasterPerkiraanController::class, 'loadAll'])->middleware('auth');
Route::get('/newperkiraanloadvalas' , [NewMasterPerkiraanController::class, 'loadValas'])->middleware('auth');
Route::get('/newperkiraangetallperkiraan' , [NewMasterPerkiraanController::class, 'getAllPerkiraan'])->middleware('auth');

// MASTER AKTIVA ==========================================================
Route::get('/masteraktiva', [MasterAktivaController::class, 'index'])->middleware('auth');
Route::get('/masteraktivaloadall', [MasterAktivaController::class, 'loadAll'])->middleware('auth');
Route::post('/masteraktivaspadd', [MasterAktivaController::class, 'spAdd'])->middleware('auth');
Route::post('/masteraktivaspdelete', [MasterAktivaController::class, 'spDelete'])->middleware('auth');
Route::get('/masteraktivaloadgroupaktiva', [MasterAktivaController::class, 'loadGroupAktiva'])->middleware('auth');
Route::get('/masteraktivaloadeditgroupaktiva', [MasterAktivaController::class, 'loadEditGroupAktiva'])->middleware('auth');
Route::get('/masteraktivaloaddevisi', [MasterAktivaController::class, 'loadDevisi'])->middleware('auth');
Route::get('/masteraktivaloadakumulasipenyusutan', [MasterAktivaController::class, 'loadAkumulasiPenyusutan'])->middleware('auth');
Route::get('/masteraktivaspdetail' , [MasterAktivaController::class, 'spDetail'])->middleware('auth');
Route::get('/masteraktivaloadvalas' , [MasterAktivaController::class, 'loadValas'])->middleware('auth');
Route::post('/masteraktivaspedit', [MasterAktivaController::class, 'spEdit'])->middleware('auth');

Route::post('spnoaktiva', [MasterAktivaController::class, 'getNoAktiva'])->middleware('auth');
Route::get('/masteraktivaloadbiayapenyusutan', [MasterAktivaController::class, 'loadBiayaPenyusutan'])->middleware('auth');
Route::post('/masteraktivaspaddsaldoawal', [MasterAktivaController::class, 'spAddSaldoAwal'])->middleware('auth');
Route::get('/masteraktivaspdetailsaldoawal' , [MasterAktivaController::class, 'spDetailSaldoAwal'])->middleware('auth');

// MASTER HUTANG ==========================================================
Route::get('/masterhutang', [MasterHutangController::class, 'index'])->middleware('auth');
Route::get('/masterhutangloadall', [MasterHutangController::class, 'loadAll'])->middleware('auth');
Route::post('/masterhutangspadd', [MasterHutangController::class, 'spAdd'])->middleware('auth');
Route::post('/masterhutangspedit', [MasterHutangController::class, 'spEdit'])->middleware('auth');
Route::post('/masterhutangspdelete', [MasterHutangController::class, 'spDelete'])->middleware('auth');
Route::get('/masterhutangspdetail', [MasterHutangController::class, 'spDetail'])->middleware('auth');
Route::get('/masterhutangloadvalas', [MasterHutangController::class, 'loadValas'])->middleware('auth');
Route::get('/masterhutangloadcustomer', [MasterHutangController::class, 'loadCustomer'])->middleware('auth');

// MASTER PIUTANG ==========================================================
Route::get('/masterpiutang', [MasterPiutangController::class, 'index'])->middleware('auth');
Route::get('/masterpiutangloadall', [MasterPiutangController::class, 'loadAll'])->middleware('auth');
Route::post('/masterpiutangspadd', [MasterPiutangController::class, 'spAdd'])->middleware('auth');
Route::post('/masterpiutangspedit', [MasterPiutangController::class, 'spEdit'])->middleware('auth');
Route::post('/masterpiutangspdelete', [MasterPiutangController::class, 'spDelete'])->middleware('auth');
Route::get('/masterpiutangspdetail', [MasterPiutangController::class, 'spDetail'])->middleware('auth');
Route::get('/masterpiutangloadvalas', [MasterPiutangController::class, 'loadValas'])->middleware('auth');
Route::get('/masterpiutangloadlokasipenerima', [MasterPiutangController::class, 'loadLokasiPenerima'])->middleware('auth');
Route::get('/masterpiutangspdetaillokasipenerima', [MasterPiutangController::class, 'spLokPenerima'])->middleware('auth');

// MASTER GIRO BUKA ==========================================================
Route::get('/mastergiro', [MasterGiroController::class, 'index'])->middleware('auth');
Route::get('/mastergiroloadallterima', [MasterGiroController::class, 'loadAllTerima'])->middleware('auth');
Route::get('/mastergiroloadallbuka', [MasterGiroController::class, 'loadAllBuka'])->middleware('auth');
Route::post('/mastergirospaddbuka', [MasterGiroController::class, 'spAddBuka'])->middleware('auth');
Route::post('/mastergirospaddterima', [MasterGiroController::class, 'spAddTerima'])->middleware('auth');
Route::post('/mastergirospeditbuka', [MasterGiroController::class, 'spEditBuka'])->middleware('auth');
Route::post('/mastergirospeditterima', [MasterGiroController::class, 'spEditTerima'])->middleware('auth');
Route::post('/mastergirospdeletebuka', [MasterGiroController::class, 'spDeleteBuka'])->middleware('auth');
Route::post('/mastergirospdeleteterima', [MasterGiroController::class, 'spDeleteTerima'])->middleware('auth');
Route::get('/mastergirospdetailbuka', [MasterGiroController::class, 'spDetailBuka'])->middleware('auth');
Route::get('/mastergirospdetailterima', [MasterGiroController::class, 'spDetailTerima'])->middleware('auth');
Route::get('/mastergiroloadkas', [MasterGiroController::class, 'loadPostingKas'])->middleware('auth');
Route::get('/mastergiroloadbank', [MasterGiroController::class, 'loadPostingBank'])->middleware('auth');

// MASTER LABA RUGI & HPP ==========================================================
Route::get('/masterlabarugi', [MasterLabaRugiController::class, 'index'])->middleware('auth');
Route::get('/masterlabarugiloadall', [MasterLabaRugiController::class, 'loadAll'])->middleware('auth');
Route::post('/masterLabaRugiSubmitAdd', [MasterLabaRugiController::class, 'spAdd'])->middleware('auth');
Route::post('/masterLabaRugiSubmitEdit', [MasterLabaRugiController::class, 'spEdit'])->middleware('auth');
Route::post('/masterLabaRugiSubmitDelete', [MasterLabaRugiController::class, 'spDelete'])->middleware('auth');
Route::post('/masterLabaRugiLoadEdit', [MasterLabaRugiController::class, 'loadEdit'])->middleware('auth');
Route::post('/masterlabarugispedit', [MasterLabaRugiController::class, 'spEdit'])->middleware('auth');
Route::post('/masterlabarugispdelete', [MasterLabaRugiController::class, 'spDelete'])->middleware('auth');
Route::get('/masterLabaRugiLoadPerkiraan', [MasterLabaRugiController::class, 'loadPerkiraan'])->middleware('auth');

// MASTER NERACA ==========================================================
Route::get('/masterneraca', [MasterNeracaController::class, 'index'])->middleware('auth');
Route::get('/masterneracaloadall', [MasterNeracaController::class, 'loadAll'])->middleware('auth');
Route::post('/masterneracaspedit', [MasterNeracaController::class, 'spEdit'])->middleware('auth');
Route::get('/masterneracaspdetail', [MasterNeracaController::class, 'spDetail'])->middleware('auth');
Route::get('/masterneracaonChangeNeraca', [MasterNeracaController::class, 'onChangeNeraca'])->middleware('auth');

// MASTER COSTING ==========================================================
Route::get('/mastercosting', [MasterCostingController::class, 'index'])->middleware('auth');
Route::get('/mastercostingloadall', [MasterCostingController::class, 'loadAll'])->middleware('auth');
Route::get('/mastercostingloadperkiraanakumulasi', [MasterCostingController::class, 'loadPerkiraanAkumulasi'])->middleware('auth');
Route::post('/mastercostingspadd', [MasterCostingController::class, 'spAdd'])->middleware('auth');
Route::post('/mastercostingspedit', [MasterCostingController::class, 'spEdit'])->middleware('auth');
Route::post('/mastercostingspdelete', [MasterCostingController::class, 'spDelete'])->middleware('auth');
Route::get('/mastercostingspdetail', [MasterCostingController::class, 'spDetail'])->middleware('auth');
Route::get('/mastercostinglistperkiraan', [MasterCostingController::class, 'listPerkiraanSubCost'])->middleware('auth');
Route::get('/mastercostinglistdetailakun', [MasterCostingController::class, 'listDetailAkun'])->middleware('auth');
Route::get('/mastercostinglistdetailakunedit', [MasterCostingController::class, 'listDetailAkunEdit'])->middleware('auth');
Route::get('/mastercostingspperkiraan', [MasterCostingController::class, 'spPerkiraan'])->middleware('auth');

// MASTER SET POSTING ==========================================================
Route::get('/mastersetposting', [MasterSetPostingController::class, 'index'])->middleware('auth');

Route::middleware('auth')->group(function () {

    // AKTIVA
    Route::get('/mastersetpostingaktiva', [MasterSetPostingAktivaController::class, 'index']);
    Route::post('/mastersetpostingaktivaspadd', [MasterSetPostingAktivaController::class, 'spAdd']);
    Route::get('/mastersetpostingaktivaloadall', [MasterSetPostingAktivaController::class, 'loadAll']);
    Route::post('/mastersetpostingaktivaspdelete', [MasterSetPostingAktivaController::class, 'spDelete']);
    Route::get('/mastersetpostingaktivaspdetail', [MasterSetPostingAktivaController::class, 'spDetail']);
    Route::get('/mastersetpostingaktivaloadperkiraan', [MasterSetPostingAktivaController::class, 'loadPerkiraan']);
    Route::get('/mastersetpostingaktivaloadperkiraanpenyusutan', [MasterSetPostingAktivaController::class, 'loadPerkiraanPenyusutan']);
    Route::get('/mastersetpostingaktivaloadakumulasi', [MasterSetPostingAktivaController::class, 'loadAkumulasi']);
    Route::post('/mastersetpostingaktivaspedit', [MasterSetPostingAktivaController::class, 'spEdit']);

    // AKUMULASI
    Route::get('/mastersetpostingakumulasi', [MasterSetPostingAkumulasiController::class, 'index']);
    Route::post('/mastersetpostingakumulasispadd', [MasterSetPostingAkumulasiController::class, 'spAdd']);
    Route::get('/mastersetpostingakumulasiloadall', [MasterSetPostingAkumulasiController::class, 'loadAll']);
    Route::post('/mastersetpostingakumulasispdelete', [MasterSetPostingAkumulasiController::class, 'spDelete']);
    Route::get('/mastersetpostingakumulasispdetail', [MasterSetPostingAkumulasiController::class, 'spDetail']);
    Route::get('/mastersetpostingakumulasiloadperkiraan', [MasterSetPostingAkumulasiController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingakumulasispedit', [MasterSetPostingAkumulasiController::class, 'spEdit']);

    // KAS
    Route::get('/mastersetpostingkas', [MasterSetPostingKasController::class, 'index']);
    Route::post('/mastersetpostingkasspadd', [MasterSetPostingKasController::class, 'spAdd']);
    Route::get('/mastersetpostingkasloadall', [MasterSetPostingKasController::class, 'loadAll']);
    Route::post('/mastersetpostingkasspdelete', [MasterSetPostingKasController::class, 'spDelete']);
    Route::get('/mastersetpostingkasspdetail', [MasterSetPostingKasController::class, 'spDetail']);
    Route::get('/mastersetpostingkasloadperkiraan', [MasterSetPostingKasController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingkasspedit', [MasterSetPostingKasController::class, 'spEdit']);

    // BANK
    Route::get('/mastersetpostingbank', [MasterSetPostingBankController::class, 'index']);
    Route::post('/mastersetpostingbankspadd', [MasterSetPostingBankController::class, 'spAdd']);
    Route::get('/mastersetpostingbankloadall', [MasterSetPostingBankController::class, 'loadAll']);
    Route::post('/mastersetpostingbankspdelete', [MasterSetPostingBankController::class, 'spDelete']);
    Route::get('/mastersetpostingbankspdetail', [MasterSetPostingBankController::class, 'spDetail']);
    Route::get('/mastersetpostingbankloadperkiraan', [MasterSetPostingBankController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingbankspedit', [MasterSetPostingBankController::class, 'spEdit']);

    // HUTANG
    Route::get('/mastersetpostinghutang', [MasterSetPostingHutangController::class, 'index']);
    Route::post('/mastersetpostinghutangspadd', [MasterSetPostingHutangController::class, 'spAdd']);
    Route::get('/mastersetpostinghutangloadall', [MasterSetPostingHutangController::class, 'loadAll']);
    Route::post('/mastersetpostinghutangspdelete', [MasterSetPostingHutangController::class, 'spDelete']);
    Route::get('/mastersetpostinghutangspdetail', [MasterSetPostingHutangController::class, 'spDetail']);
    Route::get('/mastersetpostinghutangloadperkiraan', [MasterSetPostingHutangController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostinghutangspedit', [MasterSetPostingHutangController::class, 'spEdit']);

    // PIUTANG
    Route::get('/mastersetpostingpiutang', [MasterSetPostingPiutangController::class, 'index']);
    Route::post('/mastersetpostingpiutangspadd', [MasterSetPostingPiutangController::class, 'spAdd']);
    Route::get('/mastersetpostingpiutangloadall', [MasterSetPostingPiutangController::class, 'loadAll']);
    Route::post('/mastersetpostingpiutangspdelete', [MasterSetPostingPiutangController::class, 'spDelete']);
    Route::get('/mastersetpostingpiutangspdetail', [MasterSetPostingPiutangController::class, 'spDetail']);
    Route::get('/mastersetpostingpiutangloadperkiraan', [MasterSetPostingPiutangController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingpiutangspedit', [MasterSetPostingPiutangController::class, 'spEdit']);

    // HARGA POKOK
    Route::get('/mastersetpostinghargapokok', [MasterSetPostingHargaPokokController::class, 'index']);
    Route::post('/mastersetpostinghargapokokspadd', [MasterSetPostingHargaPokokController::class, 'spAdd']);
    Route::get('/mastersetpostinghargapokokloadall', [MasterSetPostingHargaPokokController::class, 'loadAll']);
    Route::post('/mastersetpostinghargapokokspdelete', [MasterSetPostingHargaPokokController::class, 'spDelete']);
    Route::get('/mastersetpostinghargapokokspdetail', [MasterSetPostingHargaPokokController::class, 'spDetail']);
    Route::get('/mastersetpostinghargapokokloadperkiraan', [MasterSetPostingHargaPokokController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostinghargapokokspedit', [MasterSetPostingHargaPokokController::class, 'spEdit']);

    // DEPOSITO
    Route::get('/mastersetpostingdeposito', [MasterSetPostingDepositoController::class, 'index']);
    Route::post('/mastersetpostingdepositospadd', [MasterSetPostingDepositoController::class, 'spAdd']);
    Route::get('/mastersetpostingdepositoloadall', [MasterSetPostingDepositoController::class, 'loadAll']);
    Route::post('/mastersetpostingdepositospdelete', [MasterSetPostingDepositoController::class, 'spDelete']);
    Route::get('/mastersetpostingdepositospdetail', [MasterSetPostingDepositoController::class, 'spDetail']);
    Route::get('/mastersetpostingdepositoloadperkiraan', [MasterSetPostingDepositoController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingdepositospedit', [MasterSetPostingDepositoController::class, 'spEdit']);

    // UM HUTANG
    Route::get('/mastersetpostingumhutang', [MasterSetPostingUMHutangController::class, 'index']);
    Route::post('/mastersetpostingumhutangspadd', [MasterSetPostingUMHutangController::class, 'spAdd']);
    Route::get('/mastersetpostingumhutangloadall', [MasterSetPostingUMHutangController::class, 'loadAll']);
    Route::post('/mastersetpostingumhutangspdelete', [MasterSetPostingUMHutangController::class, 'spDelete']);
    Route::get('/mastersetpostingumhutangspdetail', [MasterSetPostingUMHutangController::class, 'spDetail']);
    Route::get('/mastersetpostingumhutangloadperkiraan', [MasterSetPostingUMHutangController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingumhutangspedit', [MasterSetPostingUMHutangController::class, 'spEdit']);

    // UM PIUTANG
    Route::get('/mastersetpostingumpiutang', [MasterSetPostingUMPiutangController::class, 'index']);
    Route::post('/mastersetpostingumpiutangspadd', [MasterSetPostingUMPiutangController::class, 'spAdd']);
    Route::get('/mastersetpostingumpiutangloadall', [MasterSetPostingUMPiutangController::class, 'loadAll']);
    Route::post('/mastersetpostingumpiutangspdelete', [MasterSetPostingUMPiutangController::class, 'spDelete']);
    Route::get('/mastersetpostingumpiutangspdetail', [MasterSetPostingUMPiutangController::class, 'spDetail']);
    Route::get('/mastersetpostingumpiutangloadperkiraan', [MasterSetPostingUMPiutangController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingumpiutangspedit', [MasterSetPostingUMPiutangController::class, 'spEdit']);

    // HUTANG SEMENTARA
    Route::get('/mastersetpostinghutangsementara', [MasterSetPostingHutangSementaraController::class, 'index']);
    Route::post('/mastersetpostinghutangsementaraspadd', [MasterSetPostingHutangSementaraController::class, 'spAdd']);
    Route::get('/mastersetpostinghutangsementaraloadall', [MasterSetPostingHutangSementaraController::class, 'loadAll']);
    Route::post('/mastersetpostinghutangsementaraspdelete', [MasterSetPostingHutangSementaraController::class, 'spDelete']);
    Route::get('/mastersetpostinghutangsementaraspdetail', [MasterSetPostingHutangSementaraController::class, 'spDetail']);
    Route::get('/mastersetpostinghutangsementaraloadperkiraan', [MasterSetPostingHutangSementaraController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostinghutangsementaraspedit', [MasterSetPostingHutangSementaraController::class, 'spEdit']);

    // PIUTANG SEMENTARA
    Route::get('/mastersetpostingpiutangsementara', [MasterSetPostingPiutangSementaraController::class, 'index']);
    Route::post('/mastersetpostingpiutangsementaraspadd', [MasterSetPostingPiutangSementaraController::class, 'spAdd']);
    Route::get('/mastersetpostingpiutangsementaraloadall', [MasterSetPostingPiutangSementaraController::class, 'loadAll']);
    Route::post('/mastersetpostingpiutangsementaraspdelete', [MasterSetPostingPiutangSementaraController::class, 'spDelete']);
    Route::get('/mastersetpostingpiutangsementaraspdetail', [MasterSetPostingPiutangSementaraController::class, 'spDetail']);
    Route::get('/mastersetpostingpiutangsementaraloadperkiraan', [MasterSetPostingPiutangSementaraController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingpiutangsementaraspedit', [MasterSetPostingPiutangSementaraController::class, 'spEdit']);

    // GIRO TERIMA
    Route::get('/mastersetpostinggiroterima', [MasterSetPostingGiroTerimaController::class, 'index']);
    Route::post('/mastersetpostinggiroterimaspadd', [MasterSetPostingGiroTerimaController::class, 'spAdd']);
    Route::get('/mastersetpostinggiroterimaloadall', [MasterSetPostingGiroTerimaController::class, 'loadAll']);
    Route::post('/mastersetpostinggiroterimaspdelete', [MasterSetPostingGiroTerimaController::class, 'spDelete']);
    Route::get('/mastersetpostinggiroterimaspdetail', [MasterSetPostingGiroTerimaController::class, 'spDetail']);
    Route::get('/mastersetpostinggiroterimaloadperkiraan', [MasterSetPostingGiroTerimaController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostinggiroterimaspedit', [MasterSetPostingGiroTerimaController::class, 'spEdit']);

    // GIRO BUKA
    Route::get('/mastersetpostinggirobuka', [MasterSetPostingGiroBukaController::class, 'index']);
    Route::post('/mastersetpostinggirobukaspadd', [MasterSetPostingGiroBukaController::class, 'spAdd']);
    Route::get('/mastersetpostinggirobukaloadall', [MasterSetPostingGiroBukaController::class, 'loadAll']);
    Route::post('/mastersetpostinggirobukaspdelete', [MasterSetPostingGiroBukaController::class, 'spDelete']);
    Route::get('/mastersetpostinggirobukaspdetail', [MasterSetPostingGiroBukaController::class, 'spDetail']);
    Route::get('/mastersetpostinggirobukaloadperkiraan', [MasterSetPostingGiroBukaController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostinggirobukaspedit', [MasterSetPostingGiroBukaController::class, 'spEdit']);

    // RL TAHUN LALU
    Route::get('/mastersetpostingrltahunlalu', [MasterSetPostingRLTahunLaluController::class, 'index']);
    Route::post('/mastersetpostingrltahunlaluspadd', [MasterSetPostingRLTahunLaluController::class, 'spAdd']);
    Route::get('/mastersetpostingrltahunlaluloadall', [MasterSetPostingRLTahunLaluController::class, 'loadAll']);
    Route::post('/mastersetpostingrltahunlaluspdelete', [MasterSetPostingRLTahunLaluController::class, 'spDelete']);
    Route::get('/mastersetpostingrltahunlaluspdetail', [MasterSetPostingRLTahunLaluController::class, 'spDetail']);
    Route::get('/mastersetpostingrltahunlaluloadperkiraan', [MasterSetPostingRLTahunLaluController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingrltahunlaluspedit', [MasterSetPostingRLTahunLaluController::class, 'spEdit']);

    // RL TAHUN INI
    Route::get('/mastersetpostingrltahunini', [MasterSetPostingRLTahunIniController::class, 'index']);
    Route::post('/mastersetpostingrltahuninispadd', [MasterSetPostingRLTahunIniController::class, 'spAdd']);
    Route::get('/mastersetpostingrltahuniniloadall', [MasterSetPostingRLTahunIniController::class, 'loadAll']);
    Route::post('/mastersetpostingrltahuninispdelete', [MasterSetPostingRLTahunIniController::class, 'spDelete']);
    Route::get('/mastersetpostingrltahuninispdetail', [MasterSetPostingRLTahunIniController::class, 'spDetail']);
    Route::get('/mastersetpostingrltahuniniloadperkiraan', [MasterSetPostingRLTahunIniController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingrltahuninispedit', [MasterSetPostingRLTahunIniController::class, 'spEdit']);

    // RL BULAN LALU
    Route::get('/mastersetpostingrlbulanlalu', [MasterSetPostingRLBulanLaluController::class, 'index']);
    Route::post('/mastersetpostingrlbulanlaluspadd', [MasterSetPostingRLBulanLaluController::class, 'spAdd']);
    Route::get('/mastersetpostingrlbulanlaluloadall', [MasterSetPostingRLBulanLaluController::class, 'loadAll']);
    Route::post('/mastersetpostingrlbulanlaluspdelete', [MasterSetPostingRLBulanLaluController::class, 'spDelete']);
    Route::get('/mastersetpostingrlbulanlaluspdetail', [MasterSetPostingRLBulanLaluController::class, 'spDetail']);
    Route::get('/mastersetpostingrlbulanlaluloadperkiraan', [MasterSetPostingRLBulanLaluController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingrlbulanlaluspedit', [MasterSetPostingRLBulanLaluController::class, 'spEdit']);

    // SELISIH
    Route::get('/mastersetpostingselisih', [MasterSetPostingSelisihController::class, 'index']);
    Route::post('/mastersetpostingselisihspadd', [MasterSetPostingSelisihController::class, 'spAdd']);
    Route::get('/mastersetpostingselisihloadall', [MasterSetPostingSelisihController::class, 'loadAll']);
    Route::post('/mastersetpostingselisihspdelete', [MasterSetPostingSelisihController::class, 'spDelete']);
    Route::get('/mastersetpostingselisihspdetail', [MasterSetPostingSelisihController::class, 'spDetail']);
    Route::get('/mastersetpostingselisihloadperkiraan', [MasterSetPostingSelisihController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingselisihspedit', [MasterSetPostingSelisihController::class, 'spEdit']);

    // PPN MASUKAN
    Route::get('/mastersetpostingppnmasukan', [MasterSetPostingPPNMasukanController::class, 'index']);
    Route::post('/mastersetpostingppnmasukanspadd', [MasterSetPostingPPNMasukanController::class, 'spAdd']);
    Route::get('/mastersetpostingppnmasukanloadall', [MasterSetPostingPPNMasukanController::class, 'loadAll']);
    Route::post('/mastersetpostingppnmasukanspdelete', [MasterSetPostingPPNMasukanController::class, 'spDelete']);
    Route::get('/mastersetpostingppnmasukanspdetail', [MasterSetPostingPPNMasukanController::class, 'spDetail']);
    Route::get('/mastersetpostingppnmasukanloadperkiraan', [MasterSetPostingPPNMasukanController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingppnmasukanspedit', [MasterSetPostingPPNMasukanController::class, 'spEdit']);

    // PPN KELUARAN
    Route::get('/mastersetpostingppnkeluaran', [MasterSetPostingPPNKeluaranController::class, 'index']);
    Route::post('/mastersetpostingppnkeluaranspadd', [MasterSetPostingPPNKeluaranController::class, 'spAdd']);
    Route::get('/mastersetpostingppnkeluaranloadall', [MasterSetPostingPPNKeluaranController::class, 'loadAll']);
    Route::post('/mastersetpostingppnkeluaranspdelete', [MasterSetPostingPPNKeluaranController::class, 'spDelete']);
    Route::get('/mastersetpostingppnkeluaranspdetail', [MasterSetPostingPPNKeluaranController::class, 'spDetail']);
    Route::get('/mastersetpostingppnkeluaranloadperkiraan', [MasterSetPostingPPNKeluaranController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingppnkeluaranspedit', [MasterSetPostingPPNKeluaranController::class, 'spEdit']);

    // PPH MASUKAN
    Route::get('/mastersetpostingpphmasukan', [MasterSetPostingPPHMasukanController::class, 'index']);
    Route::post('/mastersetpostingpphmasukanspadd', [MasterSetPostingPPHMasukanController::class, 'spAdd']);
    Route::get('/mastersetpostingpphmasukanloadall', [MasterSetPostingPPHMasukanController::class, 'loadAll']);
    Route::post('/mastersetpostingpphmasukanspdelete', [MasterSetPostingPPHMasukanController::class, 'spDelete']);
    Route::get('/mastersetpostingpphmasukanspdetail', [MasterSetPostingPPHMasukanController::class, 'spDetail']);
    Route::get('/mastersetpostingpphmasukanloadperkiraan', [MasterSetPostingPPHMasukanController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingpphmasukanspedit', [MasterSetPostingPPHMasukanController::class, 'spEdit']);

    // PPH KELUARAN
    Route::get('/mastersetpostingpphkeluaran', [MasterSetPostingPPHKeluaranController::class, 'index']);
    Route::post('/mastersetpostingpphkeluaranspadd', [MasterSetPostingPPHKeluaranController::class, 'spAdd']);
    Route::get('/mastersetpostingpphkeluaranloadall', [MasterSetPostingPPHKeluaranController::class, 'loadAll']);
    Route::post('/mastersetpostingpphkeluaranspdelete', [MasterSetPostingPPHKeluaranController::class, 'spDelete']);
    Route::get('/mastersetpostingpphkeluaranspdetail', [MasterSetPostingPPHKeluaranController::class, 'spDetail']);
    Route::get('/mastersetpostingpphkeluaranloadperkiraan', [MasterSetPostingPPHKeluaranController::class, 'loadPerkiraan']);
    Route::post('/mastersetpostingpphkeluaranspedit', [MasterSetPostingPPHKeluaranController::class, 'spEdit']);

});

// SUPPLIER BANK ACCOUNT ==========================================================
Route::get('/mastersupplierbankacc', [MasterSupplierBankController::class, 'index'])->middleware('auth');
Route::get('/mastersupplierbankloadall', [MasterSupplierBankController::class, 'loadAll'])->middleware('auth');
Route::post('/mastersupplierbankspadd', [MasterSupplierBankController::class, 'spAdd'])->middleware('auth');
Route::post('/mastersupplierbankspedit', [MasterSupplierBankController::class, 'spEdit'])->middleware('auth');
Route::post('/mastersupplierbankspdelete', [MasterSupplierBankController::class, 'spDelete'])->middleware('auth');
Route::get('/mastersupplierbankspdetail', [MasterSupplierBankController::class, 'spDetail'])->middleware('auth');
Route::post('/mastersupplierbankspupdate', [MasterSupplierBankController::class, 'spUpdate'])->middleware('auth');

// END OF ACCOUNTING =====================================================================================================================
//=======================================================================================================================================  

// BAHAN / BARANG =======================================================================================================================
//=======================================================================================================================================

// MASTER GUDANG ===================================================================
Route::get('/mastergudang' , [MasterGudangController::class, 'index'])->middleware('auth');
Route::post('/mastergudangspadd' , [MasterGudangController::class, 'spAdd'])->middleware('auth');
Route::get('/mastergudangspdetail' ,[MasterGudangController::class, 'spDetail'])->middleware('auth');
Route::post('/mastergudangspedit' , [MasterGudangController::class, 'spEdit'])->middleware('auth');
Route::post('/mastergudangspdelete' , [MasterGudangController::class, 'spDelete'])->middleware('auth');
Route::get('/mastergudangloadall' , [MasterGudangController::class, 'loadAll'])->middleware('auth');

// MASTER GROUP ==================================================
Route::get('/mastergroup' , [MasterGroupController::class, 'index'])->middleware('auth');
Route::post('/mastergroupspadd' ,[MasterGroupController::class, 'spAdd'])->middleware('auth');
Route::get('/mastergroupspdetail' , [MasterGroupController::class, 'spDetail'])->middleware('auth');
Route::post('/mastergroupspdelete' , [MasterGroupController::class, 'spDelete'])->middleware('auth');
Route::get('/mastergrouploadall', [MasterGroupController::class, 'loadall'])->middleware('auth');
Route::post('/mastergroupspedit' , [MasterGroupController::class, 'spEdit'])->middleware('auth');

// MASTER HEAD GROUP =================================================
Route::get('/masterheadgroup' , [MasterHeadGroupController::class, 'index'])->middleware('auth');
Route::get('/masterheadgrouploadPerkiraan' , [MasterHeadGroupController::class, 'loadPerkiraan'])->middleware('auth');
Route::get('/masterheadgrouplistgroup' , [MasterHeadGroupController::class, 'listGroup'])->middleware('auth');
Route::get('/masterheadgrouplistperkiraan' , [MasterHeadGroupController::class, 'listPerkiraan'])->middleware('auth');
Route::post('/masterheadgroupspadd' , [MasterHeadGroupController::class, 'spAdd'])->middleware('auth');
Route::get('/masterheadgroupspdetail' , [MasterHeadGroupController::class, 'spDetail'])->middleware('auth');
Route::get('/masterheadgrouploadall' , [MasterHeadGroupController::class, 'loadAll'])->middleware('auth');
Route::post('/masterheadgroupspdelete' , [MasterHeadGroupController::class, 'spDelete'])->middleware('auth');
Route::post('/masterheadgroupspedit' , [MasterHeadGroupController::class, 'spEdit'])->middleware('auth');
Route::get('/masterheadgrouplistsubgroup' , [MasterHeadGroupController::class, 'spListSubGroup'])->middleware('auth');
Route::get('/masterheadgroupspdetailsubgroup' , [MasterHeadGroupController::class, 'spDetailSubGroup'])->middleware('auth');
Route::post('/masterheadgroupspaddsubgroup' , [MasterHeadGroupController::class, 'spAddSubGroup'])->middleware('auth');
Route::post('/masterheadgroupspdeletesubgroup' , [MasterHeadGroupController::class, 'spDeleteSubGroup'])->middleware('auth');
Route::post('/masterheadgroupspeditsubgroup' , [MasterHeadGroupController::class, 'spEditSubGroup'])->middleware('auth');
Route::get('/masterheadgrouplistsubkategori' , [MasterHeadGroupController::class, 'spListSubKategori'])->middleware('auth');
Route::get('/masterheadgroupspdetailsubkategori' , [MasterHeadGroupController::class, 'spDetailSubKategori'])->middleware('auth');
Route::post('/masterheadgroupspaddsubkategori' , [MasterHeadGroupController::class, 'spAddSubKategori'])->middleware('auth');
Route::post('/masterheadgroupspeditsubkategori' , [MasterHeadGroupController::class, 'spEditSubKategori'])->middleware('auth');
Route::post('/masterheadgroupspdeletesubkategori' , [MasterHeadGroupController::class, 'spDeleteSubKategori'])->middleware('auth');
Route::post('/masterheadgrouploadperkiraansubgroup' , [MasterHeadGroupController::class, 'loadPerkiraan'])->middleware('auth');

// MASTER MERK ========================================================
Route::get('/mastermerk' , [MasterMerkController::class, 'index'])->middleware('auth');
Route::post('/mastermerkspadd' , [MasterMerkController::class, 'spAdd'])->middleware('auth');
Route::get('/mastermerkspdetail' , [MasterMerkController::class, 'spDetail'])->middleware('auth');
Route::post('/mastermerkspdelete' , [MasterMerkController::class, 'spDelete'])->middleware('auth');
Route::get('/mastermerkloadall', [MasterMerkController::class, 'loadall'])->middleware('auth');
Route::post('/mastermerkspedit' , [MasterMerkController::class, 'spEdit'])->middleware('auth');

// MASTER BARANG ====================================================================
Route::get('/masterbarang' ,[MasterBarangController::class, 'index'])->middleware('auth');
Route::get('/masterbaranglistselect' , [MasterBarangController::class, 'spListSelect'])->middleware('auth');
Route::post('/masterbarangspcheckdbbarang' , [MasterBarangController::class, 'spCheckDBBarang'])->middleware('auth');
Route::post('/masterbarangspadd' ,[MasterBarangController::class, 'spAdd'])->middleware('auth');
Route::get('/masterbarangspdetail' , [MasterBarangController::class, 'spDetail'])->middleware('auth');
Route::post('/masterbarangspedit' ,[MasterBarangController::class, 'spEdit'])->middleware('auth');
Route::get('/masterbarangloadall' , [MasterBarangController::class, 'loadAll'])->middleware('auth');
Route::get('/masterbaranglistsupplier' , [MasterBarangController::class, 'spListSupplier'])->middleware('auth');
Route::get('/masterbarangspdetailharga' ,[MasterBarangController::class, 'spDetailHarga'])->middleware('auth');
Route::post('/masterbarangspaddharga' ,[MasterBarangController::class, 'spAddHarga'])->middleware('auth');
Route::get('/masterbarangspdetailhargadetail' ,[MasterBarangController::class, 'spDetailHargaDetail'])->middleware('auth');
Route::post('/masterbarangspeditharga' ,[MasterBarangController::class, 'spEditHarga'])->middleware('auth');
Route::post('/masterbarangspdelete' , [MasterBarangController::class, 'spDelete'])->middleware('auth');
Route::post('/masterbarangspdeleteharga' , [MasterBarangController::class, 'spDeleteHarga'])->middleware('auth');
Route::get('/masterbaranglistsatuan' , [MasterBarangController::class, 'spListSatuan'])->middleware('auth');

//MASTER BARANG JASA ====================================================================
Route::get('/masterbarangjasa' , [MasterBarangJasaController::class, 'index'])->middleware('auth');
Route::get('/masterbarangjasalistselect', [MasterBarangJasaController::class, 'spListSelect'])->middleware('auth');
Route::post('/masterbarangjasaspcheckdbbarang' , [MasterBarangJasaController::class, 'spCheckDBBarang'])->middleware('auth');
Route::post('/masterbarangjasaspadd' , [MasterBarangJasaController::class, 'spAdd'])->middleware('auth');
Route::get('/masterbarangjasaspdetail' , [MasterBarangJasaController::class, 'spDetail'])->middleware('auth');
Route::post('/masterbarangjasaspedit' , [MasterBarangJasaController::class, 'spEdit'])->middleware('auth');
Route::post('/masterbarangjasaspdelete' , [MasterBarangJasaController::class, 'spDelete'])->middleware('auth');
Route::get('/masterbarangjasaloadall', [MasterBarangJasaController::class, 'loadAll'])->middleware('auth');

// MASTER LOKASI BARANG ==========================================================
Route::get('/masterlokasibarang', [MasterLokasiBarangController::class, 'index'])->middleware('auth');
Route::get('/masterlokasibarangloadall', [MasterLokasiBarangController::class, 'loadAll'])->middleware('auth');
Route::post('/masterlokasibarangspadd', [MasterLokasiBarangController::class, 'spAdd'])->middleware('auth');
Route::post('/masterlokasibarangspedit', [MasterLokasiBarangController::class, 'spEdit'])->middleware('auth');
Route::post('/masterlokasibarangspdelete', [MasterLokasiBarangController::class, 'spDelete'])->middleware('auth');
Route::get('/masterlokasibarangspdetail', [MasterLokasiBarangController::class, 'spDetail'])->middleware('auth');

// MASTER SET LOKASI BARANG ==========================================================
Route::get('/mastersetlokasibarang', [MasterSetLokasiBarangController::class, 'index'])->middleware('auth');
Route::get('/mastersetlokasibarangloadall', [MasterSetLokasiBarangController::class, 'loadAll'])->middleware('auth');
Route::post('/mastersetlokasibarangspadd', [MasterSetLokasiBarangController::class, 'spAdd'])->middleware('auth');
Route::post('/mastersetlokasibarangspedit', [MasterSetLokasiBarangController::class, 'spEdit'])->middleware('auth');
Route::post('/mastersetlokasibarangspdelete', [MasterSetLokasiBarangController::class, 'spDelete'])->middleware('auth');
Route::get('/mastersetlokasibarangspdetail', [MasterSetLokasiBarangController::class, 'spDetail'])->middleware('auth');
Route::get('/masterSetLokasiBarangLoadHistory', [MasterSetLokasiBarangController::class, 'loadHistory'])->middleware('auth');
Route::get('/masterSetLokasiBarangLoadLokasiBarang', [MasterSetLokasiBarangController::class, 'loadLokasiBarang'])->middleware('auth');
Route::post('/masterSetLokasiBarangSubmitEdit', [MasterSetLokasiBarangController::class, 'spEdit'])->middleware('auth');

// END OF BAHAN / BARANG ===============================================================================================================
//======================================================================================================================================

// SUPPLIER / CUSTOMER =================================================================================================================
//======================================================================================================================================

// MASTER AREA ============================================================
Route::get('/masterarea', [MasterAreaController::class, 'index'])->middleware('auth');
Route::post('/masterareaspadd' ,[MasterAreaController::class, 'spAdd'])->middleware('auth');
Route::post('/masterareaspdelete' , [MasterAreaController::class, 'spDelete'])->middleware('auth');
Route::post('/masterareaspdetail' , [MasterAreaController::class, 'spDetail'])->middleware('auth');
Route::post('/masterareaspedit' , [MasterAreaController::class, 'spEdit'])->middleware('auth');
Route::get('/masterarealoadall' , [MasterAreaController::class, 'loadAll'])->middleware('auth');

// MASTER KOTA ============================================================
Route::get('/masterkota' , [MasterKotaController::class, 'index'])->middleware('auth');
Route::get('/masterkotalistarea' , [MasterKotaController::class, 'spListArea'])->middleware('auth');
Route::post('/masterkotaspdetail' , [MasterKotaController::class, 'spDetail'])->middleware('auth');
Route::post('/masterkotaspedit' , [MasterKotaController::class, 'spEdit'])->middleware('auth');
Route::post('/masterkotaspdelete' , [MasterKotaController::class, 'spDelete'])->middleware('auth');
Route::post('/masterkotaspadd' , [MasterKotaController::class, 'spAdd'])->middleware('auth');
Route::get('/masterkotaloadall' , [MasterKotaController::class, 'loadAll'])->middleware('auth');

//MASTER SUPPLIER ========================================================
Route::get('/mastersupplier' , [MasterSupplierController::class, 'index'])->middleware('auth');
Route::get('/mastersupplierlistkota' , [MasterSupplierController::class, 'spListKota'])->middleware('auth');
Route::post('/mastersupplierspadd' , [MasterSupplierController::class, 'spAdd'])->middleware('auth');
Route::post('/mastersupplierspdetail' , [MasterSupplierController::class, 'spDetail'])->middleware('auth');
Route::post('/mastersupplierspdelete' , [MasterSupplierController::class, 'spDelete'])->middleware('auth');
Route::post('/mastersupplierspedit' , [MasterSupplierController::class, 'spEdit'])->middleware('auth');
Route::get('/mastersupplierloadall' , [MasterSupplierController::class, 'loadAll'])->middleware('auth');
Route::get('/mastersupplierloadalamat' , [MasterSupplierController::class, 'loadAlamat'])->middleware('auth');
Route::post('/mastersupplierspaddalamat' , [MasterSupplierController::class, 'submitAlamat'])->middleware('auth');
Route::post('/mastersupplierdeletealamat' , [MasterSupplierController::class, 'spDeleteAlamat'])->middleware('auth');
Route::post('/mastersuppliereditalamat' , [MasterSupplierController::class, 'spEditAlamat'])->middleware('auth');

//MASTER JENIS CUSTOMER ==================================================
Route::get('/masterjeniscustomer' , [MasterJenisCustomerController::class, 'index'])->middleware('auth');
Route::post('/masterjeniscustomerspadd' , [MasterJenisCustomerController::class, 'spAdd'])->middleware('auth');
Route::post('/masterjeniscustomerspdelete' , [MasterJenisCustomerController::class, 'spDelete'])->middleware('auth');
Route::get('/masterjeniscustomerspdetail' , [MasterJenisCustomerController::class, 'spDetail'])->middleware('auth');
Route::post('/masterjeniscustomerspedit' , [MasterJenisCustomerController::class, 'spEdit'])->middleware('auth');
Route::get('/masterjeniscustomerloadall' , [MasterJenisCustomerController::class, 'loadAll'])->middleware('auth');

//MASTER GROUP CUSTOMER ==================================================
Route::get('/mastergroupcustomer' , [MasterGroupCustomerController::class, 'index'])->middleware('auth');
Route::post('/mastergroupcustomerspadd' , [MasterGroupCustomerController::class, 'spAdd'])->middleware('auth');
Route::post('/mastergroupcustomerspdelete' , [MasterGroupCustomerController::class, 'spDelete'])->middleware('auth');
Route::get('/mastergroupcustomerspdetail' , [MasterGroupCustomerController::class, 'spDetail'])->middleware('auth');
Route::post('/mastergroupcustomerspedit' , [MasterGroupCustomerController::class, 'spEdit'])->middleware('auth');
Route::get('/mastergroupcustomerloadall' , [MasterGroupCustomerController::class, 'loadAll'])->middleware('auth');

//MASTER CUSTOMER ========================================================
Route::get('/mastercustomer' , [MasterCustomerController::class, 'index'])->middleware('auth');
Route::get('/mastercustomerlistselect' , [MasterCustomerController::class, 'spListSelect'])->middleware('auth');
Route::post('/mastercustomerspadd' , [MasterCustomerController::class, 'spAdd'])->middleware('auth');
Route::post('/mastercustomerspdelete' , [MasterCustomerController::class, 'spDelete'])->middleware('auth');
Route::post('/mastercustomerspdetail' , [MasterCustomerController::class, 'spDetail'])->middleware('auth');
Route::post('/mastercustomerspedit' , [MasterCustomerController::class, 'spEdit'])->middleware('auth');
Route::get('/mastercustomerloadall' , [MasterCustomerController::class, 'loadAll'])->middleware('auth');
Route::get('/mastercustomerloaddetailakun', [MasterCustomerController::class, 'loadDetailAkun'])->middleware('auth');
Route::get('/mastercustomerloaddetailperkiraan', [MasterCustomerController::class, 'loadDetailPerkiraan'])->middleware('auth');
Route::get('/mastercustomerloadperkiraanedit', [MasterCustomerController::class, 'loadPerkiraanEdit'])->middleware('auth');
Route::post('/mastercustomerspadddetailakun' , [MasterCustomerController::class, 'spAddDetailAkun'])->middleware('auth');
Route::post('/mastercustomerspadddetailakunedit' , [MasterCustomerController::class, 'spAddDetailAkunEdit'])->middleware('auth');
Route::post('/mastercustomerspdeletedetailakun' , [MasterCustomerController::class, 'spDeleteDetailAkun'])->middleware('auth');

// MASTER EKSPEDISI ==========================================================
Route::get('/masterekspedisi', [MasterEkspedisiController::class, 'index'])->middleware('auth');
Route::get('/masterekspedisiloadall', [MasterEkspedisiController::class, 'loadAll'])->middleware('auth');
Route::get('/masterEkspedisiLoadDetailAkun', [MasterEkspedisiController::class, 'loadDetailAkun'])->middleware('auth');
Route::get('/masterEkspedisiLoadDetailAkunEdit', [MasterEkspedisiController::class, 'loadDetailAkunEdit'])->middleware('auth');
Route::get('/masterEkspedisiLoadHutangPiutang', [MasterEkspedisiController::class, 'loadHutangPiutang'])->middleware('auth');
Route::post('/masterEkspedisiAddDetailAkun', [MasterEkspedisiController::class, 'submitAddDetailAkun'])->middleware('auth');
Route::post('/masterEkspedisiDeleteDetailAkun', [MasterEkspedisiController::class, 'submitDeleteDetailAkun'])->middleware('auth');
Route::post('/masterEkspedisiSubmitEditDetailAkun', [MasterEkspedisiController::class, 'submitEditDetailAkun'])->middleware('auth');

Route::post('/masterEkspedisiSubmitAdd', [MasterEkspedisiController::class, 'submitAdd'])->middleware('auth');
Route::post('/masterEkspedisiSubmitDelete', [MasterEkspedisiController::class, 'submitDelete'])->middleware('auth');
Route::post('/masterEkspedisiSubmitEdit', [MasterEkspedisiController::class, 'submitEdit'])->middleware('auth');
Route::get('/masterEkspedisiLoadDetail', [MasterEkspedisiController::class, 'spDetail'])->middleware('auth');
Route::get('/masterEkspedisiLoadKota', [MasterEkspedisiController::class, 'loadKota'])->middleware('auth');
Route::get('/masterEkspedisiLoadKotaEdit', [MasterEkspedisiController::class, 'loadKotaEdit'])->middleware('auth');

Route::get('/masterEkspedisiLoadAlamatKirim', [MasterEkspedisiController::class, 'loadAlamatKirim'])->middleware('auth');
Route::post('/masterEkspedisiGetNomorAlamatKirim', [MasterEkspedisiController::class, 'getNewNomorAlamatKirim'])->middleware('auth');
Route::post('/masterEkspedisiAddAlamatKirim', [MasterEkspedisiController::class, 'submitAddAlamatKirim'])->middleware('auth');
Route::post('/masterEkspedisiDeleteAlamatKirim', [MasterEkspedisiController::class, 'submitDeleteAlamatKirim'])->middleware('auth');
Route::get('/masterEkspedisiLoadAlamatKirimEdit', [MasterEkspedisiController::class, 'loadAlamatKirimEdit'])->middleware('auth');
Route::post('/masterEkspedisiSubmitEditAlamatKirim', [MasterEkspedisiController::class, 'submitEditAlamatKirim'])->middleware('auth');

// END OF SUPPLIER / CUSTOMER =======================================================================================================
//===================================================================================================================================

// LAIN - LAIN ======================================================================================================================
//===================================================================================================================================

// MASTER DEPARTEMEN =========================================================
Route::get('/masterdepartemen' , [MasterDepartemenController::class, 'index'])->middleware('auth');
Route::post('/masterdepartemenspadd' , [MasterDepartemenController::class, 'spAdd'])->middleware('auth');
Route::post('/masterdepartemenspdelete' , [MasterDepartemenController::class, 'spDelete'])->middleware('auth');
Route::get('/masterdepartemenspdetail' , [MasterDepartemenController::class, 'spDetail'])->middleware('auth');
Route::post('/masterdepartemenspedit' , [MasterDepartemenController::class, 'spEdit'])->middleware('auth');
Route::get('/masterdepartemenloadall' , [MasterDepartemenController::class, 'loadAll'])->middleware('auth');

// MASTER JABATAN =========================================================
Route::get('/masterjabatan' , [MasterJabatanController::class, 'index'])->middleware('auth');
Route::post('/masterjabatanspadd' , [MasterJabatanController::class, 'spAdd'])->middleware('auth');
Route::post('/masterjabatanspdelete' , [MasterJabatanController::class, 'spDelete'])->middleware('auth');
Route::get('/masterjabatanspdetail' , [MasterJabatanController::class, 'spDetail'])->middleware('auth');
Route::post('/masterjabatanspedit' , [MasterJabatanController::class, 'spEdit'])->middleware('auth');
Route::get('/masterjabatanloadall' , [MasterJabatanController::class, 'loadAll'])->middleware('auth');

// MASTER DAFTAR KARYAWAN =========================================================
Route::get('/masterdaftarkaryawan' , [MasterDaftarKaryawanController::class, 'index'])->middleware('auth');
Route::post('/masterdaftarkaryawanspadd' , [MasterDaftarKaryawanController::class, 'spAdd'])->middleware('auth');
Route::post('/masterdaftarkaryawanspdelete' , [MasterDaftarKaryawanController::class, 'spDelete'])->middleware('auth');
Route::get('/masterdaftarkaryawanspdetail' , [MasterDaftarKaryawanController::class, 'spDetail'])->middleware('auth');
Route::post('/masterdaftarkaryawanspedit' , [MasterDaftarKaryawanController::class, 'spEdit'])->middleware('auth');
Route::get('/masterdaftarkaryawanloadall' , [MasterDaftarKaryawanController::class, 'loadAll'])->middleware('auth');
Route::get('/masterKaryawanGetKeyNIK' , [MasterDaftarKaryawanController::class, 'getNewKeyNIK'])->middleware('auth');

// MASTER DAFTAR BIAYA =========================================================
Route::get('/masterdaftarbiaya' , [MasterDaftarBiayaController::class, 'index'])->middleware('auth');
Route::post('/masterdaftarbiayaspadd' , [MasterDaftarBiayaController::class, 'spAdd'])->middleware('auth');
Route::post('/masterdaftarbiayaspdelete' , [MasterDaftarBiayaController::class, 'spDelete'])->middleware('auth');
Route::get('/masterdaftarbiayaspdetail' , [MasterDaftarBiayaController::class, 'spDetail'])->middleware('auth');
Route::post('/masterdaftarbiayaspedit' , [MasterDaftarBiayaController::class, 'spEdit'])->middleware('auth');
Route::get('/masterdaftarbiayaloadall' , [MasterDaftarBiayaController::class, 'loadAll'])->middleware('auth');
Route::get('/masterdaftarbiayaselectperkiraan' , [MasterDaftarBiayaController::class, 'selectPerkiraan'])->middleware('auth');

// MASTER SALES ====================================================================
Route::get('/mastersales' ,[MasterSalesController::class, 'index'])->middleware('auth');
Route::get('/masterSalesSpDetail' , [MasterSalesController::class, 'spDetail'])->middleware('auth');
Route::post('/masterSalesSpEdit' ,[MasterSalesController::class, 'spEdit'])->middleware('auth');
Route::get('/masterSalesListGudang' , [MasterSalesController::class, 'loadGudang'])->middleware('auth');
Route::get('/masterSalesListCosting' , [MasterSalesController::class, 'loadCosting'])->middleware('auth');
Route::get('/masterSalesListCustSupp' , [MasterSalesController::class, 'loadCustSupp'])->middleware('auth');
Route::get('/masterSalesLoadDataSales' , [MasterSalesController::class, 'loadDataSales'])->middleware('auth');
Route::get('/masterSalesLoadDataSalesEdit' , [MasterSalesController::class, 'loadDataSalesEdit'])->middleware('auth');
Route::get('/masterSalesLoadKaryawan' , [MasterSalesController::class, 'loadKaryawan'])->middleware('auth');
Route::post('/masterSalesAddSalesCust' , [MasterSalesController::class, 'submitAddSalesCust'])->middleware('auth');
Route::post('/masterSalesDeleteSalesCust' , [MasterSalesController::class, 'submitDeleteSalesCust'])->middleware('auth');
Route::post('/masterSalesEditSalesCust' , [MasterSalesController::class, 'submitEditSalesCust'])->middleware('auth');
Route::get('/masterSalesLoadDataTarget' , [MasterSalesController::class, 'loadDataTarget'])->middleware('auth');
Route::get('/masterSalesListMerk' , [MasterSalesController::class, 'loadMerk'])->middleware('auth');
Route::post('/masterSalesAddTarget' , [MasterSalesController::class, 'submitAddTarget'])->middleware('auth');
Route::post('/masterSalesDeleteTarget' , [MasterSalesController::class, 'submitDeleteTarget'])->middleware('auth');
Route::get('/masterSalesLoadDataTargetEdit' , [MasterSalesController::class, 'loadDataTargetEdit'])->middleware('auth');
Route::post('/masterSalesEditTarget' , [MasterSalesController::class, 'submitEditTarget'])->middleware('auth');

Route::get('/mastersaleslistselect' , [MasterSalesController::class, 'spListSelect'])->middleware('auth');
Route::post('/mastersalesspcheckdbsales' , [MasterSalesController::class, 'spCheckDBSales'])->middleware('auth');
Route::post('/mastersalesspadd' ,[MasterSalesController::class, 'spAdd'])->middleware('auth');
Route::get('/mastersalesloadall' , [MasterSalesController::class, 'loadAll'])->middleware('auth');
Route::get('/mastersaleslistsupplier' , [MasterSalesController::class, 'spListSupplier'])->middleware('auth');
Route::get('/mastersalesspdetailharga' ,[MasterSalesController::class, 'spDetailHarga'])->middleware('auth');
Route::post('/mastersalesspaddharga' ,[MasterSalesController::class, 'spAddHarga'])->middleware('auth');
Route::get('/mastersalesspdetailhargadetail' ,[MasterSalesController::class, 'spDetailHargaDetail'])->middleware('auth');
Route::post('/mastersalesspeditharga' ,[MasterSalesController::class, 'spEditHarga'])->middleware('auth');
Route::post('/mastersalesspdelete' , [MasterSalesController::class, 'spDelete'])->middleware('auth');
Route::post('/mastersalesspdeleteharga' , [MasterSalesController::class, 'spDeleteHarga'])->middleware('auth');

// MASTER NILAI PPN =========================================================
Route::get('/masternilaippn' , [MasterNilaiPPNController::class, 'index'])->middleware('auth');
Route::post('/masternilaippnspadd' , [MasterNilaiPPNController::class, 'spAdd'])->middleware('auth');
Route::post('/masternilaippnspdelete' , [MasterNilaiPPNController::class, 'spDelete'])->middleware('auth');
Route::get('/masternilaippnspdetail' , [MasterNilaiPPNController::class, 'spDetail'])->middleware('auth');
Route::post('/masternilaippnspedit' , [MasterNilaiPPNController::class, 'spEdit'])->middleware('auth');
Route::get('/masternilaippnloadall' , [MasterNilaiPPNController::class, 'loadAll'])->middleware('auth');
Route::get('/masternilaippndefault' , [MasterNilaiPPNController::class, 'spDefaultUrut'])->middleware('auth');

// MASTER NOMOR FAKTUR PAJAK =========================================================
Route::get('/masternomorfakturpajak' , [MasterNomorFakturPajakController::class, 'index'])->middleware('auth');
Route::post('/masternomorfakturpajakspadd' , [MasterNomorFakturPajakController::class, 'spAdd'])->middleware('auth');
Route::post('/masternomorfakturpajakspdelete' , [MasterNomorFakturPajakController::class, 'spDelete'])->middleware('auth');
Route::get('/masternomorfakturpajakspdetail' , [MasterNomorFakturPajakController::class, 'spDetail'])->middleware('auth');
Route::post('/masternomorfakturpajakspedit' , [MasterNomorFakturPajakController::class, 'spEdit'])->middleware('auth');
Route::get('/masternomorfakturpajakloadall' , [MasterNomorFakturPajakController::class, 'loadAll'])->middleware('auth');
Route::get('/masternomorfakturpajakdefault' , [MasterNomorFakturPajakController::class, 'spDefaultUrut'])->middleware('auth');
Route::get('/masternomorfakturpajakupdatepenuh', [MasterNomorFakturPajakController::class, 'updateIsPenuh'])->middleware('auth');

// END OF LAIN LAIN ===================================================================================================================
//=====================================================================================================================================

// KENDARAAN ==========================================================================================================================
//=====================================================================================================================================

//MASTER NO POL =========================================================
Route::get('/masternopol' , [MasterNoPolController::class, 'index']);
Route::post('/masternopolspadd' , [MasterNoPolController::class, 'spAdd']);
Route::post('/masternopolspdelete' , [MasterNoPolController::class, 'spDelete']);
Route::get('/masternopolspdetail' , [MasterNoPolController::class, 'spDetail']);
Route::post('/masternopolspedit' , [MasterNoPolController::class, 'spEdit']);
Route::get('/masternopolloadall' , [MasterNoPolController::class, 'loadAll']);
Route::get('/masternopolselectperkiraan' , [MasterNoPolController::class, 'selectKodeCost']);

//MASTER SOPIR =========================================================
Route::get('/mastersopir' , [MasterSopirController::class, 'index']);
Route::post('/mastersopirspadd' , [MasterSopirController::class, 'spAdd']);
Route::post('/mastersopirspdelete' , [MasterSopirController::class, 'spDelete']);
Route::get('/mastersopirspdetail' , [MasterSopirController::class, 'spDetail']);
Route::post('/mastersopirspedit' , [MasterSopirController::class, 'spEdit']);
Route::get('/mastersopirloadall' , [MasterSopirController::class, 'loadAll']);
Route::get('/mastersopirselectperkiraan' , [MasterSopirController::class, 'selectKodeCost']);

// END OF KENDARAAN ======================================================================================================================
//========================================================================================================================================