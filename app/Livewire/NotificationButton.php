<?php

namespace App\Livewire;

use Livewire\Component;

class NotificationButton extends Component
{
    public bool $showModal = false;
    public $notifications = [];
    public $unreadCount = 0;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = auth()->user()->notifications()
            ->latest()
            ->limit(20)
            ->get();


        $this->unreadCount = auth()->user()->notifications()
            ->where('read_at', null)
            ->count();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->loadNotifications();
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function markAsRead($notificationId)
    {
        auth()->user()->notifications()
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);

        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        auth()->user()->notifications()
            ->where('read_at', null)
            ->update(['read_at' => now()]);
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-button');
    }
}