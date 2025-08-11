<?php

namespace App\Livewire\Admin\Users;

use App\Enums\AccountType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class UserIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $accountTypeFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'accountTypeFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAccountTypeFilter()
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

    public function getAccountTypeOptions()
    {
        return [
            '' => 'All Users',
            AccountType::BUSINESS->value => 'Business',
            AccountType::INFLUENCER->value => 'Influencer',
            AccountType::ADMIN->value => 'Admin',
        ];
    }

    public function render()
    {
        $users = User::query()
            ->with(['businessProfile', 'influencerProfile'])
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->accountTypeFilter, function (Builder $query) {
                $query->where('account_type', $this->accountTypeFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        return view('livewire.admin.users.user-index', [
            'users' => $users,
        ]);
    }
}
