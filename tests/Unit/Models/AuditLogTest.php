<?php

namespace Tests\Unit\Models;

use App\Enums\AccountType;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_an_audit_log(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $log = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'credit.grant',
            'auditable_type' => null,
            'auditable_id' => null,
            'old_values' => ['promotion_credits' => 0],
            'new_values' => ['promotion_credits' => 5],
            'metadata' => ['reason' => 'Test'],
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'admin_id' => $admin->id,
            'action' => 'credit.grant',
        ]);
    }

    #[Test]
    public function it_casts_json_fields_correctly(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $log = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'credit.grant',
            'old_values' => ['credits' => 0],
            'new_values' => ['credits' => 10],
            'metadata' => ['key' => 'value'],
        ]);

        $freshLog = AuditLog::find($log->id);

        $this->assertIsArray($freshLog->old_values);
        $this->assertIsArray($freshLog->new_values);
        $this->assertIsArray($freshLog->metadata);
        $this->assertEquals(['credits' => 0], $freshLog->old_values);
        $this->assertEquals(['credits' => 10], $freshLog->new_values);
        $this->assertEquals(['key' => 'value'], $freshLog->metadata);
    }

    #[Test]
    public function it_belongs_to_an_admin(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $log = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'test.action',
        ]);

        $this->assertInstanceOf(User::class, $log->admin);
        $this->assertEquals($admin->id, $log->admin->id);
    }

    #[Test]
    public function it_has_polymorphic_auditable_relationship(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $user = User::factory()->influencer()->withProfile()->create();
        $influencer = $user->influencer;

        $log = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'credit.grant',
            'auditable_type' => Influencer::class,
            'auditable_id' => $influencer->id,
        ]);

        $this->assertInstanceOf(Influencer::class, $log->auditable);
        $this->assertEquals($influencer->id, $log->auditable->id);
    }

    #[Test]
    public function it_can_log_actions_using_static_method(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $this->actingAs($admin);

        $user = User::factory()->business()->withProfile()->create();
        $business = $user->businesses()->wherePivot('role', 'owner')->first();

        $log = AuditLog::log(
            action: 'credit.grant',
            auditable: $business,
            oldValues: ['promotion_credits' => 0],
            newValues: ['promotion_credits' => 5],
            metadata: ['reason' => 'Test grant']
        );

        $this->assertDatabaseHas('audit_logs', [
            'admin_id' => $admin->id,
            'action' => 'credit.grant',
            'auditable_type' => Business::class,
            'auditable_id' => $business->id,
        ]);

        $this->assertEquals(['promotion_credits' => 0], $log->old_values);
        $this->assertEquals(['promotion_credits' => 5], $log->new_values);
        $this->assertEquals('Test grant', $log->metadata['reason']);
    }

    #[Test]
    public function it_returns_correct_action_description(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $grantLog = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'credit.grant',
        ]);

        $revokeLog = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'credit.revoke',
        ]);

        $customLog = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'custom.action',
        ]);

        $this->assertEquals('granted promotion credits', $grantLog->getActionDescription());
        $this->assertEquals('revoked promotion credits', $revokeLog->getActionDescription());
        $this->assertEquals('custom.action', $customLog->getActionDescription());
    }

    #[Test]
    public function it_returns_correct_target_name_for_user(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $targetUser = User::factory()->create([
            'name' => 'John Doe',
        ]);

        $log = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'user.update',
            'auditable_type' => User::class,
            'auditable_id' => $targetUser->id,
        ]);

        $this->assertEquals('John Doe', $log->getTargetName());
    }

    #[Test]
    public function it_returns_correct_target_name_for_business(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $user = User::factory()->business()->withProfile()->create();
        $business = $user->businesses()->wherePivot('role', 'owner')->first();
        $business->update(['name' => 'Test Business Inc']);

        $log = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'credit.grant',
            'auditable_type' => Business::class,
            'auditable_id' => $business->id,
        ]);

        $this->assertEquals('Test Business Inc', $log->getTargetName());
    }

    #[Test]
    public function it_returns_correct_target_name_for_influencer(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $user = User::factory()->influencer()->withProfile()->create([
            'name' => 'Jane Influencer',
        ]);

        $log = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'credit.grant',
            'auditable_type' => Influencer::class,
            'auditable_id' => $user->influencer->id,
        ]);

        $this->assertEquals('Jane Influencer', $log->getTargetName());
    }

    #[Test]
    public function it_returns_null_target_name_when_no_auditable(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $log = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'system.config',
        ]);

        $this->assertNull($log->getTargetName());
    }

    #[Test]
    public function it_stores_ip_address_and_user_agent(): void
    {
        $admin = User::factory()->create([
            'account_type' => AccountType::ADMIN,
        ]);

        $log = AuditLog::create([
            'admin_id' => $admin->id,
            'action' => 'test.action',
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 Test Browser',
        ]);

        $this->assertEquals('192.168.1.100', $log->ip_address);
        $this->assertEquals('Mozilla/5.0 Test Browser', $log->user_agent);
    }
}
