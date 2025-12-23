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
        Schema::table('messages', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['user_id']);

            // Modify the column to be nullable
            $table->foreignId('user_id')->nullable()->change();

            // Re-add the foreign key with nullOnDelete
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['user_id']);

            // Make column non-nullable again
            $table->foreignId('user_id')->nullable(false)->change();

            // Re-add the foreign key with cascadeOnDelete
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
