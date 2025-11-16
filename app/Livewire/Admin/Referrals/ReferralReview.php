<?php

namespace App\Livewire\Admin\Referrals;

use App\Enums\PayoutStatus;
use App\Models\ReferralPayoutItem;
use App\Models\ReferralPayoutItemNote;
use Flux\Flux;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ReferralReview extends Component
{
    use WithPagination;

    public int $selectedMonth;

    public int $selectedYear;

    public string $search = '';

    public string $statusFilter = '';

    public array $selectedItems = [];

    public bool $selectAll = false;

    public array $expandedEnrollments = [];

    public ?int $notesModalItemId = null;

    public string $noteText = '';

    public bool $showConfirmModal = false;

    public string $confirmAction = '';

    public mixed $confirmParameter = null;

    public string $confirmTitle = '';

    public string $confirmMessage = '';

    public string $confirmButtonText = '';

    public string $confirmButtonVariant = 'primary';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'selectedMonth' => ['except' => null],
        'selectedYear' => ['except' => null],
    ];

    public function mount()
    {
        $this->selectedMonth = $this->selectedMonth ?? now()->month;
        $this->selectedYear = $this->selectedYear ?? now()->year;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectedMonth()
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updatedSelectedYear()
    {
        $this->resetPage();
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Select all items on current page
            $this->selectedItems = $this->getPayoutItems()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function toggleEnrollment($enrollmentId)
    {
        if (in_array($enrollmentId, $this->expandedEnrollments)) {
            $this->expandedEnrollments = array_diff($this->expandedEnrollments, [$enrollmentId]);
        } else {
            $this->expandedEnrollments[] = $enrollmentId;
        }
    }

    public function openNotesModal($itemId)
    {
        $this->notesModalItemId = $itemId;
        $this->noteText = '';
    }

    public function closeNotesModal()
    {
        $this->notesModalItemId = null;
        $this->noteText = '';
    }

    public function addNote()
    {
        $noteText = trim($this->noteText);

        if (empty($noteText)) {
            Flux::toast('Please enter a note.', variant: 'warning');

            return;
        }

        if (! $this->notesModalItemId) {
            return;
        }

        ReferralPayoutItemNote::create([
            'referral_payout_item_id' => $this->notesModalItemId,
            'user_id' => auth()->id(),
            'note' => $noteText,
        ]);

        // Clear the note text
        $this->noteText = '';

        Flux::toast('Note added successfully.', variant: 'success');
    }

    public function confirmApproveItem($itemId)
    {
        $item = ReferralPayoutItem::with('referral.referred')->findOrFail($itemId);

        $this->confirmAction = 'approveItem';
        $this->confirmParameter = $itemId;
        $this->confirmTitle = 'Approve Payout Item';
        $this->confirmMessage = "Are you sure you want to approve this payout item for {$item->referral->referred->name}?";
        $this->confirmButtonText = 'Approve';
        $this->confirmButtonVariant = 'primary';
        $this->showConfirmModal = true;
    }

    public function confirmDenyItem($itemId)
    {
        $item = ReferralPayoutItem::with('referral.referred')->findOrFail($itemId);

        $this->confirmAction = 'denyItem';
        $this->confirmParameter = $itemId;
        $this->confirmTitle = 'Deny Payout Item';
        $this->confirmMessage = "Are you sure you want to deny this payout item for {$item->referral->referred->name}?";
        $this->confirmButtonText = 'Deny';
        $this->confirmButtonVariant = 'danger';
        $this->showConfirmModal = true;
    }

    public function confirmApproveAllForEnrollment($enrollmentId)
    {
        $enrollment = \App\Models\ReferralEnrollment::with('user')->findOrFail($enrollmentId);
        $pendingCount = ReferralPayoutItem::where('referral_enrollment_id', $enrollmentId)
            ->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])
            ->count();

        $this->confirmAction = 'approveAllForEnrollment';
        $this->confirmParameter = $enrollmentId;
        $this->confirmTitle = 'Approve All Items';
        $this->confirmMessage = "Are you sure you want to approve all {$pendingCount} pending item(s) for {$enrollment->user->name}?";
        $this->confirmButtonText = 'Approve All';
        $this->confirmButtonVariant = 'primary';
        $this->showConfirmModal = true;
    }

    public function confirmDenyAllForEnrollment($enrollmentId)
    {
        $enrollment = \App\Models\ReferralEnrollment::with('user')->findOrFail($enrollmentId);
        $pendingCount = ReferralPayoutItem::where('referral_enrollment_id', $enrollmentId)
            ->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])
            ->count();

        $this->confirmAction = 'denyAllForEnrollment';
        $this->confirmParameter = $enrollmentId;
        $this->confirmTitle = 'Deny All Items';
        $this->confirmMessage = "Are you sure you want to deny all {$pendingCount} pending item(s) for {$enrollment->user->name}?";
        $this->confirmButtonText = 'Deny All';
        $this->confirmButtonVariant = 'danger';
        $this->showConfirmModal = true;
    }

    public function confirmBulkApprove()
    {
        if (empty($this->selectedItems)) {
            Flux::toast('Please select items to approve.', variant: 'warning');

            return;
        }

        $this->confirmAction = 'bulkApprove';
        $this->confirmParameter = null;
        $this->confirmTitle = 'Approve Selected Items';
        $this->confirmMessage = 'Are you sure you want to approve '.count($this->selectedItems).' item(s)?';
        $this->confirmButtonText = 'Approve Selected';
        $this->confirmButtonVariant = 'primary';
        $this->showConfirmModal = true;
    }

    public function confirmBulkDeny()
    {
        if (empty($this->selectedItems)) {
            Flux::toast('Please select items to deny.', variant: 'warning');

            return;
        }

        $this->confirmAction = 'bulkDeny';
        $this->confirmParameter = null;
        $this->confirmTitle = 'Deny Selected Items';
        $this->confirmMessage = 'Are you sure you want to deny '.count($this->selectedItems).' item(s)?';
        $this->confirmButtonText = 'Deny Selected';
        $this->confirmButtonVariant = 'danger';
        $this->showConfirmModal = true;
    }

    public function executeConfirmedAction()
    {
        if (! $this->confirmAction) {
            return;
        }

        // Execute the confirmed action
        if ($this->confirmParameter !== null) {
            $this->{$this->confirmAction}($this->confirmParameter);
        } else {
            $this->{$this->confirmAction}();
        }

        // Reset confirmation state
        $this->resetConfirmModal();
    }

    public function resetConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->confirmAction = '';
        $this->confirmParameter = null;
        $this->confirmTitle = '';
        $this->confirmMessage = '';
        $this->confirmButtonText = '';
        $this->confirmButtonVariant = 'primary';
    }

    public function approveItem($itemId)
    {
        $item = ReferralPayoutItem::findOrFail($itemId);

        if ($item->status !== PayoutStatus::DRAFT && $item->status !== PayoutStatus::PENDING) {
            Flux::toast('Only draft or pending items can be approved.', variant: 'danger');

            return;
        }

        $item->update(['status' => PayoutStatus::APPROVED]);

        Flux::toast('Payout item approved successfully.', variant: 'success');
    }

    public function denyItem($itemId)
    {
        $item = ReferralPayoutItem::findOrFail($itemId);

        if ($item->status !== PayoutStatus::DRAFT && $item->status !== PayoutStatus::PENDING) {
            Flux::toast('Only draft or pending items can be denied.', variant: 'danger');

            return;
        }

        $item->update(['status' => PayoutStatus::CANCELLED]);

        Flux::toast('Payout item denied.', variant: 'success');
    }

    public function approveAllForEnrollment($enrollmentId)
    {
        $updated = ReferralPayoutItem::where('referral_enrollment_id', $enrollmentId)
            ->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])
            ->update(['status' => PayoutStatus::APPROVED]);

        if ($updated === 0) {
            Flux::toast('No pending items to approve for this referrer.', variant: 'warning');

            return;
        }

        Flux::toast("{$updated} payout items approved for this referrer.", variant: 'success');
    }

    public function denyAllForEnrollment($enrollmentId)
    {
        $updated = ReferralPayoutItem::where('referral_enrollment_id', $enrollmentId)
            ->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])
            ->update(['status' => PayoutStatus::CANCELLED]);

        if ($updated === 0) {
            Flux::toast('No pending items to deny for this referrer.', variant: 'warning');

            return;
        }

        Flux::toast("{$updated} payout items denied for this referrer.", variant: 'success');
    }

    public function bulkApprove()
    {
        if (empty($this->selectedItems)) {
            Flux::toast('Please select items to approve.', variant: 'warning');

            return;
        }

        $updated = ReferralPayoutItem::whereIn('id', $this->selectedItems)
            ->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])
            ->update(['status' => PayoutStatus::APPROVED]);

        $this->selectedItems = [];
        $this->selectAll = false;

        Flux::toast("{$updated} payout items approved successfully.", variant: 'success');
    }

    public function bulkDeny()
    {
        if (empty($this->selectedItems)) {
            Flux::toast('Please select items to deny.', variant: 'warning');

            return;
        }

        $updated = ReferralPayoutItem::whereIn('id', $this->selectedItems)
            ->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])
            ->update(['status' => PayoutStatus::CANCELLED]);

        $this->selectedItems = [];
        $this->selectAll = false;

        Flux::toast("{$updated} payout items denied.", variant: 'success');
    }

    public function getStatusOptions()
    {
        return [
            '' => 'All Statuses',
            PayoutStatus::DRAFT->value => 'Draft',
            PayoutStatus::PENDING->value => 'Pending Review',
            PayoutStatus::APPROVED->value => 'Approved',
            PayoutStatus::PROCESSING->value => 'Processing',
            PayoutStatus::PAID->value => 'Paid',
            PayoutStatus::FAILED->value => 'Failed',
            PayoutStatus::CANCELLED->value => 'Cancelled',
        ];
    }

    public function getMonthOptions()
    {
        $options = [];
        for ($i = 1; $i <= 12; $i++) {
            $options[$i] = now()->month($i)->format('F');
        }

        return $options;
    }

    public function getYearOptions()
    {
        $currentYear = now()->year;
        $options = [];
        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
            $options[$i] = (string) $i;
        }

        return $options;
    }

    protected function getPayoutItems()
    {
        // Calculate the date range for the selected month
        $startDate = now()->year($this->selectedYear)->month($this->selectedMonth)->startOfMonth();
        $endDate = now()->year($this->selectedYear)->month($this->selectedMonth)->endOfMonth();

        return ReferralPayoutItem::query()
            ->with([
                'referral.referrer',
                'referral.referred',
                'enrollment.user',
                'referralPercentageHistory',
                'notes.user',
            ])
            ->whereBetween('scheduled_payout_date', [$startDate, $endDate])
            ->when($this->search, function (Builder $query) {
                $query->whereHas('enrollment.user', function (Builder $q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function (Builder $query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('scheduled_payout_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function render()
    {
        $payoutItems = $this->getPayoutItems();

        // Group payout items by enrollment
        $groupedEnrollments = $payoutItems->groupBy('referral_enrollment_id')->map(function ($items, $enrollmentId) {
            $enrollment = $items->first()->enrollment;

            return [
                'enrollment' => $enrollment,
                'items' => $items,
                'total_amount' => $items->sum('amount'),
                'total_items' => $items->count(),
                'pending_items' => $items->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])->count(),
                'approved_items' => $items->where('status', PayoutStatus::APPROVED)->count(),
            ];
        });

        // Calculate summary statistics
        $stats = [
            'total_items' => $payoutItems->total(),
            'total_amount' => ReferralPayoutItem::whereBetween(
                'scheduled_payout_date',
                [
                    now()->year($this->selectedYear)->month($this->selectedMonth)->startOfMonth(),
                    now()->year($this->selectedYear)->month($this->selectedMonth)->endOfMonth(),
                ]
            )->sum('amount'),
            'pending_count' => ReferralPayoutItem::whereBetween(
                'scheduled_payout_date',
                [
                    now()->year($this->selectedYear)->month($this->selectedMonth)->startOfMonth(),
                    now()->year($this->selectedYear)->month($this->selectedMonth)->endOfMonth(),
                ]
            )->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])->count(),
            'approved_count' => ReferralPayoutItem::whereBetween(
                'scheduled_payout_date',
                [
                    now()->year($this->selectedYear)->month($this->selectedMonth)->startOfMonth(),
                    now()->year($this->selectedYear)->month($this->selectedMonth)->endOfMonth(),
                ]
            )->where('status', PayoutStatus::APPROVED)->count(),
        ];

        // Get the current payout item for the notes modal
        $currentPayoutItem = $this->notesModalItemId
            ? ReferralPayoutItem::with(['notes.user', 'referral.referred'])->find($this->notesModalItemId)
            : null;

        return view('livewire.admin.referrals.referral-review', [
            'groupedEnrollments' => $groupedEnrollments,
            'stats' => $stats,
            'currentPayoutItem' => $currentPayoutItem,
        ]);
    }
}
