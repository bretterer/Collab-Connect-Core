<?php

namespace App\Livewire\Admin\Users\Modals;

use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use Flux\Flux;
use Laravel\Cashier\Cashier;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class ApplyCouponModal extends Component
{
    public ?int $userId = null;

    public string $couponCode = '';

    public bool $isProcessing = false;

    #[On('open-apply-coupon-modal')]
    public function open(int $userId): void
    {
        $this->userId = $userId;
        $this->couponCode = '';
        Flux::modal('apply-coupon-modal')->show();
    }

    #[Computed]
    public function user(): ?User
    {
        return $this->userId ? User::find($this->userId) : null;
    }

    #[Computed]
    public function billable(): Business|Influencer|null
    {
        $user = $this->user;

        if (! $user) {
            return null;
        }

        if ($user->isBusinessAccount()) {
            return $user->businesses()
                ->wherePivot('role', 'owner')
                ->first();
        }

        if ($user->isInfluencerAccount() && $user->influencer) {
            return $user->influencer;
        }

        return null;
    }

    #[Computed]
    public function availableCoupons(): array
    {
        try {
            $coupons = Cashier::stripe()->coupons->all(['limit' => 100]);

            return collect($coupons->data)
                ->filter(fn ($coupon) => $coupon->valid)
                ->map(function ($coupon) {
                    $discount = $coupon->percent_off
                        ? "{$coupon->percent_off}% off"
                        : '$'.number_format($coupon->amount_off / 100, 2).' off';

                    $duration = match ($coupon->duration) {
                        'forever' => 'Forever',
                        'once' => 'One-time',
                        'repeating' => "{$coupon->duration_in_months} months",
                        default => $coupon->duration,
                    };

                    return [
                        'id' => $coupon->id,
                        'name' => $coupon->name ?? $coupon->id,
                        'discount' => $discount,
                        'duration' => $duration,
                        'label' => ($coupon->name ?? $coupon->id)." ({$discount} - {$duration})",
                    ];
                })
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    public function applyCoupon(): void
    {
        $this->validate([
            'couponCode' => 'required|string',
        ]);

        try {
            $this->isProcessing = true;
            $billable = $this->billable;

            if (! $billable?->hasStripeId()) {
                throw new \Exception('No Stripe customer found.');
            }

            Cashier::stripe()->customers->update($billable->stripe_id, [
                'coupon' => $this->couponCode,
            ]);

            $this->couponCode = '';
            Flux::modal('apply-coupon-modal')->close();
            Toaster::success('Coupon applied successfully.');

            $this->dispatch('coupon-applied');

            $this->isProcessing = false;
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            $this->isProcessing = false;
            Toaster::error('Invalid coupon code: '.$e->getMessage());
        } catch (\Exception $e) {
            $this->isProcessing = false;
            Toaster::error('Failed to apply coupon: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.modals.apply-coupon-modal');
    }
}
