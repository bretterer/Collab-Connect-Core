<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Models\Influencer;
use App\Models\PostalCode;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    /**
     * Search for influencers based on criteria.
     * This is the primary search method for businesses finding influencers.
     */
    public static function searchInfluencers(array $criteria, User $currentUser, int $perPage = 12): LengthAwarePaginator
    {
        $query = Influencer::query()
            ->where('onboarding_complete', true)
            ->whereHas('user', function ($q) use ($currentUser, $criteria) {
                $q->where('account_type', AccountType::INFLUENCER)
                    ->where('id', '!=', $currentUser->id);

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

    // ==========================================
    // Legacy methods for backwards compatibility
    // ==========================================

    /**
     * Legacy method - search for users based on criteria.
     *
     * @deprecated Use searchInfluencers() instead for business->influencer searches
     */
    public static function searchUsers(array $criteria, User $currentUser, int $perPage = 12): LengthAwarePaginator
    {
        // If business or admin is searching for influencers, use the optimized method
        if (in_array($currentUser->account_type, [AccountType::BUSINESS, AccountType::ADMIN], true)) {
            $results = self::searchInfluencers($criteria, $currentUser, $perPage);

            // Transform to User models for backwards compatibility with existing cards
            // Filter out any influencers that don't have a valid user relationship
            $users = $results->getCollection()
                ->filter(fn ($influencer) => $influencer->user !== null)
                ->map(function ($influencer) {
                    $user = $influencer->user;
                    $user->setRelation('influencer', $influencer);
                    $user->distance = $influencer->distance ?? null;

                    return $user;
                })
                ->values();

            $results->setCollection($users);

            return $results;
        }

        // Fallback for influencer->business searches (keep old behavior for now)
        return self::legacySearchUsers($criteria, $currentUser, $perPage);
    }

    /**
     * Legacy search method for influencers searching for businesses.
     */
    private static function legacySearchUsers(array $criteria, User $currentUser, int $perPage = 12): LengthAwarePaginator
    {
        $query = User::query()
            ->where('account_type', AccountType::BUSINESS)
            ->where('id', '!=', $currentUser->id);

        // Filter by saved/hidden users
        $showSavedOnly = $criteria['showSavedOnly'] ?? false;
        $hideHidden = $criteria['hideHidden'] ?? true;

        if ($showSavedOnly) {
            $savedUserIds = $currentUser->savedUsers()->pluck('saved_user_id')->toArray();
            $query->whereIn('id', $savedUserIds);
        }

        if ($hideHidden) {
            $hiddenUserIds = $currentUser->hiddenUsers()->pluck('saved_user_id')->toArray();
            if (! empty($hiddenUserIds)) {
                $query->whereNotIn('id', $hiddenUserIds);
            }
        }

        // Apply search filter
        if (! empty($criteria['search'])) {
            $search = $criteria['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply location filter
        if (! empty($criteria['location'])) {
            $location = $criteria['location'];
            $radius = $criteria['searchRadius'] ?? 50;

            if (preg_match('/^\d{5}$/', $location)) {
                $nearbyZipCodes = self::getNearbyZipCodes($location, $radius);
                if (! empty($nearbyZipCodes)) {
                    $query->whereHas('currentBusiness', function ($q) use ($nearbyZipCodes) {
                        $q->whereIn('postal_code', $nearbyZipCodes);
                    });
                }
            }
        }

        // Apply niche filter
        if (! empty($criteria['selectedNiches'])) {
            $query->whereHas('currentBusiness', function ($q) use ($criteria) {
                $q->whereIn('industry', $criteria['selectedNiches']);
            });
        }

        $query->with(['currentBusiness']);

        return $query->paginate($perPage);
    }

    /**
     * Get the target account type for search (legacy).
     *
     * @deprecated
     */
    private static function getTargetAccountType(User $currentUser): AccountType
    {
        return $currentUser->account_type === AccountType::BUSINESS
            ? AccountType::INFLUENCER
            : AccountType::BUSINESS;
    }
}
