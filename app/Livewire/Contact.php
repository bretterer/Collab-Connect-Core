<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
class Contact extends BaseComponent
{
    #[Validate('required|string|max:255')]
    public $subject = '';

    #[Validate('required|string|max:5000')]
    public $message = '';

    #[Validate('nullable|string|in:bug,feature,general,billing,account')]
    public $category = 'general';

    public $isSubmitting = false;

    public function mount()
    {
        // Pre-fill user information if authenticated
        /** @var User $user */
        $user = $this->getAuthenticatedUser();
    }

    public function sendMessage()
    {
        $this->isSubmitting = true;

        $this->validate();

        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        $supportEmail = config('collabconnect.support_email');

        try {
            Mail::send('emails.contact', [
                'user' => $user,
                'subject' => $this->subject,
                'messageContent' => $this->message,
                'category' => $this->category,
                'categoryLabel' => $this->getCategoryLabel(),
                'responseTime' => config('collabconnect.support_response_days'),
            ], function ($mail) use ($user, $supportEmail) {
                $mail->from($user->email, $user->name)
                    ->to($supportEmail)
                    ->subject('[CollabConnect Support] '.$this->subject)
                    ->replyTo($user->email, $user->name);
            });

            $this->flashSuccess('Your message has been sent successfully! We\'ll get back to you soon.');

            // Clear the form
            $this->reset(['subject', 'message', 'category']);

        } catch (\Exception $e) {
            $this->flashError('Sorry, there was an error sending your message. Please try again later.');
        } finally {
            $this->isSubmitting = false;
        }
    }

    private function getCategoryLabel(): string
    {
        return match ($this->category) {
            'bug' => 'Bug Report',
            'feature' => 'Feature Request',
            'general' => 'General Inquiry',
            'billing' => 'Billing Question',
            'account' => 'Account Issue',
            default => 'General Inquiry',
        };
    }

    public function render()
    {
        return view('livewire.contact', [
            'categories' => [
                'general' => 'General Inquiry',
                'bug' => 'Bug Report',
                'feature' => 'Feature Request',
                'billing' => 'Billing Question',
                'account' => 'Account Issue',
            ],
        ]);
    }
}
