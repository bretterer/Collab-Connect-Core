<?php

namespace App\Livewire\Auth;

use App\Enums\AccountType;
use App\Models\User;
use App\Models\Waitlist;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;
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

    public string $cf_turnstile_response = '';

    public HoneypotData $extraFields;

    public ?string $token = null;

    public ?array $betaInvite = null;

    public function mount(?string $token = null)
    {
        $this->extraFields = new HoneypotData;

        if (config('collabconnect.beta_invite_registration')) {
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
                $this->name = $this->betaInvite['full_name'];
                $this->accountType = $this->betaInvite['user_type'] === 'business' ? AccountType::BUSINESS : AccountType::INFLUENCER;
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
            return null;
        }

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
        ];
    }

    private function markInviteAsRegistered(string $token): void
    {
        $waitlistEntry = Waitlist::where('invite_token', $token)
            ->whereNotNull('invite_token')
            ->where('invite_token', '!=', '')
            ->first();

        if ($waitlistEntry) {
            $waitlistEntry->update([
                'registered_at' => now(),
            ]);
        }
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

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'cf_turnstile_response' => ['required', app(Turnstile::class)],
            'accountType' => ['required', Rule::enum(AccountType::class)],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['account_type'] = $validated['accountType'];

        // Remove cf_turnstile_response from validated data as it's not a user field
        unset($validated['cf_turnstile_response']);
        // Remove accountType from validated data as it's not a user field
        unset($validated['accountType']);

        event(new Registered(($user = User::create($validated))));

        // If this is a beta registration, mark the invite as used in CSV
        if ($this->betaInvite && $this->token) {
            $this->markInviteAsRegistered($this->token);
        }

        Auth::login($user);

        $this->redirect(route('verification.notice', absolute: false), navigate: true);
    }
}
