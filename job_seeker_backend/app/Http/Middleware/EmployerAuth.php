<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();
        if($user && ($user->role->name == 'employer'|| $user->role->name == 'admin')){
            return $next($request);
        }
        return response()->json([
            'message' => 'unauthorized access denied',
        ],401);
    }
}
