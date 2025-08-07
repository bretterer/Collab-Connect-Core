<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignRequirements extends Model
{
    use HasFactory;

    protected $table = 'campaign_requirements';

    protected $fillable = [
        'campaign_id',
        'social_requirements',
        'placement_requirements',
        'target_platforms',
        'deliverables',
        'success_metrics',
        'content_guidelines',
        'posting_restrictions',
        'specific_products',
        'additional_considerations',
    ];

    protected $casts = [
        'social_requirements' => 'array',
        'placement_requirements' => 'array',
        'target_platforms' => 'array',
        'deliverables' => 'array',
        'success_metrics' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
