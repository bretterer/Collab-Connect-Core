# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

CollabConnect is a Laravel-based two-sided marketplace platform connecting local businesses with influencers for collaborative marketing campaigns. Built with the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire) using Laravel 12.

**Key Business Logic:**
- Businesses create campaigns with detailed requirements and compensation
- Influencers discover campaigns through smart matching algorithms based on location, niche, and preferences
- Match scoring uses weighted factors: Location (40%), Niche (30%), Campaign Type (20%), Compensation (10%)
- Subscription-based revenue model (no commission structure)
- Multi-market system with market approval workflow
- Referral program with configurable percentages and automated PayPal payouts

## Development Commands

### Essential Commands
```bash
# Development server (via Laravel Herd - always running at https://collabconnect.test)
# DO NOT run php artisan serve - Herd handles this

# Database operations
php artisan migrate
php artisan migrate:fresh --seed  # Full reset with data
php artisan db:seed --class=StripeDataSeeder  # After migration reset

# Testing
php artisan test  # Run all tests
php artisan test tests/Feature/Livewire/InfluencerCampaignsTest.php  # Specific test file
php artisan test --filter=test_method_name  # Specific test method
php artisan config:clear --ansi && php artisan test  # Clear config cache and test

# Development tools
composer dev  # Runs server, queue, logs, and vite concurrently
npm run dev  # Vite development server
npm run build  # Production build

# Code quality
vendor/bin/pint  # Format code (Laravel Pint)
vendor/bin/pint --dirty  # Format only changed files

# Stripe sync
php artisan collabconnect:sync-stripe  # Sync all products and prices
php artisan collabconnect:sync-stripe --products-only  # Only sync products
php artisan collabconnect:sync-stripe --prices-only  # Only sync prices
```

## Architecture Overview

### Core Models & Relationships
- **User**: Central model with `account_type` enum (BUSINESS, INFLUENCER, ADMIN)
  - `hasOne`: `BusinessProfile` (via `currentBusiness()`), `InfluencerProfile` (via `influencer()`)
  - `belongsToMany`: `businesses` (many-to-many through `business_users` pivot)
  - `hasMany`: `campaigns`, `campaignApplications`, `socialMediaAccounts`, `messages`
  - Uses `current_business` column to track active business for multi-business accounts

- **Campaign**: Complex model with extensive fields for campaign management
  - `belongsTo`: `User` (as business owner)
  - `hasMany`: `campaignApplications`
  - Extensive enum-based fields (status, compensation_type, campaign_type)
  - Array fields: `target_platforms`, `deliverables`, `success_metrics`, `compensation_details`

- **PostalCode**: Geographic data for location-based matching
  - Contains lat/long coordinates for proximity searches
  - `withinRadius()` method for finding nearby zip codes
  - Used extensively in `SearchService` and `MatchScoreService`

- **Market**: Geographic market system
  - Markets can be enabled/disabled via settings
  - Users require market approval to access main platform
  - `belongsToMany` relationship with `PostalCode` through pivot table

- **Referral System**: Complete referral tracking and payout system
  - `ReferralEnrollment`: User enrollment in referral program
  - `Referral`: Individual referral records with commission tracking
  - `ReferralPayout`: PayPal payout batches with automated processing
  - `ReferralSettings`: Singleton model for program configuration

### Livewire Architecture
- **BaseComponent** (`app/Livewire/BaseComponent.php`): Abstract class providing common utilities
  - Authentication helpers, validation utilities
  - Enum handling, array manipulation, account type checks
  - **All Livewire components should extend BaseComponent**

- **Volt Components**: Primary approach for new interactive pages
  - Class-based Volt syntax (extends `Livewire\Volt\Component`)
  - Co-located PHP logic and Blade templates in single file
  - Create with: `php artisan make:volt [name]`

- **Wizard Pattern**: Multi-step processes use `HasWizardSteps` trait
  - Used in onboarding flows and campaign creation
  - Provides `currentStep`, `nextStep()`, `previousStep()`, progress tracking
  - Requires implementing `validateCurrentStep()` and `completeOnboarding()`

