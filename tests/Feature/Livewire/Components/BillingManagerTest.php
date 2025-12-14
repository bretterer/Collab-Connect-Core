<?php

namespace Tests\Feature\Livewire\Components;

use Tests\TestCase;

class BillingManagerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
