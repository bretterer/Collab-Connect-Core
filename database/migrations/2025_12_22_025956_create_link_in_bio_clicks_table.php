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
        Schema::create('link_in_bio_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_in_bio_settings_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('link_index');
            $table->string('link_title', 255)->nullable();
            $table->string('link_url', 2048)->nullable();
            $table->string('ip_hash', 64);
            $table->string('user_agent', 512)->nullable();
            $table->string('device_type', 20)->nullable();
            $table->timestamp('clicked_at');
            $table->timestamps();

            $table->index(['link_in_bio_settings_id', 'clicked_at']);
            $table->index(['link_in_bio_settings_id', 'link_index', 'clicked_at'], 'lib_clicks_link_index');
            $table->index('clicked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_in_bio_clicks');
    }
};
