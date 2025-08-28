<?php

namespace App\Jobs;

use App\Mail\InviteMemberToBusinessMail;
use App\Models\Business;
use App\Models\BusinessMemberInvite;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class InviteMemberToBusiness implements ShouldQueue
{
    use Queueable;

    public Business $business;
    public string $email;
    public User $invitedBy;

    /**
     * Create a new job instance.
     */
    public function __construct(Business $business, string $email, User $invitedBy)
    {
        $this->business = $business;
        $this->email = $email;
        $this->invitedBy = $invitedBy;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $invite = BusinessMemberInvite::create([
            'invited_by' => $this->invitedBy->id,
            'business_id' => $this->business->id,
            'email' => strtolower($this->email),
            'role' => 'member',
            'token' => bin2hex(random_bytes(16)),
            'invited_at' => now(),
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'accept-business-invite',
            now()->addDays(7),
            ['token' => $invite->token]
        );

        Mail::to($this->email)->send(new InviteMemberToBusinessMail($invite, $signedUrl));
    }
}
