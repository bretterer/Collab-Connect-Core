<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
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

    public string $cf_turnstile_response = '';

    public HoneypotData $extraFields;

    public function mount()
    {
        $this->extraFields = new HoneypotData;
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
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Remove cf_turnstile_response from validated data as it's not a user field
        unset($validated['cf_turnstile_response']);

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirect(route('onboarding.account-type', absolute: false), navigate: true);
    }
}
