<?php

namespace App\Livewire\Admin\Users;

use App\Enums\AccountType;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BetaInvites extends Component
{
    public $invites = [];
    private $csvPath = 'private/waitlist.csv';

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
        $fullPath = storage_path('app/' . $this->csvPath);

        if (!file_exists($fullPath)) {
            $this->invites = [];
            session()->flash('error', "Waitlist CSV file not found at: {$fullPath}");
            return;
        }

        try {
            $csvContent = file_get_contents($fullPath);
            $lines = explode("\n", $csvContent);

            if (empty($lines)) {
                $this->invites = [];
                return;
            }

            // Remove any empty lines at the end
            $lines = array_filter($lines, function($line) {
                return !empty(trim($line));
            });

            $header = str_getcsv(array_shift($lines));

            $this->invites = [];
            foreach ($lines as $index => $line) {
                $data = str_getcsv($line);

                // Pad with empty strings if needed
                while (count($data) < count($header)) {
                    $data[] = '';
                }

                // Trim to header length if too long
                if (count($data) > count($header)) {
                    $data = array_slice($data, 0, count($header));
                }

                $row = array_combine($header, $data);

                // Skip if no email (main identifier)
                if (empty(trim($row['Email'] ?? ''))) {
                    continue;
                }

                // Parse the name field
                $nameParts = explode(' ', trim($row['Name'] ?? ''), 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';

                $this->invites[] = [
                    'id' => $index,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => trim($row['Name'] ?? ''),
                    'email' => trim($row['Email'] ?? ''),
                    'user_type' => strtolower(trim($row['User Type'] ?? '')),
                    'business_name' => trim($row['Business Name'] ?? ''),
                    'follower_count' => trim($row['Follower Count'] ?? ''),
                    'invited_at' => !empty(trim($row['Invited At'] ?? '')) ? trim($row['Invited At']) : null,
                    'registered_at' => !empty(trim($row['Registered At'] ?? '')) ? trim($row['Registered At']) : null,
                    'invite_token' => !empty(trim($row['Invite Token'] ?? '')) ? trim($row['Invite Token']) : null,
                ];
            }
        } catch (\Exception $e) {
            $this->invites = [];
            session()->flash('error', 'Error reading CSV: ' . $e->getMessage());
        }
    }

    public function sendInvite($inviteIndex)
    {
        $invite = $this->invites[$inviteIndex] ?? null;

        if (!$invite || !empty($invite['invited_at'])) {
            session()->flash('error', 'Invite not found or already sent.');
            return;
        }

        // Generate secure token
        $token = Str::random(64);
        $invitedAt = now()->toISOString();

        // Update CSV with invitation data
        $this->updateCsvRow($inviteIndex, [
            'Invited At' => $invitedAt,
            'Invite Token' => $token,
        ]);

        // Create signed URL that expires in 7 days
        $signedUrl = URL::temporarySignedRoute(
            'register',
            now()->addDays(7),
            ['token' => $token]
        );

        // Create invite object for email
        $inviteData = (object) [
            'first_name' => $invite['first_name'],
            'last_name' => $invite['last_name'],
            'email' => $invite['email'],
            'account_type_interest' => $invite['user_type'] === 'business' ? AccountType::BUSINESS : AccountType::INFLUENCER,
        ];

        // Send email based on user type
        $emailClass = match($invite['user_type']) {
            'business' => \App\Mail\BetaInviteBusiness::class,
            'influencer' => \App\Mail\BetaInviteInfluencer::class,
            default => \App\Mail\BetaInviteGeneric::class,
        };

        Mail::to($invite['email'])->send(new $emailClass($inviteData, $signedUrl));

        $this->loadInvites();
        session()->flash('message', "Beta invite sent to {$invite['email']}");
    }

    public function resendInvite($inviteIndex)
    {
        $invite = $this->invites[$inviteIndex] ?? null;

        if (!$invite) {
            session()->flash('error', 'Invite not found.');
            return;
        }

        // Generate new token
        $token = Str::random(64);
        $invitedAt = now()->toISOString();

        // Update CSV with new invitation data
        $this->updateCsvRow($inviteIndex, [
            'Invited At' => $invitedAt,
            'Invite Token' => $token,
        ]);

        // Create signed URL
        $signedUrl = URL::temporarySignedRoute(
            'register',
            now()->addDays(7),
            ['token' => $token]
        );

        // Create invite object for email
        $inviteData = (object) [
            'first_name' => $invite['first_name'],
            'last_name' => $invite['last_name'],
            'email' => $invite['email'],
            'account_type_interest' => $invite['user_type'] === 'business' ? AccountType::BUSINESS : AccountType::INFLUENCER,
        ];

        // Send email
        $emailClass = match($invite['user_type']) {
            'business' => \App\Mail\BetaInviteBusiness::class,
            'influencer' => \App\Mail\BetaInviteInfluencer::class,
            default => \App\Mail\BetaInviteGeneric::class,
        };

        Mail::to($invite['email'])->send(new $emailClass($inviteData, $signedUrl));

        $this->loadInvites();
        session()->flash('message', "Beta invite resent to {$invite['email']}");
    }

    private function updateCsvRow($rowIndex, $updates)
    {
        $fullPath = storage_path('app/' . $this->csvPath);

        if (!file_exists($fullPath)) {
            return;
        }

        $csvContent = file_get_contents($fullPath);
        $lines = explode("\n", $csvContent);
        $header = str_getcsv($lines[0]);

        // Add new columns to header if they don't exist
        foreach (array_keys($updates) as $newColumn) {
            if (!in_array($newColumn, $header)) {
                $header[] = $newColumn;
            }
        }

        // Update the specific row
        if (isset($lines[$rowIndex + 1])) {
            $data = str_getcsv($lines[$rowIndex + 1]);

            // Ensure data array has same length as header
            while (count($data) < count($header)) {
                $data[] = '';
            }

            $row = array_combine($header, $data);

            // Apply updates
            foreach ($updates as $column => $value) {
                $row[$column] = $value;
            }

            // Convert back to CSV row with proper escaping
            $escapedValues = array_map(function($value) {
                return '"' . str_replace('"', '""', $value) . '"';
            }, array_values($row));

            $lines[$rowIndex + 1] = implode(',', $escapedValues);
        }

        // Update header line with proper escaping
        $escapedHeader = array_map(function($value) {
            return '"' . str_replace('"', '""', $value) . '"';
        }, $header);

        $lines[0] = implode(',', $escapedHeader);

        // Write back to file
        file_put_contents($fullPath, implode("\n", $lines));
    }
}
