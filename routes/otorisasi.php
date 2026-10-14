<?php

use App\Http\Controllers\OtorisasiRequestController;
use Illuminate\Support\Facades\Route;

// Permintaan otorisasi + notifikasi Web Push (PWA). public/sw.js dan
// public/manifest.webmanifest disajikan langsung sebagai file statis.
Route::middleware('auth')->group(function () {
    Route::post('/otorisasirequest', [OtorisasiRequestController::class, 'spRequest']);
    Route::get('/otorisasi/{id}', [OtorisasiRequestController::class, 'show'])->whereNumber('id');
    Route::post('/otorisasi/{id}/approve', [OtorisasiRequestController::class, 'spApprove'])->whereNumber('id');
    Route::post('/otorisasi/{id}/reject', [OtorisasiRequestController::class, 'spReject'])->whereNumber('id');

    Route::get('/otorisasiinbox', [OtorisasiRequestController::class, 'index']);
    Route::get('/otorisasiinboxloadall', [OtorisasiRequestController::class, 'loadAll']);
    Route::get('/otorisasicount', [OtorisasiRequestController::class, 'count']);

    Route::post('/pushsubscribe', [OtorisasiRequestController::class, 'spSubscribe']);
    Route::post('/pushunsubscribe', [OtorisasiRequestController::class, 'spUnsubscribe']);
});
