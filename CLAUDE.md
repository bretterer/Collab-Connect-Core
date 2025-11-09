# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

CollabConnect is a Laravel-based platform connecting local businesses with influencers for collaborative marketing campaigns. It's built using the TALL stack (Tailwind CSS, Alpine.js, Laravel, Livewire) with Laravel 12.

**Key Business Logic:**
- Businesses create campaigns with detailed requirements and compensation
- Influencers discover campaigns through smart matching algorithms based on location, niche, and preferences
- Match scoring uses weighted factors: Location (40%), Niche (30%), Campaign Type (20%), Compensation (10%)
- Two-sided marketplace with subscription-based revenue model (no commission structure)

## Development Commands

### Essential Commands
```bash
# Development server (via Laravel Herd - always running at https://collabconnect.test)

# Database operations
php artisan migrate
php artisan db:seed

# If resetting the database, please run
php artisan migrate --seed
#or
php artisan migrate:fresh --seed
# and then, you need to run
php artisan db:seed --class=StripeDataSeeder

# Testing
php artisan test
php artisan test tests/Feature/Livewire/InfluencerCampaignsTest.php
php artisan config:clear --ansi && php artisan test

# Development tools
composer dev

# Stripe sync
php artisan collabconnect:sync-stripe  # Sync all products and prices
php artisan collabconnect:sync-stripe --products-only  # Only sync products
php artisan collabconnect:sync-stripe --prices-only  # Only sync prices
php artisan collabconnect:sync-stripe --limit=50  # Limit items synced
php artisan collabconnect:sync-stripe --active-only  # Only sync active items
php artisan collabconnect:sync-stripe --force  # Skip confirmation prompt

# Frontend
npm run dev
npm run build
```

## Architecture Overview

### Core Models & Relationships
- **User**: Central model with account_type enum (BUSINESS, INFLUENCER, ADMIN)
  - hasOne BusinessProfile, InfluencerProfile
  - hasMany Campaigns, CampaignApplications, Notifications, SocialMediaAccounts
- **Campaign**: Complex model with extensive fields for campaign management
  - belongsTo User, hasMany CampaignApplications
  - Extensive enum-based fields (status, compensation_type, campaign_type)
- **PostalCode**: Geographic data for location-based matching
- **CampaignApplication**: Junction model for influencer applications

### Livewire Architecture
- **BaseComponent**: Abstract class providing common utilities for all Livewire components
  - Authentication helpers, validation utilities
  - Enum handling, array manipulation, account type checks
- **Wizard Pattern**: Uses HasWizardSteps trait for multi-step processes (onboarding, campaign creation)
- **Component Organization**: Grouped by feature (Auth, Campaigns, Onboarding)

### Key Services
- **CampaignService**: Campaign CRUD operations, status management, event dispatching
- **ProfileService**: User profile management and onboarding completion checks
- **SearchService**: Campaign discovery and matching algorithm implementation
- **NotificationService**: User notification management
- **ValidationService**: Centralized validation logic
- **SubscriptionService**: Subscription plan management and billing

### Event System
Events are dispatched for campaign lifecycle changes:
- CampaignPublished, CampaignScheduled, CampaignArchived, CampaignEdited
- BusinessJoined, InfluencerJoined, ProfileUpdated
- CampaignApplicationSubmitted (creates notifications)

### Enum System
Extensive use of enums with HasFormOptions trait for form generation:
- **AccountType**: BUSINESS, INFLUENCER, ADMIN
- **CampaignStatus**: DRAFT, PUBLISHED, SCHEDULED, ARCHIVED
- **CompensationType**: MONETARY, PRODUCT, BARTER, DISCOUNT
- **CampaignType**: USER_GENERATED, PRODUCT_REVIEW, BRAND_AWARENESS, EVENT_PROMOTION
- **TargetPlatform**: INSTAGRAM, TIKTOK, YOUTUBE, FACEBOOK, X
- **DeliverableType**: INSTAGRAM_POST, INSTAGRAM_REEL, INSTAGRAM_STORY, TIKTOK_VIDEO
- **SuccessMetric**: IMPRESSIONS, ENGAGEMENT_RATE, CLICKS, CONVERSIONS

## Development Guidelines

### Livewire-First Approach
- Always use Livewire components over traditional controllers
- Extend BaseComponent for all Livewire components
- Use Livewire navigation (wire:navigate) for better UX

### Enum Usage
- Always validate enum values in forms and APIs using HasFormOptions trait
- Use toOptions() method for form dropdowns
- Leverage validationRule() method for validation

