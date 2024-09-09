<?php

namespace App\Filament\Widgets;

use App\Models\Book;
use App\Models\LoanHistory;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalBooks = Book::count();
        $totalLoan = LoanHistory::count();
        return [
            Stat::make('Total Pengguna', $totalUsers,)
                ->icon('heroicon-o-user-group')
                ->description('Total pengguna yang terdaftar di sistem')
                ->descriptionColor('success')
                ->descriptionIcon('heroicon-o-chevron-up', 'before'),
            Stat::make('Total Buku Tersedia', $totalBooks)
                ->icon('heroicon-o-book-open')
                ->description('Total buku yang tersedia di perpustakaan')
                ->descriptionColor('success')
                ->descriptionIcon('heroicon-o-chevron-up', 'before'),
            Stat::make('Total Peminjaman Buku', $totalLoan)
                ->icon('heroicon-o-clipboard-document')
                ->description('Total peminjaman buku yang tercatat')
                ->descriptionColor('success')
        ];
    }
}