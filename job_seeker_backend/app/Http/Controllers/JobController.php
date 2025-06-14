<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobRequest;
use App\Models\Job;
use App\Models\PostJob;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $limit = request()->query('limit', 10);
        $search = request()->query('search');
        $page = request()->query('page', 1);
        $skip = request()->query('skip', 0);

        $query = PostJob::query();

        if ($search) {
            $query->where('title', 'LIKE', '%' . $search . '%')
                ->orWhere('description', 'LIKE', '%' . $search . '%');
        }

        if (request()->has('skip')) {
            $total = $query->count(); 
            $jobs = $query->skip($skip)->take($limit)->get();

            $hasMore = ($skip + $limit) < $total;
            $nextSkip = $hasMore ? ($skip + $limit) : null;

            return response()->json([
                'result' => $jobs,
                'message' => 'Jobs retrieved successfully',
                'status' => 200,
                'total' => $total,
                'hasMore' => $hasMore,
                'current_skip' => $skip,
                'next_skip' => $nextSkip,
                'loaded_count' => $jobs->count(),
                'remaining' => $total - ($skip + $jobs->count())
            ]);
        } else {
            $jobs = $query->paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'page' => $jobs->currentPage(),
                'result' => $jobs->items(),
                'message' => 'Jobs retrieved successfully',
                'status' => 200,
                'total' => $jobs->total(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(JobRequest $request)
    {
        $job = PostJob::create($request->all());
        return response()->json([
            'result' => $job,
            'message' => 'Job created successfully',
            'status' => 201
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $job = PostJob::find($id);
        if(!empty($job)) {
        return response()->json([
            'result'=> $job,
            'message'=> 'Job retrieved successfully',
            'status'=> 200
        ]);
    }else{
        return response()->json([
            'message'=>'Job not found',
           'status'=> 404
        ]);
    }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(JobRequest $request, string $id)
    {
        $job = PostJob::find($id);
        if(!empty($job)) {
            $job->update($request->all());
        return response()->json([
           'result'=> $job,
           'message'=> 'Job updated successfully',
           'status'=> 200
        ]);
        }else{
            return response()->json([
               'message'=>'Job not found',
              'status'=> 404
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $job = PostJob::find($id);
        if(!empty($job)) {
            $job->delete();
        return response()->json([
            'message'=> 'Job delete successfully',
            'status'=> 200
        ]);
    }else{
        return response()->json([
            'message'=>'Job not found',
           'status'=> 404
        ]);
    }
    }
}