### Route Structure
```
/dashboard - Main dashboard (post-onboarding)
/onboarding/* - Account setup flow
/campaigns/* - Campaign management (CRUD)
/discover - Influencer campaign discovery
/search - General search functionality
```

### Database Patterns
- Uses SQLite for development/testing, supports MySQL/PostgreSQL for production
- Extensive use of casts for JSON fields and enums
- Factory-based testing data generation
- Array fields must be cast as 'array' in model $casts property

### Authentication & Authorization
- Standard Laravel authentication with Livewire auth components
- Onboarding middleware (EnsureOnboardingCompleted) protects main routes
- Account type-based access control throughout application

### Frontend Stack
- Tailwind CSS 4.x with Vite plugin
- Alpine.js for JavaScript interactivity
- Livewire Flux Pro for UI components
- All running through Vite with hot reload

### Testing Strategy
- Feature tests for Livewire components and business logic
- Unit tests for services, models, and enums
- Factory-based test data generation
- In-memory SQLite for test database
- You must use the `#[Test]` attribute for all tests

## Key Files to Understand

- `app/Livewire/BaseComponent.php` - Foundation for all Livewire components
- `app/Models/User.php` - Central user model with profile relationships
- `app/Models/Campaign.php` - Complex campaign model with extensive business logic
- `app/Services/SearchService.php` - Campaign matching and discovery algorithms
- `routes/web.php` - Route structure and middleware configuration
- `app/Enums/` - Business logic enums with form option generation
- `app/Livewire/Traits/HasWizardSteps.php` - Multi-step form wizard implementation
- `app/Enums/Traits/HasFormOptions.php` - Enum form option utilities

## Common Patterns

### Form Handling
- Multi-step wizards using HasWizardSteps trait
- Real-time validation with Livewire
- Extensive use of enums for dropdown options

### Data Management
- Array-based form fields with add/remove functionality
- JSON casting for complex data structures
- Event-driven architecture for state changes

### User Experience
- Smart campaign matching with visual indicators
- Real-time filtering and search
- Progressive disclosure in complex forms


## General Information
- This has not been sent to production or anywhere outside of my local computer and github, you can update existing migrations and just run `php artisan migrate:fresh`

- Always use FluxUI where possible

## Application Settings (spatie/laravel-settings)

CollabConnect uses `spatie/laravel-settings` for managing application-wide configuration that can be changed at runtime.

### Key Concepts
- **Settings Classes**: Extend `Spatie\LaravelSettings\Settings` base class
- **Database Storage**: Settings are stored in the `settings` table with key-value pairs
- **Caching**: Settings are cached for performance (configurable in `config/settings.php`)
- **Migrations**: Settings use migrations to define and update setting values

### Creating Settings Classes

Settings classes live in `app/Settings` and must:
1. Extend `Spatie\LaravelSettings\Settings`
2. Define a `group()` method returning a unique string identifier
3. Use public properties for setting values

```php
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public bool $market_enabled;
    public string $site_name;

    public static function group(): string
    {
        return 'general';
    }
}
```

### Settings Migrations

Create settings migrations using:
```bash
php artisan make:settings-migration CreateGeneralSettings
```

Migrations support operations in the `up()` method:
- `$this->migrator->add('general.site_name', 'default_value')` - Add setting with default
- `$this->migrator->add('general.api_key', 'secret', encrypted: true)` - Add encrypted setting
- `$this->migrator->rename('old_key', 'new_key')` - Rename a setting
- `$this->migrator->update('key', fn($value) => $modified)` - Update existing value
- `$this->migrator->delete('key')` - Remove a setting
- `$this->migrator->inGroup('general', function($migrator) {})` - Group operations

### Accessing Settings

Settings can be accessed via dependency injection or the service container:

```php
// Dependency injection (preferred)
public function index(GeneralSettings $settings)
{
    if ($settings->market_enabled) {
        // ...
    }
}

// Via container
$marketEnabled = app(GeneralSettings::class)->market_enabled;
```

### Updating Settings

Modify properties and call `save()`:

```php
$settings = app(GeneralSettings::class);
$settings->market_enabled = false;
$settings->save();
```

### Property Types

Supported types include:
- Primitives: `string`, `bool`, `int`, `float`, `array`
- DateTime: `DateTimeInterface`, `DateTimeZone`
- Custom objects via casts
- Nullable properties

### Important Notes
- Settings classes must be registered in `config/settings.php`
- Changes to settings are cached - clear cache if modifying settings structure
- Settings are ideal for admin-configurable application behavior
- Use `encrypted: true` for sensitive values like API keys

===

<laravel-boost-guidelines>
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

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit"
3. Quoted Phrases (Exact Position) - query="infinite scroll" - Words must be adjacent and in that order
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit"
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms


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


