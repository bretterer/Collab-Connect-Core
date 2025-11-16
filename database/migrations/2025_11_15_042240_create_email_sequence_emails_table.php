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
        Schema::create('email_sequence_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_sequence_id')->constrained('email_sequences')->onDelete('cascade');
            $table->string('name'); // Internal name like "Day 1 @ 08:00 AM EST"
            $table->string('subject');
            $table->text('body'); // HTML email content
            $table->integer('delay_days')->default(0); // Days after subscription
            $table->time('send_time')->default('08:00:00'); // Time to send (in EST/EDT)
            $table->string('timezone')->default('America/New_York');
            $table->integer('order')->default(0); // Order in sequence
            $table->integer('sent_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sequence_emails');
    }
};
