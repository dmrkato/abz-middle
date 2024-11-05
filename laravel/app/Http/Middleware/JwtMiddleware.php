<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use ReallySimpleJWT\Token as JWT;
use Illuminate\Support\Facades\Cache;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('token')) {
            return response([
                'message' => 'Token header not provided'
            ])->setStatusCode(401);
        }

        $token = $request->header('token');

        if(!JWT::validate($token, env('JWT_SECRET'))) {
            return response([
                'message' => 'Token is not valid'
            ])->setStatusCode(401);
        }

        if (!JWT::validateExpiration($token, env('JWT_SECRET'))) {
            return response([
                'message' => 'Token has expired'
            ])->setStatusCode(401);
        }

        $payload = JWT::getPayload($token, env('JWT_SECRET'));

        $id = $payload['user_id'];
        $cacheToken = Cache::get($id);
        if ($token !== $cacheToken) {
            return response([
                'message' => 'Token is not valid'
            ])->setStatusCode(401);
        }
        Cache::delete($id);

        return $next($request);
    }
}
