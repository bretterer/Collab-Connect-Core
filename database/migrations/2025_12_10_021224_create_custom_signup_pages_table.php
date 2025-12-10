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
        Schema::create('custom_signup_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Internal name for admin reference');
            $table->string('slug')->unique()->comment('URL slug for the signup page');
            $table->string('title')->comment('Display title shown on the page');
            $table->text('description')->nullable()->comment('Page description/subtitle');
            $table->string('account_type')->comment('influencer or business');
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable()->comment('Flexible JSON for page configuration');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_signup_pages');
    }
};
