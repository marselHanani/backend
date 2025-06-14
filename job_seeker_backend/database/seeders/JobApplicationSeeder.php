<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\PostJob;

class JobApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jobSeekers = User::where('role_id', 3)->get();
        $jobs = PostJob::all();

        if ($jobs->isEmpty()) {
            // If no jobs exist, create some manually with minimal fields
            for ($i = 1; $i <= 10; $i++) {
                PostJob::create([
                    'title' => 'Job Title ' . $i,
                    'description' => 'Description for job ' . $i,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            $jobs = PostJob::all();
        }

        foreach ($jobSeekers as $seeker) {
            $applyCount = rand(3, 5);
            $randomJobs = $jobs->random(min($applyCount, $jobs->count()));

            foreach ($randomJobs as $job) {
                JobApplication::create([
                    'user_id' => $seeker->id,
                    'job_id' => $job->id,
                    'cover_letter' => 'Sample cover letter for ' . $job->title . ' position. I am very interested in this role and believe my skills are a great match.',
                    'resume' => 'resumes/sample_' . $seeker->id . '.pdf',
                    'status' => ['pending', 'reviewed', 'interview', 'accepted', 'rejected'][array_rand(['pending', 'reviewed', 'interview', 'accepted', 'rejected'])],
                    'applied_date' => now()->subDays(rand(1, 30))
                ]);
            }
        }

        if ($jobSeekers->count() > 0 && $jobs->count() > 0) {
            $specificSeeker = $jobSeekers->first();
            $specificJob = $jobs->first();

            JobApplication::create([
                'user_id' => $specificSeeker->id,
                'job_id' => $specificJob->id,
                'cover_letter' => 'Test cover letter for testing purposes.',
                'resume' => 'resumes/test_resume.pdf',
                'status' => 'pending',
                'applied_date' => now()
            ]);
        }
    }
}
