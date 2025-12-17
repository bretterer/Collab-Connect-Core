<?php

namespace App\Livewire\Admin\Businesses;

use App\Enums\BusinessIndustry;
use App\Models\Business;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BusinessIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $industryFilter = '';

    public string $subscriptionFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'industryFilter' => ['except' => ''],
        'subscriptionFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingIndustryFilter(): void
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

    public function getIndustryOptions(): array
    {
        $options = ['' => 'All Industries'];

        foreach (BusinessIndustry::cases() as $industry) {
            $options[$industry->value] = $industry->label();
        }

        return $options;
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
        $businesses = Business::query()
            ->with(['owner', 'subscriptions'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('city', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->industryFilter, function (Builder $query) {
                $query->where('industry', $this->industryFilter);
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

        return view('livewire.admin.businesses.business-index', [
            'businesses' => $businesses,
        ]);
    }
}
