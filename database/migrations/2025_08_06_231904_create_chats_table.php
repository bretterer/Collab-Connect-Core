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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('influencer_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Ensure a unique chat between two users
            $table->unique(['business_user_id', 'influencer_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
