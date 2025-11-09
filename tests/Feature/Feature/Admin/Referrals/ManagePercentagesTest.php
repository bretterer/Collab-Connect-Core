<?php

namespace Tests\Feature\Feature\Admin\Referrals;

use Tests\TestCase;

class ManagePercentagesTest extends TestCase
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
