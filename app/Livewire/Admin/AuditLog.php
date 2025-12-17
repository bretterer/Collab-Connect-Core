<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog as AuditLogModel;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AuditLog extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $action = '';

    #[Url]
    public string $dateRange = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedAction(): void
    {
        $this->resetPage();
    }

    public function updatedDateRange(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function logs()
    {
        $query = AuditLogModel::query()
            ->with(['admin', 'auditable'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('admin', function ($adminQuery) {
                    $adminQuery->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })
                    ->orWhere('action', 'like', '%'.$this->search.'%')
                    ->orWhereJsonContains('metadata->user_name', $this->search)
                    ->orWhereJsonContains('metadata->reason', $this->search);
            });
        }

        if ($this->action) {
            $query->where('action', $this->action);
        }

        if ($this->dateRange !== 'all') {
            $query->where('created_at', '>=', match ($this->dateRange) {
                'today' => now()->startOfDay(),
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'quarter' => now()->subQuarter(),
                default => now()->subYears(10),
            });
        }

        return $query->paginate(25);
    }

    #[Computed]
    public function actionOptions(): array
    {
        return AuditLogModel::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action')
            ->toArray();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => AuditLogModel::count(),
            'today' => AuditLogModel::whereDate('created_at', today())->count(),
            'this_week' => AuditLogModel::where('created_at', '>=', now()->subWeek())->count(),
            'credit_grants' => AuditLogModel::where('action', 'credit.grant')->count(),
        ];
    }

    public function getActionColor(string $action): string
    {
        return match (true) {
            str_starts_with($action, 'credit.grant') => 'green',
            str_starts_with($action, 'credit.revoke') => 'red',
            str_starts_with($action, 'subscription.cancel') => 'red',
            str_starts_with($action, 'subscription.') => 'blue',
            str_starts_with($action, 'user.') => 'purple',
            str_starts_with($action, 'coupon.') => 'yellow',
            default => 'zinc',
        };
    }

    public function render()
    {
        return view('livewire.admin.audit-log');
    }
}
