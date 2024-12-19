<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/fetch-articles', function () {
    Artisan::call('app:fetch-articles');
    return response()->json(['message' => 'articles fetched successfully']);
});