### Key Services
- **SearchService**: User search (businesses finding influencers, vice versa)
  - Complex filtering: location, niche, platforms, followers, engagement, price, quality
  - Proximity-based search using `PostalCode::withinRadius()`
  - Dynamic relationship loading based on search context

- **MatchScoreService**: Campaign matching algorithm for influencers
  - Weighted scoring: Location (40%), Niche (30%), Campaign Type (20%), Compensation (10%)
  - Returns scores 0-100 for visual indicators (green/yellow/red)

- **CampaignService**: Campaign CRUD operations and lifecycle management
  - Status transitions, validation, event dispatching
  - Handles scheduled vs. immediate publishing

- **ProfileService**: User profile management
  - Onboarding completion checks
  - Multi-business profile switching logic

- **PayPalPayoutsService**: Automated referral payout processing
  - Creates PayPal payout batches
  - Tracks payout status and updates records
  - Requires PayPal API credentials

### Event System
Events dispatched for important lifecycle changes:
- Campaign: `CampaignPublished`, `CampaignScheduled`, `CampaignArchived`, `CampaignEdited`
- User: `BusinessJoined`, `InfluencerJoined`, `ProfileUpdated`, `AccountTypeSelected`
- Applications: `CampaignApplicationSubmitted` (creates notifications)

### Enum System
Extensive use of enums with `HasFormOptions` trait for form generation:
- **AccountType**: BUSINESS, INFLUENCER, ADMIN
- **CampaignStatus**: DRAFT, PUBLISHED, SCHEDULED, ARCHIVED
- **CompensationType**: MONETARY, PRODUCT, BARTER, DISCOUNT
- **CampaignType**: USER_GENERATED, PRODUCT_REVIEW, BRAND_AWARENESS, EVENT_PROMOTION
- **TargetPlatform**: INSTAGRAM, TIKTOK, YOUTUBE, FACEBOOK, X
- **DeliverableType**: INSTAGRAM_POST, INSTAGRAM_REEL, INSTAGRAM_STORY, TIKTOK_VIDEO
- **SuccessMetric**: IMPRESSIONS, ENGAGEMENT_RATE, CLICKS, CONVERSIONS

All enums provide:
- `toOptions()`: Form dropdown data
- `validationRule()`: Validation rule string
- `label()`: Human-readable label
- `values()`: Array of all values

### Landing Pages System
Class-based block architecture for building marketing landing pages:
- **BlockRegistry**: Central registry for block types
- **BlockInterface**: All blocks must implement this interface
- **BaseBlock**: Abstract class providing common block functionality
- Block types: `TextBlock`, `ImageBlock`, `CTABlock` (with form actions)
- Blocks registered in service provider, rendered dynamically

## Development Guidelines

### Livewire-First Approach
- Always use Livewire components over traditional controllers
- Prefer Volt for new pages with interactivity
- Extend `BaseComponent` for all class-based Livewire components
- Use Livewire navigation (`wire:navigate`) for better UX

### Enum Usage
- Always validate enum values using `HasFormOptions` trait methods
- Use `toOptions()` for form dropdowns
- Use `validationRule()` for validation rules
- Never hardcode enum values; use enum cases

### Route Structure
```
/dashboard - Main dashboard (post-onboarding)
/onboarding/* - Account setup flow (business/influencer)
/campaigns/* - Campaign management (CRUD)
/discover - Influencer campaign discovery
/search - User search (businesses/influencers)
/admin/* - Admin panel (requires admin account type)
/market-waitlist - Market approval waitlist
```

### Database Patterns
- Uses MySQL for development/testing/production (configured in phpunit.xml)
- Extensive use of `casts()` method for JSON fields and enums
- Array fields must be cast as 'array' in model casts
- Factory-based test data generation
- **Can update existing migrations** - project not in production yet

### Authentication & Authorization
- Standard Laravel authentication with Livewire auth components
- `EnsureOnboardingCompleted` middleware protects main application routes
- `EnsureMarketApproved` middleware controls market access
- `EnsureAdminAccess` middleware for admin panel
- Account type-based access control throughout application

