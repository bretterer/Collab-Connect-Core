<?php

namespace App\Http\Controllers;

use App\Models\Influencer;
use App\Services\LinkInBioAnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LinkInBioTrackingController extends Controller
{
    public function __construct(
        private LinkInBioAnalyticsService $analyticsService
    ) {}

    /**
     * Track a page view for a Link-in-Bio page.
     */
    public function trackView(string $username, Request $request): JsonResponse
    {
        $influencer = Influencer::where('username', $username)->first();

        if (! $influencer) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $settings = $influencer->linkInBioSettings;

        if (! $settings || ! $settings->is_published) {
            return response()->json(['status' => 'ignored'], 200);
        }

        // Skip owner views
        if (auth()->check() && auth()->user()->influencer?->id === $influencer->id) {
            return response()->json(['status' => 'owner'], 200);
        }

        $view = $this->analyticsService->recordView($settings, $request);

        return response()->json([
            'status' => $view ? 'recorded' : 'rate_limited',
        ], 200);
    }

    /**
     * Track a link click on a Link-in-Bio page.
     */
    public function trackClick(string $username, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'link_index' => 'required|integer|min:0',
            'link_title' => 'required|string|max:255',
            'link_url' => 'required|url|max:2048',
        ]);

        $influencer = Influencer::where('username', $username)->first();

        if (! $influencer) {
            return response()->json(['status' => 'not_found'], 404);
        }

        $settings = $influencer->linkInBioSettings;

        if (! $settings || ! $settings->is_published) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $click = $this->analyticsService->recordClick(
            $settings,
            $validated['link_index'],
            $validated['link_title'],
            $validated['link_url'],
            $request
        );

        return response()->json([
            'status' => $click ? 'recorded' : 'ignored',
        ], 200);
    }
}
