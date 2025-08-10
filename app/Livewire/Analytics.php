<?php

namespace App\Livewire;

use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Analytics extends Component
{
    public $timeFrame = '6'; // months
    public $selectedMetric = 'overview';
    
    public function mount()
    {
        // Ensure only business users can access analytics
        if (!Auth::user()->isBusinessAccount()) {
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        $analyticsService = new AnalyticsService(Auth::user());
        
        $data = [
            'campaign_performance' => $analyticsService->getCampaignPerformance(),
            'application_analytics' => $analyticsService->getApplicationAnalytics(),
            'financial_analytics' => $analyticsService->getFinancialAnalytics(),
            'campaign_trends' => $analyticsService->getCampaignTrends(intval($this->timeFrame)),
            'top_performing_campaigns' => $analyticsService->getTopPerformingCampaigns(),
            'influencer_engagement' => $analyticsService->getInfluencerEngagementMetrics(),
            'geographic_performance' => $analyticsService->getGeographicPerformance(),
            'campaign_type_performance' => $analyticsService->getCampaignTypePerformance(),
        ];

        return view('livewire.analytics', $data);
    }

    public function updatedTimeFrame()
    {
        // Automatically refresh when time frame changes
    }

    public function selectMetric($metric)
    {
        $this->selectedMetric = $metric;
    }

    public function exportData()
    {
        // Future: Export analytics data as CSV/PDF
        $this->dispatch('export-analytics');
    }
}