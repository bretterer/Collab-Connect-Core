<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkInBioView extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_in_bio_settings_id',
        'ip_hash',
        'user_agent',
        'device_type',
        'referrer',
        'referrer_domain',
        'is_unique',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_unique' => 'boolean',
            'viewed_at' => 'datetime',
        ];
    }

    public function linkInBioSettings(): BelongsTo
    {
        return $this->belongsTo(LinkInBioSettings::class);
    }

    /**
     * Scope to filter views within a date range.
     */
    public function scopeInDateRange(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('viewed_at', [$start, $end]);
    }

    /**
     * Scope to filter views for a specific settings ID.
     */
    public function scopeForSettings(Builder $query, int $settingsId): Builder
    {
        return $query->where('link_in_bio_settings_id', $settingsId);
    }

    /**
     * Scope to filter only unique views.
     */
    public function scopeUniqueOnly(Builder $query): Builder
    {
        return $query->where('is_unique', true);
    }
}
