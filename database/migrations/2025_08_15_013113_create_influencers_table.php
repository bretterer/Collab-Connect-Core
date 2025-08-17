<?php

use App\Models\Influencer;
use App\Models\User;
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
        Schema::create('influencers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('county')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->json('content_types')->nullable();
            $table->json('preferred_business_types')->nullable();
            $table->json('compensation_types')->nullable();
            $table->integer('typical_lead_time_days')->nullable();
            $table->boolean('onboarding_complete')->default(false);
            $table->timestamps();
        });

        Schema::create('influencer_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Influencer::class)->constrained()->onDelete('cascade');
            $table->string('platform');
            $table->string('username');
            $table->string('url')->nullable();
            $table->integer('followers')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('influencer_socials');

        Schema::dropIfExists('influencers');
    }
};
