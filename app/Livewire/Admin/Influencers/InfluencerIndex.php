<?php

namespace App\Livewire\Admin\Influencers;

use App\Models\Influencer;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class InfluencerIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $subscriptionFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'subscriptionFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSubscriptionFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getSubscriptionOptions(): array
    {
        return [
            '' => 'All Subscriptions',
            'active' => 'Active',
            'trialing' => 'Trialing',
            'canceled' => 'Canceled',
            'none' => 'No Subscription',
        ];
    }

    public function render()
    {
        $influencers = Influencer::query()
            ->with(['user', 'subscriptions'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->whereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    })
                        ->orWhere('city', 'like', '%'.$this->search.'%')
                        ->orWhere('bio', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->subscriptionFilter, function (Builder $query) {
                match ($this->subscriptionFilter) {
                    'active' => $query->whereHas('subscriptions', fn ($q) => $q->where('stripe_status', 'active')),
                    'trialing' => $query->whereHas('subscriptions', fn ($q) => $q->where('stripe_status', 'trialing')),
                    'canceled' => $query->whereHas('subscriptions', fn ($q) => $q->where('stripe_status', 'canceled')),
                    'none' => $query->whereDoesntHave('subscriptions'),
                    default => null,
                };
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        return view('livewire.admin.influencers.influencer-index', [
            'influencers' => $influencers,
        ]);
    }
}
