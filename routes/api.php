<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        // User authenticated successfully
        return response()->json([
            'message' => 'Login successful!',
        ]);
    } else {
        return response()->json([
            'message' => 'Invalid login credentials!',
        ], 401);
    }
});

Route::post('/logout', function () {
    auth()->logout(); // For session-based authentication

    return response()->json([
        'message' => 'Successfully logged out!',
    ]);
});

Route::post('/register', [AuthController::class, 'register']);
Route::get('/get-all-articles', [ArticleController::class, 'getAllArticles']);

