<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\Books;
use App\Filament\Widgets\UserLoans;
use App\Filament\Widgets\UserSessions;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    // protected static ?string $title = 'Halaman Utama';

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            Books::class,
            UserLoans::class,
            UserSessions::class,
        ];
    }

    // public static function getWidgets(): array
    // {
    //     return [
    //         StatsOverview::class,
    //         Books::class,
    //         UserLoans::class,
    //         UserSessions::class,
    //     ];
    // }

    // protected function setUp(): void
    // {
    //     $this->getWidgets();
    // }

    // public function getWidgets(): array
    // {
    //     return static::getWidgets();
    // }
}
