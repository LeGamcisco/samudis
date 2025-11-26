<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::truncate();
        Unit::insert([
            ["name" => "ppm"],
            ["name" => "μg/Nm3"],
            ["name" => "mg/Nm3"],
            ["name" => "l/min"],
            ["name" => "m3/min"],
            ["name" => "minutes"],
            ["name" => "ton"],
            ["name" => "%"],
            ["name" => "m/s"],
            ["name" => "m3/h"],
            ["name" => "mBbar"],
            ["name" => "°"],
            ["name" => "Km/h"],
            ["name" => "Km/m"],
            ["name" => "°C"],
            ["name" => "watt/m2"],
            ["name" => "mm/h"],
            ["name" => "m3/s"],
        ]);
    }
}
