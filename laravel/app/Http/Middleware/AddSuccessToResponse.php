<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddSuccessToResponse
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
        $response = $next($request);

        $responseContent = $response->getOriginalContent();
        if (key_exists('errors', $responseContent)) {
            $responseContent['fails'] = $responseContent['errors'];
            unset($responseContent['errors']);
        }
        $responseContent = array_merge(['success' => true], $responseContent);

        if (!preg_match('/^2\d{2}$/', $response->getStatusCode(),)) {
            $responseContent['success'] = false;
        }


        $response->setContent(response()->json($responseContent)->getContent());

        return $response;
    }
}
