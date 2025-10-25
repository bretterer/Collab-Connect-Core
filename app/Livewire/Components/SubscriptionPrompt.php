<?php

namespace App\Livewire\Components;

use Livewire\Component;

class SubscriptionPrompt extends Component
{
    public string $variant = 'blue'; // blue, purple, green

    public string $heading = 'Subscribe to Unlock';

    public string $description = 'Subscribe to access all features and start building meaningful partnerships.';

    public array $features = [];

    public string $buttonText = 'View Subscription Plans';

    public function mount(
        ?string $variant = null,
        ?string $heading = null,
        ?string $description = null,
        ?array $features = null,
        ?string $buttonText = null
    ) {
        if ($variant) {
            $this->variant = $variant;
        }
        if ($heading) {
            $this->heading = $heading;
        }
        if ($description) {
            $this->description = $description;
        }
        if ($features) {
            $this->features = $features;
        }
        if ($buttonText) {
            $this->buttonText = $buttonText;
        }

        // Set default features if none provided
        if (empty($this->features)) {
            $this->features = $this->getDefaultFeatures();
        }
    }

    private function getDefaultFeatures(): array
    {
        return [
            'Access all features',
            'Unlimited usage',
            'Priority support',
            'Advanced analytics',
        ];
    }

    public function getGradientClasses(): array
    {
        return match ($this->variant) {
            'purple' => [
                'bg' => 'from-purple-50 to-pink-50 dark:from-purple-950/30 dark:to-pink-950/30',
                'border' => 'border-purple-200 dark:border-purple-800',
                'icon' => 'from-purple-500 to-pink-600',
                'button' => 'from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700',
            ],
            'green' => [
                'bg' => 'from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/30',
                'border' => 'border-green-200 dark:border-green-800',
                'icon' => 'from-green-500 to-emerald-600',
                'button' => 'from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700',
            ],
            default => [
                'bg' => 'from-blue-50 to-indigo-50 dark:from-blue-950/30 dark:to-indigo-950/30',
                'border' => 'border-blue-200 dark:border-blue-800',
                'icon' => 'from-blue-500 to-indigo-600',
                'button' => 'from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700',
            ],
        };
    }

    public function render()
    {
        return view('livewire.components.subscription-prompt', [
            'gradients' => $this->getGradientClasses(),
        ]);
    }
}
