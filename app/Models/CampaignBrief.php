<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignBrief extends Model
{
    use HasFactory;

    protected $table = 'campaign_briefs';

    protected $fillable = [
        'campaign_id',
        'project_name',
        'main_contact',
        'campaign_objective',
        'key_insights',
        'fan_motivator',
        'creative_connection',
        'target_audience',
        'timing_details',
        'additional_requirements',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}