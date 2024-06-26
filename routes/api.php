<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookCategoryController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BookCopyController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LoanController;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));

Route::get('/', function () {
    return response()->json([
        'status' => 200,
        'message' => 'Welcome to My Library API',
    ]);
});

Route::get('/error', function () {
    return response()->json([
        'status' => 401,
        'message' => 'akses ditolak',
    ]);
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Route::group(['prefix' => 'auth'], function () {
//     Route::post('register', [AuthController::class, 'register'])->name('auth.register');
//     Route::post('login', [AuthController::class, 'login'])->name('auth.login');
// });

Route::middleware('auth:sanctum')->group(function () {
    // route book
    Route::get('books', [BookController::class, 'index']);
    Route::post('books', [BookController::class, 'store']);

    // route category
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('categories', [CategoryController::class, 'store']);

    // route book category
    Route::get('book-categories', [BookCategoryController::class, 'index']);
    Route::post('book-categories', [BookCategoryController::class, 'store']);

    // route book copy
    Route::get('book-copies', [BookCopyController::class, 'index']);
    Route::post('book-copies', [BookCopyController::class, 'store']);

    // route loan
    Route::get('loans', [LoanController::class, 'index']);
    Route::post('loans', [LoanController::class, 'store']);
});
