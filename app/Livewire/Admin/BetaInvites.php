<?php

namespace App\Livewire\Admin;

use App\Enums\AccountType;
use App\Models\Invite;
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
    public $waitlistItems = [];

    public $invites = [];

    public $invite = [
        'name' => '',
        'email' => '',
        'user_type' => '',
    ];

    public function mount()
    {
        $this->loadWaitlist();
        $this->loadInvites();
    }

    public function loadInvites()
    {
        try {
            $invites = Invite::latest()->with('referralCode')->get();
            $this->invites = $invites;
        } catch (\Exception $e) {
            $this->invites = [];
            session()->flash('error', 'Error loading invites: ' . $e->getMessage());
        }
    }

    public function sendPersonalInvite()
    {
        $this->validate([
            'invite.email' => 'required|email|unique:invites,email',
            'invite.name' => 'required|string|max:255',
            'invite.user_type' => 'required|in:business,influencer',
        ],[
            'invite.email.unique' => 'This email has already been invited.',
            'invite.email.required' => 'Email is required.',
            'invite.email.email' => 'Please enter a valid email address.',
            'invite.name.required' => 'Name is required.',
            'invite.user_type.required' => 'User type is required.',
            'invite.user_type.in' => 'User type must be either business or influencer.',
        ]);

        // Generate secure token
        $token = Str::random(64);

        // Create signed URL that expires in 7 days
        $signedUrl = URL::temporarySignedRoute(
            'register',
            now()->addDays(7),
            ['token' => $token]
        );

        // Create invite object for email
        $inviteData = (object) [
            'first_name' => explode(' ', $this->invite['name'])[0] ?? '',
            'last_name' => explode(' ', $this->invite['name'])[1] ?? '',
            'email' => $this->invite['email'],
            'account_type_interest' => $this->invite['user_type'] === 'business' ? AccountType::BUSINESS : AccountType::INFLUENCER,
        ];

        // Send email based on user type
        $emailClass = match($this->invite['user_type']) {
            'business' => \App\Mail\BetaInviteBusiness::class,
            'influencer' => \App\Mail\BetaInviteInfluencer::class,
            default => \App\Mail\BetaInviteGeneric::class,
        };

        Mail::to($this->invite['email'])->send(new $emailClass($inviteData, $signedUrl));

        // Save invite to database
        Invite::create([
            'name' => $this->invite['name'],
            'email' => $this->invite['email'],
            'user_type' => $this->invite['user_type'],
            'invited_at' => now(),
            'invite_token' => $token,
            'invited_by' => auth()->id(),
        ]);

        // Reset form
        $this->invite = [
            'name' => '',
            'email' => '',
            'user_type' => '',
        ];

        $this->loadInvites();
        session()->flash('message', "Personal invite sent to {$inviteData->email}");
    }

    public function loadWaitlist()
    {
        try {
            $waitlistEntries = Waitlist::latest()->with('referralCode')->get();

            $this->waitlistItems = $waitlistEntries->map(function ($entry) {
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
            $this->waitlistItems = [];
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

        $this->loadWaitlist();
        session()->flash('message', "Beta invite sent to {$waitlistEntry->email}");
    }

    public function resendInvite($inviteId, $type = null)
    {
        if($type === 'personal') {
            $waitlistEntry = Invite::find($inviteId);
        } else {
            $waitlistEntry = Waitlist::find($inviteId);
        }

        if (!$waitlistEntry) {
            session()->flash('error', 'Invite not found.');
            return;
        }

        // Generate new token
        $token = Str::random(64);

        // Update database with new invitation data
        $waitlistEntry->update([
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

        $this->loadWaitlist();
        session()->flash('message', "Beta invite resent to {$waitlistEntry->email}");
    }

    public function addToReferralProgram($inviteId, $type = null)
    {
        if($type === 'personal') {
            $waitlistEntry = Invite::find($inviteId);
        } else {
            $waitlistEntry = Waitlist::find($inviteId);
        }

        if (!$waitlistEntry) {
            session()->flash('error', 'Invite not found.');
            return;
        }

        $referralCode = ReferralCode::create([
            'email' => $waitlistEntry->email,
            'code' => (new ReferralCode())->generateCode(),
        ]);

        Mail::to($waitlistEntry->email)->send(new \App\Mail\ReferralProgramInvite($waitlistEntry->email, $waitlistEntry->name, $referralCode->code));

        $this->loadWaitlist();
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
