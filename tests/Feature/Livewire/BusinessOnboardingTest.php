<?php

namespace Tests\Feature\Livewire;

use App\Enums\AccountType;
use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompanySize;
use App\Enums\ContactRole;
use App\Enums\YearsInBusiness;
use App\Livewire\Onboarding\BusinessOnboarding;
use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;

class BusinessOnboardingTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'account_type' => AccountType::BUSINESS,
        ]);

        $this->actingAs($this->user);
    }

    #[Test]
    public function component_renders_successfully()
    {
        Livewire::test(BusinessOnboarding::class)
            ->assertStatus(200)
            ->assertSee('Basic Business Information')
            ->assertSet('step', 1);
    }

    #[Test]
    public function component_initializes_with_correct_default_values()
    {
        Livewire::test(BusinessOnboarding::class)
            ->assertSet('step', 1)
            ->assertSet('businessName', '')
            ->assertSet('businessEmail', '')
            ->assertSet('emailNotifications', true)
            ->assertSet('marketingEmails', false)
            ->assertSet('targetAgeRange', [])
            ->assertSet('businessGoals', [])
            ->assertSet('platforms', []);
    }

    #[Test]
    public function component_loads_existing_business_data_when_present()
    {
        $business = Business::factory()->create([
            'name' => 'Test Business',
            'email' => 'test@business.com',
            'phone' => '555-1234',
            'website' => 'https://testbusiness.com',
            'primary_contact' => 'John Doe',
            'contact_role' => ContactRole::OWNER->value,
            'maturity' => YearsInBusiness::ESTABLISHED->value,
            'size' => CompanySize::SMALL_TEAM->value,
            'type' => BusinessType::ECOMMERCE,
            'industry' => BusinessIndustry::TECHNOLOGY,
            'description' => 'A test business',
            'selling_points' => 'We are awesome',
            'city' => 'Test City',
            'state' => 'Test State',
            'postal_code' => '12345',
            'target_age_range' => ['18-24', '25-34'],
            'target_gender' => ['male', 'female'],
            'business_goals' => ['brand_awareness'],
            'platforms' => ['instagram', 'facebook'],
        ]);

        $this->user->update(['current_business' => $business->id]);

        Livewire::test(BusinessOnboarding::class)
            ->assertSet('businessName', 'Test Business')
            ->assertSet('businessEmail', 'test@business.com')
            ->assertSet('phoneNumber', '555-1234')
            ->assertSet('website', 'https://testbusiness.com')
            ->assertSet('contactName', 'John Doe')
            ->assertSet('contactRole', ContactRole::OWNER->value)
            ->assertSet('yearsInBusiness', YearsInBusiness::ESTABLISHED->value)
            ->assertSet('companySize', CompanySize::SMALL_TEAM->value)
            ->assertSet('businessType', BusinessType::ECOMMERCE->value)
            ->assertSet('industry', BusinessIndustry::TECHNOLOGY->value)
            ->assertSet('businessDescription', 'A test business')
            ->assertSet('uniqueValueProposition', 'We are awesome')
            ->assertSet('city', 'Test City')
            ->assertSet('state', 'Test State')
            ->assertSet('postalCode', '12345')
            ->assertSet('targetAgeRange', ['18-24', '25-34'])
            ->assertSet('targetGender', ['male', 'female'])
            ->assertSet('businessGoals', ['brand_awareness'])
            ->assertSet('platforms', ['instagram', 'facebook']);
    }

    #[Test]
    public function step_1_validation_works_correctly()
    {
        Livewire::test(BusinessOnboarding::class)
            ->call('nextStep')
            ->assertHasErrors([
                'businessName' => 'required',
                'businessEmail' => 'required',
                'phoneNumber' => 'required',
                'contactName' => 'required',
                'contactRole' => 'required',
                'yearsInBusiness' => 'required',
                'companySize' => 'required',
            ]);
    }

    #[Test]
    public function step_1_can_be_completed_with_valid_data()
    {
        Livewire::test(BusinessOnboarding::class)
            ->set('businessName', 'Test Business')
            ->set('businessEmail', 'test@business.com')
            ->set('phoneNumber', '555-1234')
            ->set('website', 'https://testbusiness.com')
            ->set('contactName', 'John Doe')
            ->set('contactRole', ContactRole::OWNER->value)
            ->set('yearsInBusiness', YearsInBusiness::ESTABLISHED->value)
            ->set('companySize', CompanySize::SMALL_TEAM->value)
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('step', 2);

        // Verify business was created
        $this->assertDatabaseHas('businesses', [
            'name' => 'Test Business',
            'email' => 'test@business.com',
            'phone' => '555-1234',
            'website' => 'https://testbusiness.com',
            'primary_contact' => 'John Doe',
            'contact_role' => ContactRole::OWNER->value,
            'maturity' => YearsInBusiness::ESTABLISHED->value,
            'size' => CompanySize::SMALL_TEAM->value,
        ]);

        // Verify business-user relationship was created
        $this->assertDatabaseHas('business_users', [
            'user_id' => $this->user->id,
            'role' => 'owner',
        ]);
    }

    #[Test]
    public function step_2_validation_works_correctly()
    {
        $business = $this->createBusinessAndGoToStep(2);

        Livewire::test(BusinessOnboarding::class)
            ->set('step', 2)
            ->set('businessType', '') // Clear the field to trigger validation
            ->set('industry', '') // Clear the field to trigger validation
            ->set('businessDescription', '') // Clear the field to trigger validation
            ->call('nextStep')
            ->assertHasErrors([
                'businessType' => 'required',
                'industry' => 'required',
                'businessDescription' => 'required',
            ]);
    }

    #[Test]
    public function step_2_can_be_completed_with_valid_data()
    {
        $business = $this->createBusinessAndGoToStep(2);

        Livewire::test(BusinessOnboarding::class)
            ->set('step', 2)
            ->set('businessType', BusinessType::ECOMMERCE->value)
            ->set('industry', BusinessIndustry::TECHNOLOGY->value)
            ->set('businessDescription', 'A technology company')
            ->set('uniqueValueProposition', 'We innovate')
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('step', 3);

        // Verify business was updated
        $this->assertDatabaseHas('businesses', [
            'id' => $business->id,
            'type' => BusinessType::ECOMMERCE->value,
            'industry' => BusinessIndustry::TECHNOLOGY->value,
            'description' => 'A technology company',
            'selling_points' => 'We innovate',
        ]);
    }

    #[Test]
    public function step_3_can_be_completed_with_optional_data()
    {
        $business = $this->createBusinessAndGoToStep(3);

        Livewire::test(BusinessOnboarding::class)
            ->set('step', 3)
            ->set('city', 'Test City')
            ->set('state', 'Test State')
            ->set('postalCode', '12345')
            ->set('targetAgeRange', ['18-24', '25-34'])
            ->set('targetGender', ['male', 'female'])
            ->set('businessGoals', ['brand_awareness', 'product_promotion'])
            ->set('platforms', ['instagram', 'facebook', 'tiktok'])
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('step', 4);

        // Verify business was updated with step 3 data
        $business->refresh();
        $this->assertEquals('Test City', $business->city);
        $this->assertEquals('Test State', $business->state);
        $this->assertEquals('12345', $business->postal_code);
        $this->assertEquals(['18-24', '25-34'], $business->target_age_range);
        $this->assertEquals(['male', 'female'], $business->target_gender);
        $this->assertEquals(['brand_awareness', 'product_promotion'], $business->business_goals);
        $this->assertEquals(['instagram', 'facebook', 'tiktok'], $business->platforms);
    }

    #[Test]
    public function can_navigate_backwards_through_steps()
    {
        $this->createBusinessAndGoToStep(3);

        Livewire::test(BusinessOnboarding::class)
            ->set('step', 3)
            ->call('previousStep')
            ->assertSet('step', 2)
            ->call('previousStep')
            ->assertSet('step', 1);
    }

    #[Test]
    public function cannot_navigate_below_step_1()
    {
        Livewire::test(BusinessOnboarding::class)
            ->set('step', 1)
            ->call('previousStep')
            ->assertSet('step', 1);
    }

    #[Test]
    public function onboarding_caching_works_correctly()
    {
        $business = $this->createBusinessAndGoToStep(2);
        $cacheKey = 'onboarding_step_business_'.$business->id;

        // Verify cache is set
        $this->assertEquals(2, Cache::get($cacheKey));

        // Test component loads from cache
        Livewire::test(BusinessOnboarding::class)
            ->assertSet('step', 2);
    }

    #[Test]
    public function complete_onboarding_works_correctly()
    {
        $business = $this->createBusinessAndGoToStep(4);

        Livewire::test(BusinessOnboarding::class)
            ->set('step', 4)
            ->call('completeOnboarding')
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Welcome to CollabConnect! Your business profile is now complete.');

        // Verify business onboarding is marked complete
        $business->refresh();
        $this->assertEquals(1, $business->onboarding_complete);

        // Verify cache is cleared
        $cacheKey = 'onboarding_step_business_'.$business->id;
        $this->assertNull(Cache::get($cacheKey));
    }

    #[Test]
    public function business_goals_array_is_reindexed_on_update()
    {
        Livewire::test(BusinessOnboarding::class)
            ->set('businessGoals', ['brand_awareness', 'product_promotion'])
            ->assertSet('businessGoals', ['brand_awareness', 'product_promotion']);
    }

    #[Test]
    public function platforms_array_is_reindexed_on_update()
    {
        Livewire::test(BusinessOnboarding::class)
            ->set('platforms', ['instagram', 'facebook', 'tiktok'])
            ->assertSet('platforms', ['instagram', 'facebook', 'tiktok']);
    }

    #[Test]
    public function get_max_steps_returns_correct_count()
    {
        $component = Livewire::test(BusinessOnboarding::class);
        $maxSteps = $component->instance()->getMaxSteps();

        $this->assertEquals(5, $maxSteps);
    }

    #[Test]
    public function get_current_step_data_returns_correct_configuration()
    {
        $component = Livewire::test(BusinessOnboarding::class)
            ->set('step', 2);

        $stepData = $component->instance()->getCurrentStepData();

        $this->assertEquals('Business Profile & Identity', $stepData['title']);
        $this->assertEquals('step2', $stepData['component']);
        $this->assertContains('businessType', $stepData['fields']);
    }

    #[Test]
    public function validation_rules_are_correct_for_each_step()
    {
        $component = Livewire::test(BusinessOnboarding::class);
        $instance = $component->instance();

        // Step 1 rules
        $step1Rules = $instance->getValidationRulesForStep(1);
        $this->assertArrayHasKey('businessName', $step1Rules);
        $this->assertArrayHasKey('businessEmail', $step1Rules);
        $this->assertEquals('required|string|max:255', $step1Rules['businessName']);

        // Step 2 rules
        $step2Rules = $instance->getValidationRulesForStep(2);
        $this->assertArrayHasKey('businessType', $step2Rules);
        $this->assertArrayHasKey('industry', $step2Rules);
        $this->assertArrayHasKey('businessDescription', $step2Rules);

        // Step 3 rules
        $step3Rules = $instance->getValidationRulesForStep(3);
        $this->assertArrayHasKey('city', $step3Rules);
        $this->assertArrayHasKey('targetAgeRange', $step3Rules);
        $this->assertArrayHasKey('businessGoals', $step3Rules);
        $this->assertArrayHasKey('platforms', $step3Rules);

        // Step 4 has no validation rules
        $step4Rules = $instance->getValidationRulesForStep(4);
        $this->assertEmpty($step4Rules);
    }

    #[Test]
    public function email_invalid_format_shows_validation_error()
    {
        Livewire::test(BusinessOnboarding::class)
            ->set('businessEmail', 'invalid-email')
            ->call('nextStep')
            ->assertHasErrors(['businessEmail' => 'email']);
    }

    #[Test]
    public function website_invalid_url_shows_validation_error()
    {
        Livewire::test(BusinessOnboarding::class)
            ->set('businessName', 'Test Business')
            ->set('businessEmail', 'test@business.com')
            ->set('phoneNumber', '555-1234')
            ->set('website', 'not-a-valid-url')
            ->set('contactName', 'John Doe')
            ->set('contactRole', ContactRole::OWNER->value)
            ->set('yearsInBusiness', YearsInBusiness::ESTABLISHED->value)
            ->set('companySize', CompanySize::SMALL_TEAM->value)
            ->call('nextStep')
            ->assertHasErrors(['website' => 'url']);
    }

    #[Test]
    public function business_description_too_long_shows_validation_error()
    {
        $business = $this->createBusinessAndGoToStep(2);

        Livewire::test(BusinessOnboarding::class)
            ->set('step', 2)
            ->set('businessType', BusinessType::ECOMMERCE->value)
            ->set('industry', BusinessIndustry::TECHNOLOGY->value)
            ->set('businessDescription', str_repeat('a', 1001)) // Exceeds 1000 character limit
            ->call('nextStep')
            ->assertHasErrors(['businessDescription' => 'max']);
    }

    private function createBusinessAndGoToStep(int $targetStep): Business
    {
        // Create a business for the user
        $business = Business::factory()->create([
            'name' => 'Test Business',
            'email' => 'test@business.com',
            'phone' => '555-1234',
            'primary_contact' => 'John Doe',
            'contact_role' => ContactRole::OWNER->value,
            'maturity' => YearsInBusiness::ESTABLISHED->value,
            'size' => CompanySize::SMALL_TEAM->value,
        ]);

        // Link business to user
        $this->user->update(['current_business' => $business->id]);
        \App\Models\BusinessUser::create([
            'business_id' => $business->id,
            'user_id' => $this->user->id,
            'role' => 'owner',
        ]);

        // Set cache to target step
        if ($targetStep > 1) {
            Cache::put('onboarding_step_business_'.$business->id, $targetStep, now()->addHours(24));
        }

        return $business;
    }
}
