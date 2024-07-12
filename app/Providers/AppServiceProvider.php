<?php

namespace App\Providers;

use App\Events\BookAdded;
use App\Events\BookReturn;
use App\Events\LoanCreated;
use App\Listeners\HandleBookReturn;
use App\Listeners\SaveLoanHistory;
use App\Listeners\SendBookAddedNotification;
use App\Models\Loan;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        // Scramble::ignoreDefaultRoutes();
    }

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        BookReturn::class => [
            HandleBookReturn::class,
        ],
        LoanCreated::class => [
            SaveLoanHistory::class,
        ],
        BookAdded::class => [
            SendBookAddedNotification::class,
        ],
    ];


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // parent::boot();
        Scramble::extendOpenApi(function (OpenApi $openApi) {
            $openApi->secure(
                SecurityScheme::http('bearer', 'JWT')
            );
        });
    }
}
