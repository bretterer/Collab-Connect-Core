<?php

namespace App\Livewire\Admin\Referrals;

use App\Enums\PercentageChangeType;
use App\Models\ReferralEnrollment;
use App\Models\ReferralPercentageHistory;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ManagePercentages extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingEnrollmentId = null;

    public ?int $viewingHistoryEnrollmentId = null;

    public ?int $newPercentage = 10;

    public string $changeType = '';

    public ?string $expiresAt = null;

    public ?int $monthsRemaining = null;

    public string $reason = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->changeType = PercentageChangeType::PERMANENT->value;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openEditModal($enrollmentId)
    {
        $enrollment = ReferralEnrollment::with('percentageHistory')->findOrFail($enrollmentId);

        $this->editingEnrollmentId = $enrollmentId;
        $this->newPercentage = $enrollment->currentReferralPercentage() ?? 10;
        $this->changeType = PercentageChangeType::PERMANENT->value;
        $this->expiresAt = null;
        $this->monthsRemaining = null;
        $this->reason = '';
    }

    public function closeEditModal()
    {
        $this->editingEnrollmentId = null;
        $this->resetForm();
    }

    public function openHistoryModal($enrollmentId)
    {
        $this->viewingHistoryEnrollmentId = $enrollmentId;
    }

    public function closeHistoryModal()
    {
        $this->viewingHistoryEnrollmentId = null;
    }

    public function updatePercentage()
    {
        $this->validate([
            'newPercentage' => ['required', 'integer', 'min:0', 'max:100'],
            'changeType' => ['required', 'in:'.implode(',', array_map(fn ($case) => $case->value, PercentageChangeType::cases()))],
            'expiresAt' => ['nullable', 'required_if:changeType,'.PercentageChangeType::TEMPORARY_DATE->value, 'date', 'after:today'],
            'monthsRemaining' => ['nullable', 'required_if:changeType,'.PercentageChangeType::TEMPORARY_MONTHS->value, 'integer', 'min:1', 'max:120'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $enrollment = ReferralEnrollment::findOrFail($this->editingEnrollmentId);

        $oldPercentage = $enrollment->currentReferralPercentage() ?? 0;

        // Create new percentage history entry
        ReferralPercentageHistory::create([
            'referral_enrollment_id' => $enrollment->id,
            'old_percentage' => $oldPercentage,
            'new_percentage' => $this->newPercentage,
            'change_type' => $this->changeType,
            'expires_at' => $this->changeType === PercentageChangeType::TEMPORARY_DATE->value ? $this->expiresAt : null,
            'months_remaining' => $this->changeType === PercentageChangeType::TEMPORARY_MONTHS->value ? $this->monthsRemaining : null,
            'reason' => $this->reason,
            'changed_by_user_id' => auth()->id(),
        ]);

        Flux::toast('Referral percentage updated successfully.', variant: 'success');

        $this->closeEditModal();
    }

    protected function resetForm()
    {
        $this->newPercentage = 10;
        $this->changeType = PercentageChangeType::PERMANENT->value;
        $this->expiresAt = null;
        $this->monthsRemaining = null;
        $this->reason = '';
    }

    public function getChangeTypeOptions()
    {
        return collect(PercentageChangeType::cases())
            ->filter(fn ($type) => $type !== PercentageChangeType::ENROLLMENT)
            ->mapWithKeys(fn ($type) => [$type->value => $type->label()])
            ->toArray();
    }

    protected function getEnrollments()
    {
        return ReferralEnrollment::query()
            ->with(['user', 'percentageHistory' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->when($this->search, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function render()
    {
        $enrollments = $this->getEnrollments();

        // Get current enrollment for edit modal
        $currentEnrollment = $this->editingEnrollmentId
            ? ReferralEnrollment::with('user')->find($this->editingEnrollmentId)
            : null;

        // Get enrollment for history modal
        $historyEnrollment = $this->viewingHistoryEnrollmentId
            ? ReferralEnrollment::with(['user', 'percentageHistory.changedBy'])->find($this->viewingHistoryEnrollmentId)
            : null;

        return view('livewire.admin.referrals.manage-percentages', [
            'enrollments' => $enrollments,
            'currentEnrollment' => $currentEnrollment,
            'historyEnrollment' => $historyEnrollment,
        ]);
    }
}
