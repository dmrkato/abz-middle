<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use ReallySimpleJWT\Token as JWT;
use Illuminate\Support\Facades\Cache;

class TokenController extends BaseController
{
    public function getToken(Request $request)
    {
        $secretKey = env('JWT_SECRET');
        $expiration = new \DateTime('+ 40 minutes');
        $issuer = $request->getClientIp();
        $id = str()->random(32);
        $token = JWT::create($id, $secretKey, $expiration->getTimestamp(), $issuer);
        Cache::put($id, $token, $expiration);
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }
}
