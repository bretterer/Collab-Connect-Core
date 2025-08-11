<?php

namespace App\Events;

use App\Enums\AccountType;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountTypeSelected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public AccountType $accountType;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, AccountType $accountType)
    {
        $this->user = $user;
        $this->accountType = $accountType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
