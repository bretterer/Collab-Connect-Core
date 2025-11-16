<?php

namespace App\Http\Controllers;

use App\Models\LandingPage;

class LandingPageController extends Controller
{
    public function show(string $slug)
    {
        $page = LandingPage::where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        return view('landing-pages.show', compact('page'));
    }
}
