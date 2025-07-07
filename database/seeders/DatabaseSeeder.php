<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => env('INIT_USER_NAME') ?? fake()->name(),
            'email' => env('INIT_USER_EMAIL') ?? fake()->safeEmail(),
            'password' => Hash::make(env('INIT_USER_PASSWORD') ?? fake()->password()),
        ]);
    }
}
