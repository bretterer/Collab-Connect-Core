<?php

namespace Database\Factories;

use App\Models\CollaborationDeliverable;
use App\Models\CollaborationDeliverableFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CollaborationDeliverableFile>
 */
class CollaborationDeliverableFileFactory extends Factory
{
    protected $model = CollaborationDeliverableFile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collaboration_deliverable_id' => CollaborationDeliverable::factory(),
            'file_path' => 'deliverables/'.fake()->uuid().'.jpg',
            'file_name' => 'screenshot_'.fake()->word().'.jpg',
            'file_type' => 'image/jpeg',
            'uploaded_by_user_id' => User::factory(),
            'created_at' => now(),
        ];
    }

    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_path' => 'deliverables/'.fake()->uuid().'.jpg',
            'file_name' => 'screenshot_'.fake()->word().'.jpg',
            'file_type' => 'image/jpeg',
        ]);
    }

    public function png(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_path' => 'deliverables/'.fake()->uuid().'.png',
            'file_name' => 'screenshot_'.fake()->word().'.png',
            'file_type' => 'image/png',
        ]);
    }

    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_path' => 'deliverables/'.fake()->uuid().'.mp4',
            'file_name' => 'video_'.fake()->word().'.mp4',
            'file_type' => 'video/mp4',
        ]);
    }
}
