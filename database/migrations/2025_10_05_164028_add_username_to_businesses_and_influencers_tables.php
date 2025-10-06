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
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });

        Schema::table('influencers', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('user_id');
        });

        // Remove username from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('email');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });

        Schema::table('influencers', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
