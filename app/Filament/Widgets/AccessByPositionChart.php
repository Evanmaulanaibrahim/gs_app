<?php

// namespace App\Filament\Widgets;

// use App\Models\AccessLog;
// use Filament\Widgets\ChartWidget;

// class AccessByPositionChart extends ChartWidget
// {
//     protected static ?string $heading = 'Akses Berdasarkan Jabatan';

//     protected function getData(): array
//     {
//         // Ambil data AccessLog dengan relasi RFID tag, kemudian kelompokkan berdasarkan posisi
//         $groupedData = AccessLog::with('rfidTag')
//             ->whereHas('rfidTag')
//             ->get()
//             ->groupBy(fn ($log) => $log->rfidTag->position)
//             ->map(fn ($logs) => $logs->count());

//         return [
//             'datasets' => [
//                 [
//                     'label' => 'Total Akses',
//                     'data' => $groupedData->values(),
//                     'backgroundColor' => [
//                         '#4ade80', // Hijau
//                         '#fbbf24', // Kuning
//                         '#f87171', // Merah
//                         // Tambahkan warna lagi jika posisi lebih dari 3
//                         '#60a5fa', // Biru
//                         '#a78bfa', // Ungu
//                     ],
//                 ],
//             ],
//             'labels' => $groupedData->keys(),
//         ];
//     }

//     protected function getType(): string
//     {
//         return 'pie';
//     }
// }
