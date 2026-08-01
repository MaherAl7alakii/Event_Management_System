<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBannedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->banned_at !== null) {

            return response()->json([
                'message' => __('messages.account_banned'),
                'status'  => 403,
                'data'    => null,
            ], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
