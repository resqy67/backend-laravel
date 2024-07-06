<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookCategoryController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\LoanController;


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

Route::get('/user', [AuthController::class, 'getUser'])->middleware(Authenticate::using('sanctum'));
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    // route book
    Route::get('books', [BookController::class, 'index']);
    Route::post('book/store', [BookController::class, 'store']);
    Route::get('book/{uuid}', [BookController::class, 'show']);
    Route::put('book/update/{uuid}', [BookController::class, 'update']);

    // route category
    Route::get('categories', [CategoryController::class, 'index']);
    Route::post('category/store', [CategoryController::class, 'store']);

    // route book category
    Route::get('book-categories', [BookCategoryController::class, 'index']);
    Route::post('book-category/store', [BookCategoryController::class, 'store']);


    // route loan
    Route::get('loans', [LoanController::class, 'index']);
    Route::post('loan/store', [LoanController::class, 'store']);
    Route::get('loan/user/', [LoanController::class, 'searchByUserId']);
    Route::post('loan/return', [LoanController::class, 'returnBook']);
    Route::get('loan/check/{book_uuid}/{user_id}', [LoanController::class, 'checkLoan']);
});