### Frontend Stack
- **Tailwind CSS 4.x** (not v3 - different syntax)
- **Flux UI Pro** - Component library for Livewire
- Alpine.js (bundled with Livewire - do not include manually)
- Vite with hot reload

**CRITICAL - Flux Component Size Limitations:**
- **DO NOT use `size="lg"`** - will cause UnhandledMatchError
- `flux:heading` only supports: `xl`, `base`, `sm`
- `flux:button` only supports: `base`, `sm`
- Available components: accordion, autocomplete, avatar, badge, brand, breadcrumbs, button, calendar, callout, card, chart, checkbox, command, context, date-picker, dropdown, editor, field, heading, icon, input, modal, navbar, pagination, popover, profile, radio, select, separator, switch, table, tabs, text, textarea, toast, tooltip

### Toast Messages
- **DO NOT use `session()->flash()`** for toast messages
- Use `Flux::toast()` or `Toaster::toast()` for user notifications
- Available in both Livewire components and Volt components

### Testing Strategy
- Feature tests for Livewire/Volt components and business logic
- Unit tests for services, models, and enums
- Factory-based test data generation
- MySQL test database (not SQLite)
- **Must use `#[Test]` attribute** for all tests
- Livewire testing: `Livewire::test(Component::class)` or `Volt::test('component-name')`

### Settings System (spatie/laravel-settings)
- Settings stored in database, cached for performance
- Create settings classes extending `Spatie\LaravelSettings\Settings`
- Must implement `group()` method returning unique identifier
- Access via dependency injection: `function index(GeneralSettings $settings)`
- Modify with `$settings->property = value; $settings->save();`
- Create migrations: `php artisan make:settings-migration CreateGeneralSettings`
- Use `encrypted: true` for sensitive values

### Laravel Cashier / Stripe Payments
- Uses Laravel Cashier for subscription and one-time payment handling
- **CRITICAL - One-time charges with stored payment methods:**
  ```php
  $billable->charge($amount, $paymentMethodId, [
      'description' => 'Description here',
      'metadata' => [...],
      'confirm' => true,
      'payment_method_types' => ['card'],  // Required to avoid redirect URL errors
  ]);
  ```
- Always include `'confirm' => true` and `'payment_method_types' => ['card']` when charging stored payment methods
- Without these options, Stripe's automatic payment methods feature requires a `return_url` for redirect-based payment methods
- Subscription credits are managed via `SubscriptionLimitsService` and `SubscriptionCredit` model
- One-time credit purchases use `SubscriptionLimits::addCredits($billable, $key, $amount)` after successful charge

## Key Files to Understand

### Core Architecture
- `app/Livewire/BaseComponent.php` - Foundation for all Livewire components
- `app/Models/User.php` - Central user model with multi-business support
- `app/Models/Campaign.php` - Complex campaign model with extensive business logic
- `app/Enums/Traits/HasFormOptions.php` - Enum form option utilities
- `app/Livewire/Traits/HasWizardSteps.php` - Multi-step form wizard implementation

### Business Logic
- `app/Services/SearchService.php` - User search with proximity filtering
- `app/Services/MatchScoreService.php` - Campaign matching algorithm
- `app/Services/CampaignService.php` - Campaign lifecycle management
- `app/Services/PayPalPayoutsService.php` - Automated referral payouts

### Configuration
- `routes/web.php` - Route structure and middleware configuration
- `phpunit.xml` - Test configuration (MySQL database)
- `composer.json` - Package dependencies and scripts
- `app/LandingPages/BlockRegistry.php` - Landing page block system

## Common Patterns

### Multi-Step Wizards
```php
use App\Livewire\Traits\HasWizardSteps;

class MyWizard extends Component
{
    use HasWizardSteps;

    public function getTotalSteps(): int { return 4; }

    protected function validateCurrentStep(): void {
        // Validate current step
    }

    public function completeOnboarding(): void {
        // Final submission
    }
}
```

