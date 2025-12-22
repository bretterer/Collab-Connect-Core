<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkInBioClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'link_in_bio_settings_id',
        'link_index',
        'link_title',
        'link_url',
        'ip_hash',
        'user_agent',
        'device_type',
        'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
        ];
    }

    public function linkInBioSettings(): BelongsTo
    {
        return $this->belongsTo(LinkInBioSettings::class);
    }

    /**
     * Scope to filter clicks within a date range.
     */
    public function scopeInDateRange(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereBetween('clicked_at', [$start, $end]);
    }

    /**
     * Scope to filter clicks for a specific settings ID.
     */
    public function scopeForSettings(Builder $query, int $settingsId): Builder
    {
        return $query->where('link_in_bio_settings_id', $settingsId);
    }

    /**
     * Scope to filter clicks for a specific link.
     */
    public function scopeForLink(Builder $query, int $linkIndex): Builder
    {
        return $query->where('link_index', $linkIndex);
    }
}
