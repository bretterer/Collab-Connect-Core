<?php

namespace App\Listeners;

use App\Events\CampaignApplicationSubmitted;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateCampaignApplicationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CampaignApplicationSubmitted $event): void
    {
        $campaign = $event->campaign;
        $applicant = $event->applicant;
        $applicationData = $event->applicationData;

        // Create notification for the business owner
        NotificationService::createCampaignApplicationNotification(
            $campaign->user,
            $campaign->id,
            $campaign->campaign_goal,
            $applicant->name
        );
    }
}
