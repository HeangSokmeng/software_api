<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        try
        {
            /**
             * Attempt to authenticate the user and get their token
             */
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            /**
             * Token has expired
             */
            return response()->json([
                'error' => 'Token expired',
            ], 401);
        } catch (TokenInvalidException $e) {
            /**
             * Token is invalid
             */
            return response()->json([
                'error' => 'Token invalid',
            ], 401);
        } catch (JWTException $e) {
            /**
             * Token is absent or another issue occurred
             */
            return response()->json([
                'error' => 'Token absent',
            ], 401);
        }

        /**
         * If everything is fine, proceed with the request
         */
        return $next($request);
    }

}
