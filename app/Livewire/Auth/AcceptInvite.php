<?php

namespace App\Livewire\Auth;

use App\Models\BusinessMemberInvite;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class AcceptInvite extends Component
{
    public ?BusinessMemberInvite $invite = null;

    public string $token = '';

    public function mount()
    {
        // get token from query string
        $this->token = request()->query('token');

        $invite = BusinessMemberInvite::query()->where('token', $this->token)->first();

        if(!$invite) {
            abort(404, 'Invalid or expired invite token.');
        }

        if($invite->joined_at) {
            redirect('dashboard');
        }

        $authUser = auth()->user();

        // if there is no current user, redirect to register page
        if(!$authUser) {
            return redirect()->signedRoute('register', ['token' => $invite->token], 10000);
        }

        if($authUser->isInfluencerAccount()) {
            $authUser->businessInvites()->get()->each(function($invite) {
                $invite->delete();
            });

            session()->flash('toast', [
                'message' => 'Your account type does not allow you to accept business invitations.',
                'type' => 'error',
            ]);
            return redirect('dashboard');
        }

        $this->invite = $invite;
    }

    public function acceptInvite()
    {
        $businessInvite = BusinessMemberInvite::query()->where('token', $this->token)->first();
        if(!$businessInvite) {
            abort(404, 'Invalid or expired invite token.');
        }

        $authUser = auth()->user();
        if(!$authUser) {
            return redirect()->signedRoute('register', ['token' => $businessInvite->token], 10000);
        }

        if(strtolower($authUser->email) !== strtolower($businessInvite->email)) {
            abort(403, 'This invite is not for your email address.');
        }

        if(!$businessInvite->joined_at) {
            $businessInvite->update(['joined_at' => now()]);
            $authUser->businesses()->attach($businessInvite->business_id, ['role' => $businessInvite->role]);
            $authUser->setCurrentBusiness($businessInvite->business);
            $businessInvite->fresh();
        }

        if($businessInvite->joined_at) {
            redirect('dashboard');
        }

    }

    public function declineInvite()
    {
        $businessInvite = BusinessMemberInvite::query()->where('token', $this->token)->first();
        if(!$businessInvite) {
            abort(404, 'Invalid or expired invite token.');
        }

        $authUser = auth()->user();
        if(!$authUser) {
            return redirect()->signedRoute('register', ['token' => $businessInvite->token], 10000);
        }

        if(strtolower($authUser->email) !== strtolower($businessInvite->email)) {
            abort(403, 'This invite is not for your email address.');
        }

        if(!$businessInvite->joined_at) {
            $businessInvite->delete();
        }

        redirect('dashboard');
    }

    public function render()
    {
        return view('livewire.auth.accept-invite');
    }
}
