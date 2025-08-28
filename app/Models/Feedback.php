<?php

namespace App\Models;

use App\Enums\FeedbackType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'message',
        'url',
        'browser_info',
        'screenshot_path',
        'session_data',
        'github_issue_url',
        'github_issue_number',
        'resolved',
        'admin_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => FeedbackType::class,
            'browser_info' => 'array',
            'session_data' => 'array',
            'resolved' => 'boolean',
            'resolved_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsResolved(?string $adminNotes = null): void
    {
        $this->update([
            'resolved' => true,
            'resolved_at' => now(),
            'admin_notes' => $adminNotes,
        ]);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('resolved', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('resolved', true);
    }

    public function scopeByType($query, FeedbackType $type)
    {
        return $query->where('type', $type);
    }
}
