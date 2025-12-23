<?php

namespace App\Enums;

enum SystemMessageType: string
{
    case CampaignStartingSoon = 'campaign_starting_soon';
    case CampaignStarted = 'campaign_started';
    case CampaignEndingSoon = 'campaign_ending_soon';
    case CampaignEnded = 'campaign_ended';
    case FeedbackPeriodOpen = 'feedback_period_open';
    case FeedbackPeriodClosingSoon = 'feedback_period_closing_soon';
    case FeedbackPeriodClosed = 'feedback_period_closed';
    case InfluencerAccepted = 'influencer_accepted';
    case ChatArchived = 'chat_archived';

    public function label(): string
    {
        return match ($this) {
            self::CampaignStartingSoon => 'Campaign Starting Soon',
            self::CampaignStarted => 'Campaign Started',
            self::CampaignEndingSoon => 'Campaign Ending Soon',
            self::CampaignEnded => 'Campaign Ended',
            self::FeedbackPeriodOpen => 'Feedback Period Open',
            self::FeedbackPeriodClosingSoon => 'Feedback Period Closing Soon',
            self::FeedbackPeriodClosed => 'Feedback Period Closed',
            self::InfluencerAccepted => 'Influencer Accepted',
            self::ChatArchived => 'Chat Archived',
        };
    }

    public function message(): string
    {
        return match ($this) {
            self::CampaignStartingSoon => 'The campaign is starting soon. Get ready to begin your collaboration!',
            self::CampaignStarted => 'The campaign has started. Good luck with your content creation!',
            self::CampaignEndingSoon => 'The campaign is ending soon. Make sure to complete all deliverables.',
            self::CampaignEnded => 'The campaign has ended. Thank you for your participation!',
            self::FeedbackPeriodOpen => 'The feedback period is now open. Please share your thoughts on the collaboration.',
            self::FeedbackPeriodClosingSoon => 'The feedback period is closing soon. Don\'t forget to submit your feedback.',
            self::FeedbackPeriodClosed => 'The feedback period has closed.',
            self::InfluencerAccepted => 'Welcome to the campaign! This chat is now active for communication.',
            self::ChatArchived => 'This chat has been archived. You can view previous messages but cannot send new ones.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CampaignStartingSoon => 'clock',
            self::CampaignStarted => 'play',
            self::CampaignEndingSoon => 'exclamation-triangle',
            self::CampaignEnded => 'check-circle',
            self::FeedbackPeriodOpen => 'chat-bubble-left-right',
            self::FeedbackPeriodClosingSoon => 'exclamation-circle',
            self::FeedbackPeriodClosed => 'lock-closed',
            self::InfluencerAccepted => 'user-plus',
            self::ChatArchived => 'archive-box',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::CampaignStartingSoon => 'blue',
            self::CampaignStarted => 'green',
            self::CampaignEndingSoon => 'amber',
            self::CampaignEnded => 'lime',
            self::FeedbackPeriodOpen => 'cyan',
            self::FeedbackPeriodClosingSoon => 'orange',
            self::FeedbackPeriodClosed => 'zinc',
            self::InfluencerAccepted => 'emerald',
            self::ChatArchived => 'zinc',
        };
    }
}
