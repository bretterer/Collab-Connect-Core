<?php

namespace Tests\Feature\Livewire;

use App\Enums\AccountType;
use App\Livewire\Onboarding\AccountTypeSelection;
use App\Models\PostalCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OnboardingComponentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected PostalCode $postalCode;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $this->postalCode = PostalCode::factory()->create([
            'postal_code' => '12345',
            'place_name' => 'Test City',
            'admin_name1' => 'Test State',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
        ]);
    }

    #[Test]
    public function account_type_selection_component_renders_correctly()
    {
        $component = Livewire::test(AccountTypeSelection::class);

        $component->assertSuccessful();
        $component->assertSee('Welcome to CollabConnect!');
        $component->assertSee('Business');
        $component->assertSee('Influencer/Creator');
    }

    #[Test]
    public function account_type_selection_allows_selecting_business()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(AccountTypeSelection::class);

        $component->set('selectedAccountType', 'business');
        $component->call('continue');

        $component->assertRedirect(route('onboarding.business'));
    }

    #[Test]
    public function account_type_selection_allows_selecting_influencer()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $component = Livewire::test(AccountTypeSelection::class);

        $component->set('selectedAccountType', 'influencer');
        $component->call('continue');

        $component->assertRedirect(route('onboarding.influencer'));
    }

    #[Test]
    public function account_type_selection_requires_selection()
    {
        $component = Livewire::test(AccountTypeSelection::class);

        $component->call('continue');

        $component->assertHasErrors(['selectedAccountType']);
    }
}
