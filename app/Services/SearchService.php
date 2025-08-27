<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Models\PostalCode;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchService
{
    /**
     * Search for users based on criteria
     */
    public static function searchUsers(array $criteria, User $currentUser, int $perPage = 12): LengthAwarePaginator
    {
        $targetAccountType = self::getTargetAccountType($currentUser);
        $query = self::buildBaseQuery($currentUser, $targetAccountType);

        // Apply filters
        $query = self::applySearchFilter($query, $criteria['search'] ?? '');
        $query = self::applyLocationFilter($query, $criteria, $targetAccountType);
        $query = self::applyNicheFilter($query, $criteria['selectedNiches'] ?? [], $targetAccountType);
        $query = self::applyPlatformFilter($query, $criteria['selectedPlatforms'] ?? [], $targetAccountType);
        $query = self::applyFollowerFilter($query, $criteria, $targetAccountType);
        $query = self::applyEngagementFilter($query, $criteria, $targetAccountType);
        $query = self::applyPriceFilter($query, $criteria, $targetAccountType);
        $query = self::applyQualityFilter($query, $criteria, $targetAccountType);
        $query = self::applySorting($query, $criteria['sortBy'] ?? 'relevance', $targetAccountType);

        // Load relationships
        $query = self::loadRelationships($query, $targetAccountType);

        $results = $query->paginate($perPage);

        // Handle proximity search calculations
        if (! empty($criteria['location'])) {
            $results = self::handleProximitySearch($results, $criteria);
        }

        return $results;
    }

    /**
     * Get the target account type for search
     */
    private static function getTargetAccountType(User $currentUser): AccountType
    {
        return $currentUser->account_type === AccountType::BUSINESS
            ? AccountType::INFLUENCER
            : AccountType::BUSINESS;
    }

    /**
     * Build the base query
     */
    private static function buildBaseQuery(User $currentUser, AccountType $targetAccountType): Builder
    {
        return User::query()->where('account_type', $targetAccountType)
            ->where('id', '!=', $currentUser->id);
    }

    /**
     * Apply search filter
     */
    private static function applySearchFilter(Builder $query, string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%');
        });
    }

    /**
     * Apply location filter
     */
    private static function applyLocationFilter(Builder $query, array $criteria, AccountType $targetAccountType): Builder
    {
        $location = $criteria['location'] ?? '';

        if (empty($location)) {
            return $query;
        }

        $nearbyZipCodes = self::getNearbyZipCodes($location, $criteria['searchRadius'] ?? 50);

        if (! empty($nearbyZipCodes)) {
            // Use proximity search
            $relationshipMethod = $targetAccountType === AccountType::INFLUENCER ? 'influencer' : 'currentBusiness';
            $zipCodeColumn = 'postal_code';

            return $query->whereHas($relationshipMethod, function ($q) use ($nearbyZipCodes, $zipCodeColumn) {
                $q->whereIn($zipCodeColumn, $nearbyZipCodes);
            });
        } else {
            // Fallback to text search
            $relationshipMethod = $targetAccountType === AccountType::INFLUENCER ? 'influencer' : 'currentBusiness';
            $zipCodeColumn = 'postal_code';

            return $query->whereHas($relationshipMethod, function ($q) use ($location, $zipCodeColumn) {
                $q->where($zipCodeColumn, 'like', '%'.$location.'%');
            });
        }
    }

    /**
     * Get nearby zip codes for proximity search
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
     * Apply niche filter
     */
    private static function applyNicheFilter(Builder $query, array $selectedNiches, AccountType $targetAccountType): Builder
    {
        if (empty($selectedNiches)) {
            return $query;
        }

        $relationshipMethod = $targetAccountType === AccountType::INFLUENCER ? 'influencer' : 'currentBusiness';
        $nicheColumn = $targetAccountType === AccountType::INFLUENCER ? 'content_types' : 'industry';

        return $query->whereHas($relationshipMethod, function ($q) use ($selectedNiches, $nicheColumn) {
            // Wrap the multiple `whereJsonContains` conditions in a closure
            $q->where(function ($query) use ($selectedNiches, $nicheColumn) {
                foreach ($selectedNiches as $niche) {
                    // Use `orWhereJsonContains` for each niche to create an OR condition
                    $query->orWhereJsonContains($nicheColumn, $niche);
                }
            });
        });
    }

    /**
     * Apply platform filter (influencers only)
     */
    private static function applyPlatformFilter(Builder $query, array $selectedPlatforms, AccountType $targetAccountType): Builder
    {
        if (empty($selectedPlatforms) || $targetAccountType !== AccountType::INFLUENCER) {
            return $query;
        }

        return $query->whereHas('socialMediaAccounts', function ($q) use ($selectedPlatforms) {
            $q->whereIn('platform', $selectedPlatforms);
        });
    }

    /**
     * Apply follower count filter (influencers only)
     */
    private static function applyFollowerFilter(Builder $query, array $criteria, AccountType $targetAccountType): Builder
    {
        if ($targetAccountType !== AccountType::INFLUENCER) {
            return $query;
        }

        $minFollowers = $criteria['minFollowers'] ?? null;
        $maxFollowers = $criteria['maxFollowers'] ?? null;

        if (! $minFollowers && ! $maxFollowers) {
            return $query;
        }

        return $query->whereHas('socialMediaAccounts', function ($q) use ($minFollowers, $maxFollowers) {
            if ($minFollowers) {
                $q->where('follower_count', '>=', $minFollowers);
            }
            if ($maxFollowers) {
                $q->where('follower_count', '<=', $maxFollowers);
            }
        });
    }

    /**
     * Apply sorting
     */
    /**
     * Apply engagement rate filter (influencers only)
     */
    private static function applyEngagementFilter(Builder $query, array $criteria, AccountType $targetAccountType): Builder
    {
        if ($targetAccountType !== AccountType::INFLUENCER) {
            return $query;
        }

        $minEngagement = $criteria['minEngagementRate'] ?? null;
        $maxEngagement = $criteria['maxEngagementRate'] ?? null;

        if (! $minEngagement && ! $maxEngagement) {
            return $query;
        }

        return $query->whereHas('socialMediaAccounts', function ($q) use ($minEngagement, $maxEngagement) {
            if ($minEngagement) {
                $q->where('engagement_rate', '>=', $minEngagement);
            }
            if ($maxEngagement) {
                $q->where('engagement_rate', '<=', $maxEngagement);
            }
        });
    }

    /**
     * Apply price range filter (influencers only)
     */
    private static function applyPriceFilter(Builder $query, array $criteria, AccountType $targetAccountType): Builder
    {
        if ($targetAccountType !== AccountType::INFLUENCER) {
            return $query;
        }

        $minPrice = $criteria['minPriceRange'] ?? null;
        $maxPrice = $criteria['maxPriceRange'] ?? null;

        if (! $minPrice && ! $maxPrice) {
            return $query;
        }

        return $query->whereHas('influencer', function ($q) use ($minPrice, $maxPrice) {
            if ($minPrice) {
                $q->where('min_rate', '>=', $minPrice);
            }
            if ($maxPrice) {
                $q->where('max_rate', '<=', $maxPrice);
            }
        });
    }

    /**
     * Apply content quality filter (influencers only)
     */
    private static function applyQualityFilter(Builder $query, array $criteria, AccountType $targetAccountType): Builder
    {
        if ($targetAccountType !== AccountType::INFLUENCER || empty($criteria['contentQuality'])) {
            return $query;
        }

        return $query->whereHas('influencer', function ($q) use ($criteria) {
            $q->where('content_quality_score', '>=', $criteria['contentQuality']);
        });
    }

    private static function applySorting(Builder $query, string $sortBy, AccountType $targetAccountType): Builder
    {
        return match ($sortBy) {
            'name' => $query->orderBy('name'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'followers' => $targetAccountType === AccountType::INFLUENCER
                ? $query->leftJoin('social_media_accounts', 'users.id', '=', 'social_media_accounts.user_id')
                    ->orderBy('social_media_accounts.follower_count', 'desc')
                    ->select('users.*')
                : $query->orderBy('id'),
            'engagement' => $targetAccountType === AccountType::INFLUENCER
                ? $query->leftJoin('social_media_accounts', 'users.id', '=', 'social_media_accounts.user_id')
                    ->orderBy('social_media_accounts.engagement_rate', 'desc')
                    ->select('users.*')
                : $query->orderBy('id'),
            'quality' => $targetAccountType === AccountType::INFLUENCER
                ? $query->leftJoin('influencers', 'users.id', '=', 'influencers.user_id')
                    ->orderBy('influencers.content_quality_score', 'desc')
                    ->select('users.*')
                : $query->orderBy('id'),
            'relevance' => $query->orderBy('created_at', 'desc'), // Default relevance sorting
            'distance' => $query->orderBy('id'), // Will be handled after pagination
            default => $query->orderBy('created_at', 'desc'),
        };
    }

    /**
     * Load relationships based on account type
     */
    private static function loadRelationships(Builder $query, AccountType $targetAccountType): Builder
    {
        if ($targetAccountType === AccountType::INFLUENCER) {
            return $query->with([
                'influencer',
                'socialMediaAccounts',
                'influencer.postalCodeInfo' => function($query) {
                    $query->select(['postal_code', 'place_name', 'admin_name1', 'admin_code1']);
                }
            ]);
        } else {
            return $query->with(['currentBusiness']);
        }
    }

    /**
     * Handle proximity search calculations
     */
    private static function handleProximitySearch(LengthAwarePaginator $results, array $criteria): LengthAwarePaginator
    {
        $location = $criteria['location'] ?? '';
        $sortBy = $criteria['sortBy'] ?? 'name';

        if (! preg_match('/^\d{5}$/', $location)) {
            return $results;
        }

        $searchPostalCode = PostalCode::where('postal_code', $location)
            ->where('country_code', 'US')
            ->first();

        if (! $searchPostalCode) {
            return $results;
        }

        // Calculate distances
        $results->getCollection()->transform(function ($user) use ($searchPostalCode) {
            $userPostalCode = $user->getPostalCodeInfo();
            $user->distance = $userPostalCode ? $searchPostalCode->distanceTo($userPostalCode) : null;

            return $user;
        });

        // Sort by distance if requested
        if ($sortBy === 'distance') {
            $sorted = $results->getCollection()->sortBy(function ($user) {
                return $user->distance ?? 9999; // Put users without distance at the end
            })->values();

            $results->setCollection($sorted);
        }

        return $results;
    }

    /**
     * Get search metadata
     */
    public static function getSearchMetadata(array $criteria, User $currentUser): array
    {
        $targetAccountType = self::getTargetAccountType($currentUser);
        $location = $criteria['location'] ?? '';
        $nearbyZipCodes = [];
        $searchPostalCode = null;
        $isProximitySearch = false;

        if (! empty($location) && preg_match('/^\d{5}$/', $location)) {
            $searchPostalCode = PostalCode::where('postal_code', $location)
                ->where('country_code', 'US')
                ->first();

            if ($searchPostalCode) {
                $nearbyZipCodes = self::getNearbyZipCodes($location, $criteria['searchRadius'] ?? 50);
                $isProximitySearch = ! empty($nearbyZipCodes);
            }
        }

        return [
            'targetAccountType' => $targetAccountType,
            'searchingFor' => $targetAccountType === AccountType::INFLUENCER ? 'influencers' : 'businesses',
            'searchPostalCode' => $searchPostalCode,
            'isProximitySearch' => $isProximitySearch,
            'nearbyZipCodes' => $nearbyZipCodes,
        ];
    }
}
