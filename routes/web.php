<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::get('csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    });

});
