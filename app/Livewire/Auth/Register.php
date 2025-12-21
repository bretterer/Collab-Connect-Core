<?php

namespace App\Livewire\Auth;

use App\Enums\AccountType;
use App\Events\UserRegisteredWithReferral;
use App\Models\BusinessMemberInvite;
use App\Models\Invite;
use App\Models\MarketWaitlist;
use App\Models\MarketZipcode;
use App\Models\PostalCode;
use App\Models\ReferralEnrollment;
use App\Models\User;
use App\Models\Waitlist;
use App\Settings\RegistrationMarkets;
use Combindma\FacebookPixel\Facades\MetaPixel;
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

    public string $postal_code = '';

    public AccountType $accountType = AccountType::INFLUENCER;

    public HoneypotData $extraFields;

    public ?string $token = null;

    public ?array $betaInvite = null;

    public ?ReferralEnrollment $referralEnrollment = null;

    public Invite|Waitlist|null $waitlistEntry = null;

    public bool $registrationMarketsEnabled = true;

    public function mount(?string $token = null)
    {
        $this->extraFields = new HoneypotData;
        $settings = app(RegistrationMarkets::class);
        $this->registrationMarketsEnabled = $settings->enabled;

        $referralCode = request()->cookie('referral_code', null);
        if ($referralCode) {
            $this->referralEnrollment = ReferralEnrollment::where('code', $referralCode)->first();
        }

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

                if ($this->betaInvite['business_invite']) {
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

    private function markInviteAsRegistered(): void
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
        $this->postal_code = trim($this->postal_code);

        // Build validation rules conditionally based on market_enabled setting
        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'accountType' => ['required', Rule::enum(AccountType::class)],
        ];

        // Only validate postal code if markets are enabled
        if ($this->registrationMarketsEnabled) {
            $validationRules['postal_code'] = ['required', 'string', 'max:10', function ($attribute, $value, $fail) {
                // Validate that the postal code exists in our postal_codes table
                $postalCode = PostalCode::where('postal_code', $value)
                    ->where('country_code', 'US')
                    ->first();

                if (! $postalCode) {
                    $fail('The postal code you entered is not valid.');
                }
            }];
        }

        $validated = $this->validate($validationRules);

        $validated['password'] = Hash::make($validated['password']);
        $validated['account_type'] = $validated['accountType'];

        // Check if postal code is in an active market (only if markets are enabled)
        $isMarketApproved = true; // Default to approved if markets are disabled
        if ($this->registrationMarketsEnabled && ! empty($this->postal_code)) {
            $isMarketApproved = MarketZipcode::isInActiveMarket($this->postal_code);
            $validated['market_approved'] = $isMarketApproved;
        } else {
            // If markets are disabled, always approve
            $validated['market_approved'] = true;
            // If markets are disabled but postal code was provided, add it to validated data
            if (! empty($this->postal_code)) {
                $validated['postal_code'] = $this->postal_code;
            }
        }

        // Remove accountType from validated data as it's not a user field
        unset($validated['accountType']);

        $user = User::create($validated);

        // Only fire Registered event (which sends verification email) for approved users
        if ($isMarketApproved) {
            event(new Registered($user));
        }

        // If market is not approved, add user to waitlist (only if markets are enabled)
        if ($this->registrationMarketsEnabled && ! $isMarketApproved) {
            MarketWaitlist::create([
                'user_id' => $user->id,
                'postal_code' => $this->postal_code,
            ]);
        }

        if ($this->referralEnrollment) {
            event(new UserRegisteredWithReferral($user, $this->referralEnrollment));
            cookie()->queue('referral_code', null, -1); // Clear referral code cookie
        }

        // If this is a beta registration, mark the invite as used in CSV
        if ($this->betaInvite && $this->token) {
            $this->markInviteAsRegistered();
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

        // Redirect to market waitlist page if not approved (and markets enabled), otherwise to email verification
        if ($this->registrationMarketsEnabled && ! $isMarketApproved) {
            MetaPixel::flashEvent('CompleteRegistration', ['user_id' => $user->id, 'market_waitlist' => true]);
            $this->redirect(route('market-waitlist'), navigate: true);
        } else {
            MetaPixel::flashEvent('CompleteRegistration', ['user_id' => $user->id]);
            $this->redirect(route('verification.notice', absolute: false), navigate: true);
        }
    }
}
