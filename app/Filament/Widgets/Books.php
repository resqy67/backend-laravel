<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Book;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

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
            'labels' => $data->map(fn(TrendValue $value) => $value->date), // Format label bulan dan tahun
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
            'week' => 'Minggu Lalu',
            'month' => 'Bulan Lalu',
            'year' => 'Tahun Ini',
        ];
    }

    // Fungsi untuk menentukan rentang waktu berdasarkan filter
    private function getStartEndDates(): array
    {
        switch ($this->filter) {
            case 'today':
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay(),
                ];
            case 'week':
                return [
                    'start' => now()->subWeek()->startOfWeek(),
                    'end' => now()->subWeek()->endOfWeek(),
                ];
            case 'month':
                return [
                    'start' => now()->subMonth()->startOfMonth(),
                    'end' => now()->subMonth()->endOfMonth(),
                ];
            case 'year':
            default:
                return [
                    'start' => now()->startOfYear(),
                    'end' => now()->endOfYear(),
                ];
        }
    }
}
