<?php

namespace App\Livewire\LinkInBio;

use App\Livewire\BaseComponent;
use App\Livewire\Traits\EnforcesTierAccess;
use App\Models\LinkInBioSettings;
use App\Services\LinkInBioAnalyticsService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Analytics extends BaseComponent
{
    use EnforcesTierAccess;

    public string $dateRange = '30';

    public ?string $customStartDate = null;

    public ?string $customEndDate = null;

    public function mount(): void
    {
        $influencer = auth()->user()?->influencer;

        if (! $influencer || ! $influencer->linkInBioSettings) {
            $this->redirect(route('link-in-bio.index'));

            return;
        }
    }

    #[Computed]
    public function hasAdvancedAnalyticsAccess(): bool
    {
        return auth()->user()?->influencer?->hasFeatureAccess('analytics_advanced') ?? false;
    }

    #[Computed]
    public function requiredTierForAnalytics(): ?string
    {
        return auth()->user()?->influencer?->getTierRequiredFor('analytics_advanced');
    }

    #[Computed]
    public function settings(): ?LinkInBioSettings
    {
        return auth()->user()?->influencer?->linkInBioSettings;
    }

    #[Computed]
    public function startDate(): Carbon
    {
        if ($this->dateRange === 'custom' && $this->customStartDate) {
            return Carbon::parse($this->customStartDate)->startOfDay();
        }

        return now()->subDays((int) $this->dateRange)->startOfDay();
    }

    #[Computed]
    public function endDate(): Carbon
    {
        if ($this->dateRange === 'custom' && $this->customEndDate) {
            return Carbon::parse($this->customEndDate)->endOfDay();
        }

        return now()->endOfDay();
    }

    #[Computed]
    public function overviewMetrics(): array
    {
        if (! $this->hasAdvancedAnalyticsAccess || ! $this->settings) {
            return [
                'views' => 0,
                'unique_visitors' => 0,
                'clicks' => 0,
                'ctr' => 0,
            ];
        }

        return app(LinkInBioAnalyticsService::class)->getOverviewMetrics(
            $this->settings,
            $this->startDate,
            $this->endDate
        );
    }

    #[Computed]
    public function viewsChartData(): array
    {
        if (! $this->hasAdvancedAnalyticsAccess || ! $this->settings) {
            return [];
        }

        $data = app(LinkInBioAnalyticsService::class)->getViewsOverTime(
            $this->settings,
            $this->startDate,
            $this->endDate
        );

        return $this->formatChartData($data, 'views');
    }

    #[Computed]
    public function clicksChartData(): array
    {
        if (! $this->hasAdvancedAnalyticsAccess || ! $this->settings) {
            return [];
        }

        $data = app(LinkInBioAnalyticsService::class)->getClicksOverTime(
            $this->settings,
            $this->startDate,
            $this->endDate
        );

        return $this->formatChartData($data, 'clicks');
    }

    #[Computed]
    public function deviceBreakdown(): array
    {
        if (! $this->hasAdvancedAnalyticsAccess || ! $this->settings) {
            return [
                'mobile' => 0,
                'desktop' => 0,
                'tablet' => 0,
            ];
        }

        return app(LinkInBioAnalyticsService::class)->getDeviceBreakdown(
            $this->settings,
            $this->startDate,
            $this->endDate
        );
    }

    #[Computed]
    public function topReferrers(): Collection
    {
        if (! $this->hasAdvancedAnalyticsAccess || ! $this->settings) {
            return collect();
        }

        return app(LinkInBioAnalyticsService::class)->getTopReferrers(
            $this->settings,
            $this->startDate,
            $this->endDate
        );
    }

    #[Computed]
    public function linkPerformance(): Collection
    {
        if (! $this->hasAdvancedAnalyticsAccess || ! $this->settings) {
            return collect();
        }

        return app(LinkInBioAnalyticsService::class)->getPerLinkPerformance(
            $this->settings,
            $this->startDate,
            $this->endDate
        );
    }

    public function updatedDateRange(): void
    {
        // Clear custom dates when switching to preset ranges
        if ($this->dateRange !== 'custom') {
            $this->customStartDate = null;
            $this->customEndDate = null;
        }

        // Force recomputation of all data
        unset($this->overviewMetrics);
        unset($this->viewsChartData);
        unset($this->clicksChartData);
        unset($this->deviceBreakdown);
        unset($this->topReferrers);
        unset($this->linkPerformance);
    }

    public function updatedCustomStartDate(): void
    {
        $this->updatedDateRange();
    }

    public function updatedCustomEndDate(): void
    {
        $this->updatedDateRange();
    }

    private function formatChartData(Collection $data, string $field): array
    {
        return $data->map(fn ($item) => [
            'date' => $item->date,
            $field => $item->count,
        ])->toArray();
    }

    public function render()
    {
        return view('livewire.link-in-bio.analytics');
    }
}
