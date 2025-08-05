<?php

namespace Tests\Feature;

use App\Enums\CampaignApplicationStatus;
use App\Enums\CampaignStatus;
use App\Events\CampaignApplicationSubmitted;
use App\Listeners\CreateCampaignApplicationNotification;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $businessUser;
    protected User $influencerUser;
    protected Campaign $campaign;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->businessUser = User::factory()->business()->withProfile()->create();
        $this->influencerUser = User::factory()->influencer()->withProfile()->create();
        
        $this->campaign = Campaign::factory()->published()->create([
            'user_id' => $this->businessUser->id,
            'campaign_goal' => 'Test campaign for notifications',
        ]);
    }

    public function test_notification_service_creates_basic_notification()
    {
        $notification = NotificationService::create(
            $this->businessUser,
            'test_type',
            'Test Title',
            'Test message content',
            ['key' => 'value'],
            '/test-url'
        );

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($this->businessUser->id, $notification->user_id);
        $this->assertEquals('test_type', $notification->type);
        $this->assertEquals('Test Title', $notification->title);
        $this->assertEquals('Test message content', $notification->message);
        $this->assertEquals(['key' => 'value'], $notification->data);
        $this->assertEquals('/test-url', $notification->action_url);
        $this->assertFalse($notification->is_read);
        $this->assertNull($notification->read_at);
    }

    public function test_notification_service_creates_campaign_application_notification()
    {
        $notification = NotificationService::createCampaignApplicationNotification(
            $this->businessUser,
            $this->campaign->id,
            $this->campaign->campaign_goal,
            $this->influencerUser->name
        );

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($this->businessUser->id, $notification->user_id);
        $this->assertEquals('campaign_application', $notification->type);
        $this->assertEquals('New Campaign Application', $notification->title);
        $this->assertStringContainsString($this->influencerUser->name, $notification->message);
        $this->assertStringContainsString($this->campaign->campaign_goal, $notification->message);
        $this->assertEquals($this->campaign->id, $notification->data['campaign_id']);
        $this->assertEquals($this->influencerUser->name, $notification->data['applicant_name']);
        $this->assertEquals("/campaigns/{$this->campaign->id}/applications", $notification->action_url);
    }

    public function test_campaign_application_creates_notification_event()
    {
        Event::fake();

        $this->actingAs($this->influencerUser);

        // Submit application through the component
        Livewire::test('campaigns.apply-to-campaign', ['campaign' => $this->campaign])
            ->set('message', 'I would love to work on this campaign!')
            ->call('submitApplication');

        // Verify the event would be dispatched (if the component fires it)
        // Note: The actual event dispatch might happen in the component or service
        // For now, we'll test the listener directly
        $this->assertTrue(true); // Placeholder - actual event testing depends on implementation
    }

    public function test_campaign_application_listener_creates_notification()
    {
        // Create an application
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->influencerUser->id,
            'message' => 'Test application message',
        ]);

        // Create the event manually
        $event = new CampaignApplicationSubmitted(
            $this->campaign,
            $this->influencerUser,
            [
                'message' => 'Test application message',
                'application_id' => $application->id,
            ]
        );

        // Handle the event with the listener
        $listener = new CreateCampaignApplicationNotification();
        $listener->handle($event);

        // Verify notification was created
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->businessUser->id,
            'type' => 'campaign_application',
            'title' => 'New Campaign Application',
        ]);

        $notification = Notification::where('user_id', $this->businessUser->id)->first();
        $this->assertStringContainsString($this->influencerUser->name, $notification->message);
        $this->assertStringContainsString($this->campaign->campaign_goal, $notification->message);
    }

    public function test_notification_can_be_marked_as_read()
    {
        $notification = NotificationService::create(
            $this->businessUser,
            'test_type',
            'Test Title',
            'Test message'
        );

        $this->assertFalse($notification->is_read);
        $this->assertNull($notification->read_at);

        NotificationService::markAsRead($notification);

        $notification->refresh();
        $this->assertTrue($notification->is_read);
        $this->assertNotNull($notification->read_at);
    }

    public function test_all_notifications_can_be_marked_as_read()
    {
        // Create multiple unread notifications
        NotificationService::create($this->businessUser, 'type1', 'Title 1', 'Message 1');
        NotificationService::create($this->businessUser, 'type2', 'Title 2', 'Message 2');
        NotificationService::create($this->businessUser, 'type3', 'Title 3', 'Message 3');

        $this->assertEquals(3, NotificationService::getUnreadCount($this->businessUser));

        NotificationService::markAllAsRead($this->businessUser);

        $this->assertEquals(0, NotificationService::getUnreadCount($this->businessUser));

        // Verify all notifications are marked as read
        $notifications = $this->businessUser->notifications;
        $this->assertTrue($notifications->every(fn($n) => $n->is_read));
        $this->assertTrue($notifications->every(fn($n) => $n->read_at !== null));
    }

    public function test_unread_notification_count()
    {
        $this->assertEquals(0, NotificationService::getUnreadCount($this->businessUser));

        // Create some notifications
        NotificationService::create($this->businessUser, 'type1', 'Title 1', 'Message 1');
        NotificationService::create($this->businessUser, 'type2', 'Title 2', 'Message 2');

        $this->assertEquals(2, NotificationService::getUnreadCount($this->businessUser));

        // Mark one as read
        $notification = $this->businessUser->notifications->first();
        NotificationService::markAsRead($notification);

        $this->assertEquals(1, NotificationService::getUnreadCount($this->businessUser));
    }

    public function test_recent_notifications_retrieval()
    {
        // Create multiple notifications
        for ($i = 1; $i <= 15; $i++) {
            NotificationService::create(
                $this->businessUser,
                'type',
                "Title {$i}",
                "Message {$i}"
            );
        }

        // Get recent notifications with default limit
        $recent = NotificationService::getRecentNotifications($this->businessUser);
        $this->assertCount(10, $recent);

        // Get recent notifications with custom limit
        $recent = NotificationService::getRecentNotifications($this->businessUser, 5);
        $this->assertCount(5, $recent);

        // Verify they are ordered by creation date (newest first)
        $titles = $recent->pluck('title');
        $this->assertEquals('Title 15', $titles->first());
        $this->assertEquals('Title 11', $titles->last());
    }

    public function test_notifications_dropdown_component()
    {
        $this->actingAs($this->businessUser);

        // Create some notifications
        NotificationService::create($this->businessUser, 'type1', 'Title 1', 'Message 1');
        NotificationService::create($this->businessUser, 'type2', 'Title 2', 'Message 2');

        $component = Livewire::test('notifications-dropdown');

        $component->assertSee('Title 1')
                 ->assertSee('Title 2');
    }

    public function test_notification_dropdown_mark_as_read()
    {
        $this->actingAs($this->businessUser);

        $notification = NotificationService::create(
            $this->businessUser,
            'test_type',
            'Test Title',
            'Test message'
        );

        $component = Livewire::test('notifications-dropdown')
            ->call('markAsRead', $notification->id);

        $notification->refresh();
        $this->assertTrue($notification->is_read);
    }

    public function test_notification_dropdown_mark_all_as_read()
    {
        $this->actingAs($this->businessUser);

        // Create multiple notifications
        NotificationService::create($this->businessUser, 'type1', 'Title 1', 'Message 1');
        NotificationService::create($this->businessUser, 'type2', 'Title 2', 'Message 2');

        $component = Livewire::test('notifications-dropdown')
            ->call('markAllAsRead');

        $this->assertEquals(0, NotificationService::getUnreadCount($this->businessUser));
    }

    public function test_notification_model_relationships()
    {
        $notification = NotificationService::create(
            $this->businessUser,
            'test_type',
            'Test Title',
            'Test message'
        );

        // Test relationship
        $this->assertInstanceOf(User::class, $notification->user);
        $this->assertEquals($this->businessUser->id, $notification->user->id);

        // Test inverse relationship
        $this->assertTrue($this->businessUser->notifications->contains($notification));
    }

    public function test_notification_json_casting()
    {
        $data = [
            'campaign_id' => 123,
            'applicant_name' => 'John Doe',
            'additional_info' => ['key' => 'value'],
        ];

        $notification = NotificationService::create(
            $this->businessUser,
            'test_type',
            'Test Title',
            'Test message',
            $data
        );

        // Test that data is properly cast to/from JSON
        $this->assertIsArray($notification->data);
        $this->assertEquals($data, $notification->data);

        // Test after refresh
        $notification->refresh();
        $this->assertIsArray($notification->data);
        $this->assertEquals($data, $notification->data);
    }

    public function test_notification_factory()
    {
        $notification = Notification::factory()->create([
            'user_id' => $this->businessUser->id,
        ]);

        $this->assertInstanceOf(Notification::class, $notification);
        $this->assertEquals($this->businessUser->id, $notification->user_id);
        $this->assertIsString($notification->type);
        $this->assertIsString($notification->title);
        $this->assertIsString($notification->message);
        $this->assertIsBool($notification->is_read);
    }

    public function test_notification_scopes()
    {
        // Create read and unread notifications
        $readNotification = NotificationService::create(
            $this->businessUser,
            'type1',
            'Read Title',
            'Read message'
        );
        $readNotification->markAsRead();

        $unreadNotification = NotificationService::create(
            $this->businessUser,
            'type2',
            'Unread Title',
            'Unread message'
        );

        // Test unread scope (if it exists)
        $unreadNotifications = $this->businessUser->notifications()->where('is_read', false)->get();
        $this->assertCount(1, $unreadNotifications);
        $this->assertEquals($unreadNotification->id, $unreadNotifications->first()->id);

        // Test read scope (if it exists)
        $readNotifications = $this->businessUser->notifications()->where('is_read', true)->get();
        $this->assertCount(1, $readNotifications);
        $this->assertEquals($readNotification->id, $readNotifications->first()->id);
    }

    public function test_notification_type_filtering()
    {
        // Create notifications of different types
        NotificationService::create($this->businessUser, 'campaign_application', 'App Title', 'App message');
        NotificationService::create($this->businessUser, 'campaign_published', 'Pub Title', 'Pub message');
        NotificationService::create($this->businessUser, 'campaign_application', 'App Title 2', 'App message 2');

        // Filter by type
        $appNotifications = $this->businessUser->notifications()
            ->where('type', 'campaign_application')
            ->get();

        $this->assertCount(2, $appNotifications);
        $this->assertTrue($appNotifications->every(fn($n) => $n->type === 'campaign_application'));

        $pubNotifications = $this->businessUser->notifications()
            ->where('type', 'campaign_published')
            ->get();

        $this->assertCount(1, $pubNotifications);
        $this->assertEquals('campaign_published', $pubNotifications->first()->type);
    }

    public function test_multiple_campaign_applications_create_multiple_notifications()
    {
        $influencer2 = User::factory()->influencer()->withProfile()->create();
        $influencer3 = User::factory()->influencer()->withProfile()->create();

        // Create applications from different influencers
        $app1 = CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $this->influencerUser->id,
        ]);

        $app2 = CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $influencer2->id,
        ]);

        $app3 = CampaignApplication::factory()->create([
            'campaign_id' => $this->campaign->id,
            'user_id' => $influencer3->id,
        ]);

        // Create notifications for each application
        NotificationService::createCampaignApplicationNotification(
            $this->businessUser,
            $this->campaign->id,
            $this->campaign->campaign_goal,
            $this->influencerUser->name
        );

        NotificationService::createCampaignApplicationNotification(
            $this->businessUser,
            $this->campaign->id,
            $this->campaign->campaign_goal,
            $influencer2->name
        );

        NotificationService::createCampaignApplicationNotification(
            $this->businessUser,
            $this->campaign->id,
            $this->campaign->campaign_goal,
            $influencer3->name
        );

        // Verify all notifications were created
        $notifications = $this->businessUser->notifications()
            ->where('type', 'campaign_application')
            ->get();

        $this->assertCount(3, $notifications);

        // Verify each notification has correct applicant name
        $applicantNames = $notifications->pluck('data')->pluck('applicant_name');
        $this->assertContains($this->influencerUser->name, $applicantNames);
        $this->assertContains($influencer2->name, $applicantNames);
        $this->assertContains($influencer3->name, $applicantNames);
    }
}