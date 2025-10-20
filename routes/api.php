<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OpdController;
use App\Http\Controllers\ShareBeritaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('login')->group(function () {
    Route::post('/', [AuthController::class, 'login'])->middleware('guest');
});

Route::prefix('/')->middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('share-berita')->middleware('role:pegawai')->group(function () {
        Route::post('/store', [ShareBeritaController::class, 'store']);
        Route::get('/{id}', [ShareBeritaController::class, 'index']);
    });
    Route::get('/get-ranking-berita-perperson', [ShareBeritaController::class, 'getRankingBeritaPerPerson']);
    Route::get('/get-ranking-berita-perperson-per-opd', [ShareBeritaController::class, 'getRankingBeritaPerPersonPerOpd']);

    Route::get('/get-opd-summary', [OpdController::class, 'getOpdSummary']);
});
