<?php

namespace Tests\Unit\Enums;

use Tests\TestCase;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Enums\CampaignType;
use App\Enums\CollaborationGoal;
use App\Enums\SubscriptionPlan;
use App\Enums\AccountType;
use PHPUnit\Framework\Attributes\Test;

class HasFormOptionsTraitTest extends TestCase
{
    #[Test]
    public function it_returns_options_for_niche_enum()
    {
        $options = Niche::toOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        // Check that each option has value and label
        foreach ($options as $option) {
            $this->assertArrayHasKey('value', $option);
            $this->assertArrayHasKey('label', $option);
            $this->assertIsString($option['value']);
            $this->assertIsString($option['label']);
        }

        // Check specific values
        $fashionOption = collect($options)->firstWhere('value', Niche::FASHION->value);
        $this->assertNotNull($fashionOption);
        $this->assertEquals('Fashion & Style', $fashionOption['label']);

        $beautyOption = collect($options)->firstWhere('value', Niche::BEAUTY->value);
        $this->assertNotNull($beautyOption);
        $this->assertEquals('Beauty & Skincare', $beautyOption['label']);
    }

    #[Test]
    public function it_returns_options_for_social_platform_enum()
    {
        $options = SocialPlatform::toOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        // Check specific values
        $instagramOption = collect($options)->firstWhere('value', SocialPlatform::INSTAGRAM->value);
        $this->assertNotNull($instagramOption);
        $this->assertEquals('Instagram', $instagramOption['label']);

        $tiktokOption = collect($options)->firstWhere('value', SocialPlatform::TIKTOK->value);
        $this->assertNotNull($tiktokOption);
        $this->assertEquals('TikTok', $tiktokOption['label']);
    }

    #[Test]
    public function it_returns_options_for_campaign_type_enum()
    {
        $options = CampaignType::toOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        // Check specific values
        $sponsoredOption = collect($options)->firstWhere('value', CampaignType::SPONSORED_POSTS->value);
        $this->assertNotNull($sponsoredOption);
        $this->assertEquals('Sponsored social media posts', $sponsoredOption['label']);

        $productOption = collect($options)->firstWhere('value', CampaignType::PRODUCT_REVIEWS->value);
        $this->assertNotNull($productOption);
        $this->assertEquals('Product or service reviews', $productOption['label']);
    }

    #[Test]
    public function it_returns_options_for_collaboration_goal_enum()
    {
        $options = CollaborationGoal::toOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        // Check specific values
        $brandOption = collect($options)->firstWhere('value', CollaborationGoal::BRAND_AWARENESS->value);
        $this->assertNotNull($brandOption);
        $this->assertEquals('Increase brand awareness in local community', $brandOption['label']);

        $salesOption = collect($options)->firstWhere('value', CollaborationGoal::CUSTOMER_ACQUISITION->value);
        $this->assertNotNull($salesOption);
        $this->assertEquals('Acquire new customers', $salesOption['label']);
    }

    #[Test]
    public function it_returns_options_for_subscription_plan_enum()
    {
        $options = SubscriptionPlan::toOptions();

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);

        // Check specific values
        $starterOption = collect($options)->firstWhere('value', SubscriptionPlan::BUSINESS_STARTER->value);
        $this->assertNotNull($starterOption);
        $this->assertEquals('Starter - $29/month (Perfect for single location businesses)', $starterOption['label']);

