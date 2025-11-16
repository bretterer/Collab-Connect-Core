<?php

namespace App\Livewire\Admin\Referrals;

use App\Enums\PayoutStatus;
use App\Models\ReferralPayout;
use App\Services\PayPalPayoutsService;
use Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PayoutManagement extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $monthFilter = '';

    public array $selectedPayouts = [];

    public bool $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'monthFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingMonthFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all payouts on current page that are not PAID
            $this->selectedPayouts = $this->getPayoutsQuery()
                ->where('status', '!=', PayoutStatus::PAID)
                ->pluck('id')
                ->toArray();
        } else {
            $this->selectedPayouts = [];
        }
    }

    protected function getPayoutsQuery(): Builder
    {
        return ReferralPayout::query()
            ->with(['enrollment.user'])
            ->when($this->search, function (Builder $query) {
                $query->whereHas('enrollment.user', function (Builder $q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->monthFilter, function (Builder $query) {
                // Format: YYYY-MM
                [$year, $month] = explode('-', $this->monthFilter);
                $query->where('year', $year)->where('month', $month);
            })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->orderBy('created_at', 'desc');
    }

    public function retriggerPayouts()
    {
        if (empty($this->selectedPayouts)) {
            Flux::toast('Please select at least one payout to retrigger.', variant: 'danger');

            return;
        }

        try {
            $payouts = ReferralPayout::query()
                ->with('enrollment')
                ->whereIn('id', $this->selectedPayouts)
                ->where('status', '!=', PayoutStatus::PAID)
                ->get();

            if ($payouts->isEmpty()) {
                Flux::toast('No valid payouts selected.', variant: 'danger');

                return;
            }

            // Verify all enrollments have PayPal connected
            $missingPayPal = $payouts->filter(function ($payout) {
                return ! $payout->enrollment->hasPayPalConnected();
            });

            if ($missingPayPal->count() > 0) {
                Flux::toast(
                    "Cannot retrigger: {$missingPayPal->count()} payout(s) have missing PayPal information.",
                    variant: 'danger'
                );

                return;
            }

            // Create batch payout using PayPal service
            $paypalService = app(PayPalPayoutsService::class);
            $result = $paypalService->createBatchPayout($payouts->all());

            if (! $result) {
                Flux::toast('Failed to create batch payout. Check logs for details.', variant: 'danger');

                return;
            }

            // Update all payouts with batch info
            $batchId = $result['batch_header']['payout_batch_id'];

            foreach ($payouts as $payout) {
                $payout->update([
                    'status' => PayoutStatus::PROCESSING,
                    'paypal_batch_id' => $batchId,
                    'processed_at' => now(),
                ]);
            }

            // Clear selection
            $this->selectedPayouts = [];
            $this->selectAll = false;

            Flux::toast(
                "Successfully triggered batch payout for {$payouts->count()} payout(s). Batch ID: {$batchId}",
                variant: 'success'
            );
        } catch (\Exception $e) {
            \Log::error('Batch payout retrigger failed', [
                'error' => $e->getMessage(),
                'selected_payouts' => $this->selectedPayouts,
            ]);

            Flux::toast('An error occurred while retriggering payouts: '.$e->getMessage(), variant: 'danger');
        }
    }

    public function getMonthOptions(): array
    {
        $months = ReferralPayout::query()
            ->select('year', 'month')
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->mapWithKeys(function ($payout) {
                $value = sprintf('%04d-%02d', $payout->year, $payout->month);
                $label = now()->month($payout->month)->year($payout->year)->format('F Y');

                return [$value => $label];
            })
            ->toArray();

        return ['' => 'All Months'] + $months;
    }

    public function render()
    {
        $payouts = $this->getPayoutsQuery()->paginate(20);

        $stats = [
            'total_payouts' => ReferralPayout::count(),
            'pending_amount' => ReferralPayout::where('status', PayoutStatus::PENDING)->sum('amount'),
            'failed_count' => ReferralPayout::where('status', PayoutStatus::FAILED)->count(),
            'paid_amount' => ReferralPayout::where('status', PayoutStatus::PAID)->sum('amount'),
        ];

        // Convert PayoutStatus::toOptions() to simple key-value array
        $statusOptions = collect(PayoutStatus::toOptions())
            ->mapWithKeys(fn ($option) => [$option['value'] => $option['label']])
            ->toArray();

        return view('livewire.admin.referrals.payout-management', [
            'payouts' => $payouts,
            'stats' => $stats,
            'statusOptions' => ['' => 'All Status'] + $statusOptions,
            'monthOptions' => $this->getMonthOptions(),
        ]);
    }
}
