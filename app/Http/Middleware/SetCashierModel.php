<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetCashierModel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        if ($request->user()->isInfluencerAccount()) {
            \Laravel\Cashier\Cashier::useCustomerModel(\App\Models\Influencer::class);
        } elseif ($request->user()->isBusinessAccount()) {
            \Laravel\Cashier\Cashier::useCustomerModel(\App\Models\Business::class);
        } else {
            \Laravel\Cashier\Cashier::useCustomerModel(\App\Models\User::class);
        }

        return $next($request);
    }
}
