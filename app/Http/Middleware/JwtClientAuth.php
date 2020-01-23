<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class JwtClientAuth
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
        $token= str_replace('Bearer ', "" , $request->header('Authorization'));

        try { 
           JWTAuth::setToken($token); //<-- set token and check
            if (! $claim = JWTAuth::getPayload()) {
                return response()->json(array('message'=>'user_not_found'), 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(array('message'=>'Expired Token'), 404);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(array('message'=>'Invalid Token'), 404);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(array('message'=>'Absent Token'), 404);
        } 

        // the token is valid and we have exposed the contents
        $request["CustomerId"] = $claim["sub"];
        return $next($request);
    }
}
