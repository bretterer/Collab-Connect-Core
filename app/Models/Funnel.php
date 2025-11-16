<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funnel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'pages',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'pages' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function emailSequences(): HasMany
    {
        return $this->hasMany(EmailSequence::class);
    }

    public function landingPages()
    {
        if (empty($this->pages)) {
            return collect();
        }

        return LandingPage::whereIn('id', $this->pages)
            ->get()
            ->sortBy(function ($page) {
                return array_search($page->id, $this->pages);
            });
    }
}
