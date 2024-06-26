<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookCategoryController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;


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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('books', [BookController::class, 'index']);
    Route::post('book/store', [BookController::class, 'store']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('category/store', [CategoryController::class, 'store']);
    Route::get('book-categories', [BookCategoryController::class, 'index']);
    Route::post('book-category/store', [BookCategoryController::class, 'store']);
});
