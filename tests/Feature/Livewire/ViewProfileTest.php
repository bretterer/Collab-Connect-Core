<?php

namespace Tests\Feature\Livewire;

use App\Livewire\ViewProfile;
use App\Models\Business;
use App\Models\Influencer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class ViewProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_influencer_profile_successfully(): void
    {
        $user = User::factory()->influencer()->create();
        $influencer = Influencer::factory()->create(['user_id' => $user->id, 'username' => 'testinfluencer']);

        Livewire::test(ViewProfile::class, ['username' => 'testinfluencer'])
            ->assertStatus(200)
            ->assertSee($user->name);
    }

    #[Test]
    public function it_renders_business_profile_successfully(): void
    {
        $user = User::factory()->business()->create();
        $business = Business::factory()->create(['username' => 'testbusiness']);
        $user->businesses()->attach($business->id, ['role' => 'owner']);
        $user->current_business = $business->id;
        $user->save();

        Livewire::test(ViewProfile::class, ['username' => 'testbusiness'])
            ->assertStatus(200)
            ->assertSee($business->name);
    }

    #[Test]
    public function it_renders_profile_by_user_id_when_username_not_found(): void
    {
        $user = User::factory()->influencer()->create();
        Influencer::factory()->create(['user_id' => $user->id]);

        Livewire::test(ViewProfile::class, ['username' => (string) $user->id])
            ->assertStatus(200)
            ->assertSee($user->name);
    }
}
