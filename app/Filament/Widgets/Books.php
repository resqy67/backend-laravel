<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Book;
use Filament\Widgets\ChartWidget;

class Books extends ChartWidget
{
    protected static ?string $heading = 'Buku Terbaru Tahun Ini';

    public ?string $filter = 'year';

    protected function getData(): array
    {
        $data = Trend::model(Book::class)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();
        return [
            'datasets' => [
                [
                    'label' => 'Buku Terbaru',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
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
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
}