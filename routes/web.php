<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/fetch-articles', function () {
    Artisan::call('app:fetch-articles');
    return response()->json(['message' => 'articles fetched successfully']);
});
Route::get('/get-author', [ArticleController::class, 'getAuthor']);

