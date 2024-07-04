<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\LoanController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Artisan::command('loan:returnbook', function () {
//     LoanController::returnBook($argument); // Replace $argument with the actual argument you need to pass
// })->daily();