### Enum Form Options
```php
// In Livewire component
$platforms = TargetPlatform::toOptions();

// In validation
'platform' => ['required', TargetPlatform::validationRule()],

// In Blade
@foreach(TargetPlatform::toOptions() as $option)
    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
@endforeach
```

### Proximity Search
```php
$postalCode = PostalCode::where('postal_code', $zipCode)->first();
$nearbyZips = $postalCode->withinRadius(50); // 50 mile radius
```

### Match Score Calculation
```php
use App\Facades\MatchScore;

$score = MatchScore::calculate($campaign, $influencer);
$color = MatchScore::getScoreColor($score); // green/yellow/red
```

## Important Notes
- Application served via **Laravel Herd** at `https://collabconnect.test`
- **Never run `php artisan serve`** - Herd handles this automatically
- Use `get-absolute-url` MCP tool to generate URLs for the user
- **Always run `vendor/bin/pint --dirty`** before committing code
- Use Laravel Boost MCP tools for documentation, tinker, logs, and more
- Do not use `$table->enum` ever. If you need an email in the table, use strings and create or use an existing enum class

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3.27
- laravel/cashier (CASHIER) - v15
- laravel/framework (LARAVEL) - v12
- laravel/nightwatch (NIGHTWATCH) - v1
- laravel/pennant (PENNANT) - v1
- laravel/prompts (PROMPTS) - v0
- laravel/reverb (REVERB) - v1
- livewire/flux (FLUXUI_FREE) - v2
- livewire/flux-pro (FLUXUI_PRO) - v2
- livewire/livewire (LIVEWIRE) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v4
- laravel-echo (ECHO) - v2

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure - don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.


=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double check the available parameters.

## URLs
- Whenever you share a project URL with the user you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain / IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation specific for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The 'search-docs' tool is perfect for all Laravel related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel-ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries - package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over comments. Never use comments within the code itself unless there is something _very_ complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.


=== herd rules ===

## Laravel Herd

- The application is served by Laravel Herd and will be available at: https?://[kebab-case-project-dir].test. Use the `get-absolute-url` tool to generate URLs for the user to ensure valid URLs.
- You must not run any commands to make the site available via HTTP(s). It is _always_ available through Laravel Herd.


