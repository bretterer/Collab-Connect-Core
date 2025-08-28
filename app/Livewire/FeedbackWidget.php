<?php

namespace App\Livewire;

use App\Enums\FeedbackType;
use App\Models\Feedback;
use Illuminate\Support\Facades\Storage;
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

    public ?string $screenshotData = null;

    public function mount()
    {
        // Set default type
        $this->type = FeedbackType::BUG_REPORT->value;
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['type', 'subject', 'message', 'screenshotData']);
    }

    public function submit()
    {
        $this->validate();

        try {
            $screenshotPath = null;

            // Handle screenshot if provided
            if ($this->screenshotData) {
                $screenshotPath = $this->saveScreenshot($this->screenshotData);
            }

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
                'url' => url()->current(),
                'browser_info' => $browserInfo,
                'screenshot_path' => $screenshotPath,
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

    private function saveScreenshot(string $screenshotData): string
    {
        // Extract base64 data (remove data:image/png;base64, prefix)
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $screenshotData);
        $imageData = base64_decode($imageData);

        $filename = 'feedback/screenshots/'.auth()->id().'_'.time().'.png';

        Storage::disk('local')->put($filename, $imageData);

        return $filename;
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
