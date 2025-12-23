<?php

namespace App\Events;

use App\Models\CollaborationDeliverable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliverableStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public CollaborationDeliverable $deliverable;

    public function __construct(CollaborationDeliverable $deliverable)
    {
        $this->deliverable = $deliverable->load('collaboration');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('collaboration.'.$this->deliverable->collaboration_id),
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
            'deliverable' => [
                'id' => $this->deliverable->id,
                'deliverable_type' => $this->deliverable->deliverable_type->value,
                'deliverable_label' => $this->deliverable->deliverable_type->label(),
                'status' => $this->deliverable->status->value,
                'status_label' => $this->deliverable->status->label(),
                'status_color' => $this->deliverable->status->color(),
                'post_url' => $this->deliverable->post_url,
                'submitted_at' => $this->deliverable->submitted_at?->toISOString(),
                'approved_at' => $this->deliverable->approved_at?->toISOString(),
                'updated_at' => $this->deliverable->updated_at->toISOString(),
            ],
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'deliverable.status.changed';
    }
}
