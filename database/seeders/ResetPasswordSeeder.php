<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResetPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::get();
        foreach ($users as $user) {
            $user->update([
                'password' => Hash::make($user->email),
            ]);
            echo $user->email. " was reset " . PHP_EOL;
        }
    }
}
