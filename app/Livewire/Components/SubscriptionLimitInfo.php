<?php

namespace App\Livewire\Components;

use App\Facades\SubscriptionLimits;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class SubscriptionLimitInfo extends Component
{
    /**
     * The key for the subscription limit to display.
     */
    public string $limitKey;

    /**
     * The action text describing what will use the credit.
     * e.g., "Publishing this campaign", "Submitting this application"
     */
    public string $actionText = 'This action';

    /**
     * The name of what the credit is for.
     * e.g., "campaign publish credit", "application credit"
     */
    public string $creditName = 'credit';

    /**
     * Optional variant for visual styling: 'default', 'warning', 'info'
     */
    public string $variant = 'default';

    /**
     * Whether this is for a scheduled action (shows different messaging).
     */
    public bool $isScheduled = false;

    /**
     * The billable model (Business or Influencer).
     * Stored without type hint to avoid Livewire container resolution issues.
     */
    public $billable = null;

    public function mount(
        string $limitKey,
        string $actionText = 'This action',
        string $creditName = 'credit',
        string $variant = 'default',
        bool $isScheduled = false,
        $billable = null
    ): void {
        $this->limitKey = $limitKey;
        $this->actionText = $actionText;
        $this->creditName = $creditName;
        $this->variant = $variant;
        $this->isScheduled = $isScheduled;
        $this->billable = $billable;
    }

    public function getLimitInfoProperty(): array
    {
        $billable = $this->resolveBillable();

        if (! $billable) {
            return [
                'remaining' => 0,
                'limit' => 0,
                'is_unlimited' => false,
                'reset_date' => null,
                'formatted_reset_date' => null,
            ];
        }

        return SubscriptionLimits::getLimitInfo($billable, $this->limitKey);
    }

    protected function resolveBillable(): ?Model
    {
        if ($this->billable) {
            return $this->billable;
        }

        $user = auth()->user();

        if (! $user) {
            return null;
        }

        // Resolve based on account type
        if ($user->isInfluencerAccount()) {
            return $user->influencer;
        }

        if ($user->isBusinessAccount()) {
            return $user->currentBusiness;
        }

        return null;
    }

    public function render()
    {
        return view('livewire.components.subscription-limit-info');
    }
}
