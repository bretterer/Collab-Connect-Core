<?php

use Livewire\Volt\Volt;

it('can render', function () {
    $component = Volt::test('influencers.view-profile');

    $component->assertSee('');
});
