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
        Schema::table('business_member_invites', function (Blueprint $table) {
            $table->unique(['email', 'business_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_member_invites', function (Blueprint $table) {
            $table->dropUnique(['email', 'business_id']);
        });
    }
};
