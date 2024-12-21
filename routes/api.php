<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserPreferenceController;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\Authenticate as JWTAuthenticate;


Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
   
    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login successful!',
            'token' => $token,
        ]);
    } else {
        return response()->json([
            'message' => 'Invalid login credentials!',

        ], 401);
    }
});

Route::post('/register', [AuthController::class, 'register']);

Route::middleware(JWTAuthenticate::class)->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/get-all-articles', [ArticleController::class, 'getAllArticles']);
    Route::get('/categories', [ArticleController::class, 'getCategories']);
    Route::get('/get-categories-by-source-id', [ArticleController::class, 'getCategoriesBySourceId']);
    Route::get('/sources', [ArticleController::class, 'getSources']);
    Route::get('/authors', [ArticleController::class, 'getAuthors']);
    Route::get('/preferences', [UserPreferenceController::class, 'getPreferences']);
    Route::post('/preferences', [UserPreferenceController::class, 'savePreferences']);
});





