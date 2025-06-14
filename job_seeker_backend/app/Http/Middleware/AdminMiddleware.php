<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
     $hasToken = auth('api')->check();
     if(!$hasToken){
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
        ], 401);
     }
    $user = auth('api')->user();
    if($user->role->name == 'admin'){
        return $next($request);
    }else{
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized'
         ], 401);
    }
    }
}
