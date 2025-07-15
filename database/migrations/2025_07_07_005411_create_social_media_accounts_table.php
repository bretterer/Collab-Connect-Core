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
        Schema::create('social_media_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform'); // instagram, tiktok, youtube, facebook, x
            $table->string('username');
            $table->string('url')->nullable();
            $table->integer('follower_count')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            // Indexes for search performance
            $table->index(['user_id']); // For user relationship queries
            $table->index(['platform']); // For platform filtering
            $table->index(['follower_count']); // For follower count sorting/filtering
            $table->index(['is_primary']); // For finding primary accounts
            $table->index(['user_id', 'platform']); // For user's specific platform accounts
            $table->index(['platform', 'follower_count']); // For platform-specific follower sorting
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media_accounts');
    }
};
