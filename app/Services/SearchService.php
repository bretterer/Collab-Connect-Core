<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\PostalCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    /**
     * Search for profiles based on type and criteria.
     * This is the primary public API for searching.
     *
     * @param  string  $type  'businesses' or 'influencers'
     */
    public static function searchProfiles(string $type, array $criteria, User $currentUser, int $perPage = 12): LengthAwarePaginator
    {
        return match ($type) {
            'businesses' => self::searchBusinesses($criteria, $currentUser, $perPage),
            'influencers' => self::searchInfluencers($criteria, $currentUser, $perPage),
            default => throw new \InvalidArgumentException("Invalid search type: {$type}. Must be 'businesses' or 'influencers'."),
        };
    }

    /**
     * Search for businesses based on criteria.
     * Used when influencers are searching for businesses.
     */
    private static function searchBusinesses(array $criteria, User $currentUser, int $perPage = 12): LengthAwarePaginator
    {
        $query = Business::query();

        // Exclude current user's businesses
        $currentUserBusinessIds = $currentUser->businesses()->pluck('businesses.id')->toArray();
        if (! empty($currentUserBusinessIds)) {
            $query->whereNotIn('id', $currentUserBusinessIds);
        }

        // Filter by saved/hidden users (via owner relationship)
        $showSavedOnly = $criteria['showSavedOnly'] ?? false;
        $hideHidden = $criteria['hideHidden'] ?? true;

        if ($showSavedOnly) {
            $savedUserIds = $currentUser->savedUsers()->pluck('saved_user_id')->toArray();
            $query->whereHas('owner', fn ($ownerQuery) => $ownerQuery->whereIn('users.id', $savedUserIds));
        }

        if ($hideHidden) {
            $hiddenUserIds = $currentUser->hiddenUsers()->pluck('saved_user_id')->toArray();
            if (! empty($hiddenUserIds)) {
                $query->whereHas('owner', fn ($ownerQuery) => $ownerQuery->whereNotIn('users.id', $hiddenUserIds));
            }
        }

        // Apply filters
        $query = self::applyBusinessSearchFilter($query, $criteria['search'] ?? '');
        $query = self::applyBusinessLocationFilter($query, $criteria);
        $query = self::applyBusinessIndustryFilter($query, $criteria['selectedNiches'] ?? []);

        // Prioritize promoted profiles first if any criteria is set
        if (! empty($criteria['search'])
            || ! empty($criteria['location'])
            || ! empty($criteria['selectedNiches'])
        ) {
            $query = $query->orderByDesc('is_promoted');
        }

        // Apply secondary sorting
        $query = self::applyBusinessSorting($query, $criteria['sortBy'] ?? 'relevance', $criteria);

        // Eager load relationships for the cards
        $query->with([
            'owner',
            'postalCodeInfo' => fn ($q) => $q->select(['postal_code', 'place_name', 'admin_name1', 'admin_code1', 'latitude', 'longitude']),
            'media',
        ]);

        $results = $query->paginate($perPage);

        // Calculate distances if location search is active
        if (! empty($criteria['location']) && preg_match('/^\d{5}$/', $criteria['location'])) {
            $results = self::calculateBusinessDistances($results, $criteria['location']);
        }

        return $results;
    }

    /**
     * Search for influencers based on criteria.
     * Used when businesses are searching for influencers.
     */
    private static function searchInfluencers(array $criteria, User $currentUser, int $perPage = 12): LengthAwarePaginator
    {
        $query = Influencer::query()
            ->where('onboarding_complete', true)
            ->where('is_searchable', true)
            ->whereHas('user', function ($q) use ($currentUser, $criteria) {
                $q->where('id', '!=', $currentUser->id);

                // Filter by saved/hidden users
                $showSavedOnly = $criteria['showSavedOnly'] ?? false;
                $hideHidden = $criteria['hideHidden'] ?? true;

                if ($showSavedOnly) {
                    $savedUserIds = $currentUser->savedUsers()->pluck('saved_user_id')->toArray();
                    $q->whereIn('id', $savedUserIds);
                }

                if ($hideHidden) {
                    $hiddenUserIds = $currentUser->hiddenUsers()->pluck('saved_user_id')->toArray();
                    if (! empty($hiddenUserIds)) {
                        $q->whereNotIn('id', $hiddenUserIds);
                    }
                }
            });

        // Apply filters
        $query = self::applyInfluencerSearchFilter($query, $criteria['search'] ?? '');
        $query = self::applyInfluencerLocationFilter($query, $criteria);
        $query = self::applyInfluencerNicheFilter($query, $criteria['selectedNiches'] ?? []);
        $query = self::applyInfluencerPlatformFilter($query, $criteria['selectedPlatforms'] ?? []);
        $query = self::applyInfluencerFollowerFilter($query, $criteria);

        // If any criteria is set, prioritize promoted profiles first
        if (! empty($criteria['search'])
            || ! empty($criteria['location'])
            || ! empty($criteria['selectedNiches'])
            || ! empty($criteria['selectedPlatforms'])
            || ! empty($criteria['minFollowers'])
            || ! empty($criteria['maxFollowers'])
        ) {
            $query = $query->orderByDesc('is_promoted');
        }

        // Apply secondary sorting after prioritizing promoted profiles
        $query = self::applyInfluencerSorting($query, $criteria['sortBy'] ?? 'relevance', $criteria);

        // Eager load relationships for the cards
        $query->with([
            'user',
            'socialAccounts',
            'postalCodeInfo' => fn ($q) => $q->select(['postal_code', 'place_name', 'admin_name1', 'admin_code1', 'latitude', 'longitude']),
            'media',
        ]);

        $results = $query->paginate($perPage);

        // Calculate distances if location search is active
        if (! empty($criteria['location']) && preg_match('/^\d{5}$/', $criteria['location'])) {
            $results = self::calculateInfluencerDistances($results, $criteria['location']);
        }

        return $results;
    }

    // ==========================================
    // Business Search Filters
    // ==========================================

    /**
     * Apply text search filter to business query.
     */
    private static function applyBusinessSearchFilter(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $search = trim($search);

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Apply location/proximity filter to business query.
     */
    private static function applyBusinessLocationFilter(Builder $query, array $criteria): Builder
    {
        $location = $criteria['location'] ?? '';

        if (empty($location)) {
            return $query;
        }

        // For 5-digit zip codes, use proximity search
        if (preg_match('/^\d{5}$/', $location)) {
            $radius = (int) ($criteria['searchRadius'] ?? 50);
            $nearbyZipCodes = self::getNearbyZipCodes($location, $radius);

            if (! empty($nearbyZipCodes)) {
                return $query->whereIn('postal_code', $nearbyZipCodes);
            }
        }

        // Fallback to text search on postal_code
        return $query->where('postal_code', 'like', "%{$location}%");
    }

    /**
     * Apply industry filter to business query.
     */
    private static function applyBusinessIndustryFilter(Builder $query, array $selectedNiches): Builder
    {
        if (empty($selectedNiches)) {
            return $query;
        }

        return $query->whereIn('industry', $selectedNiches);
    }

    /**
     * Apply sorting to business query.
     */
    private static function applyBusinessSorting(Builder $query, string $sortBy, array $criteria): Builder
    {
        return match ($sortBy) {
            'name' => $query->orderBy('name'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'distance' => $query->orderBy('id'), // Distance sorting happens after pagination
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Calculate distances for paginated business results.
     */
    private static function calculateBusinessDistances(LengthAwarePaginator $results, string $zipCode): LengthAwarePaginator
    {
        $searchPostalCode = PostalCode::where('postal_code', $zipCode)
            ->where('country_code', 'US')
            ->first();

        if (! $searchPostalCode) {
            return $results;
        }

        $results->getCollection()->transform(function ($business) use ($searchPostalCode) {
            $businessPostalCode = $business->postalCodeInfo;
            $business->distance = $businessPostalCode
                ? $searchPostalCode->distanceTo($businessPostalCode)
                : null;

            return $business;
        });

        // Re-sort by distance if that's the selected sort
        $sorted = $results->getCollection()->sortBy(function ($business) {
            return $business->distance ?? 99999;
        })->values();

        $results->setCollection($sorted);

        return $results;
    }

    // ==========================================
    // Influencer Search Filters
    // ==========================================

    /**
     * Apply text search filter to influencer query.
     */
    private static function applyInfluencerSearchFilter(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        $search = trim($search);

        return $query->where(function ($q) use ($search) {
            $q->where('username', 'like', "%{$search}%")
                ->orWhere('bio', 'like', "%{$search}%")
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Apply location/proximity filter to influencer query.
     */
    private static function applyInfluencerLocationFilter(Builder $query, array $criteria): Builder
    {
        $location = $criteria['location'] ?? '';

        if (empty($location)) {
            return $query;
        }

        // For 5-digit zip codes, use proximity search
        if (preg_match('/^\d{5}$/', $location)) {
            $radius = (int) ($criteria['searchRadius'] ?? 50);
            $nearbyZipCodes = self::getNearbyZipCodes($location, $radius);

            if (! empty($nearbyZipCodes)) {
                return $query->whereIn('postal_code', $nearbyZipCodes);
            }
        }

        // Fallback to text search on postal_code
        return $query->where('postal_code', 'like', "%{$location}%");
    }

    /**
     * Apply niche/content type filter to influencer query.
     */
    private static function applyInfluencerNicheFilter(Builder $query, array $selectedNiches): Builder
    {
        if (empty($selectedNiches)) {
            return $query;
        }

        return $query->where(function ($q) use ($selectedNiches) {
            foreach ($selectedNiches as $niche) {
                $q->orWhereJsonContains('content_types', $niche);
            }
        });
    }

    /**
     * Apply social platform filter to influencer query.
     */
    private static function applyInfluencerPlatformFilter(Builder $query, array $selectedPlatforms): Builder
    {
        if (empty($selectedPlatforms)) {
            return $query;
        }

        return $query->whereHas('socialAccounts', function ($q) use ($selectedPlatforms) {
            $q->whereIn('platform', $selectedPlatforms);
        });
    }

    /**
     * Apply follower count filter to influencer query.
     * Filters by TOTAL followers across all social accounts using a subquery.
     */
    private static function applyInfluencerFollowerFilter(Builder $query, array $criteria): Builder
    {
        $minFollowers = $criteria['minFollowers'] ?? null;
        $maxFollowers = $criteria['maxFollowers'] ?? null;

        if (empty($minFollowers) && empty($maxFollowers)) {
            return $query;
        }

        // Use whereRaw with a correlated subquery to filter by total followers
        $subquery = '(SELECT COALESCE(SUM(followers), 0) FROM influencer_socials WHERE influencer_socials.influencer_id = influencers.id)';

        if (! empty($minFollowers) && ! empty($maxFollowers)) {
            $query->whereRaw("{$subquery} BETWEEN ? AND ?", [(int) $minFollowers, (int) $maxFollowers]);
        } elseif (! empty($minFollowers)) {
            $query->whereRaw("{$subquery} >= ?", [(int) $minFollowers]);
        } else {
            $query->whereRaw("{$subquery} <= ?", [(int) $maxFollowers]);
        }

        return $query;
    }

    /**
     * Apply sorting to influencer query.
     */
    private static function applyInfluencerSorting(Builder $query, string $sortBy, array $criteria): Builder
    {
        return match ($sortBy) {
            'name' => $query->join('users', 'influencers.user_id', '=', 'users.id')
                ->orderBy('users.name')
                ->select('influencers.*'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'followers' => $query->withSum('socialAccounts', 'followers')
                ->orderByDesc('social_accounts_sum_followers'),
            'quality' => $query->orderByDesc('content_quality_score'),
            'distance' => $query->orderBy('id'), // Distance sorting happens after pagination
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Calculate distances for paginated influencer results.
     */
    private static function calculateInfluencerDistances(LengthAwarePaginator $results, string $zipCode): LengthAwarePaginator
    {
        $searchPostalCode = PostalCode::where('postal_code', $zipCode)
            ->where('country_code', 'US')
            ->first();

        if (! $searchPostalCode) {
            return $results;
        }

        $results->getCollection()->transform(function ($influencer) use ($searchPostalCode) {
            $influencerPostalCode = $influencer->postalCodeInfo;
            $influencer->distance = $influencerPostalCode
                ? $searchPostalCode->distanceTo($influencerPostalCode)
                : null;

            return $influencer;
        });

        // Re-sort by distance if that's the selected sort
        $sorted = $results->getCollection()->sortBy(function ($influencer) {
            return $influencer->distance ?? 99999;
        })->values();

        $results->setCollection($sorted);

        return $results;
    }

    // ==========================================
    // Shared Utilities
    // ==========================================

    /**
     * Get nearby zip codes for proximity search.
     */
    private static function getNearbyZipCodes(string $location, int $radius): array
    {
        if (! preg_match('/^\d{5}$/', $location)) {
            return [];
        }

        $postalCode = PostalCode::where('postal_code', $location)
            ->where('country_code', 'US')
            ->first();

        if (! $postalCode) {
            return [];
        }

        return $postalCode->withinRadius($radius)->pluck('postal_code')->toArray();
    }

    /**
     * Get search metadata for the UI.
     */
    public static function getSearchMetadata(array $criteria): array
    {
        $location = $criteria['location'] ?? '';
        $searchPostalCode = null;
        $isProximitySearch = false;
        $nearbyCount = 0;

        if (! empty($location) && preg_match('/^\d{5}$/', $location)) {
            $searchPostalCode = PostalCode::where('postal_code', $location)
                ->where('country_code', 'US')
                ->first();

            if ($searchPostalCode) {
                $nearbyZipCodes = self::getNearbyZipCodes($location, $criteria['searchRadius'] ?? 50);
                $isProximitySearch = ! empty($nearbyZipCodes);
                $nearbyCount = count($nearbyZipCodes);
            }
        }

        return [
            'searchPostalCode' => $searchPostalCode,
            'isProximitySearch' => $isProximitySearch,
            'nearbyZipCodesCount' => $nearbyCount,
        ];
    }

    /**
     * Get available filter options for the UI.
     */
    public static function getFilterOptions(): array
    {
        return [
            'radiusOptions' => [
                ['value' => 10, 'label' => '10 miles'],
                ['value' => 25, 'label' => '25 miles'],
                ['value' => 50, 'label' => '50 miles'],
                ['value' => 100, 'label' => '100 miles'],
                ['value' => 250, 'label' => '250 miles'],
            ],
            'followerPresets' => [
                ['min' => null, 'max' => 1000, 'label' => 'Nano (< 1K)'],
                ['min' => 1000, 'max' => 10000, 'label' => 'Micro (1K - 10K)'],
                ['min' => 10000, 'max' => 100000, 'label' => 'Mid (10K - 100K)'],
                ['min' => 100000, 'max' => 1000000, 'label' => 'Macro (100K - 1M)'],
                ['min' => 1000000, 'max' => null, 'label' => 'Mega (1M+)'],
            ],
            'sortOptions' => [
                ['value' => 'relevance', 'label' => 'Most Relevant'],
                ['value' => 'newest', 'label' => 'Newest First'],
                ['value' => 'followers', 'label' => 'Most Followers'],
                ['value' => 'quality', 'label' => 'Highest Quality'],
                ['value' => 'name', 'label' => 'Name (A-Z)'],
            ],
        ];
    }
}
