<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('user_type');
            $table->string('referral_code')->nullable();
            $table->string('business_name')->nullable();
            $table->string('follower_count')->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->string('invite_token')->nullable();
            $table->timestamps();
        });

        // Get current waitlist data from CSV if it exists and insert into the database
        $csvPath = storage_path('app/private/waitlist.csv');
        if (file_exists($csvPath)) {
            $csvContent = file_get_contents($csvPath);
            $lines = explode("\n", $csvContent);
            $header = str_getcsv(array_shift($lines));

            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue;
                }

                $data = str_getcsv($line);
                while (count($data) < count($header)) {
                    $data[] = '';
                }

                if (count($data) !== count($header)) {
                    continue;
                }

                $row = array_combine($header, $data);

                try {
                    DB::table('waitlists')->insert([
                        'name' => trim($row['Name'] ?? ''),
                        'email' => trim($row['Email'] ?? ''),
                        'user_type' => strtolower(trim($row['User Type'] ?? '')),
                        'referral_code' => trim($row['Referral Code'] ?? ''),
                        'business_name' => trim($row['Business Name'] ?? ''),
                        'follower_count' => trim($row['Follower Count'] ?? ''),
                        'invited_at' => $row['Invited At'] ? \Carbon\Carbon::parse($row['Invited At']) : null,
                        'invite_token' => $row['Invite Token'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error inserting waitlist data: '.$e->getMessage());
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waitlists');
    }
};
