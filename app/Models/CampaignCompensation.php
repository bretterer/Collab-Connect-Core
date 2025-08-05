<?php

namespace App\Models;

use App\Enums\CompensationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignCompensation extends Model
{
    use HasFactory;

    protected $table = 'campaign_compensations';

    protected $fillable = [
        'campaign_id',
        'compensation_type',
        'compensation_amount',
        'compensation_description',
        'compensation_details',
    ];

    protected $casts = [
        'compensation_type' => CompensationType::class,
        'compensation_amount' => 'integer',
        'compensation_details' => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function getCompensationDisplayAttribute(): string
    {
        if ($this->compensation_type === CompensationType::MONETARY) {
            return '$' . number_format($this->compensation_amount);
        }

        if ($this->compensation_description) {
            return $this->compensation_description;
        }

        return $this->compensation_type->label();
    }

    public function isMonetaryCompensation(): bool
    {
        return $this->compensation_type === CompensationType::MONETARY;
    }
}