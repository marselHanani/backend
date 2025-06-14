<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\PostJob;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends Controller
{

    public function index(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $status = $request->query('status');
        $userId = $request->query('user_id');
        $jobId = $request->query('job_id');

        $query = JobApplication::with(['user', 'job']);

        // Filter by status if provided
        if ($status) {
            $query->where('status', $status);
        }

        // Filter by user_id if provided (for job seekers to see their applications)
        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Filter by job_id if provided (for employers to see applications for a specific job)
        if ($jobId) {
            $query->where('job_id', $jobId);
        }

        $applications = $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'page' => $applications->currentPage(),
            'result' => $applications->items(),
            'message' => 'Applications retrieved successfully',
            'status' => 200,
            'total' => $applications->total(),
            'last_page' => $applications->lastPage(),
            'per_page' => $applications->perPage(),
        ]);
    }

    /**
     * Store a newly created resource in the job_applications table (explicit DB version)
     */
    public function storeInDatabase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:post_jobs,id',
            'cover_letter' => 'required|string',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 422
            ], 422);
        }
        // Check if user has already applied for this job
        $userId = $request->input('user_id');
        if (!$userId || !is_numeric($userId) || intval($userId) <= 0) {
            return response()->json([
                'message' => 'A valid user_id is required.',
                'status' => 422
            ], 422);
        }
        $existingApplication = \App\Models\JobApplication::where('user_id', $userId)
            ->where('job_id', $request->job_id)
            ->first();
        if ($existingApplication) {
            return response()->json([
                'message' => 'You have already applied for this job',
                'status' => 409
            ], 409);
        }
        // Store resume file
        $resumePath = null;
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $originalName = $file->getClientOriginalName();
            $destination = 'resumes/' . $originalName;
            if (Storage::disk('public')->exists($destination)) {
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $timestamp = time();
                $destination = 'resumes/' . $nameWithoutExt . '_' . $timestamp . '.' . $extension;
            }
            $resumePath = $file->storeAs('resumes', basename($destination), 'public');
        }
        // Create job application
        $application = \App\Models\JobApplication::create([
            'user_id' => $userId,
            'job_id' => $request->job_id,
            'cover_letter' => $request->cover_letter,
            'resume' => $resumePath,
            'status' => 'pending',
            'applied_date' => now(),
        ]);
        // Get job details for notification
        $job = \App\Models\PostJob::find($request->job_id);
        // Create notification for admin
        \App\Models\Notification::create([
            'user_id' => 1, // Admin user ID
            'title' => 'New Job Application',
            'message' => 'New job application received for ' . $job->title,
            'is_read' => false,
            'type' => 'application',
        ]);
        return response()->json([
            'result' => $application->load('job'),
            'message' => 'Application submitted successfully',
            'status' => 201
        ], 201);
    }

    /**
     * Retrieve job applications from the job_applications table (explicit DB version)
     */
    public function getFromDatabase(Request $request)
    {
        $userId = $request->query('user_id');
        if (!$userId) {
            return response()->json([
                'message' => 'user_id query parameter is required',
                'status' => 400
            ], 400);
        }
        $applications = \App\Models\JobApplication::with(['user', 'job'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'result' => $applications,
            'message' => 'Applications retrieved successfully',
            'status' => 200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:post_jobs,id',
            'cover_letter' => 'required|string',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 422
            ], 422);
        }

        // Check if user has already applied for this job
        $userId = $request->input('user_id');
        if (!$userId || !is_numeric($userId) || intval($userId) <= 0) {
            return response()->json([
                'message' => 'A valid user_id is required.',
                'status' => 422
            ], 422);
        }
        $existingApplication = JobApplication::where('user_id', $userId)
            ->where('job_id', $request->job_id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'You have already applied for this job',
                'status' => 409
            ], 409);
        }

        // Store resume file
        $resumePath = null;
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $originalName = $file->getClientOriginalName();
            $destination = 'resumes/' . $originalName;
            if (Storage::disk('public')->exists($destination)) {
                $nameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $timestamp = time();
                $destination = 'resumes/' . $nameWithoutExt . '_' . $timestamp . '.' . $extension;
            }
            $resumePath = $file->storeAs('resumes', basename($destination), 'public');
        }

        // Create job application
        $application = JobApplication::create([
            'user_id' => $userId,
            'job_id' => $request->job_id,
            'cover_letter' => $request->cover_letter,
            'resume' => $resumePath,
            'status' => 'pending',
            'applied_date' => now(),
        ]);

        // Get job details for notification
        $job = PostJob::find($request->job_id);

        // Create notification for admin
        Notification::create([
            'user_id' => 1, // Admin user ID
            'title' => 'New Job Application',
            'message' => 'New job application received for ' . $job->title,
            'is_read' => false,
            'type' => 'application',
        ]);

        return response()->json([
            'result' => $application->load('job'),
            'message' => 'Application submitted successfully',
            'status' => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $application = JobApplication::with(['user', 'job' => function($query) {
            $query->with(['users' => function($q) {
                $q->with('posts'); // Get employer details
            }]);
        }])->find($id);

        if (!$application) {
            return response()->json([
                'message' => 'Application not found',
                'status' => 404
            ], 404);
        }

        // Check if the user is authorized to view this application
        if (Auth::id() !== $application->user_id && Auth::user()->role_id !== 1) {
            return response()->json([
                'message' => 'Unauthorized access',
                'status' => 403
            ], 403);
        }

        // Add resume URL if available
        $resumeUrl = null;
        if ($application->resume) {
            $resumeUrl = url('storage/' . $application->resume);
        }

        // Get application timeline/history (for future implementation)
        $timeline = [
            [
                'status' => 'applied',
                'date' => $application->applied_date,
                'message' => 'Application submitted'
            ]
        ];

        // Add status change events if status is not pending
        if ($application->status !== 'pending') {
            $timeline[] = [
                'status' => $application->status,
                'date' => $application->updated_at,
                'message' => 'Status updated to ' . ucfirst($application->status)
            ];
        }

        return response()->json([
            'result' => [
                'application' => $application,
                'resume_url' => $resumeUrl,
                'timeline' => $timeline,
                'days_since_applied' => now()->diffInDays($application->applied_date)
            ],
            'message' => 'Application retrieved successfully',
            'status' => 200
        ]);
    }


    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:pending,reviewing,accepted,rejected',
            'cover_letter' => 'sometimes|string',
            'resume' => 'sometimes|file|mimes:pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'status' => 422
            ], 422);
        }

        $application = JobApplication::find($id);

        if (!$application) {
            return response()->json([
                'message' => 'Application not found',
                'status' => 404
            ], 404);
        }

        // Check authorization
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role_id === 1;
        $isOwner = $currentUser->id === $application->user_id;

        // Only admin can change status
        if (isset($request->status) && !$isAdmin) {
            return response()->json([
                'message' => 'Unauthorized to change application status',
                'status' => 403
            ], 403);
        }

        // Only the owner can update cover letter and resume
        if (($request->has('cover_letter') || $request->hasFile('resume')) && !$isOwner) {
            return response()->json([
                'message' => 'Unauthorized to update application details',
                'status' => 403
            ], 403);
        }

        // Update resume if provided
        if ($request->hasFile('resume')) {
            // Delete old resume if exists
            if ($application->resume) {
                Storage::disk('public')->delete($application->resume);
            }
            $resumePath = $request->file('resume')->store('resumes', 'public');
            $application->resume = $resumePath;
        }

        // Update other fields
        if ($request->has('cover_letter')) {
            $application->cover_letter = $request->cover_letter;
        }

        if ($request->has('status')) {
            $oldStatus = $application->status;
            $application->status = $request->status;

            // Create notification for status change
            if ($oldStatus !== $request->status) {
                Notification::create([
                    'user_id' => $application->user_id,
                    'message' => 'Your application status for ' . $application->job->title . ' has been updated to ' . $request->status,
                    'is_read' => false,
                    'type' => 'status_update',
                ]);
            }
        }

        $application->save();

        return response()->json([
            'result' => $application->load(['user', 'job']),
            'message' => 'Application updated successfully',
            'status' => 200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $application = JobApplication::find($id);

        if (!$application) {
            return response()->json([
                'message' => 'Application not found',
                'status' => 404
            ], 404);
        }

        // Check authorization
        $currentUser = Auth::user();
        $isAdmin = $currentUser->role_id === 1;
        $isOwner = $currentUser->id === $application->user_id;

        if (!$isAdmin && !$isOwner) {
            return response()->json([
                'message' => 'Unauthorized to delete this application',
                'status' => 403
            ], 403);
        }

        // Delete resume file if exists
        if ($application->resume) {
            Storage::disk('public')->delete($application->resume);
        }

        $application->delete();

        return response()->json([
            'message' => 'Application deleted successfully',
            'status' => 200
        ]);
    }

    /**
     * Get applications by user
     */
    public function getApplicationsByUser(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $status = $request->query('status');
        $sortBy = $request->query('sort_by', 'created_at'); // Default sort by creation date
        $sortOrder = $request->query('sort_order', 'desc'); // Default sort order is descending

        $query = JobApplication::with(['job' => function($query) {
            $query->with(['users' => function($q) {
                $q->with('posts'); // Get employer details
            }]);
        }])->where('user_id', Auth::id());

        // Filter by status if provided
        if ($status) {
            $query->where('status', $status);
        }

        // Apply sorting
        $allowedSortFields = ['created_at', 'applied_date', 'status'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';

        $query->orderBy($sortBy, $sortOrder);

        $applications = $query->paginate($limit, ['*'], 'page', $page);

        // Get application status counts for filter badges
        $statusCounts = [
            'all' => JobApplication::where('user_id', Auth::id())->count(),
            'pending' => JobApplication::where('user_id', Auth::id())->where('status', 'pending')->count(),
            'reviewing' => JobApplication::where('user_id', Auth::id())->where('status', 'reviewing')->count(),
            'accepted' => JobApplication::where('user_id', Auth::id())->where('status', 'accepted')->count(),
            'rejected' => JobApplication::where('user_id', Auth::id())->where('status', 'rejected')->count(),
        ];

        // Format the application data to include additional information
        $formattedApplications = [];
        foreach ($applications->items() as $application) {
            $formattedApplication = $application->toArray();
            $formattedApplication['days_since_applied'] = now()->diffInDays($application->applied_date);
            $formattedApplication['resume_url'] = $application->resume ? url('storage/' . $application->resume) : null;
            $formattedApplications[] = $formattedApplication;
        }

        return response()->json([
            'page' => $applications->currentPage(),
            'result' => $formattedApplications,
            'status_counts' => $statusCounts,
            'message' => 'Applications retrieved successfully',
            'status' => 200,
            'total' => $applications->total(),
            'last_page' => $applications->lastPage(),
            'per_page' => $applications->perPage(),
        ]);
    }

    /**
     * Search for jobs and apply filters
     */
    public function searchJobs(Request $request)
    {
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $search = $request->query('search');
        $jobType = $request->query('job_type');
        $jobLevel = $request->query('job_level');
        $timeType = $request->query('time_type');
        $minSalary = $request->query('min_salary');
        $maxSalary = $request->query('max_salary');
        $location = $request->query('location');
        $sortBy = $request->query('sort_by', 'created_at'); // Default sort by creation date
        $sortOrder = $request->query('sort_order', 'desc'); // Default sort order is descending

        $query = PostJob::with('users'); // Eager load the employer relationship

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%')
                  ->orWhere('tags', 'LIKE', '%' . $search . '%')
                  ->orWhere('job_role', 'LIKE', '%' . $search . '%');
            });
        }

        // Apply job type filter
        if ($jobType) {
            $query->where('job_type', $jobType);
        }

        // Apply job level filter
        if ($jobLevel) {
            $query->where('job_level', $jobLevel);
        }

        // Apply time type filter
        if ($timeType) {
            $query->where('time_type', $timeType);
        }

        // Apply salary range filter
        if ($minSalary) {
            $query->where('salary_minimum', '>=', $minSalary);
        }

        if ($maxSalary) {
            $query->where('salary_maximum', '<=', $maxSalary);
        }

        // Apply location filter
        if ($location) {
            $query->where(function($q) use ($location) {
                $q->where('city', 'LIKE', '%' . $location . '%')
                  ->orWhere('location', 'LIKE', '%' . $location . '%');
            });
        }

        // Exclude expired jobs
        $query->where('expiration', '>=', now());

        // Apply sorting
        $allowedSortFields = ['created_at', 'title', 'salary_minimum', 'expiration'];
        $sortBy = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? strtolower($sortOrder) : 'desc';

        $query->orderBy($sortBy, $sortOrder);

        $jobs = $query->paginate($limit, ['*'], 'page', $page);

        // Get available filter options for dropdowns
        $jobTypes = PostJob::distinct()->pluck('job_type')->filter()->values();
        $jobLevels = PostJob::distinct()->pluck('job_level')->filter()->values();
        $timeTypes = PostJob::distinct()->pluck('time_type')->filter()->values();
        $locations = PostJob::distinct()->pluck('city')->filter()->values();

        return response()->json([
            'page' => $jobs->currentPage(),
            'result' => $jobs->items(),
            'filter_options' => [
                'job_types' => $jobTypes,
                'job_levels' => $jobLevels,
                'time_types' => $timeTypes,
                'locations' => $locations
            ],
            'message' => 'Jobs retrieved successfully',
            'status' => 200,
            'total' => $jobs->total(),
            'last_page' => $jobs->lastPage(),
            'per_page' => $jobs->perPage(),
        ]);
    }

    /**
     * Get job details by ID
     */
    public function getJobDetail(string $id)
    {
        $job = PostJob::with(['users' => function($query) {
            $query->with('posts'); // Get employer details
        }])->find($id);

        if (!$job) {
            return response()->json([
                'message' => 'Job not found',
                'status' => 404
            ], 404);
        }

        // Check if user has applied to this job
        $hasApplied = false;
        $application = null;

        if (Auth::check()) {
            $application = JobApplication::where('user_id', Auth::id())
                ->where('job_id', $id)
                ->first();

            $hasApplied = $application !== null;
        }

        // Get similar jobs based on job type, level, or tags
        $similarJobs = PostJob::where('id', '!=', $id)
            ->where(function($query) use ($job) {
                $query->where('job_type', $job->job_type)
                    ->orWhere('job_level', $job->job_level)
                    ->orWhere('job_role', $job->job_role);

                // If job has tags, also search by tags
                if ($job->tags) {
                    $tags = explode(',', $job->tags);
                    foreach ($tags as $tag) {
                        $query->orWhere('tags', 'LIKE', '%' . trim($tag) . '%');
                    }
                }
            })
            ->where('expiration', '>=', now())
            ->take(5)
            ->get();

        // Format job requirements and responsibilities as arrays if they are comma-separated strings
        $requirements = $job->requirements;
        if (is_string($requirements) && strpos($requirements, ',') !== false) {
            $requirements = array_map('trim', explode(',', $requirements));
        }

        $responsibilities = $job->responsibilities;
        if (is_string($responsibilities) && strpos($responsibilities, ',') !== false) {
            $responsibilities = array_map('trim', explode(',', $responsibilities));
        }

        // Count total applications for this job (only visible to admin or employer)
        $totalApplications = 0;
        if (Auth::check() && (Auth::user()->role_id == 1 || Auth::user()->role_id == 3)) {
            $totalApplications = JobApplication::where('job_id', $id)->count();
        }

        return response()->json([
            'result' => [
                'job' => $job,
                'has_applied' => $hasApplied,
                'application' => $hasApplied ? $application : null,
                'similar_jobs' => $similarJobs,
                'formatted_requirements' => $requirements,
                'formatted_responsibilities' => $responsibilities,
                'total_applications' => $totalApplications,
                'days_remaining' => now()->diffInDays($job->expiration, false)
            ],
            'message' => 'Job details retrieved successfully',
            'status' => 200
        ]);
    }
}
