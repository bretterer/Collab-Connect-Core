<?php

namespace App\Http\Middleware;

use App\Settings\RegistrationMarkets;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMarketApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $registrationMarkets = app(RegistrationMarkets::class);
        if (! $registrationMarkets->enabled) {
            return $next($request);
        }
        $user = $request->user();

        // Allow admins to bypass market approval check
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // Allow legacy users (no postal_code) to bypass market approval check
        if ($user && ! $user->postal_code) {
            return $next($request);
        }

        // If user has a postal_code but is not market approved, redirect to waitlist page
        if ($user && ! $user->market_approved) {
            return redirect()->route('market-waitlist');
        }

        return $next($request);
    }
}
