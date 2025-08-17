<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNeedsOnboarding
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->isBusinessAccount() && $user->currentBusiness->onboarding_complete) {
            return redirect()->route('dashboard');
        }

        if ($user->isInfluencerAccount() && $user->influencer->onboarding_complete) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
