<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CollaborationDeliverableFile extends Model
{
    /** @use HasFactory<\Database\Factories\CollaborationDeliverableFileFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'collaboration_deliverable_id',
        'file_path',
        'file_name',
        'file_type',
        'uploaded_by_user_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function deliverable(): BelongsTo
    {
        return $this->belongsTo(CollaborationDeliverable::class, 'collaboration_deliverable_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('linode')->url($this->file_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->file_type, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with($this->file_type, 'video/');
    }
}
