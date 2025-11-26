<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(["email" => "superuser@samu.com"],[
            'name' => 'Superuser',
            'group_id' => 0,
            'email' => 'superuser@samu.com',
            'password' => '$2y$10$2M.pfYzSLJuixSsDKLJ23.GivIo7T/sypAV8XS9wfF3T3W1JTUFIm',
        ]);
        User::updateOrCreate(["email" => "admin@samu.com"],[
            'name' => 'Administrator',
            'group_id' => 1,
            'email' => 'admin@samu.com',
            'password' => '$2y$10$pT5RBjgCfJcQLgBeA6Dtv.0rz7QEy8baR4vF4CTBiqqY.Td/BDz5W',
        ]);
        User::updateOrCreate(["email" => "operator@samu.com"],[
            'name' => 'Operator',
            'group_id' => 2,
            'email' => 'operator@samu.com',
            'password' => '$2y$10$6C4dFmWF0ZB9EQQkkfxT/.308Tr8W83L7ip0XfpJxaXTN.9xkBov.',
        ]);
    }
}
