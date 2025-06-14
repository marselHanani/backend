<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JobSeekerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ifAuth = auth('api')->check();
        if(!$ifAuth){
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = auth('api')->user();
        if($user->role->name == 'job_seeker'){
           return $next($request);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
