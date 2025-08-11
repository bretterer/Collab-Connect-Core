<?php

namespace Database\Seeders;

use App\Enums\AccountType;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the specific business and influencer users created in DatabaseSeeder
        $mainBusinessUser = User::where('email', env('INIT_BUSINESS_EMAIL', 'business@example.com'))->first();
        $mainInfluencerUser = User::where('email', env('INIT_INFLUENCER_EMAIL', 'influencer@example.com'))->first();

        if (! $mainBusinessUser || ! $mainInfluencerUser) {
            $this->command->warn('Main business or influencer user not found. Skipping message seeding.');

            return;
        }

        // 1. Create conversation between the main business and main influencer
        $mainChat = Chat::findOrCreateBetweenUsers($mainBusinessUser, $mainInfluencerUser);
        $this->createConversation($mainChat, $mainBusinessUser, $mainInfluencerUser);

        // Get additional business and influencer users from AccountSeeder
        $additionalBusinessUsers = User::where('account_type', AccountType::BUSINESS)
            ->where('id', '!=', $mainBusinessUser->id)
            ->limit(5)
            ->get();

        $additionalInfluencerUsers = User::where('account_type', AccountType::INFLUENCER)
            ->where('id', '!=', $mainInfluencerUser->id)
            ->limit(10)
            ->get();

        // 2. Create 5-10 other conversations from the main business to other influencers
        $conversationCount = 0;
        foreach ($additionalInfluencerUsers->take(8) as $influencer) {
            $chat = Chat::findOrCreateBetweenUsers($mainBusinessUser, $influencer);
            $this->createConversation($chat, $mainBusinessUser, $influencer);
            $conversationCount++;
        }

        // 3. Create 5-10 other conversations from the main influencer to other businesses
        foreach ($additionalBusinessUsers as $business) {
            if ($conversationCount >= 16) {
                break;
            } // Limit total conversations
            $chat = Chat::findOrCreateBetweenUsers($business, $mainInfluencerUser);
            $this->createConversation($chat, $business, $mainInfluencerUser);
            $conversationCount++;
        }

        $this->command->info("Created {$conversationCount} conversations with messages.");
    }

    /**
     * Create a realistic conversation between two users
     */
    private function createConversation(Chat $chat, User $businessUser, User $influencerUser): void
    {
        $conversations = [
            [
                ['user' => $businessUser, 'message' => "Hi {$influencerUser->name}! I came across your profile and I'm really impressed with your content. I think you'd be a great fit for our upcoming campaign."],
                ['user' => $influencerUser, 'message' => "Hi {$businessUser->name}! Thank you so much for reaching out. I'd love to hear more about your campaign. What kind of collaboration are you looking for?"],
                ['user' => $businessUser, 'message' => "We're launching a new product line and looking for authentic influencers to showcase it. The compensation would be $500 plus free products. Are you interested?"],
                ['user' => $influencerUser, 'message' => "That sounds really interesting! Can you tell me more about the products and what type of content you're looking for?"],
                ['user' => $businessUser, 'message' => "Of course! I'll send over the campaign brief with all the details. The timeline is flexible, so we can work around your schedule."],
            ],
            [
                ['user' => $businessUser, 'message' => 'Hello! I love your content style and think it would align perfectly with our brand values. Would you be open to discussing a collaboration?'],
                ['user' => $influencerUser, 'message' => "Hi there! Thanks for the compliment. I'd definitely be open to learning more. What kind of brand are you representing?"],
                ['user' => $businessUser, 'message' => "We're a local sustainable fashion brand. We're looking for influencers who are passionate about eco-friendly fashion to help spread our message."],
                ['user' => $influencerUser, 'message' => "That's amazing! Sustainability is something I'm really passionate about. What would the collaboration involve?"],
            ],
            [
                ['user' => $influencerUser, 'message' => "Hi! I saw your campaign posting and I'm really interested. Your brand mission resonates with me and my audience."],
                ['user' => $businessUser, 'message' => "Hi {$influencerUser->name}! Thank you for your interest. I checked out your profile and your engagement rates are impressive. Let's discuss the details."],
                ['user' => $influencerUser, 'message' => 'Perfect! I have some creative ideas for how we could showcase your products. When would be a good time to discuss this further?'],
                ['user' => $businessUser, 'message' => "I'm available this week for a call. Let's schedule something that works for both of us."],
            ],
            [
                ['user' => $businessUser, 'message' => "Hey {$influencerUser->name}! Your recent posts about local businesses caught my attention. I'd love to discuss a potential partnership."],
                ['user' => $influencerUser, 'message' => 'Hi! I love supporting local businesses. What did you have in mind?'],
                ['user' => $businessUser, 'message' => "We're opening a new location and would love to have you come try our menu and share your experience with your followers."],
            ],
            [
                ['user' => $influencerUser, 'message' => 'Hello! I applied for your campaign and wanted to follow up. I have some great ideas for content creation.'],
                ['user' => $businessUser, 'message' => "Thanks for applying! I've been reviewing applications and yours stood out. What kind of content were you thinking?"],
                ['user' => $influencerUser, 'message' => 'I was thinking a mix of Instagram posts and stories, maybe a reel showing the product in action. I could also do some behind-the-scenes content.'],
            ],
        ];

        // Select a random conversation template
        $conversation = fake()->randomElement($conversations);

        // Create messages with realistic timestamps
        $baseTime = now()->subDays(fake()->numberBetween(1, 30));
        $currentTime = $baseTime;

        foreach ($conversation as $index => $messageData) {
            // Add realistic delays between messages (5 minutes to 2 hours)
            if ($index > 0) {
                $currentTime = $currentTime->addMinutes(fake()->numberBetween(5, 120));
            }

            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $messageData['user']->id,
                'body' => $messageData['message'],
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
                // Randomly mark some messages as read
                'read_at' => fake()->boolean(70) ? $currentTime->addMinutes(fake()->numberBetween(1, 30)) : null,
                'read_by_user_id' => fake()->boolean(70) ?
                    ($messageData['user']->id === $businessUser->id ? $influencerUser->id : $businessUser->id) : null,
            ]);
        }
    }
}
