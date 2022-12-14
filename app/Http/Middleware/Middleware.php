<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException as Exception;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\Batoul;

class Middleware
{
    use Batoul;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $token = $request->header('auth-token');
        $request->headers->set('auth-token', (string)$token, true);
        $request->headers->set('Authorization', 'Bearer ' . $token, true);
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return $this->sendError(401, 'Unauthenticated user');
        } catch (Exception $e) {

            return $this->sendError(401, 'token_invalid');
        }
        return $next($request);

    }
}
