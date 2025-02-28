<?php

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/privacy', function() {
    return view('privacy');
});

Route::get('/terms-and-condition', function() {
    return view('terms-and-conditions');
});

Route::domain('/')->group(function () {
    Scramble::registerUiRoute('api');
    Scramble::registerJsonSpecificationRoute('api.json');
});
