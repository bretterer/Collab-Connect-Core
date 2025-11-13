<?php

namespace App\Http\Middleware;

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
        $user = $request->user();

        // Allow admins to bypass market approval check
        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // If user is not market approved, redirect to waitlist page
        if ($user && ! $user->market_approved) {
            return redirect()->route('market-waitlist');
        }

        return $next($request);
    }
}
