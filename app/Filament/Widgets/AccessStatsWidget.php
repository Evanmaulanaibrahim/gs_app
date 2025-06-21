<?php

namespace App\Filament\Widgets;

use App\Models\AccessLog;
use App\Models\RFIDTag;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AccessStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s'; // Auto-refresh setiap 30 detik
    protected static bool $isLazy = true; // Lazy loading untuk performa

    protected function getStats(): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // Hitung statistik untuk 30 hari terakhir
        $recentAccess = AccessLog::where('access_time', '>=', $thirtyDaysAgo);
        $totalRecentAccess = $recentAccess->count();
        $grantedRecentAccess = $recentAccess->where('access_granted', true)->count();
        $deniedRecentAccess = $totalRecentAccess - $grantedRecentAccess;
        
        // Hitung statistik keseluruhan
        $totalUsers = RFIDTag::count();
        $totalAccessAllTime = AccessLog::count();
        $grantedAccessAllTime = AccessLog::where('access_granted', true)->count();
        
        // Hitung rasio dengan handling division by zero
        $successRate = $totalRecentAccess > 0 
            ? round(($grantedRecentAccess / $totalRecentAccess) * 100)
            : 0;

        return [
            Stat::make('Total Pengguna', $totalUsers)
                ->description($this->getUserBreakdown())
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary')
                ->chart($this->getUserRegistrationTrend())
                ->chartColor('primary'),
                
            Stat::make('Akses Diberikan', $grantedRecentAccess)
                ->description("{$this->getDailyAverage($grantedRecentAccess)}/hari | Total: {$grantedAccessAllTime}")
                ->descriptionIcon('heroicon-o-lock-open')
                ->color('success')
                ->chart($this->getAccessTrend(true)),
                
            Stat::make('Akses Ditolak', $deniedRecentAccess)
                ->description("{$this->getDailyAverage($deniedRecentAccess)}/hari | Total: " . ($totalAccessAllTime - $grantedAccessAllTime))
                ->descriptionIcon('heroicon-o-lock-closed')
                ->color('danger')
                ->chart($this->getAccessTrend(false)),
                
            Stat::make('Rasio Keberhasilan', "{$successRate}%")
                ->description($this->getSuccessRateComparison($successRate))
                ->descriptionIcon($successRate > 70 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($this->getRateColor($successRate))
                ->chart($this->getSuccessRateTrend()),
        ];
    }

    // Helper methods
    private function getUserBreakdown(): string
    {
        $counts = RFIDTag::selectRaw('position, count(*) as count')
            ->groupBy('position')
            ->pluck('count', 'position');
            
        return sprintf(
            "Direktur: %d | Manajer: %d | Karyawan: %d",
            $counts['director'] ?? 0,
            $counts['manager'] ?? 0,
            $counts['employee'] ?? 0
        );
    }

    private function getDailyAverage(int $count): float
    {
        return round($count / 30, 1);
    }

    private function getRateColor(int $rate): string
    {
        return match (true) {
            $rate >= 80 => 'success',
            $rate >= 60 => 'info',
            $rate >= 40 => 'warning',
            default => 'danger',
        };
    }

    private function getSuccessRateComparison(int $currentRate): string
    {
        $lastPeriodRate = $this->getPreviousPeriodRate();
        
        if ($lastPeriodRate === 0) {
            return 'Tidak ada data periode sebelumnya';
        }
        
        $difference = $currentRate - $lastPeriodRate;
        $trend = $difference >= 0 ? 'meningkat' : 'menurun';
        
        return sprintf(
            '%s %d%% dari periode sebelumnya',
            $trend,
            abs($difference)
        );
    }

    private function getPreviousPeriodRate(): int
    {
        $sixtyDaysAgo = Carbon::now()->subDays(60);
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        $previousAccess = AccessLog::whereBetween('access_time', [$sixtyDaysAgo, $thirtyDaysAgo]);
        $totalPrevious = $previousAccess->count();
        
        if ($totalPrevious === 0) {
            return 0;
        }
        
        $grantedPrevious = $previousAccess->where('access_granted', true)->count();
        
        return round(($grantedPrevious / $totalPrevious) * 100);
    }

    private function getUserRegistrationTrend(): array
    {
        return $this->generateTrendData(RFIDTag::class);
    }

    private function getAccessTrend(bool $granted): array
    {
        return $this->generateTrendData(
            AccessLog::class,
            fn ($query) => $query->where('access_granted', $granted)
        );
    }

    private function getSuccessRateTrend(): array
    {
        $data = [];
        $days = 7; // Mingguan
        
        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();
            
            $total = AccessLog::whereBetween('access_time', [$start, $end])->count();
            $granted = AccessLog::whereBetween('access_time', [$start, $end])
                ->where('access_granted', true)
                ->count();
                
            $rate = $total > 0 ? round(($granted / $total) * 100) : 0;
            
            $data[] = $rate;
        }
        
        return $data;
    }

    private function generateTrendData(string $model, ?callable $queryModifier = null): array
    {
        $data = [];
        $days = 7; // Mingguan
        
        for ($i = $days; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();
            
            $query = $model::whereBetween('created_at', [$start, $end]);
            
            if ($queryModifier) {
                $queryModifier($query);
            }
            
            $data[] = $query->count();
        }
        
        return $data;
    }
}