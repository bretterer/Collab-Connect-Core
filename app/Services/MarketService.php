<?php

namespace App\Services;

use App\Jobs\ProcessWaitlistSubscription;
use App\Models\Market;
use App\Models\MarketWaitlist;
use App\Models\User;
use App\Notifications\MarketOpenedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Collection;

class MarketService
{
    /**
     * Approve waitlisted users for the given postal codes.
     *
     * @param  array<string>  $postalCodes
     * @return array{approved_count: int, users: Collection<int, User>}
     */
    public function approveWaitlistedUsersForPostalCodes(
        array $postalCodes,
        ?Market $market = null,
        bool $sendNotification = false
    ): array {
        $approvedUsers = collect();

        foreach ($postalCodes as $postalCode) {
            // Get waitlist entries with subscription data before processing
            $waitlistEntries = MarketWaitlist::where('postal_code', $postalCode)
                ->with('user')
                ->get();

            foreach ($waitlistEntries as $waitlistEntry) {
                $user = $waitlistEntry->user;

                if (! $user || $user->market_approved) {
                    // Clean up orphaned or already approved entries
                    $waitlistEntry->delete();

                    continue;
                }

                // Approve the user
                $user->update(['market_approved' => true]);

                // Dispatch job to create subscription if waitlist has subscription details
                if ($waitlistEntry->subscription_stripe_price_id) {
                    ProcessWaitlistSubscription::dispatch(
                        $user->id,
                        $waitlistEntry->subscription_stripe_price_id,
                        $waitlistEntry->intended_trial_days ?? 14,
                        $waitlistEntry->custom_signup_page_id
                    );
                }

                // Remove from waitlist after subscription is created
                $waitlistEntry->delete();

                // Trigger email verification for newly approved users
                if (! $user->hasVerifiedEmail()) {
                    event(new Registered($user));
                }

                // Queue welcome notification if enabled
                if ($sendNotification && $market) {
                    $user->notify(new MarketOpenedNotification($market));
                }

                $approvedUsers->push($user);
            }
        }

        return [
            'approved_count' => $approvedUsers->count(),
            'users' => $approvedUsers,
        ];
    }

    /**
     * Approve all waitlisted users for an active market's postal codes.
     *
     * @return array{approved_count: int, users: Collection<int, User>}
     */
    public function approveWaitlistedUsersForMarket(
        Market $market,
        bool $sendNotification = false
    ): array {
        if (! $market->is_active) {
            return ['approved_count' => 0, 'users' => collect()];
        }

        $postalCodes = $market->zipcodes()->pluck('postal_code')->toArray();

        return $this->approveWaitlistedUsersForPostalCodes($postalCodes, $market, $sendNotification);
    }
}
