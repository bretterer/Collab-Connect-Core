<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is not authenticated, let them through
        if (! $user) {
            return $next($request);
        }

        // If user is admin, let them through
        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isInfluencerAccount() && ! $user->influencer->onboarding_complete) {
            return redirect()->route('onboarding.influencer');
        }
        if ($user->isBusinessAccount() && ! $user->currentBusiness->onboarding_complete) {
            return redirect()->route('onboarding.business');
        }

        return $next($request);
    }
}
