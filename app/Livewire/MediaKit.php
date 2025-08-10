<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MediaKit extends Component
{
    public function mount()
    {
        // Ensure only influencer users can access media kit
        if (!Auth::user()->isInfluencerAccount()) {
            return redirect()->route('dashboard');
        }
    }

    public function render()
    {
        return view('livewire.media-kit');
    }

    public function generateMediaKit()
    {
        // Future: Generate PDF media kit
        $this->dispatch('generate-media-kit');
    }

    public function previewMediaKit()
    {
        // Future: Preview media kit before download
        $this->dispatch('preview-media-kit');
    }
}