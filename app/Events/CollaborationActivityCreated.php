<?php

namespace App\Events;

use App\Models\CollaborationActivity;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CollaborationActivityCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CollaborationActivity $activity;

    public function __construct(CollaborationActivity $activity)
    {
        $this->activity = $activity->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('collaboration.'.$this->activity->collaboration_id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'activity' => [
                'id' => $this->activity->id,
                'type' => $this->activity->type->value,
                'type_label' => $this->activity->type->label(),
                'description' => $this->activity->description,
                'icon' => $this->activity->icon,
                'color' => $this->activity->color,
                'user_id' => $this->activity->user_id,
                'user_name' => $this->activity->user?->name,
                'metadata' => $this->activity->metadata,
                'created_at' => $this->activity->created_at->toISOString(),
            ],
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'activity.created';
    }
}
