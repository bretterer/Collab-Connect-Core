<?php

use App\Models\Business;
use App\Models\User;
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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('primary_contact');
            $table->string('contact_role');
            $table->string('maturity')->nullable();
            $table->string('size')->nullable();
            $table->text('description')->nullable();
            $table->text('selling_points')->nullable();
            $table->string('type')->nullable();
            $table->string('industry')->nullable();
            $table->text('logo')->nullable();
            $table->boolean('onboarding_complete')->default(false);
            $table->timestamps();
        });

        Schema::create('business_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Business::class)->constrained()->onDelete('cascade');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->string('role')->default('member');
            $table->timestamps();
        });

        Schema::create('business_socials', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Business::class)->constrained()->onDelete('cascade');
            $table->string('platform');
            $table->string('url');
            $table->integer('followers')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignIdFor(Business::class, 'current_business')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_business']);
            $table->dropColumn('current_business');
        });
        Schema::dropIfExists('business_socials');
        Schema::dropIfExists('business_users');
        Schema::dropIfExists('businesses');
    }
};
