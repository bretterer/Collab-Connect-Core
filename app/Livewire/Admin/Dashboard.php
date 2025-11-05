<?php

namespace App\Livewire\Admin;

use App\Enums\AccountType;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard');
    }

    /**
     * Get total user counts by type
     */
    public function getUserCounts(): array
    {
        return [
            'total_users' => User::count(),
            'business_users' => User::where('account_type', AccountType::BUSINESS)->count(),
            'influencer_users' => User::where('account_type', AccountType::INFLUENCER)->count(),
            'admin_users' => User::where('account_type', AccountType::ADMIN)->count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Get campaign statistics
     */
    public function getCampaignStats(): array
    {
        return [
            'total_campaigns' => Campaign::count(),
            'published_campaigns' => Campaign::where('status', CampaignStatus::PUBLISHED)->count(),
            'draft_campaigns' => Campaign::where('status', CampaignStatus::DRAFT)->count(),
            'scheduled_campaigns' => Campaign::where('status', CampaignStatus::SCHEDULED)->count(),
            'campaigns_this_month' => Campaign::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Get application statistics
     */
    public function getApplicationStats(): array
    {
        return [
            'total_applications' => CampaignApplication::count(),
            'pending_applications' => CampaignApplication::where('status', \App\Enums\CampaignApplicationStatus::PENDING)->count(),
            'accepted_applications' => CampaignApplication::where('status', \App\Enums\CampaignApplicationStatus::ACCEPTED)->count(),
            'rejected_applications' => CampaignApplication::where('status', \App\Enums\CampaignApplicationStatus::REJECTED)->count(),
            'applications_this_month' => CampaignApplication::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Get recent users
     */
    public function getRecentUsers()
    {
        return User::query()
            ->whereNot('account_type', AccountType::ADMIN)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent campaigns
     */
    public function getRecentCampaigns()
    {
        return Campaign::query()
            ->with('business')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get pending applications
     */
    public function getPendingApplications()
    {
        return CampaignApplication::query()
            ->where('status', \App\Enums\CampaignApplicationStatus::PENDING)
            ->orderBy('submitted_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get system health metrics
     */
    public function getSystemHealth(): array
    {
        // Calculate conversion rates
        $totalApplications = CampaignApplication::count();
        $acceptedApplications = CampaignApplication::where('status', \App\Enums\CampaignApplicationStatus::ACCEPTED)->count();
        $applicationConversionRate = $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 1) : 0;

        // Calculate platform engagement
        $publishedCampaigns = Campaign::where('status', CampaignStatus::PUBLISHED)->count();
        $totalCampaigns = Campaign::count();
        $campaignPublishRate = $totalCampaigns > 0 ? round(($publishedCampaigns / $totalCampaigns) * 100, 1) : 0;

        return [
            'application_conversion_rate' => $applicationConversionRate,
            'campaign_publish_rate' => $campaignPublishRate,
            'active_users_today' => User::whereDate('updated_at', today())->count(),
            'platform_health_score' => min(100, round(($applicationConversionRate + $campaignPublishRate) / 2, 1)),
        ];
    }
}
