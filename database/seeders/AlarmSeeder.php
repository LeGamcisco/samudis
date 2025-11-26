<?php

namespace Database\Seeders;

use App\Models\AlarmConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AlarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AlarmConfiguration::updateOrCreate(['id' => 1],[
            'enable_email' => 0,
            'enable_telegram' => 0,
            'sent_from' => "company@samu",
            'sent_to' => "environtment@samu",
            'protocol' => "smtp",
            'host' => "smtp.mailtrap.io",
            'smtp_user' => "company@egayeway",
            'smtp_pass' => "company@samu",
            'smtp_port' => "25",
            'timeout' => 60,
            'telegram_chat_id' => "",
            'telegram_bot_token' => "",
        ]);
    }
}
