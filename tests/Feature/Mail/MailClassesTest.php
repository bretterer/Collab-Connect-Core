<?php

namespace Tests\Feature\Mail;

use App\Mail\BetaInviteBusiness;
use App\Mail\BetaInviteGeneric;
use App\Mail\BetaInviteInfluencer;
use App\Mail\BetaSignupConfirmation;
use App\Mail\BetaSignupNotification;
use App\Mail\ContactConfirmation;
use App\Mail\ContactInquiry;
use App\Mail\EmailSequenceMail;
use App\Mail\InviteMemberToBusinessMail;
use App\Mail\LandingPageSignup;
use App\Mail\ReferralProgramInvite;
use App\Mail\ReviewRequestMail;
use App\Models\Business;
use App\Models\BusinessMemberInvite;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Collaboration;
use App\Models\EmailSequence;
use App\Models\EmailSequenceEmail;
use App\Models\EmailSequenceSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MailClassesTest extends TestCase
{
    use RefreshDatabase;

    // ==================== ContactInquiry Tests ====================

    #[Test]
    public function contact_inquiry_has_correct_subject(): void
    {
        $mail = new ContactInquiry(
            firstName: 'John',
            lastName: 'Doe',
            email: 'john@example.com',
            inquirySubject: 'Partnership Opportunity',
            message: 'I would like to discuss a partnership.'
        );

        $this->assertEquals('[CollabConnect Contact] Partnership Opportunity', $mail->envelope()->subject);
    }

    #[Test]
    public function contact_inquiry_has_reply_to_address(): void
    {
        $mail = new ContactInquiry(
            firstName: 'Jane',
            lastName: 'Smith',
            email: 'jane@example.com',
            inquirySubject: 'Question',
            message: 'I have a question.'
        );

        $envelope = $mail->envelope();
        $replyTo = $envelope->replyTo;

        $this->assertNotEmpty($replyTo);
        $this->assertEquals('jane@example.com', $replyTo[0]->address);
        $this->assertEquals('Jane Smith', $replyTo[0]->name);
    }

    #[Test]
    public function contact_inquiry_uses_correct_view(): void
    {
        $mail = new ContactInquiry(
            firstName: 'John',
            lastName: 'Doe',
            email: 'john@example.com',
            inquirySubject: 'Test',
            message: 'Test message'
        );

        $this->assertEquals('emails.contact-inquiry', $mail->content()->markdown);
    }

    #[Test]
    public function contact_inquiry_passes_data_to_view(): void
    {
        $mail = new ContactInquiry(
            firstName: 'John',
            lastName: 'Doe',
            email: 'john@example.com',
            inquirySubject: 'Test Subject',
            message: 'Test message content',
            newsletter: true
        );

        $content = $mail->content();

        $this->assertEquals('John', $content->with['firstName']);
        $this->assertEquals('Doe', $content->with['lastName']);
        $this->assertEquals('john@example.com', $content->with['email']);
        $this->assertEquals('Test Subject', $content->with['subject']);
        $this->assertEquals('Test message content', $content->with['message']);
        $this->assertTrue($content->with['newsletter']);
    }

    // ==================== ContactConfirmation Tests ====================

    #[Test]
    public function contact_confirmation_can_be_constructed(): void
    {
        $mail = new ContactConfirmation(
            firstName: 'John',
            lastName: 'Doe',
            inquirySubject: 'Partnership Inquiry'
        );

        $this->assertInstanceOf(ContactConfirmation::class, $mail);
        $this->assertEquals('John', $mail->firstName);
        $this->assertEquals('Doe', $mail->lastName);
        $this->assertEquals('Partnership Inquiry', $mail->inquirySubject);
    }

    #[Test]
    public function contact_confirmation_has_correct_subject(): void
    {
        $mail = new ContactConfirmation(
            firstName: 'Jane',
            lastName: 'Smith',
            inquirySubject: 'Question About Services'
        );

        $this->assertEquals('Thank you for contacting CollabConnect', $mail->envelope()->subject);
    }

    #[Test]
    public function contact_confirmation_uses_correct_view(): void
    {
        $mail = new ContactConfirmation(
            firstName: 'John',
            lastName: 'Doe',
            inquirySubject: 'Test Subject'
        );

        $this->assertEquals('emails.contact-confirmation', $mail->content()->markdown);
    }

    #[Test]
    public function contact_confirmation_passes_data_to_view(): void
    {
        $mail = new ContactConfirmation(
            firstName: 'John',
            lastName: 'Doe',
            inquirySubject: 'My Inquiry Subject'
        );

        $content = $mail->content();

        $this->assertEquals('John', $content->with['firstName']);
        $this->assertEquals('Doe', $content->with['lastName']);
        $this->assertEquals('My Inquiry Subject', $content->with['subject']);
    }

    // ==================== InviteMemberToBusinessMail Tests ====================

    #[Test]
    public function invite_member_mail_has_correct_subject(): void
    {
        $business = Business::factory()->create(['name' => 'Acme Corp']);
        $invite = BusinessMemberInvite::create([
            'business_id' => $business->id,
            'invited_by' => User::factory()->create()->id,
            'email' => 'member@example.com',
            'role' => 'member',
            'token' => 'test-token',
            'invited_at' => now(),
        ]);

        $mail = new InviteMemberToBusinessMail(
            invite: $invite,
            signedUrl: 'https://example.com/accept-invite'
        );

        $this->assertEquals("You've been invited to join Acme Corp", $mail->envelope()->subject);
    }

    #[Test]
    public function invite_member_mail_uses_correct_view(): void
    {
        $business = Business::factory()->create();
        $invite = BusinessMemberInvite::create([
            'business_id' => $business->id,
            'invited_by' => User::factory()->create()->id,
            'email' => 'member@example.com',
            'role' => 'member',
            'token' => 'test-token',
            'invited_at' => now(),
        ]);

        $mail = new InviteMemberToBusinessMail(
            invite: $invite,
            signedUrl: 'https://example.com/accept-invite'
        );

        $this->assertEquals('emails.business-member-invite', $mail->content()->markdown);
    }

    #[Test]
    public function invite_member_mail_has_public_properties(): void
    {
        $business = Business::factory()->create();
        $invite = BusinessMemberInvite::create([
            'business_id' => $business->id,
            'invited_by' => User::factory()->create()->id,
            'email' => 'member@example.com',
            'role' => 'member',
            'token' => 'test-token',
            'invited_at' => now(),
        ]);

        $signedUrl = 'https://example.com/accept-invite?token=abc123';

        $mail = new InviteMemberToBusinessMail(
            invite: $invite,
            signedUrl: $signedUrl
        );

        $this->assertEquals($invite->id, $mail->invite->id);
        $this->assertEquals($signedUrl, $mail->signedUrl);
    }

    // ==================== ReferralProgramInvite Tests ====================

    #[Test]
    public function referral_invite_has_correct_subject(): void
    {
        $mail = new ReferralProgramInvite(
            email: 'referral@example.com',
            name: 'John',
            referralCode: 'REF123'
        );

        $this->assertEquals('Earn $5 for Every Friend You Refer to CollabConnect!', $mail->envelope()->subject);
    }

    #[Test]
    public function referral_invite_uses_correct_view(): void
    {
        $mail = new ReferralProgramInvite(
            email: 'referral@example.com',
            name: 'John',
            referralCode: 'REF123'
        );

        $this->assertEquals('emails.referral-program-invite', $mail->content()->markdown);
    }

    #[Test]
    public function referral_invite_has_public_properties(): void
    {
        $mail = new ReferralProgramInvite(
            email: 'referral@example.com',
            name: 'John Doe',
            referralCode: 'REF456'
        );

        $this->assertEquals('referral@example.com', $mail->email);
        $this->assertEquals('John Doe', $mail->name);
        $this->assertEquals('REF456', $mail->referralCode);
    }

    // ==================== EmailSequenceMail Tests ====================

    #[Test]
    public function email_sequence_mail_has_correct_subject(): void
    {
        $sequence = EmailSequence::factory()->create();
        $email = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Welcome Email',
            'subject' => 'Original Subject',
            'body' => 'Email body content',
            'order' => 1,
            'delay_days' => 0,
        ]);
        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'subscriber@example.com',
            'subscribed_at' => now(),
        ]);

        $mail = new EmailSequenceMail(
            email: $email,
            subscriber: $subscriber,
            processedBody: '<p>Processed body</p>',
            processedSubject: 'Processed Subject Line',
            processedPreviewText: 'Preview text',
            unsubscribeUrl: 'https://example.com/unsubscribe',
            sendId: 123
        );

        $this->assertEquals('Processed Subject Line', $mail->envelope()->subject);
    }

    #[Test]
    public function email_sequence_mail_uses_correct_view(): void
    {
        $sequence = EmailSequence::factory()->create();
        $email = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Follow Up Email',
            'subject' => 'Subject',
            'body' => 'Body',
            'order' => 1,
            'delay_days' => 0,
        ]);
        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'subscriber@example.com',
            'subscribed_at' => now(),
        ]);

        $mail = new EmailSequenceMail(
            email: $email,
            subscriber: $subscriber,
            processedBody: '<p>Body</p>',
            processedSubject: 'Subject',
            processedPreviewText: null,
            unsubscribeUrl: 'https://example.com/unsubscribe',
            sendId: 1
        );

        $this->assertEquals('emails.sequence.message', $mail->content()->markdown);
    }

    #[Test]
    public function email_sequence_mail_wraps_links_with_tracking(): void
    {
        $sequence = EmailSequence::factory()->create();
        $email = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Link Test Email',
            'subject' => 'Subject',
            'body' => 'Body',
            'order' => 1,
            'delay_days' => 0,
        ]);
        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'subscriber@example.com',
            'subscribed_at' => now(),
        ]);

        $bodyWithLink = '<p>Click <a href="https://example.com/page">here</a> to learn more.</p>';

        $mail = new EmailSequenceMail(
            email: $email,
            subscriber: $subscriber,
            processedBody: $bodyWithLink,
            processedSubject: 'Subject',
            processedPreviewText: null,
            unsubscribeUrl: 'https://example.com/unsubscribe',
            sendId: 456
        );

        // The processed body should contain the tracking URL
        $this->assertStringContainsString('/email/click/', $mail->processedBody);
        $this->assertStringContainsString('url=', $mail->processedBody);
    }

    #[Test]
    public function email_sequence_mail_does_not_double_wrap_tracking_links(): void
    {
        $sequence = EmailSequence::factory()->create();
        $email = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Double Wrap Test',
            'subject' => 'Subject',
            'body' => 'Body',
            'order' => 1,
            'delay_days' => 0,
        ]);
        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'subscriber@example.com',
            'subscribed_at' => now(),
        ]);

        $bodyWithTrackingLink = '<p>Click <a href="https://example.com/email/click/123?url=test">here</a>.</p>';

        $mail = new EmailSequenceMail(
            email: $email,
            subscriber: $subscriber,
            processedBody: $bodyWithTrackingLink,
            processedSubject: 'Subject',
            processedPreviewText: null,
            unsubscribeUrl: 'https://example.com/unsubscribe',
            sendId: 789
        );

        // Should not be double-wrapped
        $this->assertEquals(1, substr_count($mail->processedBody, '/email/click/'));
    }

    #[Test]
    public function email_sequence_mail_preserves_anchor_links(): void
    {
        $sequence = EmailSequence::factory()->create();
        $email = EmailSequenceEmail::create([
            'email_sequence_id' => $sequence->id,
            'name' => 'Anchor Test Email',
            'subject' => 'Subject',
            'body' => 'Body',
            'order' => 1,
            'delay_days' => 0,
        ]);
        $subscriber = EmailSequenceSubscriber::create([
            'email_sequence_id' => $sequence->id,
            'email' => 'subscriber@example.com',
            'subscribed_at' => now(),
        ]);

        $bodyWithAnchor = '<p>Jump to <a href="#section">this section</a>.</p>';

        $mail = new EmailSequenceMail(
            email: $email,
            subscriber: $subscriber,
            processedBody: $bodyWithAnchor,
            processedSubject: 'Subject',
            processedPreviewText: null,
            unsubscribeUrl: 'https://example.com/unsubscribe',
            sendId: 100
        );

        // Anchor links should not be wrapped with tracking
        $this->assertStringContainsString('href="#section"', $mail->processedBody);
    }

    // ==================== Beta Invite Mails Tests ====================

    #[Test]
    public function beta_invite_business_can_be_constructed(): void
    {
        $invite = (object) [
            'email' => 'business@example.com',
            'name' => 'John Business',
        ];

        $mail = new BetaInviteBusiness(
            invite: $invite,
            signedUrl: 'https://example.com/register?token=abc123'
        );

        $this->assertInstanceOf(BetaInviteBusiness::class, $mail);
        $this->assertEquals('business@example.com', $mail->invite->email);
        $this->assertEquals('John Business', $mail->invite->name);
    }

    #[Test]
    public function beta_invite_business_has_correct_subject(): void
    {
        $invite = (object) ['email' => 'test@example.com', 'name' => 'Test'];
        $mail = new BetaInviteBusiness($invite, 'https://example.com');

        $this->assertEquals("You're Invited to Join CollabConnect Beta - Businesses", $mail->envelope()->subject);
    }

    #[Test]
    public function beta_invite_influencer_can_be_constructed(): void
    {
        $invite = (object) [
            'email' => 'influencer@example.com',
            'name' => 'Jane Influencer',
        ];

        $mail = new BetaInviteInfluencer(
            invite: $invite,
            signedUrl: 'https://example.com/register?token=def456'
        );

        $this->assertInstanceOf(BetaInviteInfluencer::class, $mail);
        $this->assertEquals('influencer@example.com', $mail->invite->email);
        $this->assertEquals('Jane Influencer', $mail->invite->name);
    }

    #[Test]
    public function beta_invite_influencer_has_correct_subject(): void
    {
        $invite = (object) ['email' => 'test@example.com', 'name' => 'Test'];
        $mail = new BetaInviteInfluencer($invite, 'https://example.com');

        $this->assertEquals("You're Invited to Join CollabConnect Beta - Influencers", $mail->envelope()->subject);
    }

    #[Test]
    public function beta_invite_generic_can_be_constructed(): void
    {
        $invite = (object) [
            'email' => 'user@example.com',
            'name' => 'Generic User',
        ];

        $mail = new BetaInviteGeneric(
            invite: $invite,
            signedUrl: 'https://example.com/register?token=ghi789'
        );

        $this->assertInstanceOf(BetaInviteGeneric::class, $mail);
        $this->assertEquals('user@example.com', $mail->invite->email);
        $this->assertEquals('Generic User', $mail->invite->name);
    }

    #[Test]
    public function beta_invite_generic_has_correct_subject(): void
    {
        $invite = (object) ['email' => 'test@example.com', 'name' => 'Test'];
        $mail = new BetaInviteGeneric($invite, 'https://example.com');

        $this->assertEquals("You're Invited to Join CollabConnect Beta", $mail->envelope()->subject);
    }

    // ==================== Beta Signup Mails Tests ====================

    #[Test]
    public function beta_signup_confirmation_can_be_constructed(): void
    {
        $mail = new BetaSignupConfirmation(
            name: 'New Signup',
            userType: 'business'
        );

        $this->assertInstanceOf(BetaSignupConfirmation::class, $mail);
        $this->assertEquals('New Signup', $mail->name);
        $this->assertEquals('business', $mail->userType);
    }

    #[Test]
    public function beta_signup_confirmation_with_business_details(): void
    {
        $mail = new BetaSignupConfirmation(
            name: 'Business Owner',
            userType: 'business',
            businessName: 'Acme Corp'
        );

        $this->assertEquals('Acme Corp', $mail->businessName);
    }

    #[Test]
    public function beta_signup_confirmation_with_influencer_details(): void
    {
        $mail = new BetaSignupConfirmation(
            name: 'Influencer Name',
            userType: 'influencer',
            followerCount: '10000'
        );

        $this->assertEquals('10000', $mail->followerCount);
    }

    #[Test]
    public function beta_signup_notification_can_be_constructed(): void
    {
        $mail = new BetaSignupNotification(
            name: 'New Signup',
            email: 'signup@example.com',
            userType: 'business'
        );

        $this->assertInstanceOf(BetaSignupNotification::class, $mail);
        $this->assertEquals('New Signup', $mail->name);
        $this->assertEquals('signup@example.com', $mail->email);
        $this->assertEquals('business', $mail->userType);
    }

    #[Test]
    public function beta_signup_notification_has_correct_subject_for_business(): void
    {
        $mail = new BetaSignupNotification(
            name: 'John Doe',
            email: 'john@example.com',
            userType: 'business'
        );

        $this->assertEquals('[CollabConnect Beta] New Business Signup: John Doe', $mail->envelope()->subject);
    }

    #[Test]
    public function beta_signup_notification_has_correct_subject_for_influencer(): void
    {
        $mail = new BetaSignupNotification(
            name: 'Jane Doe',
            email: 'jane@example.com',
            userType: 'influencer'
        );

        $this->assertEquals('[CollabConnect Beta] New Influencer Signup: Jane Doe', $mail->envelope()->subject);
    }

    // ==================== LandingPageSignup Tests ====================

    #[Test]
    public function landing_page_signup_can_be_constructed(): void
    {
        $mail = new LandingPageSignup(
            name: 'Landing User',
            email: 'landing@example.com'
        );

        $this->assertInstanceOf(LandingPageSignup::class, $mail);
        $this->assertEquals('landing@example.com', $mail->email);
        $this->assertEquals('Landing User', $mail->name);
    }

    #[Test]
    public function landing_page_signup_has_correct_subject(): void
    {
        $mail = new LandingPageSignup(
            name: 'John Smith',
            email: 'john@example.com'
        );

        $this->assertEquals('New Landing Page Signup - John Smith', $mail->envelope()->subject);
    }

    #[Test]
    public function landing_page_signup_uses_correct_view(): void
    {
        $mail = new LandingPageSignup(
            name: 'Test User',
            email: 'test@example.com'
        );

        $this->assertEquals('emails.landing-page-signup', $mail->content()->view);
    }

    // ==================== ReviewRequestMail Tests ====================

    #[Test]
    public function review_request_mail_can_be_constructed(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();
        $campaign = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'project_name' => 'Test Campaign',
        ]);
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
        ]);
        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'campaign_application_id' => $application->id,
            'business_id' => $businessUser->currentBusiness->id,
            'influencer_id' => $influencer->id,
        ]);

        $mail = new ReviewRequestMail(
            collaboration: $collaboration,
            recipient: $influencer,
            otherParty: $businessUser,
            recipientRole: 'influencer'
        );

        $this->assertInstanceOf(ReviewRequestMail::class, $mail);
        $this->assertEquals($collaboration->id, $mail->collaboration->id);
        $this->assertEquals($influencer->id, $mail->recipient->id);
        $this->assertEquals('influencer', $mail->recipientRole);
    }

    #[Test]
    public function review_request_mail_has_correct_subject(): void
    {
        $businessUser = User::factory()->business()->withProfile()->create();
        $influencer = User::factory()->influencer()->withProfile()->create();
        $campaign = Campaign::factory()->create([
            'business_id' => $businessUser->currentBusiness->id,
            'project_name' => 'Summer Marketing Campaign',
        ]);
        $application = CampaignApplication::factory()->create([
            'campaign_id' => $campaign->id,
            'user_id' => $influencer->id,
        ]);
        $collaboration = Collaboration::factory()->create([
            'campaign_id' => $campaign->id,
            'campaign_application_id' => $application->id,
            'business_id' => $businessUser->currentBusiness->id,
            'influencer_id' => $influencer->id,
        ]);

        $mail = new ReviewRequestMail(
            collaboration: $collaboration,
            recipient: $influencer,
            otherParty: $businessUser,
            recipientRole: 'influencer'
        );

        $this->assertStringContainsString('Summer Marketing Campaign', $mail->envelope()->subject);
        $this->assertStringContainsString('Share your experience', $mail->envelope()->subject);
    }

    // ==================== Mail Has No Attachments Tests ====================

    #[Test]
    public function contact_inquiry_has_no_attachments(): void
    {
        $mail = new ContactInquiry(
            firstName: 'John',
            lastName: 'Doe',
            email: 'john@example.com',
            inquirySubject: 'Test',
            message: 'Test message'
        );

        $this->assertEmpty($mail->attachments());
    }

    #[Test]
    public function referral_invite_has_no_attachments(): void
    {
        $mail = new ReferralProgramInvite(
            email: 'test@example.com',
            name: 'Test',
            referralCode: 'REF'
        );

        $this->assertEmpty($mail->attachments());
    }

    #[Test]
    public function invite_member_mail_has_no_attachments(): void
    {
        $business = Business::factory()->create();
        $invite = BusinessMemberInvite::create([
            'business_id' => $business->id,
            'invited_by' => User::factory()->create()->id,
            'email' => 'member@example.com',
            'role' => 'member',
            'token' => 'test-token',
            'invited_at' => now(),
        ]);

        $mail = new InviteMemberToBusinessMail(
            invite: $invite,
            signedUrl: 'https://example.com/accept-invite'
        );

        $this->assertEmpty($mail->attachments());
    }
}
