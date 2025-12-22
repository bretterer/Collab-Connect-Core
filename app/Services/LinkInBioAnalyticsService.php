<?php

namespace App\Services;

use App\Models\LinkInBioClick;
use App\Models\LinkInBioSettings;
use App\Models\LinkInBioView;
use App\Settings\LinkInBioAnalyticsSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LinkInBioAnalyticsService
{
    public function __construct(
        private LinkInBioAnalyticsSettings $settings
    ) {}

    /**
     * Record a page view for a Link-in-Bio page.
     */
    public function recordView(LinkInBioSettings $settings, Request $request): ?LinkInBioView
    {
        if ($this->shouldRespectDnt($request)) {
            return null;
        }

        $ipHash = $this->hashIp($request->ip());

        if (! $this->isWithinRateLimit($settings, $ipHash)) {
            return null;
        }

        $userAgent = $request->userAgent();
        $referrer = $request->header('referer');

        return LinkInBioView::create([
            'link_in_bio_settings_id' => $settings->id,
            'ip_hash' => $ipHash,
            'user_agent' => $userAgent ? substr($userAgent, 0, 512) : null,
            'device_type' => $this->detectDeviceType($userAgent),
            'referrer' => $referrer ? substr($referrer, 0, 512) : null,
            'referrer_domain' => $this->extractReferrerDomain($referrer),
            'is_unique' => $this->isUniqueVisitor($settings, $ipHash),
            'viewed_at' => now(),
        ]);
    }

    /**
     * Record a link click.
     */
    public function recordClick(
        LinkInBioSettings $settings,
        int $linkIndex,
        string $linkTitle,
        string $linkUrl,
        Request $request
    ): ?LinkInBioClick {
        if ($this->shouldRespectDnt($request)) {
            return null;
        }

        $userAgent = $request->userAgent();

        return LinkInBioClick::create([
            'link_in_bio_settings_id' => $settings->id,
            'link_index' => $linkIndex,
            'link_title' => substr($linkTitle, 0, 255),
            'link_url' => substr($linkUrl, 0, 2048),
            'ip_hash' => $this->hashIp($request->ip()),
            'user_agent' => $userAgent ? substr($userAgent, 0, 512) : null,
            'device_type' => $this->detectDeviceType($userAgent),
            'clicked_at' => now(),
        ]);
    }

    /**
     * Get overview metrics for a date range.
     *
     * @return array{views: int, unique_visitors: int, clicks: int, ctr: float}
     */
    public function getOverviewMetrics(LinkInBioSettings $settings, Carbon $startDate, Carbon $endDate): array
    {
        $views = $settings->views()
            ->inDateRange($startDate, $endDate)
            ->count();

        $uniqueVisitors = $settings->views()
            ->inDateRange($startDate, $endDate)
            ->distinct('ip_hash')
            ->count('ip_hash');

        $clicks = $settings->clicks()
            ->inDateRange($startDate, $endDate)
            ->count();

        $ctr = $views > 0 ? round(($clicks / $views) * 100, 2) : 0;

        return [
            'views' => $views,
            'unique_visitors' => $uniqueVisitors,
            'clicks' => $clicks,
            'ctr' => $ctr,
        ];
    }

    /**
     * Get views over time grouped by date.
     */
    public function getViewsOverTime(
        LinkInBioSettings $settings,
        Carbon $startDate,
        Carbon $endDate,
        string $granularity = 'day'
    ): Collection {
        $dateFormat = $this->getDateFormat($granularity);

        $data = $settings->views()
            ->inDateRange($startDate, $endDate)
            ->select(DB::raw("DATE_FORMAT(viewed_at, '{$dateFormat}') as date"), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        return $this->fillDateGaps($data, $startDate, $endDate, $granularity);
    }

    /**
     * Get clicks over time grouped by date.
     */
    public function getClicksOverTime(
        LinkInBioSettings $settings,
        Carbon $startDate,
        Carbon $endDate,
        string $granularity = 'day'
    ): Collection {
        $dateFormat = $this->getDateFormat($granularity);

        $data = $settings->clicks()
            ->inDateRange($startDate, $endDate)
            ->select(DB::raw("DATE_FORMAT(clicked_at, '{$dateFormat}') as date"), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        return $this->fillDateGaps($data, $startDate, $endDate, $granularity);
    }

    /**
     * Fill in missing dates with zero counts.
     */
    private function fillDateGaps(Collection $data, Carbon $startDate, Carbon $endDate, string $granularity): Collection
    {
        $result = collect();
        $current = $startDate->copy();
        $phpFormat = $this->getPhpDateFormat($granularity);

        while ($current->lte($endDate)) {
            $dateKey = $current->format($phpFormat);
            $result->push((object) [
                'date' => $dateKey,
                'count' => $data->has($dateKey) ? $data->get($dateKey)->count : 0,
            ]);

            match ($granularity) {
                'week' => $current->addWeek(),
                'month' => $current->addMonth(),
                default => $current->addDay(),
            };
        }

        return $result;
    }

    /**
     * Get PHP date format string for formatting Carbon dates.
     */
    private function getPhpDateFormat(string $granularity): string
    {
        return match ($granularity) {
            'week' => 'Y-W',
            'month' => 'Y-m',
            default => 'Y-m-d',
        };
    }

    /**
     * Get device type breakdown.
     *
     * @return array<string, int>
     */
    public function getDeviceBreakdown(LinkInBioSettings $settings, Carbon $startDate, Carbon $endDate): array
    {
        $breakdown = $settings->views()
            ->inDateRange($startDate, $endDate)
            ->select('device_type', DB::raw('COUNT(*) as count'))
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        return [
            'mobile' => $breakdown['mobile'] ?? 0,
            'desktop' => $breakdown['desktop'] ?? 0,
            'tablet' => $breakdown['tablet'] ?? 0,
        ];
    }

    /**
     * Get top referrers.
     */
    public function getTopReferrers(
        LinkInBioSettings $settings,
        Carbon $startDate,
        Carbon $endDate,
        int $limit = 10
    ): Collection {
        return $settings->views()
            ->inDateRange($startDate, $endDate)
            ->whereNotNull('referrer_domain')
            ->where('referrer_domain', '!=', '')
            ->select('referrer_domain', DB::raw('COUNT(*) as count'))
            ->groupBy('referrer_domain')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get per-link performance metrics.
     */
    public function getPerLinkPerformance(LinkInBioSettings $settings, Carbon $startDate, Carbon $endDate): Collection
    {
        $totalViews = $settings->views()
            ->inDateRange($startDate, $endDate)
            ->count();

        return $settings->clicks()
            ->inDateRange($startDate, $endDate)
            ->select(
                'link_index',
                'link_title',
                'link_url',
                DB::raw('COUNT(*) as clicks')
            )
            ->groupBy('link_index', 'link_title', 'link_url')
            ->orderByDesc('clicks')
            ->get()
            ->map(function ($link) use ($totalViews) {
                $link->ctr = $totalViews > 0 ? round(($link->clicks / $totalViews) * 100, 2) : 0;

                return $link;
            });
    }

    /**
     * Clean up old analytics data based on retention settings.
     */
    public function cleanupOldData(): int
    {
        $cutoffDate = now()->subDays($this->settings->dataRetentionDays);

        $deletedViews = LinkInBioView::where('viewed_at', '<', $cutoffDate)->delete();
        $deletedClicks = LinkInBioClick::where('clicked_at', '<', $cutoffDate)->delete();

        return $deletedViews + $deletedClicks;
    }

    /**
     * Hash an IP address for privacy-safe storage.
     */
    private function hashIp(?string $ip): string
    {
        $salt = config('app.key');

        return hash('sha256', $ip.$salt);
    }

    /**
     * Check if DNT header is set and should be respected.
     */
    private function shouldRespectDnt(Request $request): bool
    {
        return $request->header('DNT') === '1';
    }

    /**
     * Detect device type from user agent.
     */
    private function detectDeviceType(?string $userAgent): string
    {
        if (! $userAgent) {
            return 'desktop';
        }

        $userAgent = strtolower($userAgent);

        // Check for tablets first (before mobile, as tablets often contain mobile keywords)
        $tabletKeywords = ['ipad', 'tablet', 'kindle', 'playbook', 'silk'];
        foreach ($tabletKeywords as $keyword) {
            if (str_contains($userAgent, $keyword)) {
                return 'tablet';
            }
        }

        // Check for mobile devices
        $mobileKeywords = ['mobile', 'android', 'iphone', 'ipod', 'blackberry', 'windows phone', 'opera mini', 'iemobile'];
        foreach ($mobileKeywords as $keyword) {
            if (str_contains($userAgent, $keyword)) {
                return 'mobile';
            }
        }

        return 'desktop';
    }

    /**
     * Extract the domain from a referrer URL.
     */
    private function extractReferrerDomain(?string $referrer): ?string
    {
        if (! $referrer) {
            return null;
        }

        $parsed = parse_url($referrer);

        return $parsed['host'] ?? null;
    }

    /**
     * Check if view is within rate limit.
     */
    private function isWithinRateLimit(LinkInBioSettings $settings, string $ipHash): bool
    {
        $rateLimitMinutes = $this->settings->viewRateLimitMinutes;
        $cutoffTime = now()->subMinutes($rateLimitMinutes);

        $recentView = $settings->views()
            ->where('ip_hash', $ipHash)
            ->where('viewed_at', '>=', $cutoffTime)
            ->exists();

        return ! $recentView;
    }

    /**
     * Check if this is a unique visitor (first view ever for this IP).
     */
    private function isUniqueVisitor(LinkInBioSettings $settings, string $ipHash): bool
    {
        return ! $settings->views()
            ->where('ip_hash', $ipHash)
            ->exists();
    }

    /**
     * Get MySQL date format string for grouping.
     */
    private function getDateFormat(string $granularity): string
    {
        return match ($granularity) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };
    }
}
