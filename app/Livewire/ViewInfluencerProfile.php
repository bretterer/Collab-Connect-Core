<?php

namespace App\Livewire;

use App\Enums\AccountType;
use App\Models\User;
use App\Services\ReviewService;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ViewInfluencerProfile extends Component
{
    public User $user;

    public function mount($username)
    {
        // Find user by  influencer username, or ID
        $this->user = User::WhereHas('influencer', function ($query) use ($username) {
            $query->where('username', $username);
        })
            ->orWhere('id', $username)
            ->with(['influencer'])
            ->firstOrFail();

        // Track ViewContent for influencer profile viewing
        MetaPixel::track('ViewContent', [
            'content_type' => 'influencer_profile',
            'content_ids' => [$this->user->influencer?->id ?? $this->user->id],
            'content_name' => $this->user->name,
        ]);
    }

    public function render()
    {
        // Determine which profile view to show based on account type
        if ($this->user->account_type === AccountType::INFLUENCER) {
            $reviewService = app(ReviewService::class);

            return view('livewire.profiles.influencer-profile', [
                'user' => $this->user,
                'averageRating' => $reviewService->getAverageRating($this->user),
                'reviewCount' => $reviewService->getReviewCount($this->user),
                'reviews' => $reviewService->getReviewsForUser($this->user),
            ]);
        }
        abort(404, 'Profile not found');
    }
}