=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] <name>` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.


=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- No middleware files in `app/Http/Middleware/`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- **No app\Console\Kernel.php** - use `bootstrap/app.php` or `routes/console.php` for console configuration.
- **Commands auto-register** - files in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

### Directives
- DO NOT use the `@php` blade directive unless there is an absoulte need to do so. This clutters up the blade file. Any need for php should be done inside of the livewire component.


=== pennant/core rules ===

## Laravel Pennant

- This application uses Laravel Pennant for feature flag management, providing a flexible system for controlling feature availability across different organizations and user types.
- Use the `search-docs` tool if available, in combination with existing codebase conventions, to assist the user effectively with feature flags.


=== fluxui-pro/core rules ===

## Flux UI Pro

- This project is using the Pro version of Flux UI. It has full access to the free components and variants, as well as full access to the Pro components and variants.
- Flux UI is a component library for Livewire. Flux is a robust, hand-crafted, UI component library for your Livewire applications. It's built using Tailwind CSS and provides a set of components that are easy to use and customize.
- You should use Flux UI components when available.
- Fallback to standard Blade components if Flux is unavailable.
- If available, use Laravel Boost's `search-docs` tool to get the exact documentation and code snippets available for this project.
- Flux UI components look like this:

<code-snippet name="Flux UI component usage example" lang="blade">
    <flux:button variant="primary"/>
</code-snippet>


### Available Components
This is correct as of Boost installation, but there may be additional components within the codebase.

<available-flux-components>
accordion, autocomplete, avatar, badge, brand, breadcrumbs, button, calendar, callout, card, chart, checkbox, command, context, date-picker, dropdown, editor, field, heading, file upload, icon, input, modal, navbar, pagination, pillbox, popover, profile, radio, select, separator, switch, table, tabs, text, textarea, toast, tooltip
</available-flux-components>


=== livewire/core rules ===

## Livewire Core
- Use the `search-docs` tool to find exact version specific documentation for how to write Livewire & Livewire tests.
- Use the `php artisan make:livewire [Posts\CreatePost]` artisan command to create new components
- State should live on the server, with the UI reflecting it.
- All Livewire requests hit the Laravel backend, they're like regular HTTP requests. Always validate form data, and run authorization checks in Livewire actions.

## Livewire Best Practices
- Livewire components require a single root element.
- Use `wire:loading` and `wire:dirty` for delightful loading states.
- Add `wire:key` in loops:

    ```blade
    @foreach ($items as $item)
        <div wire:key="item-{{ $item->id }}">
            {{ $item->name }}
        </div>
    @endforeach
    ```

- Prefer lifecycle hooks like `mount()`, `updatedFoo()` for initialization and reactive side effects:

<code-snippet name="Lifecycle hook examples" lang="php">
    public function mount(User $user) { $this->user = $user; }
    public function updatedSearch() { $this->resetPage(); }
</code-snippet>


## Testing Livewire

<code-snippet name="Example Livewire component test" lang="php">
    Livewire::test(Counter::class)
        ->assertSet('count', 0)
        ->call('increment')
        ->assertSet('count', 1)
        ->assertSee(1)
        ->assertStatus(200);
</code-snippet>


    <code-snippet name="Testing a Livewire component exists within a page" lang="php">
        $this->get('/posts/create')
        ->assertSeeLivewire(CreatePost::class);
    </code-snippet>


=== livewire/v3 rules ===

## Livewire 3

### Key Changes From Livewire 2
- These things changed in Livewire 2, but may not have been updated in this application. Verify this application's setup to ensure you conform with application conventions.
    - Use `wire:model.live` for real-time updates, `wire:model` is now deferred by default.
    - Components now use the `App\Livewire` namespace (not `App\Http\Livewire`).
    - Use `$this->dispatch()` to dispatch events (not `emit` or `dispatchBrowserEvent`).
    - Use the `components.layouts.app` view as the typical layout path (not `layouts.app`).

### New Directives
- `wire:show`, `wire:transition`, `wire:cloak`, `wire:offline`, `wire:target` are available for use. Use the documentation to find usage examples.

### Alpine
- Alpine is now included with Livewire, don't manually include Alpine.js.
- Plugins included with Alpine: persist, intersect, collapse, and focus.

### Lifecycle Hooks
- You can listen for `livewire:init` to hook into Livewire initialization, and `fail.status === 419` for the page expiring:

<code-snippet name="livewire:load example" lang="js">
document.addEventListener('livewire:init', function () {
    Livewire.hook('request', ({ fail }) => {
        if (fail && fail.status === 419) {
            alert('Your session expired');
        }
    });

    Livewire.hook('message.failed', (message, component) => {
        console.error(message);
    });
});
</code-snippet>

### General
- Do not use `wire:confirm`, instead use a modal


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before committing to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


=== phpunit/core rules ===

## PHPUnit Core

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `php artisan make:test --phpunit <name>` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should test all of the happy paths, failure paths, and weird paths.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files, these are core to the application.

### Running Tests
- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `php artisan test`.
- To run all tests in a file: `php artisan test tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --filter=testName` (recommended after making a change to a related file).


=== tailwindcss/core rules ===

## Tailwind Core

- Use Tailwind CSS classes to style HTML, check and use existing tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc..)
- Think through class placement, order, priority, and defaults - remove redundant classes, add classes to parent or child carefully to limit repetition, group elements logically
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing, don't use margins.

    <code-snippet name="Valid Flex Gap Spacing Example" lang="html">
        <div class="flex gap-8">
            <div>Superior</div>
            <div>Michigan</div>
            <div>Erie</div>
        </div>
    </code-snippet>


### Dark Mode
- New pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.
<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>


### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option - use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |


=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test` with a specific filename or filter.
</laravel-boost-guidelines>
