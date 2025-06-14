<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employer;
use App\Models\User;

class EmployerSeeder extends Seeder
{
    public function run()
    {
        $userAhmad = User::where('username', 'ahmadali')->first();
        $userSara = User::where('username', 'sarakhalil')->first();

        if ($userAhmad) {
            Employer::firstOrCreate(
                ['user_id' => $userAhmad->id],
                [
                    'company_name' => 'Ahmad Co',
                    'company_email' => 'ahmad@example.com',
                    'status' => 'Pending',
                ]
            );
        }

        if ($userSara) {
            Employer::firstOrCreate(
                ['user_id' => $userSara->id],
                [
                    'company_name' => 'Sara LLC',
                    'company_email' => 'sara@example.com',
                    'status' => 'Pending',
                ]
            );
        }
    }
}
