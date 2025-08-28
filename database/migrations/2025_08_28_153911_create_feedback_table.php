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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('type'); // FeedbackType enum
            $table->string('subject');
            $table->text('message');
            $table->string('url')->nullable(); // Current page URL
            $table->json('browser_info')->nullable(); // User agent, screen size, etc.
            $table->string('screenshot_path')->nullable(); // Path to screenshot
            $table->json('session_data')->nullable(); // Any relevant session data
            $table->string('github_issue_url')->nullable(); // Link to created GitHub issue
            $table->integer('github_issue_number')->nullable(); // GitHub issue number
            $table->boolean('resolved')->default(false);
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'created_at']);
            $table->index(['resolved', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
