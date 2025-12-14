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
        Schema::table('stripe_prices', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('stripe_id');
            $table->string('currency')->nullable()->after('unit_amount');
            $table->string('lookup_key')->nullable()->after('product_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stripe_prices', function (Blueprint $table) {
            $table->dropColumn('product_name');
            $table->dropColumn('currency');
            $table->dropColumn('lookup_key');
        });
    }
};
