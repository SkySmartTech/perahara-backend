<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@perahara.com'],
            [
                'username'        => 'perahera_admin',
                'password'        => Hash::make('perahara@123'),
                'user_type'       => 'admin',
                'service_type_id' => null,
            ]
        );
    }
}
