<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     // Note: Postal code import removed from global setup for performance
    //     // Individual tests that need postal codes should create them using factories
    // }

    // protected function tearDown(): void
    // {
    //     // Ensure any hanging transactions are properly closed
    //     if ($this->app && $this->app->bound('db')) {
    //         try {
    //             $connection = $this->app['db']->connection();
    //             if ($connection->transactionLevel() > 0) {
    //                 $connection->rollBack();
    //             }
    //         } catch (\Exception $e) {
    //             // Silently handle any transaction cleanup errors
    //         }
    //     }

    //     parent::tearDown();
    // }

    /**
     * Helper method to import postal codes for tests that need them
     */
    protected function importPostalCodes(int $chunk = 100): void
    {
        $this->artisan('collabconnect:import-postal-codes', ['--chunk' => $chunk]);
    }
}
