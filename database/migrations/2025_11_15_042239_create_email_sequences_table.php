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
        Schema::create('email_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('subscribe_triggers')->nullable(); // Array of trigger configurations
            $table->json('unsubscribe_triggers')->nullable(); // Array of unsubscribe trigger configurations
            $table->foreignId('funnel_id')->nullable()->constrained('funnels')->onDelete('cascade');
            $table->integer('total_subscribers')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sequences');
    }
};
