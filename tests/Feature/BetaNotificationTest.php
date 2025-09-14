<?php

namespace Tests\Feature;

use App\Livewire\Components\BetaNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Tests\TestCase;

class BetaNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_beta_notification_shows_when_login_success_session_is_set()
    {
        Session::put('login_success', true);

        Livewire::test(BetaNotification::class)
            ->assertSet('showModal', true)
            ->assertSee('Welcome to CollabConnect Beta!')
            ->assertSee('excited to have you on board')
            ->assertSee('reporting widget')
            ->assertSee('building our userbase');
    }

    public function test_beta_notification_does_not_show_when_login_success_session_is_not_set()
    {
        Session::forget('login_success');

        Livewire::test(BetaNotification::class)
            ->assertSet('showModal', false)
            ->assertDontSee('Welcome to CollabConnect Beta!');
    }

    public function test_beta_notification_can_be_closed()
    {
        Session::put('login_success', true);

        Livewire::test(BetaNotification::class)
            ->assertSet('showModal', true)
            ->call('closeModal')
            ->assertSet('showModal', false);
    }

    public function test_login_success_session_is_cleared_after_mount()
    {
        Session::put('login_success', true);

        Livewire::test(BetaNotification::class);

        // Session should be cleared after the component mounts
        $this->assertFalse(Session::has('login_success'));
    }
}