<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AccessLog;
use App\Models\RFIDTag;
use Carbon\Carbon;

class AccessLogsTableSeeder extends Seeder
{
    public function run()
    {
        // Kosongkan tabel terlebih dahulu
        AccessLog::truncate();

        // Ambil beberapa RFID tag yang sudah ada
        $tags = RFIDTag::take(5)->get();

        if ($tags->isEmpty()) {
            $this->command->info('Tidak ada data RFIDTag! Silakan jalankan RFIDTagsTableSeeder terlebih dahulu.');
            return;
        }

        // Data akses yang akan dibuat
        $accessLogs = [];

        // Buat data akses untuk 30 hari terakhir
        for ($i = 0; $i < 100; $i++) {
            $tag = $tags->random();
            $accessTime = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 24));

            $accessLogs[] = [
                'rfid_tag_id' => $tag->id,
                'access_granted' => $tag->hasAccess(),
                'device_id' => 'ESP32_' . strtoupper(bin2hex(random_bytes(4))),
                'access_time' => $accessTime,
                'created_at' => $accessTime,
                'updated_at' => $accessTime,
            ];
        }

        // Masukkan data ke database
        AccessLog::insert($accessLogs);

        $this->command->info('Berhasil menambahkan ' . count($accessLogs) . ' data access log!');
    }
}