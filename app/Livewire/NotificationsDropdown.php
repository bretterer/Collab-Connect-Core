<?php

namespace App\Livewire;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationsDropdown extends Component
{
    public bool $showDropdown = false;
    public $notifications = [];
    public $unreadCount = 0;

    protected $listeners = ['refreshNotifications' => 'loadNotifications'];

    public function mount()
    {
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notifications-dropdown');
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function loadNotifications()
    {
        $user = Auth::user();
        $this->notifications = NotificationService::getRecentNotifications($user, 10);
        $this->unreadCount = NotificationService::getUnreadCount($user);
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            NotificationService::markAsRead($notification);
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        NotificationService::markAllAsRead(Auth::user());
        $this->loadNotifications();
    }
}