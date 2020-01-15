<?php

namespace App\Http\Middleware;

use Closure;


class Cors 
{
    public function handle($request, Closure $next)
    {
        // return apache_request_headers();
        // return response()->json($request);
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type,content-type, authorization, Accept, Authorization, X-Request-With,*');
        header('Access-Control-Allow-Credentials: true');
        // return response()->json($request->all());
        return $next($request);
        
        if (!$request->isMethod('options')) 
        {
            return $next($request);
        }
    }
}
