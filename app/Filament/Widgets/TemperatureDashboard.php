<?php

namespace App\Filament\Widgets;

use App\Models\Temperature;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TemperatureDashboard extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $latestStatus = Temperature::latest()->first()?->status;
        $statusColor = $latestStatus === 'Detected' ? 'danger' : 'success';
        $statusIcon = $latestStatus === 'Detected' ? 'heroicon-o-fire' : 'heroicon-o-check-circle';

        $avgTemperature = number_format(Temperature::where('created_at', '>=', now()->subDay())->avg('temperature'), 2);
        $avgHumidity = number_format(Temperature::where('created_at', '>=', now()->subDay())->avg('humidity'), 2);
        $avgAir = number_format(Temperature::where('created_at', '>=', now()->subDay())->avg('air'), 2);

        $temperatureData = Trend::model(Temperature::class)->between(now()->subDay(), now())->perHour()->average('temperature');
        $humidityData = Trend::model(Temperature::class)->between(now()->subDay(), now())->perHour()->average('humidity');
        $airData = Trend::model(Temperature::class)->between(now()->subDay(), now())->perHour()->average('air');

        return [
            Stat::make('Suhu Rata²', $avgTemperature.'°C')
                ->description('24 jam terakhir')
                ->chart($temperatureData->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color('danger'),
                
            Stat::make('Kelembaban Rata²', $avgHumidity.'%')
                ->description('24 jam terakhir')
                ->chart($humidityData->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color('info'),

            Stat::make('Kualitas Udara', $avgAir.' ppm')
                ->description('24 jam terakhir')
                ->chart($airData->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color('success'),
                
            Stat::make('Status', $latestStatus ?? 'No Data')
                ->description('Pembacaan terakhir')
                ->color($statusColor)
                ->icon($statusIcon),
        ];
    }
}