<?php

namespace App\Livewire\Auth;

use App\Enums\AccountType;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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

                if (!$this->betaInvite) {
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
        $fullPath = storage_path('app/private/waitlist.csv');

        if (!file_exists($fullPath)) {
            return null;
        }

        $csvContent = file_get_contents($fullPath);
        $lines = explode("\n", $csvContent);
        $header = str_getcsv(array_shift($lines));

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $data = str_getcsv($line);

            // Ensure data array has same length as header
            while (count($data) < count($header)) {
                $data[] = '';
            }

            if (count($data) !== count($header)) continue;

            $row = array_combine($header, $data);

            if (isset($row['Invite Token']) && !empty(trim($row['Invite Token'])) && $row['Invite Token'] === $token) {
                $nameParts = explode(' ', trim($row['Name'] ?? ''), 2);

                return [
                    'first_name' => $nameParts[0] ?? '',
                    'last_name' => $nameParts[1] ?? '',
                    'full_name' => trim($row['Name'] ?? ''),
                    'email' => trim($row['Email'] ?? ''),
                    'user_type' => strtolower(trim($row['User Type'] ?? '')),
                    'business_name' => trim($row['Business Name'] ?? ''),
                    'follower_count' => trim($row['Follower Count'] ?? ''),
                    'invited_at' => $row['Invited At'] ?? null,
                    'invite_token' => $row['Invite Token'] ?? null,
                ];
            }
        }

        return null;
    }

    private function markInviteAsRegistered(string $token): void
    {
        $fullPath = storage_path('app/private/waitlist.csv');

        if (!file_exists($fullPath)) {
            return;
        }

        $csvContent = file_get_contents($fullPath);
        $lines = explode("\n", $csvContent);
        $header = str_getcsv($lines[0]);

        // Add Registered At column if it doesn't exist
        if (!in_array('Registered At', $header)) {
            $header[] = 'Registered At';
        }

        // Find and update the row with the matching token
        foreach ($lines as $index => $line) {
            if ($index === 0) continue; // Skip header
            if (empty(trim($line))) continue;

            $data = str_getcsv($line);

            // Ensure data array has same length as header
            while (count($data) < count($header)) {
                $data[] = '';
            }

            if (count($data) !== count($header)) continue;

            $row = array_combine($header, $data);

            if (isset($row['Invite Token']) && !empty(trim($row['Invite Token'])) && $row['Invite Token'] === $token) {
                $row['Registered At'] = now()->toISOString();

                // Convert back to CSV row with proper escaping
                $escapedValues = array_map(function ($value) {
                    return '"' . str_replace('"', '""', $value) . '"';
                }, array_values($row));

                $lines[$index] = implode(',', $escapedValues);
                break;
            }
        }

        // Update header line with proper escaping
        $escapedHeader = array_map(function ($value) {
            return '"' . str_replace('"', '""', $value) . '"';
        }, $header);

        $lines[0] = implode(',', $escapedHeader);

        // Write back to file
        file_put_contents($fullPath, implode("\n", $lines));
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
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
