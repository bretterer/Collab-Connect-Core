<?php

namespace Tests\Feature;

use App\Enums\FeedbackType;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_feedback_can_be_created()
    {
        $user = User::factory()->create();

        $feedback = Feedback::create([
            'user_id' => $user->id,
            'type' => FeedbackType::BUG_REPORT,
            'subject' => 'Test Bug Report',
            'message' => 'This is a test bug report message.',
            'url' => 'https://test.com',
            'browser_info' => ['user_agent' => 'Test Agent'],
            'session_data' => ['timestamp' => now()->toISOString()],
            'resolved' => false,
        ]);

        $this->assertDatabaseHas('feedback', [
            'user_id' => $user->id,
            'type' => 'bug_report',
            'subject' => 'Test Bug Report',
            'message' => 'This is a test bug report message.',
            'resolved' => false,
        ]);

        $this->assertEquals(FeedbackType::BUG_REPORT, $feedback->type);
        $this->assertFalse($feedback->resolved);
    }

    public function test_feedback_can_be_marked_as_resolved()
    {
        $feedback = Feedback::factory()->create([
            'resolved' => false,
        ]);

        $this->assertFalse($feedback->resolved);
        $this->assertNull($feedback->resolved_at);

        $feedback->markAsResolved('Test resolution notes');

        $this->assertTrue($feedback->resolved);
        $this->assertEquals('Test resolution notes', $feedback->admin_notes);
        $this->assertNotNull($feedback->resolved_at);
    }

    public function test_feedback_enum_has_correct_labels()
    {
        $this->assertEquals('Bug Report', FeedbackType::BUG_REPORT->label());
        $this->assertEquals('Feature Request', FeedbackType::FEATURE_REQUEST->label());
        $this->assertEquals('General Feedback', FeedbackType::GENERAL_FEEDBACK->label());
    }

    public function test_feedback_model_relationships()
    {
        $user = User::factory()->create();
        $feedback = Feedback::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($feedback->user->is($user));
    }

    public function test_feedback_scopes()
    {
        // Create specific test data
        Feedback::query()->delete(); // Clear any existing data

        Feedback::factory()->create(['resolved' => true, 'type' => FeedbackType::FEATURE_REQUEST]);
        Feedback::factory()->create(['resolved' => false, 'type' => FeedbackType::GENERAL_FEEDBACK]);
        Feedback::factory()->create(['type' => FeedbackType::BUG_REPORT, 'resolved' => false]);

        $this->assertCount(1, Feedback::resolved()->get());
        $this->assertCount(2, Feedback::unresolved()->get());
        $this->assertCount(1, Feedback::byType(FeedbackType::BUG_REPORT)->get());
    }
}
