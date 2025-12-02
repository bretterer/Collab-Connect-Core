<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'collaboration_id',
        'reviewer_id',
        'reviewee_id',
        'reviewer_type',
        'rating',
        'comment',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'submitted_at' => 'datetime',
        ];
    }

    public function collaboration(): BelongsTo
    {
        return $this->belongsTo(Collaboration::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function isFromBusiness(): bool
    {
        return $this->reviewer_type === 'business';
    }

    public function isFromInfluencer(): bool
    {
        return $this->reviewer_type === 'influencer';
    }

    public function isSubmitted(): bool
    {
        return $this->submitted_at !== null;
    }

    public function scopeFromBusiness($query)
    {
        return $query->where('reviewer_type', 'business');
    }

    public function scopeFromInfluencer($query)
    {
        return $query->where('reviewer_type', 'influencer');
    }

    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    public function scopeForReviewee($query, int $userId)
    {
        return $query->where('reviewee_id', $userId);
    }

    public function scopeForReviewer($query, int $userId)
    {
        return $query->where('reviewer_id', $userId);
    }
}
