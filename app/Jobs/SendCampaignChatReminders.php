<?php

namespace App\Jobs;

use App\Enums\CampaignStatus;
use App\Enums\SystemMessageType;
use App\Models\Campaign;
use App\Models\Chat;
use App\Services\ChatService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class SendCampaignChatReminders implements ShouldQueue
{
    use Queueable;

    /**
     * Days before an event to send "soon" reminders.
     */
    protected int $reminderDays = 3;

    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * Note: "Campaign started" and "Campaign ended" messages are now sent by
     * CampaignService::startCampaign() and completeCampaign() to avoid duplicates.
     * This job only handles the "soon" reminder messages.
     */
    public function handle(): void
    {
        $this->sendCampaignStartingSoonReminders();
        $this->sendCampaignEndingSoonReminders();
    }

    /**
     * Send reminders for campaigns starting soon.
     */
    protected function sendCampaignStartingSoonReminders(): void
    {
        $targetDate = now()->addDays($this->reminderDays)->toDateString();

        $campaigns = Campaign::query()
            ->whereIn('status', [CampaignStatus::PUBLISHED, CampaignStatus::IN_PROGRESS])
            ->where('campaign_start_date', $targetDate)
            ->get();

        foreach ($campaigns as $campaign) {
            $this->sendSystemMessageToAllChats(
                $campaign,
                SystemMessageType::CampaignStartingSoon,
                "The campaign \"{$campaign->project_name}\" is starting in {$this->reminderDays} days!"
            );
        }
    }

    /**
     * Send messages when campaigns start.
     */
    protected function sendCampaignStartedMessages(): void
    {
        $today = now()->toDateString();

        $campaigns = Campaign::query()
            ->whereIn('status', [CampaignStatus::PUBLISHED, CampaignStatus::IN_PROGRESS])
            ->where('campaign_start_date', $today)
            ->get();

        foreach ($campaigns as $campaign) {
            $this->sendSystemMessageToAllChats(
                $campaign,
                SystemMessageType::CampaignStarted,
                "The campaign \"{$campaign->project_name}\" has started! Good luck with your content creation."
            );
        }
    }

    /**
     * Send reminders for campaigns ending soon.
     */
    protected function sendCampaignEndingSoonReminders(): void
    {
        $targetDate = now()->addDays($this->reminderDays)->toDateString();

        $campaigns = Campaign::query()
            ->where('status', CampaignStatus::IN_PROGRESS)
            ->where('campaign_completion_date', $targetDate)
            ->get();

        foreach ($campaigns as $campaign) {
            $this->sendSystemMessageToAllChats(
                $campaign,
                SystemMessageType::CampaignEndingSoon,
                "The campaign \"{$campaign->project_name}\" is ending in {$this->reminderDays} days. Please ensure all deliverables are completed."
            );
        }
    }

    /**
     * Send messages when campaigns end and archive chats.
     */
    protected function sendCampaignEndedMessages(): void
    {
        $today = now()->toDateString();

        $campaigns = Campaign::query()
            ->where('status', CampaignStatus::IN_PROGRESS)
            ->where('campaign_completion_date', $today)
            ->get();

        foreach ($campaigns as $campaign) {
            $this->sendSystemMessageToAllChats(
                $campaign,
                SystemMessageType::CampaignEnded,
                "The campaign \"{$campaign->project_name}\" has ended. Thank you for your participation!"
            );

            // Archive all chats for this campaign
            ChatService::archiveCampaignChats($campaign);
        }
    }

    /**
     * Send a system message to all chats for a campaign.
     */
    protected function sendSystemMessageToAllChats(
        Campaign $campaign,
        SystemMessageType $type,
        string $message
    ): void {
        // Use cache to prevent sending duplicate messages
        $cacheKey = "chat_reminder:{$campaign->id}:{$type->value}:".now()->toDateString();

        if (Cache::has($cacheKey)) {
            return;
        }

        $chats = Chat::forCampaign($campaign)
            ->active()
            ->get();

        foreach ($chats as $chat) {
            ChatService::sendSystemMessage($chat, $type, $message);
        }

        // Cache for 24 hours to prevent duplicate messages
        Cache::put($cacheKey, true, now()->addHours(24));
    }
}
