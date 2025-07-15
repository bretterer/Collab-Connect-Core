<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Note: Postal code import removed from global setup for performance
        // Individual tests that need postal codes should create them using factories
    }

    /**
     * Helper method to import postal codes for tests that need them
     */
    protected function importPostalCodes(int $chunk = 100): void
    {
        $this->artisan('collabconnect:import-postal-codes', ['--chunk' => $chunk]);
    }
}
