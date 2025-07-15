<?php

namespace Tests\Unit\Livewire;

use Tests\TestCase;
use App\Livewire\BaseComponent;
use App\Models\User;
use App\Models\BusinessProfile;
use App\Models\InfluencerProfile;
use App\Enums\AccountType;
use App\Enums\Niche;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class TestableBaseComponent extends BaseComponent
{
    public function render()
    {
        return '<div>Test Component</div>';
    }
}

class BaseComponentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);
    }

    #[Test]
    public function it_gets_authenticated_user_when_logged_in()
    {
        $this->actingAs($this->user);

        $component = new TestableBaseComponent();
        $authenticatedUser = $component->getAuthenticatedUser();

        $this->assertInstanceOf(User::class, $authenticatedUser);
        $this->assertEquals($this->user->id, $authenticatedUser->id);
    }

    #[Test]
    public function it_returns_null_when_user_not_authenticated()
    {
        $component = new TestableBaseComponent();
        $authenticatedUser = $component->getAuthenticatedUser();

        $this->assertNull($authenticatedUser);
    }

    #[Test]
    public function it_safely_redirects_to_route()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(TestableBaseComponent::class);

        $component->call('safeRedirect', 'dashboard');

        $component->assertRedirect(route('dashboard'));
    }

    #[Test]
    public function it_safely_redirects_to_url()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(TestableBaseComponent::class);

        $component->call('safeRedirect', '/custom-url');

        $component->assertRedirect('/custom-url');
    }

    #[Test]
    public function it_flashes_message_and_redirects()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(TestableBaseComponent::class);

        $component->call('flashAndRedirect', 'Success!', 'success', 'dashboard');

        $component->assertRedirect(route('dashboard'));
        $component->assertSessionHas('flash.message', 'Success!');
        $component->assertSessionHas('flash.type', 'success');
    }

    #[Test]
    public function it_flashes_error_message()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(TestableBaseComponent::class);

        $component->call('flashError', 'Error occurred!');

        $component->assertSessionHas('flash.message', 'Error occurred!');
        $component->assertSessionHas('flash.type', 'error');
    }

    #[Test]
    public function it_flashes_success_message()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(TestableBaseComponent::class);

        $component->call('flashSuccess', 'Success!');

        $component->assertSessionHas('flash.message', 'Success!');
        $component->assertSessionHas('flash.type', 'success');
    }

    #[Test]
    public function it_adds_item_to_array()
    {
        $component = new TestableBaseComponent();

        $array = ['item1', 'item2'];
        $result = $component->addToArray($array, 'item3');

        $this->assertEquals(['item1', 'item2', 'item3'], $result);
    }

    #[Test]
    public function it_does_not_add_duplicate_item_to_array()
    {
        $component = new TestableBaseComponent();

        $array = ['item1', 'item2'];
        $result = $component->addToArray($array, 'item2');

        $this->assertEquals(['item1', 'item2'], $result);
    }

    #[Test]
    public function it_removes_item_from_array()
    {
        $component = new TestableBaseComponent();

        $array = ['item1', 'item2', 'item3'];
        $result = $component->removeFromArray($array, 'item2');

        $this->assertEquals(['item1', 'item3'], array_values($result));
    }

    #[Test]
    public function it_removes_non_existent_item_from_array_gracefully()
    {
        $component = new TestableBaseComponent();

        $array = ['item1', 'item2', 'item3'];
        $result = $component->removeFromArray($array, 'item4');

        $this->assertEquals(['item1', 'item2', 'item3'], array_values($result));
    }

    #[Test]
    public function it_filters_empty_values_from_array()
    {
        $component = new TestableBaseComponent();

        $array = ['item1', '', 'item3', null, 'item5', 0, false];
        $result = $component->filterEmptyValues($array);

        $this->assertEquals(['item1', 'item3', 'item5'], array_values($result));
    }

    #[Test]
    public function it_gets_enum_options()
    {
        $component = new TestableBaseComponent();

        $options = $component->getEnumOptions(Niche::class);

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
        }
    }

    #[Test]
    public function it_checks_if_user_is_business_account()
    {
        $this->actingAs($this->user);

        $component = new TestableBaseComponent();

        $this->assertTrue($component->isBusinessAccount());
    }

    #[Test]
    public function it_checks_if_user_is_influencer_account()
    {
        $influencerUser = User::factory()->create([
            'account_type' => AccountType::INFLUENCER,
        ]);

        $this->actingAs($influencerUser);

        $component = new TestableBaseComponent();

        $this->assertTrue($component->isInfluencerAccount());
    }

    #[Test]
    public function it_returns_false_for_account_type_checks_when_not_authenticated()
    {
        $component = new TestableBaseComponent();

        $this->assertFalse($component->isBusinessAccount());
        $this->assertFalse($component->isInfluencerAccount());
    }

    #[Test]
    public function it_handles_safe_redirect_when_not_authenticated()
    {
        $component = Livewire::test(TestableBaseComponent::class);

        $component->call('safeRedirect', 'dashboard');

        $component->assertRedirect(route('login'));
    }

    #[Test]
    public function it_handles_flash_messages_when_not_authenticated()
    {
        $component = Livewire::test(TestableBaseComponent::class);

        $component->call('flashError', 'Error occurred!');

        $component->assertSessionHas('flash.message', 'Error occurred!');
        $component->assertSessionHas('flash.type', 'error');
    }

    #[Test]
    public function it_handles_array_manipulation_with_empty_arrays()
    {
        $component = new TestableBaseComponent();

        $result = $component->addToArray([], 'item1');
        $this->assertEquals(['item1'], $result);

        $result = $component->removeFromArray([], 'item1');
        $this->assertEquals([], $result);

        $result = $component->filterEmptyValues([]);
        $this->assertEquals([], $result);
    }

    #[Test]
    public function it_handles_array_manipulation_with_null_values()
    {
        $component = new TestableBaseComponent();

        $result = $component->addToArray(['item1', null], 'item2');
        $this->assertEquals(['item1', null, 'item2'], $result);

        $result = $component->removeFromArray(['item1', null, 'item2'], null);
        $this->assertEquals(['item1', 'item2'], array_values($result));
    }

    #[Test]
    public function it_handles_complex_array_filtering()
    {
        $component = new TestableBaseComponent();

        $complexArray = [
            'valid_string',
            '',
            0,
            '0',
            false,
            null,
            'another_valid_string',
            [],
            ['not_empty'],
            (object) [],
            (object) ['prop' => 'value'],
        ];

        $result = $component->filterEmptyValues($complexArray);

        $this->assertContains('valid_string', $result);
        $this->assertContains('another_valid_string', $result);
        $this->assertContains('0', $result);
        $this->assertContains(['not_empty'], $result);
        $this->assertContains((object) ['prop' => 'value'], $result);

        $this->assertNotContains('', $result);
        $this->assertNotContains(0, $result);
        $this->assertNotContains(false, $result);
        $this->assertNotContains(null, $result);
        $this->assertNotContains([], $result);
        $this->assertNotContains((object) [], $result);
    }

    #[Test]
    public function it_works_with_livewire_component_lifecycle()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(TestableBaseComponent::class);

        $component->assertSet('user', null);

        $component->call('getAuthenticatedUser');

        $this->assertNotNull($component->instance()->getAuthenticatedUser());
    }

    #[Test]
    public function it_preserves_array_keys_when_appropriate()
    {
        $component = new TestableBaseComponent();

        $associativeArray = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];

        $result = $component->removeFromArray($associativeArray, 'value2');

        $this->assertEquals([
            'key1' => 'value1',
            'key3' => 'value3',
        ], $result);
    }

    #[Test]
    public function it_handles_enum_options_with_invalid_enum_class()
    {
        $component = new TestableBaseComponent();

        $options = $component->getEnumOptions('InvalidEnumClass');

        $this->assertEquals([], $options);
    }

    #[Test]
    public function it_handles_redirect_with_invalid_route()
    {
        $this->actingAs($this->user);

        $component = Livewire::test(TestableBaseComponent::class);

        $component->call('safeRedirect', 'invalid-route');

        $component->assertRedirect('/invalid-route');
    }
}