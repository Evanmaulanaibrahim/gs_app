<?php

namespace App\Filament\Widgets;

use App\Models\AccessLog;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class RecentAccessChart extends ChartWidget
{
    protected static ?string $heading = 'Aktivitas Akses 7 Hari Terakhir';

    protected function getData(): array
    {
        $data = Trend::model(AccessLog::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Akses',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
}