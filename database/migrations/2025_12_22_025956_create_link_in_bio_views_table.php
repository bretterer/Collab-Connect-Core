<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('link_in_bio_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_in_bio_settings_id')->constrained()->cascadeOnDelete();
            $table->string('ip_hash', 64);
            $table->string('user_agent', 512)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->string('referrer', 512)->nullable();
            $table->string('referrer_domain', 255)->nullable();
            $table->boolean('is_unique')->default(true);
            $table->timestamp('viewed_at');
            $table->timestamps();

            $table->index(['link_in_bio_settings_id', 'viewed_at']);
            $table->index(['link_in_bio_settings_id', 'ip_hash', 'viewed_at'], 'lib_views_rate_limit_index');
            $table->index('viewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_in_bio_views');
    }
};
