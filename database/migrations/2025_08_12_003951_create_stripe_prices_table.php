<?php

use App\Models\StripeProduct;
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
        Schema::create('stripe_prices', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id')->unique();
            $table->boolean('active')->default(true);
            $table->string('billing_scheme')->default('per_unit');
            $table->boolean('livemode')->default(false);
            $table->json('metadata')->nullable();
            $table->foreignIdFor(StripeProduct::class)->constrained()->onDelete('cascade');
            $table->json('recurring')->nullable();
            $table->string('type');
            $table->integer('unit_amount');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_prices');
    }
};
