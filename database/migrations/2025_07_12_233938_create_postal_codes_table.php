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
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->id();
            $table->char('country_code', 2)->index(); // ISO country code, 2 characters
            $table->string('postal_code', 20)->index(); // Postal code
            $table->string('place_name', 180); // Place name
            $table->string('admin_name1', 100)->nullable(); // 1st order subdivision (state)
            $table->string('admin_code1', 20)->nullable(); // 1st order subdivision code
            $table->string('admin_name2', 100)->nullable(); // 2nd order subdivision (county/province)
            $table->string('admin_code2', 20)->nullable(); // 2nd order subdivision code
            $table->string('admin_name3', 100)->nullable(); // 3rd order subdivision (community)
            $table->string('admin_code3', 20)->nullable(); // 3rd order subdivision code
            $table->decimal('latitude', 10, 8)->nullable(); // Estimated latitude (WGS84)
            $table->decimal('longitude', 11, 8)->nullable(); // Estimated longitude (WGS84)
            $table->tinyInteger('accuracy')->nullable(); // Accuracy of lat/lng from 1=estimated, 4=geonameid, 6=centroid
            $table->timestamps();

            // Composite index for common lookups
            $table->index(['country_code', 'postal_code']);
            $table->index(['country_code', 'admin_code1']);

            // Critical indexes for proximity search performance
            $table->index(['country_code', 'latitude', 'longitude']); // For bounding box queries
            $table->index(['latitude', 'longitude']); // For coordinate-based queries
            $table->index(['country_code', 'latitude']); // For latitude range queries
            $table->index(['country_code', 'longitude']); // For longitude range queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postal_codes');
    }
};
