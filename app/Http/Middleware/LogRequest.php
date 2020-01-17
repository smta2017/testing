<?php

namespace App\Http\Middleware;

use Closure;
use App\requestLog;
class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $requestLog = new requestLog();
        $requestLog->request=$request;
        
        $response = $next($request);
        
        $requestLog->response=$response;
        $requestLog->save();
        
        return $response;

    }
}
