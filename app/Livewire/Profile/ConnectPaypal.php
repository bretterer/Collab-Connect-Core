<?php

namespace App\Livewire\Profile;

use App\Models\ReferralEnrollment;
use App\Services\PayPalPayoutsService;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ConnectPaypal extends Component
{
    public ?ReferralEnrollment $enrollment = null;

    #[Validate('required|email')]
    public string $paypalEmail = '';

    public bool $showConnectForm = false;

    public function mount(): void
    {
        $this->enrollment = auth()->user()->referralEnrollment;

        if ($this->enrollment && $this->enrollment->paypal_email) {
            $this->paypalEmail = $this->enrollment->paypal_email;
        }
    }

    public function connectPayPal(): void
    {
        if (! $this->enrollment) {
            $this->addError('enrollment', 'You must be enrolled in the referral program first.');

            return;
        }

        $this->validate();

        $paypalService = app(PayPalPayoutsService::class);
        $success = $paypalService->linkPayPalAccount($this->enrollment, $this->paypalEmail);

        if ($success) {
            $this->showConnectForm = false;
            $this->enrollment->refresh();

            Flux::toast(
                heading: 'PayPal Connected',
                text: 'Your PayPal account has been connected successfully!',
                variant: 'success',
            );
        } else {
            $this->addError('paypalEmail', 'Failed to verify PayPal email. Please check the format and try again.');
        }
    }

    public function disconnectPayPal(): void
    {
        if (! $this->enrollment) {
            return;
        }

        $paypalService = app(PayPalPayoutsService::class);
        $success = $paypalService->disconnectPayPalAccount($this->enrollment);

        if ($success) {
            $this->enrollment->refresh();
            $this->paypalEmail = '';

            Flux::toast(
                heading: 'PayPal Disconnected',
                text: 'Your PayPal account has been disconnected.',
                variant: 'success',
            );
        } else {
            $this->addError('disconnect', 'Failed to disconnect PayPal account.');
        }
    }

    public function toggleConnectForm(): void
    {
        $this->showConnectForm = ! $this->showConnectForm;
        $this->reset('paypalEmail');
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.profile.connect-paypal');
    }
}
