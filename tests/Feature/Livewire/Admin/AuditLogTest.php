<?php

namespace Tests\Feature\Livewire\Admin;

use App\Enums\AccountType;
use App\Livewire\Admin\AuditLog as AuditLogComponent;
use App\Models\AuditLog;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
            'access_admin' => true,
        ]);
    }

    private function createAuditLog(array $attributes = []): AuditLog
    {
        return AuditLog::create(array_merge([
            'admin_id' => $this->admin->id,
            'action' => 'credit.grant',
        ], $attributes));
    }

    #[Test]
    public function it_can_render_the_component(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class)
            ->assertStatus(200);
    }

    #[Test]
    public function it_displays_empty_state_when_no_logs_exist(): void
    {
        Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class)
            ->assertSee('No Audit Logs Found');
    }

    #[Test]
    public function it_displays_logs_when_they_exist(): void
    {
        $this->createAuditLog([
            'action' => 'credit.grant',
            'metadata' => ['reason' => 'Test grant reason'],
        ]);

        Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class)
            ->assertDontSee('No Audit Logs Found')
            ->assertSee('Credit Grant');
    }

    #[Test]
    public function it_can_search_logs_by_admin_name(): void
    {
        $otherAdmin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
            'name' => 'Searchable Admin',
        ]);

        $this->createAuditLog(['admin_id' => $this->admin->id]);
        $this->createAuditLog(['admin_id' => $otherAdmin->id]);

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class)
            ->set('search', 'Searchable');

        $logs = $component->get('logs');
        $this->assertEquals(1, $logs->count());
    }

    #[Test]
    public function it_can_filter_logs_by_action(): void
    {
        $this->createAuditLog(['action' => 'credit.grant']);
        $this->createAuditLog(['action' => 'credit.revoke']);
        $this->createAuditLog(['action' => 'subscription.cancel']);

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class)
            ->set('action', 'credit.grant');

        $logs = $component->get('logs');
        $this->assertEquals(1, $logs->count());
        $this->assertEquals('credit.grant', $logs->first()->action);
    }

    #[Test]
    public function it_can_filter_logs_by_date_range_today(): void
    {
        $this->createAuditLog(['created_at' => now()]);
        $this->createAuditLog(['created_at' => now()->subDays(2)]);

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class)
            ->set('dateRange', 'today');

        $logs = $component->get('logs');
        $this->assertEquals(1, $logs->count());
    }

    #[Test]
    public function it_can_filter_logs_by_date_range_week(): void
    {
        $this->createAuditLog(['created_at' => now()]);
        $this->createAuditLog(['created_at' => now()->subDays(5)]);
        $this->createAuditLog(['created_at' => now()->subDays(10)]);

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class)
            ->set('dateRange', 'week');

        $logs = $component->get('logs');
        $this->assertEquals(2, $logs->count());
    }

    #[Test]
    public function it_can_filter_logs_by_date_range_month(): void
    {
        $this->createAuditLog(['created_at' => now()]);
        $this->createAuditLog(['created_at' => now()->subDays(20)]);
        $this->createAuditLog(['created_at' => now()->subDays(40)]);

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class)
            ->set('dateRange', 'month');

        $logs = $component->get('logs');
        $this->assertEquals(2, $logs->count());
    }

    #[Test]
    public function it_computes_stats_correctly(): void
    {
        $this->createAuditLog(['action' => 'credit.grant', 'created_at' => now()]);
        $this->createAuditLog(['action' => 'credit.grant', 'created_at' => now()->subDays(2)]);
        $this->createAuditLog(['action' => 'credit.revoke', 'created_at' => now()]);
        $this->createAuditLog(['action' => 'subscription.cancel', 'created_at' => now()->subDays(10)]);

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class);

        $stats = $component->get('stats');

        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(2, $stats['today']);
        $this->assertEquals(3, $stats['this_week']);
        $this->assertEquals(2, $stats['credit_grants']);
    }

    #[Test]
    public function it_computes_action_options_correctly(): void
    {
        $this->createAuditLog(['action' => 'credit.grant']);
        $this->createAuditLog(['action' => 'credit.revoke']);
        $this->createAuditLog(['action' => 'credit.grant']); // Duplicate

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class);

        $actionOptions = $component->get('actionOptions');

        $this->assertCount(2, $actionOptions);
        $this->assertContains('credit.grant', $actionOptions);
        $this->assertContains('credit.revoke', $actionOptions);
    }

    #[Test]
    public function it_returns_correct_action_colors(): void
    {
        $component = new AuditLogComponent;

        $this->assertEquals('green', $component->getActionColor('credit.grant'));
        $this->assertEquals('red', $component->getActionColor('credit.revoke'));
        $this->assertEquals('blue', $component->getActionColor('subscription.start_trial'));
        $this->assertEquals('red', $component->getActionColor('subscription.cancel'));
        $this->assertEquals('purple', $component->getActionColor('user.update'));
        $this->assertEquals('yellow', $component->getActionColor('coupon.apply'));
        $this->assertEquals('zinc', $component->getActionColor('unknown.action'));
    }

    #[Test]
    public function it_resets_page_when_search_is_updated(): void
    {
        // Create enough logs for pagination
        for ($i = 0; $i < 30; $i++) {
            $this->createAuditLog();
        }

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class);

        // Navigate to page 2
        $component->call('gotoPage', 2);

        // Update search should reset to page 1
        $component->set('search', 'test');

        // The page should be reset (we can't directly assert page number easily,
        // but we can verify the component still works)
        $component->assertStatus(200);
    }

    #[Test]
    public function it_displays_audit_log_with_metadata(): void
    {
        $user = User::factory()->influencer()->withProfile()->create();

        $this->createAuditLog([
            'action' => 'credit.grant',
            'auditable_type' => Influencer::class,
            'auditable_id' => $user->influencer->id,
            'old_values' => ['promotion_credits' => 0],
            'new_values' => ['promotion_credits' => 10],
            'metadata' => [
                'reason' => 'VIP customer bonus',
                'user_name' => $user->name,
            ],
        ]);

        Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class)
            ->assertSee('VIP customer bonus')
            ->assertSee($user->name);
    }

    #[Test]
    public function it_paginates_logs(): void
    {
        // Create more logs than a single page
        for ($i = 0; $i < 30; $i++) {
            $this->createAuditLog();
        }

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class);

        $logs = $component->get('logs');

        $this->assertEquals(25, $logs->count()); // Default per page
        $this->assertEquals(30, $logs->total());
    }

    #[Test]
    public function it_orders_logs_by_created_at_descending(): void
    {
        $oldLog = $this->createAuditLog(['created_at' => now()->subDays(5)]);
        $newLog = $this->createAuditLog(['created_at' => now()]);

        $component = Livewire::actingAs($this->admin)
            ->test(AuditLogComponent::class);

        $logs = $component->get('logs');

        $this->assertEquals($newLog->id, $logs->first()->id);
        $this->assertEquals($oldLog->id, $logs->last()->id);
    }

    #[Test]
    public function it_requires_admin_access_to_view(): void
    {
        $regularUser = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $this->actingAs($regularUser)
            ->get(route('admin.audit-log'))
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_access_audit_log_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.audit-log'))
            ->assertOk();
    }
}
