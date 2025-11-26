<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Configuration::updateOrCreate(['id' => 1], [
           "interval_request" => 1,
           "interval_sending" => 60,
           "interval_retry" => 60,
           "interval_das_logs" => 5,
           "interval_average" => 60,
           "delay_sending" => 1,
           "day_backup" => 1,
           "manual_backup" => 1,
           "main_path" => "C:\\xampp\\htdocs\\egateway\\",
           "mysql_path" => "C:\\Program Files\\PostgreSQL\\16\\bin\\",
           "created_by" => "system",
           "created_ip" => "127.0.0.1",
        ]);
    }
}
