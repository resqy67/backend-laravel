<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Book;
use Filament\Widgets\ChartWidget;
// use Carbon\Carbon;
use Illuminate\Support\Carbon;

class Books extends ChartWidget
{
    protected static ?string $heading = 'Buku Terbaru';

    public ?string $filter = 'year';

    protected function getData(): array
    {
        // Ambil rentang waktu berdasarkan filter yang dipilih
        $startEndDates = $this->getStartEndDates();

        $data = Trend::model(Book::class)
            ->between(
                start: $startEndDates['start'],
                end: $startEndDates['end'],
            )
            ->perMonth() // Sesuaikan dengan rentang waktu jika diperlukan
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Buku Terbaru',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4A90E2',
                    'backgroundColor' => 'rgba(74, 144, 226, 0.2)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
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
            'year' => 'Tahun Ini',
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
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                break;
        }

        return [
            'start' => $start,
            'end' => $end,
        ];
    }
}
