<?php

use App\Livewire\ViewProfile;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(ViewProfile::class)
        ->assertStatus(200);
});
