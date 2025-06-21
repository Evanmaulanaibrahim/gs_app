<?php

namespace Database\Seeders;

use App\Models\Temperature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemperatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Temperature::factory()->count(1000)->create();
    }
}
