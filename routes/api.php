<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));

Route::apiResource('books', App\Http\Controllers\Api\BookController::class);

Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);

Route::apiResource('bookCategories', App\Http\Controllers\Api\BookCategoriesController::class);

Route::apiResource('users', App\Http\Controllers\Api\UserController::class);