=== fluxui-free/core rules ===

## Flux UI Free

- This project is using the free edition of Flux UI. It has full access to the free components and variants, but does not have access to the Pro components.
- Flux UI is a component library for Livewire. Flux is a robust, hand-crafted, UI component library for your Livewire applications. It's built using Tailwind CSS and provides a set of components that are easy to use and customize.
- You should use Flux UI components when available.
- Fallback to standard Blade components if Flux is unavailable.
- If available, use Laravel Boost's `search-docs` tool to get the exact documentation and code snippets available for this project.
- Flux UI components look like this:
<code-snippet name="Flux UI Component Usage Example" lang="blade">
    <button type="button" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg w-10 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2)] [[data-flux-button-group]_&amp;]:border-e-0 [:is([data-flux-button-group]&gt;&amp;:last-child,_[data-flux-button-group]_:last-child&gt;&amp;)]:border-e-[1px] dark:[:is([data-flux-button-group]&gt;&amp;:last-child,_[data-flux-button-group]_:last-child&gt;&amp;)]:border-e-0 dark:[:is([data-flux-button-group]&gt;&amp;:last-child,_[data-flux-button-group]_:last-child&gt;&amp;)]:border-s-[1px] [:is([data-flux-button-group]&gt;&amp;:not(:first-child),_[data-flux-button-group]_:not(:first-child)&gt;&amp;)]:border-s-[color-mix(in_srgb,var(--color-accent-foreground),transparent_85%)]" data-flux-button="data-flux-button" data-flux-group-target="data-flux-group-target">

    </button>
</code-snippet>

### Available Components
This is correct as of Boost installation, but there may be additional components within the codebase.

<available-flux-components>
avatar, badge, brand, breadcrumbs, button, callout, checkbox, dropdown, field, heading, icon, input, modal, navbar, profile, radio, select, separator, switch, text, textarea, tooltip
</available-flux-components>


=== fluxui-pro/core rules ===

## Flux UI Pro

- This project is using the Pro version of Flux UI. It has full access to the free components and variants, as well as full access to the Pro components and variants.
- Flux UI is a component library for Livewire. Flux is a robust, hand-crafted, UI component library for your Livewire applications. It's built using Tailwind CSS and provides a set of components that are easy to use and customize.
- You should use Flux UI components when available.
- Fallback to standard Blade components if Flux is unavailable.
- If available, use Laravel Boost's `search-docs` tool to get the exact documentation and code snippets available for this project.
- Flux UI components look like this:
<code-snippet name="Flux UI component usage example" lang="blade">
    <button type="button" class="relative items-center font-medium justify-center gap-2 whitespace-nowrap disabled:opacity-75 dark:disabled:opacity-75 disabled:cursor-default disabled:pointer-events-none h-10 text-sm rounded-lg w-10 inline-flex  bg-[var(--color-accent)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] text-[var(--color-accent-foreground)] border border-black/10 dark:border-0 shadow-[inset_0px_1px_--theme(--color-white/.2)] [[data-flux-button-group]_&amp;]:border-e-0 [:is([data-flux-button-group]&gt;&amp;:last-child,_[data-flux-button-group]_:last-child&gt;&amp;)]:border-e-[1px] dark:[:is([data-flux-button-group]&gt;&amp;:last-child,_[data-flux-button-group]_:last-child&gt;&amp;)]:border-e-0 dark:[:is([data-flux-button-group]&gt;&amp;:last-child,_[data-flux-button-group]_:last-child&gt;&amp;)]:border-s-[1px] [:is([data-flux-button-group]&gt;&amp;:not(:first-child),_[data-flux-button-group]_:not(:first-child)&gt;&amp;)]:border-s-[color-mix(in_srgb,var(--color-accent-foreground),transparent_85%)]" data-flux-button="data-flux-button" data-flux-group-target="data-flux-group-target">

    </button>
</code-snippet>

### Available Components
This is correct as of Boost installation, but there may be additional components within the codebase.

<available-flux-components>
accordion, autocomplete, avatar, badge, brand, breadcrumbs, button, calendar, callout, card, chart, checkbox, command, context, date-picker, dropdown, editor, field, heading, icon, input, modal, navbar, pagination, popover, profile, radio, select, separator, switch, table, tabs, text, textarea, toast, tooltip
</available-flux-components>

### CRITICAL: Flux Component Size Limitations

**DO NOT use `size="lg"` with Flux components** - it will cause "UnhandledMatchError: Unhandled match case 'lg'"

