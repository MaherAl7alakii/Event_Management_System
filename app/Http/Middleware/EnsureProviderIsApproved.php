<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProviderIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();


        if ($user && $user->hasRole('service_provider')) {

            $profile = $user->serviceProvider;

            if (!$profile) {
                return response()->json([
                    'status'  => 403,
                    'message' => 'You must complete and create your profile first to proceed.',
                ], Response::HTTP_FORBIDDEN);
            }

            if ($profile->approval_status !== 'approved') {

                $message = $profile->approval_status === 'pending'
                    ? 'Your account is currently under review by the administration. Please wait for approval.'
                    : 'Your profile has been rejected. Please contact support or update the required details.';

                return response()->json([
                    'status'  => 403,
                    'message' => $message,
                    'approval_status' => $profile->approval_status
                ], Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
