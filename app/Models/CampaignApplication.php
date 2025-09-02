<?php

namespace App\Models;

use App\Enums\CampaignApplicationStatus;
use App\Events\CampaignApplicationCreated;
use Database\Factories\CampaignApplicationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignApplication extends Model
{
    /** @use HasFactory<CampaignApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'user_id',
        'message',
        'status',
        'submitted_at',
        'reviewed_at',
        'review_notes',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'status' => CampaignApplicationStatus::class,
    ];

    protected $dispatchesEvents = [
        'created' => CampaignApplicationCreated::class,
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function isPending(): bool
    {
        return $this->status === CampaignApplicationStatus::PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === CampaignApplicationStatus::ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === CampaignApplicationStatus::REJECTED;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): CampaignApplicationFactory
    {
        return CampaignApplicationFactory::new();
    }
}
