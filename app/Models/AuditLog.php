<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log an admin action.
     *
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     * @param  array<string, mixed>|null  $metadata
     */
    public static function log(
        string $action,
        ?Model $auditable = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'admin_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get a human-readable description of the action.
     */
    public function getActionDescription(): string
    {
        $descriptions = [
            'credit.grant' => 'granted promotion credits',
            'credit.revoke' => 'revoked promotion credits',
            'subscription.start_trial' => 'started a trial subscription',
            'subscription.cancel' => 'canceled subscription',
            'subscription.cancel_trial' => 'canceled trial',
            'subscription.extend_trial' => 'extended trial',
            'subscription.swap_plan' => 'changed subscription plan',
            'subscription.resume' => 'resumed subscription',
            'coupon.apply' => 'applied a coupon',
            'user.update' => 'updated user account',
            'user.toggle_admin' => 'toggled admin access',
        ];

        return $descriptions[$this->action] ?? $this->action;
    }

    /**
     * Get the target name for display.
     */
    public function getTargetName(): ?string
    {
        if (! $this->auditable) {
            return null;
        }

        if ($this->auditable instanceof User) {
            return $this->auditable->name ?? $this->auditable->email;
        }

        if ($this->auditable instanceof Business) {
            return $this->auditable->name ?? 'Business #'.$this->auditable->id;
        }

        if ($this->auditable instanceof Influencer) {
            return $this->auditable->user?->name ?? 'Influencer #'.$this->auditable->id;
        }

        return 'Record #'.$this->auditable_id;
    }
}