        $professionalOption = collect($options)->firstWhere('value', SubscriptionPlan::BUSINESS_PROFESSIONAL->value);
        $this->assertNotNull($professionalOption);
        $this->assertEquals('Professional - $79/month (Great for multi-location businesses)', $professionalOption['label']);
    }

    #[Test]
    public function it_returns_values_array_for_niche_enum()
    {
        $values = Niche::values();

        $this->assertIsArray($values);
        $this->assertNotEmpty($values);

        // Check that all enum values are present
        $this->assertContains(Niche::FASHION->value, $values);
        $this->assertContains(Niche::BEAUTY->value, $values);
        $this->assertContains(Niche::TECHNOLOGY->value, $values);
        $this->assertContains(Niche::FITNESS->value, $values);
        $this->assertContains(Niche::FOOD->value, $values);
        $this->assertContains(Niche::TRAVEL->value, $values);
        $this->assertContains(Niche::LIFESTYLE->value, $values);
        $this->assertContains(Niche::ENTERTAINMENT->value, $values);
        $this->assertContains(Niche::AUTOMOTIVE->value, $values);
        $this->assertContains(Niche::PETS->value, $values);
    }

    #[Test]
    public function it_returns_labels_array_for_social_platform_enum()
    {
        $labels = SocialPlatform::labels();

        $this->assertIsArray($labels);
        $this->assertNotEmpty($labels);

        // Check that all enum labels are present
        $this->assertContains('Instagram', $labels);
        $this->assertContains('TikTok', $labels);
        $this->assertContains('YouTube', $labels);
        $this->assertContains('X (Twitter)', $labels);
        $this->assertContains('Facebook', $labels);
        $this->assertContains('LinkedIn', $labels);
    }

    #[Test]
    public function it_returns_validation_rule_for_campaign_type_enum()
    {
        $rule = CampaignType::validationRule();

        $this->assertIsString($rule);
        $this->assertStringStartsWith('in:', $rule);

        // Check that all enum values are included in the rule
        $this->assertStringContainsString(CampaignType::SPONSORED_POSTS->value, $rule);
        $this->assertStringContainsString(CampaignType::PRODUCT_REVIEWS->value, $rule);
        $this->assertStringContainsString(CampaignType::BRAND_PARTNERSHIPS->value, $rule);
        $this->assertStringContainsString(CampaignType::EVENT_COVERAGE->value, $rule);
        $this->assertStringContainsString(CampaignType::GIVEAWAYS->value, $rule);
        $this->assertStringContainsString(CampaignType::SEASONAL_CONTENT->value, $rule);
    }

    #[Test]
    public function it_validates_enum_values_correctly()
    {
        // Test valid values
        $this->assertTrue(CollaborationGoal::isValid(CollaborationGoal::BRAND_AWARENESS->value));
        $this->assertTrue(CollaborationGoal::isValid(CollaborationGoal::CUSTOMER_ACQUISITION->value));
        $this->assertTrue(CollaborationGoal::isValid(CollaborationGoal::PRODUCT_LAUNCHES->value));

        // Test invalid values
        $this->assertFalse(CollaborationGoal::isValid('invalid_value'));
        $this->assertFalse(CollaborationGoal::isValid(''));
        $this->assertFalse(CollaborationGoal::isValid(null));
    }

    #[Test]
    public function it_converts_from_value_correctly()
    {
        // Test valid conversions
        $fashionEnum = Niche::fromValue(Niche::FASHION->value);
        $this->assertEquals(Niche::FASHION, $fashionEnum);

        $beautyEnum = Niche::fromValue(Niche::BEAUTY->value);
        $this->assertEquals(Niche::BEAUTY, $beautyEnum);

        // Test invalid conversion
        $invalidEnum = Niche::fromValue('invalid_value');
        $this->assertNull($invalidEnum);
    }

    #[Test]
    public function it_returns_random_enum_value()
    {
        $randomNiche = Niche::random();
        $this->assertInstanceOf(Niche::class, $randomNiche);
        $this->assertContains($randomNiche->value, Niche::values());

        $randomPlatform = SocialPlatform::random();
        $this->assertInstanceOf(SocialPlatform::class, $randomPlatform);
        $this->assertContains($randomPlatform->value, SocialPlatform::values());
    }

    #[Test]
    public function it_returns_consistent_options_structure()
    {
        $enums = [
            Niche::class,
            SocialPlatform::class,
            CampaignType::class,
            CollaborationGoal::class,
            SubscriptionPlan::class,
            // AccountType::class, // Skip AccountType as it uses integer values
        ];

        foreach ($enums as $enumClass) {
            $options = $enumClass::toOptions();

            $this->assertIsArray($options);
            $this->assertNotEmpty($options);

            foreach ($options as $option) {
                $this->assertArrayHasKey('value', $option);
                $this->assertArrayHasKey('label', $option);
                $this->assertIsString($option['value']);
                $this->assertIsString($option['label']);
                $this->assertNotEmpty($option['value']);
                $this->assertNotEmpty($option['label']);
            }
        }
    }

    #[Test]
    public function it_returns_unique_values_in_options()
    {
        $enums = [
            Niche::class,
            SocialPlatform::class,
            CampaignType::class,
            CollaborationGoal::class,
            SubscriptionPlan::class,
            // AccountType::class, // Skip AccountType as it uses integer values
        ];

        foreach ($enums as $enumClass) {
            $options = $enumClass::toOptions();
            $values = collect($options)->pluck('value')->toArray();
            $labels = collect($options)->pluck('label')->toArray();

            $this->assertEquals(count($values), count(array_unique($values)));
            $this->assertEquals(count($labels), count(array_unique($labels)));
        }
    }

    #[Test]
    public function it_returns_non_empty_labels_in_options()
    {
        $enums = [
            Niche::class,
            SocialPlatform::class,
            CampaignType::class,
            CollaborationGoal::class,
            SubscriptionPlan::class,
            // AccountType::class, // Skip AccountType as it uses integer values
        ];

        foreach ($enums as $enumClass) {
            $options = $enumClass::toOptions();

            foreach ($options as $option) {
                $this->assertNotEmpty(trim($option['label']));
                $this->assertNotEmpty(trim($option['value']));
            }
        }
    }

    #[Test]
    public function it_validates_all_enum_values_correctly()
    {
        $enums = [
            Niche::class,
            SocialPlatform::class,
            CampaignType::class,
            CollaborationGoal::class,
            SubscriptionPlan::class,
            // AccountType::class, // Skip AccountType as it uses integer values
        ];

        foreach ($enums as $enumClass) {
            $values = $enumClass::values();

            foreach ($values as $value) {
                $this->assertTrue($enumClass::isValid($value), "Value '{$value}' should be valid for {$enumClass}");
            }

            // Test invalid values
            $this->assertFalse($enumClass::isValid('invalid_value'));
            $this->assertFalse($enumClass::isValid(''));
            $this->assertFalse($enumClass::isValid(null));
        }
    }

    #[Test]
    public function it_converts_all_enum_values_from_value_correctly()
    {
        $enums = [
            Niche::class,
            SocialPlatform::class,
            CampaignType::class,
            CollaborationGoal::class,
            SubscriptionPlan::class,
            // AccountType::class, // Skip AccountType as it uses integer values
        ];

        foreach ($enums as $enumClass) {
            $values = $enumClass::values();

            foreach ($values as $value) {
                $enum = $enumClass::fromValue($value);
                $this->assertInstanceOf($enumClass, $enum);
                $this->assertEquals($value, $enum->value);
            }

            // Test invalid conversion
            $invalidEnum = $enumClass::fromValue('invalid_value');
            $this->assertNull($invalidEnum);
        }
    }

    #[Test]
    public function it_returns_consistent_validation_rules()
    {
        $enums = [
            Niche::class,
            SocialPlatform::class,
            CampaignType::class,
            CollaborationGoal::class,
            SubscriptionPlan::class,
            // AccountType::class, // Skip AccountType as it uses integer values
        ];

        foreach ($enums as $enumClass) {
            $rule = $enumClass::validationRule();
            $values = $enumClass::values();

            $this->assertIsString($rule);
            $this->assertStringStartsWith('in:', $rule);

            foreach ($values as $value) {
                $this->assertStringContainsString($value, $rule);
            }
        }
    }
}