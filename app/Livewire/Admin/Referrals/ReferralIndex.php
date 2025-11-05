<?php

namespace App\Livewire\Admin\Referrals;

use App\Enums\PayoutStatus;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPayout;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ReferralIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $paypalStatusFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'paypalStatusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPaypalStatusFilter()
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

    public function getPaypalStatusOptions()
    {
        return [
            '' => 'All Status',
            'connected' => 'PayPal Connected',
            'not_connected' => 'Not Connected',
            'needs_verification' => 'Needs Verification',
        ];
    }

    public function getStats()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return [
            'total_enrolled' => ReferralEnrollment::count(),
            'total_active_referrals' => \App\Models\Referral::active()->count(),
            'pending_payouts_this_month' => ReferralPayout::where('month', $currentMonth)
                ->where('year', $currentYear)
                ->where('status', PayoutStatus::PENDING)
                ->sum('amount'),
            'total_paid_out' => ReferralPayout::where('status', PayoutStatus::PAID)
                ->sum('amount'),
        ];
    }

    public function render()
    {
        $enrollments = ReferralEnrollment::query()
            ->with(['user', 'referrals'])
            ->when($this->search, function (Builder $query) {
                $query->whereHas('user', function (Builder $q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->paypalStatusFilter, function (Builder $query) {
                match ($this->paypalStatusFilter) {
                    'connected' => $query->where('paypal_verified', true)->whereNotNull('paypal_email'),
                    'not_connected' => $query->whereNull('paypal_email'),
                    'needs_verification' => $query->whereNotNull('paypal_email')->where('paypal_verified', false),
                    default => $query,
                };
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(20);

        return view('livewire.admin.referrals.referral-index', [
            'enrollments' => $enrollments,
            'stats' => $this->getStats(),
        ]);
    }
}
