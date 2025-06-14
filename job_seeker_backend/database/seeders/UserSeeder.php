<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate(
            ['username' => 'ahmadali'], 
            [
                'first_name' => 'Ahmad',
                'last_name' => 'Ali',
                'email' => 'ahmad@example.com',
                'password' => Hash::make('password123'), 
                'role_id' => 2,  
                'is_verified' => 1,
            ]
        );

        User::firstOrCreate(
            ['username' => 'sarakhalil'],
            [
                'first_name' => 'Sara',
                'last_name' => 'Khalil',
                'email' => 'sara@example.com',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'is_verified' => 1,
            ]
        );
    }
}
