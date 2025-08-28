<?php

namespace App\Livewire;

use App\Enums\FeedbackType;
use App\Models\Feedback;
use Livewire\Attributes\Validate;
use Livewire\Component;

class FeedbackWidget extends Component
{
    public bool $showModal = false;

    #[Validate('required|in:bug_report,feature_request,general_feedback')]
    public string $type = '';

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|max:2000')]
    public string $message = '';

    public string $currentUrl = '';

    public function mount()
    {
        // Set default type
        $this->type = FeedbackType::BUG_REPORT->value;
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->dispatch('open-feedback-modal');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['type', 'subject', 'message', 'currentUrl']);
    }

    public function submit()
    {
        $this->validate();

        try {

            // Collect browser info
            $browserInfo = [
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'referrer' => request()->header('referer'),
            ];

            // Collect session data (only non-sensitive data)
            $sessionData = [
                'current_url' => url()->current(),
                'previous_url' => url()->previous(),
                'timestamp' => now()->toISOString(),
            ];

            Feedback::create([
                'user_id' => auth()->id(),
                'type' => $this->type,
                'subject' => $this->subject,
                'message' => $this->message,
                'url' => $this->currentUrl ?: url()->current(),
                'browser_info' => $browserInfo,
                'screenshot_path' => null,
                'session_data' => $sessionData,
            ]);

            session()->flash('banner', [
                'type' => 'success',
                'message' => 'Thank you for your feedback! We\'ll review it shortly.',
                'closable' => true,
            ]);

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('banner', [
                'type' => 'error',
                'message' => 'There was an error submitting your feedback. Please try again.',
                'closable' => true,
            ]);
        }
    }

    public function getFeedbackTypes()
    {
        return FeedbackType::toOptions();
    }

    public function render()
    {
        return view('livewire.feedback-widget');
    }
}
