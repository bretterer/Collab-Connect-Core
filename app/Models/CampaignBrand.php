<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignBrand extends Model
{
    use HasFactory;

    protected $table = 'campaign_brands';

    protected $fillable = [
        'campaign_id',
        'brand_overview',
        'brand_essence',
        'brand_pillars',
        'current_advertising_campaign',
        'brand_story',
        'brand_guidelines',
    ];

    protected $casts = [
        'brand_pillars' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
