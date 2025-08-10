<?php

namespace App\Services;

use App\Enums\CampaignApplicationStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalyticsService
{
    public function __construct(
        private User $user
    ) {}

    /**
     * Get campaign performance analytics
     */
    public function getCampaignPerformance(): array
    {
        $campaigns = $this->getUserCampaigns();

        $totalCampaigns = $campaigns->count();
        $publishedCampaigns = $campaigns->where('status', CampaignStatus::PUBLISHED)->count();
        $draftCampaigns = $campaigns->where('status', CampaignStatus::DRAFT)->count();
        $completedCampaigns = $campaigns->where('status', CampaignStatus::ARCHIVED)->count();

        $completionRate = $totalCampaigns > 0 ? round(($completedCampaigns / $totalCampaigns) * 100, 1) : 0;
        $publishRate = $totalCampaigns > 0 ? round(($publishedCampaigns / $totalCampaigns) * 100, 1) : 0;

        return [
            'total_campaigns' => $totalCampaigns,
            'published_campaigns' => $publishedCampaigns,
            'draft_campaigns' => $draftCampaigns,
            'completed_campaigns' => $completedCampaigns,
            'completion_rate' => $completionRate,
            'publish_rate' => $publishRate,
        ];
    }

    /**
     * Get application analytics
     */
    public function getApplicationAnalytics(): array
    {
        $applications = $this->getUserApplications();

        $totalApplications = $applications->count();
        $pendingApplications = $applications->where('status', CampaignApplicationStatus::PENDING)->count();
        $acceptedApplications = $applications->where('status', CampaignApplicationStatus::ACCEPTED)->count();
        $rejectedApplications = $applications->where('status', CampaignApplicationStatus::REJECTED)->count();

        $acceptanceRate = $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 1) : 0;
        $rejectionRate = $totalApplications > 0 ? round(($rejectedApplications / $totalApplications) * 100, 1) : 0;

        // Average time to respond
        $avgResponseTime = $this->getAverageResponseTime();

        return [
            'total_applications' => $totalApplications,
            'pending_applications' => $pendingApplications,
            'accepted_applications' => $acceptedApplications,
            'rejected_applications' => $rejectedApplications,
            'acceptance_rate' => $acceptanceRate,
            'rejection_rate' => $rejectionRate,
            'avg_response_time_hours' => $avgResponseTime,
        ];
    }

    /**
     * Get financial analytics
     */
    public function getFinancialAnalytics(): array
    {
        $campaigns = $this->getUserCampaigns();

        // Calculate total budget allocated
        $totalBudget = $campaigns->sum(function ($campaign) {
            return $this->getCampaignBudget($campaign);
        });

        // Calculate spent budget (completed campaigns)
        $spentBudget = $campaigns->where('status', CampaignStatus::ARCHIVED)->sum(function ($campaign) {
            return $this->getCampaignBudget($campaign);
        });

        // Calculate active budget (published campaigns)
        $activeBudget = $campaigns->where('status', CampaignStatus::PUBLISHED)->sum(function ($campaign) {
            return $this->getCampaignBudget($campaign);
        });

        // Average cost per campaign
        $avgCostPerCampaign = $campaigns->count() > 0 ? round($totalBudget / $campaigns->count(), 2) : 0;

        // Average cost per influencer
        $totalInfluencers = $campaigns->sum('influencer_count');
        $avgCostPerInfluencer = $totalInfluencers > 0 ? round($totalBudget / $totalInfluencers, 2) : 0;

        return [
            'total_budget' => $totalBudget,
            'spent_budget' => $spentBudget,
            'active_budget' => $activeBudget,
            'remaining_budget' => $totalBudget - $spentBudget - $activeBudget,
            'avg_cost_per_campaign' => $avgCostPerCampaign,
            'avg_cost_per_influencer' => $avgCostPerInfluencer,
        ];
    }

    /**
     * Get campaign performance over time
     */
    public function getCampaignTrends(int $months = 6): array
    {
        $startDate = Carbon::now()->subMonths($months);

        $campaigns = $this->getUserCampaigns()
            ->where('created_at', '>=', $startDate);

        $monthlyData = [];

        for ($i = 0; $i < $months; $i++) {
            $month = Carbon::now()->subMonths($i);
            $monthLabel = $month->format('M Y');

            $monthlyCampaigns = $campaigns->filter(function ($campaign) use ($month) {
                return $campaign->created_at->format('Y-m') === $month->format('Y-m');
            });

            $monthlyApplications = $this->getUserApplications()->filter(function ($application) use ($month) {
                return $application->submitted_at->format('Y-m') === $month->format('Y-m');
            });

            $monthlyData[] = [
                'month' => $monthLabel,
                'campaigns_created' => $monthlyCampaigns->count(),
                'campaigns_published' => $monthlyCampaigns->where('status', CampaignStatus::PUBLISHED)->count(),
                'applications_received' => $monthlyApplications->count(),
                'applications_accepted' => $monthlyApplications->where('status', CampaignApplicationStatus::ACCEPTED)->count(),
            ];
        }

        return array_reverse($monthlyData);
    }

    /**
     * Get top performing campaigns
     */
    public function getTopPerformingCampaigns(int $limit = 5): Collection
    {
        $campaigns = Campaign::where('user_id', $this->user->id)
            ->where('status', '!=', CampaignStatus::DRAFT)
            ->withCount([
                'applications',
                'applications as accepted_applications_count' => function ($query) {
                    $query->where('status', CampaignApplicationStatus::ACCEPTED);
                }
            ])
            ->get();
            
        return $campaigns->sortByDesc(function ($campaign) {
                // Score based on application count and acceptance rate
                $applicationCount = $campaign->applications_count ?: 0;
                $acceptedCount = $campaign->accepted_applications_count ?: 0;
                $acceptanceRate = $applicationCount > 0 ? $acceptedCount / $applicationCount : 0;

                return ($applicationCount * 0.7) + ($acceptanceRate * 100 * 0.3);
            })
            ->take($limit)
            ->values();
    }

    /**
     * Get influencer engagement metrics
     */
    public function getInfluencerEngagementMetrics(): array
    {
        $applications = $this->getUserApplications();

        // Unique influencers who applied
        $uniqueInfluencers = $applications->pluck('user_id')->unique()->count();

        // Repeat collaborators (influencers with multiple accepted applications)
        $repeatCollaborators = $applications->where('status', CampaignApplicationStatus::ACCEPTED)
            ->groupBy('user_id')
            ->filter(function ($group) {
                return $group->count() > 1;
            })
            ->count();

        // Most active influencers
        $topInfluencers = $applications->groupBy('user_id')
            ->map(function ($group) {
                return [
                    'user' => $group->first()->user,
                    'application_count' => $group->count(),
                    'accepted_count' => $group->where('status', CampaignApplicationStatus::ACCEPTED)->count(),
                ];
            })
            ->sortByDesc('application_count')
            ->take(5)
            ->values();

        return [
            'unique_influencers' => $uniqueInfluencers,
            'repeat_collaborators' => $repeatCollaborators,
            'top_influencers' => $topInfluencers,
            'avg_applications_per_campaign' => $this->getAverageApplicationsPerCampaign(),
        ];
    }

    /**
     * Get geographic performance
     */
    public function getGeographicPerformance(): array
    {
        $campaigns = $this->getUserCampaigns()->where('target_zip_code', '!=', null);

        $zipCodePerformance = $campaigns->groupBy('target_zip_code')
            ->map(function ($campaignGroup) {
                $zipCode = $campaignGroup->first()->target_zip_code;
                $totalApplications = $campaignGroup->sum(function ($campaign) {
                    return $campaign->applications->count();
                });

                return [
                    'zip_code' => $zipCode,
                    'campaign_count' => $campaignGroup->count(),
                    'total_applications' => $totalApplications,
                    'avg_applications' => $campaignGroup->count() > 0 ? round($totalApplications / $campaignGroup->count(), 1) : 0,
                ];
            })
            ->sortByDesc('avg_applications')
            ->take(10)
            ->values();

        return [
            'top_performing_areas' => $zipCodePerformance,
            'total_areas_targeted' => $campaigns->pluck('target_zip_code')->unique()->count(),
        ];
    }

    /**
     * Get campaign type performance
     */
    public function getCampaignTypePerformance(): array
    {
        $campaigns = $this->getUserCampaigns()->where('campaign_type', '!=', null);

        return $campaigns->groupBy('campaign_type')
            ->map(function ($campaignGroup, $type) {
                $totalApplications = $campaignGroup->sum(function ($campaign) {
                    return $campaign->applications->count();
                });

                $acceptedApplications = $campaignGroup->sum(function ($campaign) {
                    return $campaign->applications->where('status', CampaignApplicationStatus::ACCEPTED)->count();
                });

                return [
                    'type' => $type,
                    'type_label' => $type && is_object($type) && method_exists($type, 'label') ? $type->label() : ($type ?? 'Unknown'),
                    'campaign_count' => $campaignGroup->count(),
                    'total_applications' => $totalApplications,
                    'accepted_applications' => $acceptedApplications,
                    'avg_applications' => $campaignGroup->count() > 0 ? round($totalApplications / $campaignGroup->count(), 1) : 0,
                    'acceptance_rate' => $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('avg_applications')
            ->values()
            ->toArray();
    }

    /**
     * Get user's campaigns
     */
    private function getUserCampaigns(): Collection
    {
        return Campaign::where('user_id', $this->user->id)
            ->with(['applications'])
            ->get();
    }

    /**
     * Get user's campaign applications
     */
    private function getUserApplications(): Collection
    {
        return CampaignApplication::whereHas('campaign', function ($query) {
            $query->where('user_id', $this->user->id);
        })
        ->with(['user', 'campaign'])
        ->get();
    }

    /**
     * Calculate campaign budget
     */
    private function getCampaignBudget(Campaign $campaign): float
    {
        if (!$campaign->compensation || !$campaign->compensation->isMonetaryCompensation()) {
            return 0;
        }

        $amount = $campaign->compensation->compensation_amount ?? 0;
        $influencerCount = $campaign->influencer_count ?? 1;

        return $amount * $influencerCount;
    }

    /**
     * Get average response time to applications
     */
    private function getAverageResponseTime(): float
    {
        $respondedApplications = $this->getUserApplications()
            ->whereNotNull('reviewed_at')
            ->where('reviewed_at', '>', 'submitted_at');

        if ($respondedApplications->isEmpty()) {
            return 0;
        }

        $totalHours = $respondedApplications->sum(function ($application) {
            return $application->submitted_at->diffInHours($application->reviewed_at);
        });

        return round($totalHours / $respondedApplications->count(), 1);
    }

    /**
     * Get average applications per campaign
     */
    private function getAverageApplicationsPerCampaign(): float
    {
        $campaigns = $this->getUserCampaigns()->where('status', '!=', CampaignStatus::DRAFT);

        if ($campaigns->isEmpty()) {
            return 0;
        }

        $totalApplications = $campaigns->sum(function ($campaign) {
            return $campaign->applications->count();
        });

        return round($totalApplications / $campaigns->count(), 1);
    }
}