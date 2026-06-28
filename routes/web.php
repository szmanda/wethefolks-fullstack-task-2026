<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::get('csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    });

    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'dashboard']);
    Route::post('ads', [\App\Http\Controllers\AdsController::class, 'store']);
    Route::get('ads/{id}/track-impression', [\App\Http\Controllers\AdsController::class, 'trackImpression']);
    Route::get('ads/{id}/track-click', [\App\Http\Controllers\AdsController::class, 'trackClick']);
    Route::options('ads/{id}/track-impression', fn () => response()->noContent()->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'POST, OPTIONS'));
    Route::options('ads/{id}/track-click', fn () => response()->noContent()->header('Access-Control-Allow-Origin', '*')->header('Access-Control-Allow-Methods', 'POST, OPTIONS'));
});
