<?php

namespace App\Livewire\Business;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BusinessCampaigns extends Component
{
    use WithPagination;

    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        $campaigns = $this->user->currentBusiness
            ? $this->user->currentBusiness->campaigns()->paginate(12)
            : collect()->paginate(12);

        return view('livewire.business.business-campaigns', [
            'campaigns' => $campaigns,
        ]);
    }
}
