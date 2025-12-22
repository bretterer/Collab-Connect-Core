<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('link_in_bio_analytics.dataRetentionDays', 90);
        $this->migrator->add('link_in_bio_analytics.viewRateLimitMinutes', 60);
    }
};
