<?php

namespace App\Livewire\Admin\Users;

use App\Enums\AccountType;
use App\Models\ReferralCode;
use App\Models\Waitlist;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.app')]
class BetaInvites extends Component
{
    public $invites = [];

    public function mount()
    {
        $this->loadInvites();
    }

    public function render()
    {
        return view('livewire.admin.users.beta-invites');
    }

    public function loadInvites()
    {
        try {
            $waitlistEntries = Waitlist::latest()->with('referralCode')->get();

            $this->invites = $waitlistEntries->map(function ($entry) {
                // Parse the name field
                $nameParts = explode(' ', trim($entry->name), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';

                return [
                    'id' => $entry->id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $entry->name,
                    'email' => $entry->email,
                    'user_type' => $entry->user_type,
                    'business_name' => $entry->business_name,
                    'follower_count' => $entry->follower_count,
                    'invited_at' => $entry->invited_at,
                    'registered_at' => null, // Will be handled by user registration
                    'invite_token' => $entry->invite_token,
                    'referralCode' => $entry->referralCode ? $entry->referralCode->code : null,
                ];
            })->toArray();
        } catch (\Exception $e) {
            $this->invites = [];
            session()->flash('error', 'Error loading waitlist: ' . $e->getMessage());
        }
    }

    public function sendInvite($inviteId)
    {
        $waitlistEntry = Waitlist::find($inviteId);

        if (!$waitlistEntry || $waitlistEntry->invited_at) {
            session()->flash('error', 'Invite not found or already sent.');
            return;
        }

        // Generate secure token
        $token = Str::random(64);

        // Update database with invitation data
        $waitlistEntry->update([
            'invited_at' => now(),
            'invite_token' => $token,
        ]);

        // Create signed URL that expires in 7 days
        $signedUrl = URL::temporarySignedRoute(
            'register',
            now()->addDays(7),
            ['token' => $token]
        );

        // Create invite object for email
        $inviteData = (object) [
            'first_name' => explode(' ', $waitlistEntry->name)[0] ?? '',
            'last_name' => explode(' ', $waitlistEntry->name)[1] ?? '',
            'email' => $waitlistEntry->email,
            'account_type_interest' => $waitlistEntry->user_type === 'business' ? AccountType::BUSINESS : AccountType::INFLUENCER,
        ];

        // Send email based on user type
        $emailClass = match($waitlistEntry->user_type) {
            'business' => \App\Mail\BetaInviteBusiness::class,
            'influencer' => \App\Mail\BetaInviteInfluencer::class,
            default => \App\Mail\BetaInviteGeneric::class,
        };

        Mail::to($waitlistEntry->email)->send(new $emailClass($inviteData, $signedUrl));

        $this->loadInvites();
        session()->flash('message', "Beta invite sent to {$waitlistEntry->email}");
    }

    public function resendInvite($inviteId)
    {
        $waitlistEntry = Waitlist::find($inviteId);

        if (!$waitlistEntry) {
            session()->flash('error', 'Invite not found.');
            return;
        }

        // Generate new token
        $token = Str::random(64);

        // Update database with new invitation data
        $waitlistEntry->update([
            'invited_at' => now(),
            'invite_token' => $token,
        ]);

        // Create signed URL
        $signedUrl = URL::temporarySignedRoute(
            'register',
            now()->addDays(7),
            ['token' => $token]
        );

        // Create invite object for email
        $inviteData = (object) [
            'first_name' => explode(' ', $waitlistEntry->name)[0] ?? '',
            'last_name' => explode(' ', $waitlistEntry->name)[1] ?? '',
            'email' => $waitlistEntry->email,
            'account_type_interest' => $waitlistEntry->user_type === 'business' ? AccountType::BUSINESS : AccountType::INFLUENCER,
        ];

        // Send email
        $emailClass = match($waitlistEntry->user_type) {
            'business' => \App\Mail\BetaInviteBusiness::class,
            'influencer' => \App\Mail\BetaInviteInfluencer::class,
            default => \App\Mail\BetaInviteGeneric::class,
        };

        Mail::to($waitlistEntry->email)->send(new $emailClass($inviteData, $signedUrl));

        $this->loadInvites();
        session()->flash('message', "Beta invite resent to {$waitlistEntry->email}");
    }

    public function addToReferralProgram($inviteId)
    {
        $waitlistEntry = Waitlist::find($inviteId);

        if (!$waitlistEntry) {
            session()->flash('error', 'Invite not found.');
            return;
        }

        $referralCode = ReferralCode::create([
            'email' => $waitlistEntry->email,
            'code' => (new ReferralCode())->generateCode(),
        ]);

        Mail::to($waitlistEntry->email)->send(new \App\Mail\ReferralProgramInvite($waitlistEntry->email, $waitlistEntry->name, $referralCode));

        $this->loadInvites();
        Toaster::success("Referral program added for {$waitlistEntry->email}");
    }

    public function resendReferralCode($inviteId)
    {
        $waitlistEntry = Waitlist::find($inviteId);

        if (!$waitlistEntry || !$waitlistEntry->referralCode) {
            session()->flash('error', 'Referral code not found.');
            return;
        }

        Mail::to($waitlistEntry->email)->send(new \App\Mail\ReferralProgramInvite($waitlistEntry->email, $waitlistEntry->name, $waitlistEntry->referralCode->code));

        session()->flash('message', "Referral code resent to {$waitlistEntry->email}");
    }

}
