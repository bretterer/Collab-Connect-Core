# Funnel & Email Sequence System Implementation Guide

## Overview
This document outlines the implementation of the funnel and email sequence system for CollabConnect landing pages.

## ✅ Completed Components

### 1. Database Schema
All migrations created and ready to run (`php artisan migrate`):

- **funnels** - Stores funnel configurations with ordered landing pages
- **email_sequences** - Email sequences with subscribe/unsubscribe triggers
- **email_sequence_emails** - Individual emails with timing, content, and statistics
- **email_sequence_subscribers** - Subscriber management with status tracking
- **email_sequence_sends** - Individual send tracking with open/click data

### 2. Eloquent Models
All models created with relationships and helper methods:

- `App\Models\Funnel` - Manages funnel and landing page relationships
- `App\Models\EmailSequence` - Sequence management with trigger handling
- `App\Models\EmailSequenceEmail` - Email content with stat calculations (open_rate, click_rate)
- `App\Models\EmailSequenceSubscriber` - Subscriber with unsubscribe functionality
- `App\Models\EmailSequenceSend` - Individual send tracking

### 3. Enums
- `App\Enums\SubscriberStatus` - ACTIVE, UNSUBSCRIBED, BOUNCED, COMPLAINED
- `App\Enums\EmailTriggerType` - FORM_SUBMITTED, OFFER_PURCHASED, LANDING_PAGE_VISITED
- `App\Enums\EmailSendStatus` - PENDING, SENT, FAILED, CANCELLED

### 4. Queue Jobs
- `App\Jobs\SendEmailSequenceEmail` - Sends individual emails with merge tag support
- `App\Jobs\ProcessEmailSequenceSubscriber` - Schedules all emails for new subscribers

### 5. Services
- `App\Models\EmailSequenceService` - Core business logic for:
  - Subscribing users to sequences
  - Unsubscribing users
  - Processing pending sends
  - Handling form submission triggers
  - Handling landing page visit triggers

### 6. Livewire Components (Created, need implementation)
- `Admin/EmailSequences/EmailSequenceIndex`
- `Admin/EmailSequences/EmailSequenceEdit`
- `Admin/Funnels/FunnelIndex`
- `Admin/Funnels/FunnelEdit`

## 🔨 Implementation Steps Needed

### Step 1: Run Migrations
```bash
php artisan migrate
```

### Step 2: Add Routes
Add to `routes/web.php` in the admin section:

```php
// Email Sequences
Route::prefix('admin/email-sequences')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', \App\Livewire\Admin\EmailSequences\EmailSequenceIndex::class)->name('admin.email-sequences.index');
    Route::get('/create', \App\Livewire\Admin\EmailSequences\EmailSequenceEdit::class)->name('admin.email-sequences.create');
    Route::get('/{emailSequence}/edit', \App\Livewire\Admin\EmailSequences\EmailSequenceEdit::class)->name('admin.email-sequences.edit');
});

// Funnels
Route::prefix('admin/funnels')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', \App\Livewire\Admin\Funnels\FunnelIndex::class)->name('admin.funnels.index');
    Route::get('/create', \App\Livewire\Admin\Funnels\FunnelEdit::class)->name('admin.funnels.create');
    Route::get('/{funnel}/edit', \App\Livewire\Admin\Funnels\FunnelEdit::class)->name('admin.funnels.edit');
});

// Email Sequence Public Routes
Route::get('/unsubscribe/{subscriber}', function ($subscriberId) {
    $subscriber = \App\Models\EmailSequenceSubscriber::findOrFail($subscriberId);

    // Verify token
    $token = request('token');
    $validToken = hash_hmac('sha256', $subscriber->id, config('app.key'));

    if ($token !== $validToken) {
        abort(403);
    }

    app(\App\Services\EmailSequenceService::class)->unsubscribe($subscriber, 'user_clicked_link');

    return view('email-sequences.unsubscribed');
})->name('email-sequence.unsubscribe');

// Tracking pixels for opens
Route::get('/email/track/{send}', function ($sendId) {
    $send = \App\Models\EmailSequenceSend::findOrFail($sendId);
    $send->markAsOpened();

    return response()->file(public_path('images/pixel.gif'));
})->name('email-sequence.track');
```

### Step 3: Set Up Scheduled Command
Create a command to process pending email sends:

```bash
php artisan make:command ProcessEmailSequenceSends
```

```php
<?php

namespace App\Console\Commands;

use App\Services\EmailSequenceService;
use Illuminate\Console\Command;

class ProcessEmailSequenceSends extends Command
{
    protected $signature = 'email-sequences:process';
    protected $description = 'Process pending email sequence sends';

    public function handle(EmailSequenceService $service): void
    {
        $count = $service->processPendingSends();
        $this->info("Processed {$count} pending email sends");
    }
}
```

Add to `routes/console.php` or `app/Console/Kernel.php`:
```php
Schedule::command('email-sequences:process')->everyMinute();
```

### Step 4: Integrate with Form Submissions
In your form submission handler (likely in a Livewire component or controller), add:

```php
use App\Services\EmailSequenceService;

public function submitForm()
{
    // Your existing form submission logic...

    // Trigger email sequences
    app(EmailSequenceService::class)->handleFormSubmission(
        formId: $this->form->id,
        data: $this->formData
    );
}
```

### Step 5: Build Livewire UI Components

#### EmailSequenceIndex Component
This should display a table of all email sequences with:
- Name, description
- Number of subscribers
- Number of emails in sequence
- Edit/Delete actions

