<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     return $next($request);
    // }

    public function handle(Request $request, Closure $next, $userType)
    {
    //     $user = auth()->user();

    // if (!$user || !in_array($user->type, $userType)) {
    //     return redirect()->route('login');
    // }

    // return $next($request);
    //     // ];
    //     // return response()->json($response,201); 
    if (Auth::user() && Auth::user()->type == $userType) {
        return $next($request);
    }
    return response([
        'message' => 'You don\'t have permission to perform this action'
    ], 401);
    }
}
?>