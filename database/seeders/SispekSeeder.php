<?php

namespace Database\Seeders;

use App\Models\Sispek;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SispekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sispek::insert([
            "server" => "https://ditppu.menlhk.go.id/sispekv2/",
            "app_id" => "",
            "app_secret" => "",
            "api_get_token" => "api/v2/token",
            "api_get_kode_cerobong" => "api/v2/cerobong",
            "api_get_parameter" => "api/v2/parameter",
            "api_post_data" => "api/v2/submit",
        ]);
    }
}
