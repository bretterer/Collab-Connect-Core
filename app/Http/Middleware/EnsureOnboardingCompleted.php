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

        // If user is trying to access onboarding routes, let them through
        if ($request->routeIs('onboarding.*')) {
            return $next($request);
        }

        // If user is trying to access logout, let them through
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        // If user needs onboarding, redirect them to onboarding
        if ($user->needsOnboarding()) {
            return redirect()->route('onboarding.account-type');
        }

        return $next($request);
    }
}
