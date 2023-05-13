<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
    //     $response = $next($request);

    //     $response->header('Access-Control-Allow-Origin', '*');
    //     $response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //     $response->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization, X-Requested-With');

    //     return $response;
    // }


    $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization',
    ];

    if ($request->getMethod() == "OPTIONS") {
        // The client is sending an OPTIONS request. Return the response with the CORS headers.
        return response()->json([], 204, $headers);
    }

    // Let the request pass through the middleware and add the CORS headers to the response.
    $response = $next($request);
    foreach ($headers as $key => $value) {
        $response->headers->set($key, $value);
    }
    return $response;
}
}