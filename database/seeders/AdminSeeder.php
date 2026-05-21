<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'user_email' => 'admin@gmail.com',
        ], [
            'user_login' => 'admin',
            'user_nicename' => 'admin',
            'user_pass' => Hash::make('password'),
            'display_name' => 'Admin',
        ]);
    }
}
