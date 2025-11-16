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
        Schema::create('email_sequence_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_sequence_email_id')->constrained('email_sequence_emails')->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained('email_sequence_subscribers')->onDelete('cascade');
            $table->timestamp('scheduled_at'); // When it should be sent
            $table->timestamp('sent_at')->nullable(); // When it was actually sent
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->string('status')->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['scheduled_at', 'status']);
            $table->index('subscriber_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sequence_sends');
    }
};
