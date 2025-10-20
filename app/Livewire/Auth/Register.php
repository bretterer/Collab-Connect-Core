<?php

namespace App\Livewire\Auth;

use App\Enums\AccountType;
use App\Models\BusinessMemberInvite;
use App\Models\Invite;
use App\Models\User;
use App\Models\Waitlist;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

#[Layout('layouts.auth')]
class Register extends Component
{
    use UsesSpamProtection;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public AccountType $accountType = AccountType::INFLUENCER;

    public HoneypotData $extraFields;

    public ?string $token = null;

    public ?array $betaInvite = null;

    public Invite|Waitlist|null $waitlistEntry = null;

    public function mount(?string $token = null)
    {
        $this->extraFields = new HoneypotData;

        if (config('collabconnect.beta_registration_only')) {
            $this->token = request()->query('token', $token);

            // If we have a beta invitation token, load the invitation from CSV
            if ($this->token) {
                $this->betaInvite = $this->findInviteByToken($this->token);

                if (! $this->betaInvite) {
                    // Handle invalid or expired token
                    session()->flash('error', 'Invalid or expired invitation token.');
                    $this->redirect('/', navigate: true);

                    return;
                }

                // Pre-fill form with invitation data
                $this->email = $this->betaInvite['email'];
                $this->name = $this->betaInvite['full_name'] ?? $this->betaInvite['name'];
                $this->accountType = $this->betaInvite['user_type'] === 'business' ? AccountType::BUSINESS : AccountType::INFLUENCER;

                if($this->betaInvite['business_invite']) {
                    $this->accountType = AccountType::BUSINESS;
                }
            }
        }
    }

    private function findInviteByToken(string $token): ?array
    {
        $waitlistEntry = Waitlist::where('invite_token', $token)
            ->whereNotNull('invite_token')
            ->where('invite_token', '!=', '')
            ->first();

        if (! $waitlistEntry) {
            $waitlistEntry = Invite::where('invite_token', $token)
                ->whereNotNull('invite_token')
                ->where('invite_token', '!=', '')
                ->first();
        }

        if (! $waitlistEntry) {
            $waitlistEntry = BusinessMemberInvite::where('token', $token)
                ->first();

            return [
                'full_name' => '',
                'email' => $waitlistEntry->email,
                'user_type' => 'business',
                'business_invite' => true,
                'business_id' => $waitlistEntry->business_id,
            ];
        }

        if (! $waitlistEntry) {
            return null;
        }

        $this->waitlistEntry = $waitlistEntry;

        $nameParts = explode(' ', trim($waitlistEntry->name), 2);

        return [
            'first_name' => $nameParts[0] ?? '',
            'last_name' => $nameParts[1] ?? '',
            'full_name' => $waitlistEntry->name,
            'email' => $waitlistEntry->email,
            'user_type' => $waitlistEntry->user_type,
            'business_name' => $waitlistEntry->business_name,
            'follower_count' => $waitlistEntry->follower_count,
            'invited_at' => $waitlistEntry->invited_at,
            'invite_token' => $waitlistEntry->invite_token,
            'business_invite' => false,
        ];
    }

    private function markInviteAsRegistered(string $token): void
    {
        if ($this->waitlistEntry === null) {
            return;
        }

        $this->waitlistEntry->update([
                'registered_at' => now(),
        ]);

    }

    public function setAccountType(AccountType $accountType): void
    {
        $this->accountType = $accountType;
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $this->protectAgainstSpam();

        $this->email = strtolower($this->email);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'accountType' => ['required', Rule::enum(AccountType::class)],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['account_type'] = $validated['accountType'];

        // Remove accountType from validated data as it's not a user field
        unset($validated['accountType']);

        event(new Registered(($user = User::create($validated))));

        // If this is a beta registration, mark the invite as used in CSV
        if ($this->betaInvite && $this->token) {
            $this->markInviteAsRegistered($this->token);
        }

        if ($this->betaInvite && $this->betaInvite['business_invite'] === true) {
            // If this is a business invite, automatically accept the invite
            $businessInvite = BusinessMemberInvite::where('email', $this->betaInvite['email'])
                ->where('business_id', $this->betaInvite['business_id'])
                ->first();

            if ($businessInvite) {
                $businessInvite->update(['joined_at' => now()]);
                $user->businesses()->attach($businessInvite->business_id, ['role' => $businessInvite->role]);
                $user->setCurrentBusiness($businessInvite->business);
            }

        }

        Auth::login($user);

        $this->redirect(route('verification.notice', absolute: false), navigate: true);
    }
}
