<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Book;
use App\Models\Loan;
use App\Models\LoanHistory;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class Books extends ChartWidget
{
    protected static ?string $heading = 'Statistik Buku dan Peminjaman';

    public ?string $filter = 'month';

    protected array|string|int $columnSpan = 2;

    protected static ?string $maxHeight = '200px';

    protected function getData(): array
    {
        $data = $this->getTrendData();
        $loanData = $this->getLoanTrendData();

        return [
            'datasets' => [
                [
                    'label' => 'Buku Terbaru',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4A90E2',
                    'backgroundColor' => 'rgba(74, 144, 226, 0.2)',
                    'fill' => true,
                ],
                [
                    'label' => 'Peminjaman Buku',
                    'data' => $loanData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#E24A4A',
                    'backgroundColor' => 'rgba(226, 74, 74, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date), // Format tanggal jika diperlukan
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            // 'year' => 'Tahun Ini',
        ];
    }

    // Fungsi untuk menentukan rentang waktu berdasarkan filter
    private function getStartEndDates(): array
    {
        $start = null;
        $end = null;

        $filter = $this->filter;

        switch ($filter) {
            case 'today':
                $start = Carbon::today();
                $end = Carbon::today()->endOfDay();
                break;
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;
                // case 'year':
                //     $start = Carbon::now()->startOfYear();
                //     $end = Carbon::now()->endOfYear();
                //     break;
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    private function getTrendData()
    {
        $startEndDates = $this->getStartEndDates();

        $trendQuery = Trend::model(Book::class)
            ->between(
                start: $startEndDates['start'],
                end: $startEndDates['end'],
            );

        switch ($this->filter) {
            case 'today':
                $trendQuery->perHour();
                break;
            case 'week':
                $trendQuery->perDay();
                break;
            case 'month':
                $trendQuery->perMonth();
                break;
            case 'year':
                $trendQuery->perYear();
                break;
            default:
                $trendQuery->perMonth(); // Default ke perMonth jika filter tidak dikenali
                break;
        }

        return $trendQuery->count();
    }

    private function getLoanTrendData()
    {
        $startEndDates = $this->getStartEndDates();

        $trendQuery = Trend::model(LoanHistory::class)
            ->between(
                start: $startEndDates['start'],
                end: $startEndDates['end'],
            );

        switch ($this->filter) {
            case 'today':
                $trendQuery->perHour();
                break;
            case 'week':
                $trendQuery->perDay();
                break;
            case 'month':
                $trendQuery->perMonth();
                break;
            case 'year':
                $trendQuery->perYear();
                break;
            default:
                $trendQuery->perMonth(); // Default ke perMonth jika filter tidak dikenali
                break;
        }

        return $trendQuery->count();
    }
}
