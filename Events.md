# Laravel Events Documentation

This document outlines all the Laravel Events available in the CollabConnect system that can be hooked into for future functionality.

## User Events

### BusinessJoined
**Fired when:** A business user completes their onboarding process
**Properties:**
- `User $user` - The business user who joined
- `BusinessProfile $businessProfile` - The created business profile

**Description:** This event is fired when a business user successfully completes their onboarding process and their business profile is created. This is the final step in the business registration flow.

---

### InfluencerJoined
**Fired when:** An influencer user completes their onboarding process
**Properties:**
- `User $user` - The influencer user who joined
- `InfluencerProfile $influencerProfile` - The created influencer profile

**Description:** This event is fired when an influencer user successfully completes their onboarding process and their influencer profile is created. This is the final step in the influencer registration flow.

---

### ProfileUpdated
**Fired when:** A user updates their profile information
**Properties:**
- `User $user` - The user whose profile was updated
- `string $profileType` - The type of profile updated ('business' or 'influencer')
- `array $changes` - Array of changed fields and their new values

**Description:** This event is fired whenever a user updates their profile information. The event includes details about what specific fields were changed, allowing for targeted notifications or processing.

---

## Campaign Events

### CampaignPublished
**Fired when:** A campaign is published and made live
**Properties:**
- `Campaign $campaign` - The campaign that was published
- `User $publisher` - The user who published the campaign

**Description:** This event is fired when a business publishes a campaign, making it visible to influencers. This is a key moment for notifications and analytics.

---

### CampaignScheduled
**Fired when:** A campaign is scheduled for future publication
**Properties:**
- `Campaign $campaign` - The campaign that was scheduled
- `User $scheduler` - The user who scheduled the campaign
- `string $scheduledDate` - The date when the campaign will be published

**Description:** This event is fired when a business schedules a campaign for future publication. Useful for reminder systems and workflow management.

---

### CampaignUnpublished
**Fired when:** A campaign is unpublished (converted back to draft)
**Properties:**
- `Campaign $campaign` - The campaign that was unpublished
- `User $unpublisher` - The user who unpublished the campaign

**Description:** This event is fired when a business unpublishes a campaign, converting it back to draft status. Useful for tracking campaign lifecycle changes.

---

### CampaignEdited
**Fired when:** A campaign is edited/updated
**Properties:**
- `Campaign $campaign` - The campaign that was edited
- `User $editor` - The user who edited the campaign
- `array $changes` - Array of changed fields and their new values

**Description:** This event is fired when a campaign is edited, including details about what specific fields were changed. Useful for audit trails and notifications.

---

### CampaignArchived
**Fired when:** A campaign is archived
**Properties:**
- `Campaign $campaign` - The campaign that was archived
- `User $archiver` - The user who archived the campaign

**Description:** This event is fired when a campaign is archived. Useful for cleanup processes and analytics.

---

### CampaignApplicationSubmitted
**Fired when:** An influencer applies to a campaign
**Properties:**
- `Campaign $campaign` - The campaign being applied to
- `User $applicant` - The influencer applying to the campaign
- `array $applicationData` - The application data submitted

**Description:** This event is fired when an influencer submits an application to a campaign. This is a critical moment for notifications and workflow management.

---

## Built-in Laravel Events

### Registered
**Fired when:** A new user registers
**Properties:**
- `User $user` - The newly registered user

**Description:** This is Laravel's built-in event that fires when a new user registers. Available for integration with external systems and welcome workflows.

---

## Event Usage Examples

### Listening to Events

```php
// In a service provider or listener
Event::listen(BusinessJoined::class, function (BusinessJoined $event) {
    // Send welcome email
    // Create analytics record
    // Trigger onboarding completion workflow
});

Event::listen(CampaignPublished::class, function (CampaignPublished $event) {
    // Notify relevant influencers
    // Update search index
    // Send notifications
});
```

### Creating Listeners

```php
// Create a listener class
class SendWelcomeEmail
{
    public function handle(BusinessJoined $event): void
    {
        // Send welcome email logic
    }
}
```

### Broadcasting Events

All events are set up for broadcasting and can be configured to broadcast to specific channels for real-time updates.

---

## Event Timing

- **BusinessJoined/InfluencerJoined**: Fired immediately after profile creation during onboarding
- **CampaignPublished**: Fired when campaign status changes to 'published'
- **CampaignScheduled**: Fired when campaign status changes to 'scheduled'
- **CampaignUnpublished**: Fired when campaign status changes from 'published' to 'draft'
- **CampaignEdited**: Fired after any campaign field is updated
- **CampaignArchived**: Fired when campaign status changes to 'archived'
- **ProfileUpdated**: Fired after any profile field is updated
- **CampaignApplicationSubmitted**: Fired when application is submitted (future implementation)

---

## Integration Points

These events can be used for:
- Email notifications
- Push notifications
- Analytics tracking
- Audit logging
- Real-time updates
- External system integrations
- Workflow automation
- Marketing automation
- Customer success workflows