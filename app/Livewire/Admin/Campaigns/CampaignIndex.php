<?php

namespace App\Livewire\Admin\Campaigns;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CampaignIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getStatusOptions()
    {
        return [
            '' => 'All Campaigns',
            CampaignStatus::DRAFT->value => 'Draft',
            CampaignStatus::PUBLISHED->value => 'Published',
            CampaignStatus::SCHEDULED->value => 'Scheduled',
            CampaignStatus::ARCHIVED->value => 'Archived',
        ];
    }

    public function render()
    {
        $campaigns = Campaign::query()
            ->with(['user.businessProfile', 'applications'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('campaign_goal', 'like', '%'.$this->search.'%')
                        ->orWhere('campaign_description', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function (Builder $userQuery) {
                            $userQuery->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        return view('livewire.admin.campaigns.campaign-index', [
            'campaigns' => $campaigns,
        ]);
    }
}
