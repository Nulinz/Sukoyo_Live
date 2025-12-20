<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Login;

class LoginSeeder extends Seeder
{
    public function run()
    {
        Login::create([
            'empcode' => 'admin',
            'password' => Hash::make('Admin123@'),
            'role' => 'admin',
        ]);
    }
}
