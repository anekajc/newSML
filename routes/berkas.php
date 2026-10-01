<?php

use App\Http\Controllers\Berkas\NewSetPemakaiController;
use App\Http\Controllers\Berkas\NewSetupPeriodeKerjaController;
use App\Http\Controllers\Berkas\KunciPeriodeKerjaController;
use App\Http\Controllers\Berkas\SetNomorTransaksiController;
use App\Http\Controllers\Berkas\GantiPasswordController;
use App\Http\Controllers\Berkas\BerkasMenuController;
use App\Http\Controllers\Berkas\BerkasStatusController;
use Illuminate\Support\Facades\Route;

// Semua route Berkas wajib login. Dulu hanya berkasstatus & gantipassword yang memakai
// middleware auth - route lain (newsetpemakai, berkasmenu, kunciperiode, setnomortransaksi)
// langsung membaca \Auth::User()->username, jadi begitu sesi habis halaman jatuh ke
// 500 Internal Server Error, bukan diarahkan ke halaman login.
Route::middleware('auth')->group(function () {

// Berkas
Route::controller(NewSetPemakaiController::class)->group(function () {
    Route::get('/newsetpemakai', 'index');
    Route::get('/newsetpemakailoadall', 'loadAll');

    Route::post('/newsetpemakailistakses', 'listAkses');
    Route::post('/newsetpemakaidetailuser', 'detailUser');

    Route::post('/newsetpemakaispupdateakses', 'spUpdateAkses');
    Route::post('/newsetpemakaispupdateaksesheader', 'spUpdateAksesHeader');
    Route::post('/newsetpemakailistaksesreport', 'listAksesReport');
    Route::post('/newsetpemakaispupdateaksesreport', 'spUpdateAksesReport');
    Route::post('/newsetpemakailistcoa', 'listCoa');
    Route::post('/newsetpemakaiupdateiskirimcoa', 'updateIsKirimCOA');
    Route::post('/newsetpemakaiupdateaddcoa', 'updateAddAksesCOA');
    Route::post('/newsetpemakaiupdateaddallcoa', 'updateAddAllAksesCOA');
    Route::post('/newsetpemakaideleteaksescoa', 'deleteAksesCOA');
    Route::post('/newsetpemakaideleteallaksescoa', 'deleteAllAksesCOA');
    Route::get('/newsetpemakaiLoadKaryawan', 'loadKaryawan');
    Route::get('/newsetpemakaiLoadDepartemen', 'loadDepartemen');
    Route::get('/newsetpemakaiLoadJabatan', 'loadJabatan');
    Route::post('/newsetpemakaiAddUser', 'submitAddUser');
    Route::post('/newsetpemakaideleteuser', 'deleteUser');
});

Route::get('/kunciperiodekerja', [KunciPeriodeKerjaController::class, 'index']);
Route::get('kunciperiodeload', [KunciPeriodeKerjaController::class, 'kunciPeriodeLoad']);
Route::post('kunciperiodetoggle', [KunciPeriodeKerjaController::class, 'kunciPeriodeToggle']);

Route::get('/setnomortransaksi', [SetNomorTransaksiController::class, 'index']);
Route::get('/dbnomorspdetail', [SetNomorTransaksiController::class, 'spDetail']);
Route::post('setnomortransaksispedit', [SetNomorTransaksiController::class, 'submitEdit']);
Route::get('/setnomortransaksilogo', [SetNomorTransaksiController::class, 'logo']);

// BERKAS MENU ====================================================================================
Route::get('/berkasmenu', [BerkasMenuController::class, 'index']);
Route::get('/berkasmenuloadall', [BerkasMenuController::class, 'loadAll']);
Route::post('/berkasmenuspadd', [BerkasMenuController::class, 'spAdd']);
Route::post('/berkasmenuspedit', [BerkasMenuController::class, 'spEdit']);
Route::post('/berkasmenuspdelete', [BerkasMenuController::class, 'spDelete']);
Route::get('/berkasmenuspdetail', [BerkasMenuController::class, 'spDetail']);

// ONLIN / OFFLINE ====================================================================================
Route::get('/berkasstatus', [BerkasStatusController::class, 'index'])->middleware('auth');
Route::get('/berkasstatusloadall', [BerkasStatusController::class, 'loadAll'])->middleware('auth');
Route::post('/berkasstatusspedit', [BerkasStatusController::class, 'spEdit'])->middleware('auth');
Route::get('/berkasstatusspdetail', [BerkasStatusController::class, 'spDetail'])->middleware('auth');

// GANTI PASSWORD
Route::get('/gantipassword', [GantiPasswordController::class, 'index'])->middleware('auth');
Route::post('/gantipassworduser', [GantiPasswordController::class, 'gantiPassword'])->middleware('auth');

});
