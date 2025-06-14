<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\JobUser;
use App\Models\Notification;
use App\Models\PostJob;
use App\Models\Role;
use App\Models\User;
use App\Models\Report;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'employer']);
        Role::firstOrCreate(['name' => 'job_seeker']);;
        $this->call(AdminInfoSeeder::class);
        $this->call(PostJobsTableSeeder::class);
        $this->call(UserSeeder::class);
        User::factory(20)->create();
        Employer::factory(20)->create();
        //$this->call(EmployerSeeder::class);

        //User::factory(10)->create();
        
        PostJob::factory(30)->create();
        //Employer::factory(3)->create();
        Notification::factory(10)->create();
        JobApplication::factory(10)->create();
        Report::factory(count: 10)->create();
        JobUser::factory(10)->create();
    }
}
