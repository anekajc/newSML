<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utilitas\HitungUlangStockController;
use App\Http\Controllers\Utilitas\PostingController;
use App\Http\Controllers\Utilitas\TutupBukuController;

Route::middleware('auth')->group(function () {
    Route::get('/hitungulangstock', [HitungUlangStockController::class, 'index']);
    Route::post('/hitungulangstockproses', [HitungUlangStockController::class, 'proses']);

    Route::get('/posting', [PostingController::class, 'index']);
    Route::post('/postingproses', [PostingController::class, 'proses']);

    Route::get('/tutupbuku', [TutupBukuController::class, 'index']);
    Route::post('/tutupbukusemua', [TutupBukuController::class, 'prosesSemua']);
    Route::post('/tutupbukuselisihkurs', [TutupBukuController::class, 'prosesSelisihKurs']);
    Route::post('/tutupbukuaktiva', [TutupBukuController::class, 'prosesAktiva']);
    Route::post('/tutupbukuhitungulangneraca', [TutupBukuController::class, 'prosesHitungUlangNeraca']);
    Route::post('/tutupbukuhpprugilaba', [TutupBukuController::class, 'prosesHppRugiLaba']);
    Route::post('/tutupbukudashboard', [TutupBukuController::class, 'prosesDashboard']);
});
