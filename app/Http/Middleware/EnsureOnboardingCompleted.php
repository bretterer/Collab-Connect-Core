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

        if ($user->hasBusinessInvitePending()) {
            if($user->isInfluencerAccount()) {
                $user->businessInvites()->get()->each(function($invite) {
                    // $invite->delete();
                });
            }

            $businessInvite = $user->businessInvites()->whereNull('joined_at')->latest()->first();
            if ($businessInvite) {
                return redirect()->signedRoute('accept-business-invite', ['token' => $businessInvite->token], 1000);
            }
        }

        if ($user->isInfluencerAccount() && ! $user->influencer->onboarding_complete) {
            return redirect()->route('onboarding.influencer');
        }

        if ($user->isBusinessAccount() && ($user->currentBusiness == null ||! $user->currentBusiness->onboarding_complete)) {
            return redirect()->route('onboarding.business');
        }

        return $next($request);
    }
}