**Supported sizes for most Flux components:**
- `flux:heading` - Only supports: `xl`, `base`, `sm` (NO 'lg' or other sizes)
- `flux:button` - Only supports: `sm`, `base` (NO 'lg')
- `flux:text` - Generally doesn't use size prop

**Always use these safe size values:**
- For headings: `xl` (largest), `base` (medium), `sm` (small)
- For buttons: `base` (default), `sm` (small)

**Examples of CORRECT usage:**
```blade
<flux:heading size="xl">Large Heading</flux:heading>
<flux:heading size="base">Normal Heading</flux:heading>
<flux:button size="base">Normal Button</flux:button>
```

**Examples of INCORRECT usage (will error):**
```blade
<flux:heading size="lg">Will Error!</flux:heading>
<flux:button size="lg">Will Error!</flux:button>
```


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

- Prefer lifecycle hooks like `mount()`, `updatedFoo()`) for initialization and reactive side effects:

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


=== volt/core rules ===

## Livewire Volt

- This project uses Livewire Volt for interactivity within its pages. New pages requiring interactivity must also use Livewire Volt. There is documentation available for it.
- Make new Volt components using `php artisan make:volt [name] [--test] [--pest]`
- Volt is a **class-based** and **functional** API for Livewire that supports single-file components, allowing a component's PHP logic and Blade templates to co-exist in the same file
- Livewire Volt allows PHP logic and Blade templates in one file. Components use the `@livewire("volt-anonymous-fragment-eyJuYW1lIjoidm9sdC1hbm9ueW1vdXMtZnJhZ21lbnQtNTNlNDRjOWRhZTNmZDZjOTJiN2M2OTU1ZmNlMDYzYzciLCJwYXRoIjoic3RvcmFnZVwvZnJhbWV3b3JrXC92aWV3c1wvMTE3MzI2YjJmNDg4NmViODZiNGFmZjZhNjRiNzY3MDEuYmxhZGUucGhwIn0=", Livewire\Volt\Precompilers\ExtractFragments::componentArguments([...get_defined_vars(), ...array (
)]))
</code-snippet>
@endverbatim

### Volt Class Based Component Example
To get started, define an anonymous class that extends Livewire\Volt\Component. Within the class, you may utilize all of the features of Livewire using traditional Livewire syntax:


<code-snippet name="Volt Class-based Volt Component Example" lang="php">
use Livewire\Volt\Component;

new class extends Component {
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }
} ?>

<div>
    <h1>{{ $count }}</h1>
    <button wire:click="increment">+</button>
</div>
</code-snippet>


### Testing Volt & Volt Components
- Use the existing directory for tests if it already exists. Otherwise, fallback to `tests/Feature/Volt`.

<code-snippet name="Livewire Test Example" lang="php">
use Livewire\Volt\Volt;

test('counter increments', function () {
    Volt::test('counter')
        ->assertSee('Count: 0')
        ->call('increment')
        ->assertSee('Count: 1');
});
</code-snippet>


<code-snippet name="Volt Component Test Using Pest" lang="php">
declare(strict_types=1);

use App\Models\{User, Product};
use Livewire\Volt\Volt;

test('product form creates product', function () {
    $user = User::factory()->create();

    Volt::test('pages.products.create')
        ->actingAs($user)
        ->set('form.name', 'Test Product')
        ->set('form.description', 'Test Description')
        ->set('form.price', 99.99)
        ->call('create')
        ->assertHasNoErrors();

    expect(Product::where('name', 'Test Product')->exists())->toBeTrue();
});
</code-snippet>


### Common Patterns


<code-snippet name="CRUD With Volt" lang="php">
<?php

use App\Models\Product;
use function Livewire\Volt\{state, computed};

state(['editing' => null, 'search' => '']);

$products = computed(fn() => Product::when($this->search,
    fn($q) => $q->where('name', 'like', "%{$this->search}%")
)->get());

$edit = fn(Product $product) => $this->editing = $product->id;
$delete = fn(Product $product) => $product->delete();

?>

<!-- HTML / UI Here -->
</code-snippet>



<code-snippet name="Real-Time Search With Volt" lang="php">
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="Search..."
    />
</code-snippet>



<code-snippet name="Loading States With Volt" lang="php">
    <flux:button wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save</span>
        <span wire:loading>Saving...</span>
    </flux:button>
</code-snippet>


=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.


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
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.


=== tailwindcss/v4 rules ===

## Tailwind 4

- Always use Tailwind CSS v4 - do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff"
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


## Custom Notes that are important
- Do not use session()->flash() for toast messages.  Anything that needs a message, use either `Flux::toast`, or `Toaster` toast messages