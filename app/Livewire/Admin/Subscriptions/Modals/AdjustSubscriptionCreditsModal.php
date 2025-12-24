<?php

namespace App\Livewire\Admin\Subscriptions\Modals;

use App\Facades\SubscriptionLimits;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Influencer;
use App\Subscription\SubscriptionMetadataSchema;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class AdjustSubscriptionCreditsModal extends Component
{
    public ?string $billableType = null;

    public ?int $billableId = null;

    public string $creditKey = '';

    public int $newValue = 0;

    public string $reason = '';

    public bool $isProcessing = false;

    #[On('open-adjust-subscription-credits-modal')]
    public function open(string $billableType, int $billableId, string $creditKey): void
    {
        $this->billableType = $billableType;
        $this->billableId = $billableId;
        $this->creditKey = $creditKey;
        $this->reason = '';

        // Get current value
        $this->newValue = $this->currentCredits;

        Flux::modal('adjust-subscription-credits-modal')->show();
    }

    #[Computed]
    public function billable(): Business|Influencer|null
    {
        if (! $this->billableType || ! $this->billableId) {
            return null;
        }

        return match ($this->billableType) {
            'business' => Business::find($this->billableId),
            'influencer' => Influencer::find($this->billableId),
            default => null,
        };
    }

    #[Computed]
    public function currentCredits(): int
    {
        $billable = $this->billable;

        if (! $billable) {
            return 0;
        }

        return SubscriptionLimits::getRemainingCredits($billable, $this->creditKey);
    }

    #[Computed]
    public function creditLabel(): string
    {
        $labels = SubscriptionMetadataSchema::getLabels();

        return $labels[$this->creditKey] ?? $this->creditKey;
    }

    #[Computed]
    public function planLimit(): int
    {
        $billable = $this->billable;

        if (! $billable) {
            return 0;
        }

        return SubscriptionLimits::getLimit($billable, $this->creditKey);
    }

    #[Computed]
    public function isUnlimited(): bool
    {
        return $this->planLimit === SubscriptionMetadataSchema::UNLIMITED;
    }

    public function adjustCredits(): void
    {
        $this->validate([
            'newValue' => 'required|integer|min:0|max:1000',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->isProcessing = true;

            $billable = $this->billable;

            if (! $billable) {
                throw new \Exception('No billable profile found.');
            }

            $oldCredits = $this->currentCredits;

            SubscriptionLimits::setCredit($billable, $this->creditKey, $this->newValue);

            AuditLog::log(
                action: 'subscription_credit.adjust',
                auditable: $billable,
                oldValues: [$this->creditKey => $oldCredits],
                newValues: [$this->creditKey => $this->newValue],
                metadata: [
                    'credit_key' => $this->creditKey,
                    'credit_label' => $this->creditLabel,
                    'reason' => $this->reason,
                    'adjusted_by' => auth()->user()?->name,
                ]
            );

            $change = $this->newValue - $oldCredits;
            $changeText = $change > 0 ? "+{$change}" : $change;

            $this->reason = '';
            Flux::modal('adjust-subscription-credits-modal')->close();
            Toaster::success("Credits adjusted ({$changeText}) for {$this->creditLabel}.");

            $this->dispatch('subscription-credits-updated');

            $this->isProcessing = false;
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to adjust credits: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.subscriptions.modals.adjust-subscription-credits-modal');
    }
}