#### EmailSequenceEdit Component
This should allow editing:
- Sequence name and description
- Subscribe triggers (form submissions, offers, landing pages)
- Unsubscribe triggers
- List of emails in the sequence
- Email editor for each email (subject, body, delay, send time)

#### FunnelIndex Component
Display all funnels with their landing pages in order.

#### FunnelEdit Component
Allow creating/editing funnels:
- Funnel name and description
- Drag-and-drop ordering of landing pages
- Email sequences attached to the funnel

### Step 6: Create Email Templates
Create views for:
- `resources/views/email-sequences/unsubscribed.blade.php` - Unsubscribe confirmation page
- Email templates for common sequences

### Step 7: Add Navigation
Add menu items to your admin navigation for:
- Email Sequences
- Funnels

## Usage Examples

### Creating an Email Sequence via Code

```php
use App\Models\EmailSequence;
use App\Models\EmailSequenceEmail;

// Create sequence
$sequence = EmailSequence::create([
    'name' => 'Welcome Series',
    'description' => 'Welcome new subscribers',
    'subscribe_triggers' => [
        [
            'type' => 'form_submitted',
            'form_id' => 1,
        ]
    ],
    'created_by' => auth()->id(),
]);

// Add emails
EmailSequenceEmail::create([
    'email_sequence_id' => $sequence->id,
    'name' => 'Day 1 @ 08:00 AM EST',
    'subject' => 'Welcome to our community!',
    'body' => '<h1>Hi {{first_name}}!</h1><p>Welcome!</p><p><a href="{{unsubscribe_url}}">Unsubscribe</a></p>',
    'delay_days' => 0,
    'send_time' => '08:00:00',
    'timezone' => 'America/New_York',
    'order' => 1,
]);

EmailSequenceEmail::create([
    'email_sequence_id' => $sequence->id,
    'name' => 'Day 2 @ 08:00 AM EST',
    'subject' => 'Here\'s what you need to know',
    'body' => '<h1>Hi {{first_name}}!</h1><p>Day 2 content...</p>',
    'delay_days' => 1,
    'send_time' => '08:00:00',
    'timezone' => 'America/New_York',
    'order' => 2,
]);
```

### Subscribing a User

```php
use App\Services\EmailSequenceService;
use App\Models\EmailSequence;

$sequence = EmailSequence::find(1);

app(EmailSequenceService::class)->subscribe(
    sequence: $sequence,
    email: 'user@example.com',
    firstName: 'John',
    lastName: 'Doe',
    metadata: ['source' => 'landing_page'],
    source: 'form_1'
);
```

## Available Merge Tags

Use these in email bodies:
- `{{first_name}}` - Subscriber's first name
- `{{last_name}}` - Subscriber's last name
- `{{email}}` - Subscriber's email
- `{{unsubscribe_url}}` - Unsubscribe link

## Database Schema Reference

### email_sequences table
- `id` - Primary key
- `name` - Sequence name
- `description` - Sequence description
- `subscribe_triggers` - JSON array of trigger configurations
- `unsubscribe_triggers` - JSON array of unsubscribe triggers
- `funnel_id` - Optional funnel association
- `total_subscribers` - Cached subscriber count
- `created_by` / `updated_by` - User tracking
- `created_at` / `updated_at` / `deleted_at`

### email_sequence_emails table
- `id` - Primary key
- `email_sequence_id` - Foreign key
- `name` - Internal name (e.g., "Day 1 @ 08:00 AM EST")
- `subject` - Email subject line
- `body` - HTML email body
- `delay_days` - Days after subscription to send
- `send_time` - Time of day to send (e.g., "08:00:00")
- `timezone` - Timezone for send_time
- `order` - Order in sequence
- `sent_count` / `opened_count` / `clicked_count` / `unsubscribed_count` - Statistics
- `created_at` / `updated_at`

### email_sequence_subscribers table
- `id` - Primary key
- `email_sequence_id` - Foreign key
- `email` - Subscriber email
- `first_name` / `last_name` - Subscriber name
- `metadata` - JSON additional data
- `status` - active, unsubscribed, bounced, complained
- `subscribed_at` / `unsubscribed_at` - Timestamps
- `unsubscribe_reason` - Why they unsubscribed
- `source` - Where they came from
- `created_at` / `updated_at`

### email_sequence_sends table
- `id` - Primary key
- `email_sequence_email_id` - Foreign key
- `subscriber_id` - Foreign key
- `scheduled_at` - When to send
- `sent_at` - When actually sent
- `opened_at` / `clicked_at` - Engagement tracking
- `status` - pending, sent, failed, cancelled
- `error_message` - Error if failed
- `created_at` / `updated_at`

## Next Steps for Full Implementation

1. **Run migrations** - `php artisan migrate`
2. **Add routes** - Copy route definitions above
3. **Set up cron** - Add scheduled command for processing sends
4. **Build UI components** - Implement the Livewire component logic and Blade views
5. **Integrate with forms** - Hook into existing form submission logic
6. **Test** - Create test sequences and subscribers
7. **Configure email** - Ensure Laravel mail configuration is correct

## Testing Checklist

- [ ] Migrations run successfully
- [ ] Can create an email sequence
- [ ] Can add emails to a sequence
- [ ] Form submission triggers subscription
- [ ] Emails are scheduled correctly based on delay_days
- [ ] Scheduled emails are processed by cron job
- [ ] Emails are sent with correct merge tags
- [ ] Unsubscribe link works
- [ ] Stats are tracked correctly (sent, opened, clicked)
- [ ] Can create and manage funnels
- [ ] Funnel pages display in correct order
