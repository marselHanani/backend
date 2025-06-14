<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'email' =>'Admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'username' => 'Admin',
            'email_verified_at' => now(),
            'is_verified' => 1,
            'role_id' => 1,
        ]);
        User::create([
            'first_name' => 'marsell',
            'last_name' => 'marsell',
            'email' =>'marsell@gmail.com',
            'password' => Hash::make('Marsel@123'),
            'username' => 'marsell',
            'email_verified_at' => now(),
            'is_verified' => 1,
            'role_id' => 2,
        ]);
    }
}

