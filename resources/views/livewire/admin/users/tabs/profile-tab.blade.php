<flux:card>
    @if($user->isBusinessAccount() && $user->currentBusiness)
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Business Profile</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                    View business information and profile details.
                </flux:text>
            </div>

            <flux:separator />

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <flux:label>Business Name</flux:label>
                    <flux:text class="mt-1">{{ $user->currentBusiness->name }}</flux:text>
                </div>
                <div>
                    <flux:label>Industry</flux:label>
                    <flux:text class="mt-1">{{ $user->currentBusiness->industry }}</flux:text>
                </div>
                <div>
                    <flux:label>Primary Location</flux:label>
                    <flux:text class="mt-1">{{ $user->currentBusiness->primary_zip_code }}</flux:text>
                </div>
                <div>
                    <flux:label>Location Count</flux:label>
                    <flux:text class="mt-1">{{ $user->currentBusiness->location_count }}</flux:text>
                </div>
                <div>
                    <flux:label>Contact Name</flux:label>
                    <flux:text class="mt-1">{{ $user->currentBusiness->contact_name }}</flux:text>
                </div>
                <div>
                    <flux:label>Contact Email</flux:label>
                    <flux:text class="mt-1">{{ $user->currentBusiness->email }}</flux:text>
                </div>
            </div>

            @if($user->currentBusiness->websites)
                <div>
                    <flux:label>Websites</flux:label>
                    <div class="mt-2 space-y-1">
                        @foreach($user->currentBusiness->websites as $website)
                            <div>
                                <a href="{{ $website }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                                    {{ $website }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($user->currentBusiness->description)
                <div>
                    <flux:label>Description</flux:label>
                    <flux:text class="mt-2">{{ $user->currentBusiness->description }}</flux:text>
                </div>
            @endif
        </div>
    @elseif($user->isInfluencerAccount() && $user->influencer)
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Influencer Profile</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                    View influencer information and profile details.
                </flux:text>
            </div>

            <flux:separator />

            @if($user->influencer->bio)
                <div>
                    <flux:label>Bio</flux:label>
                    <flux:text class="mt-2">{{ $user->influencer->bio }}</flux:text>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <flux:label>Primary Location</flux:label>
                    <flux:text class="mt-1">{{ $user->influencer->primary_zip_code }}</flux:text>
                </div>

                @if($user->influencer->niche_categories)
                    <div>
                        <flux:label>Niche Categories</flux:label>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($user->influencer->niche_categories as $category)
                                <flux:badge>{{ $category }}</flux:badge>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            @if($user->socialMediaAccounts->count() > 0)
                <div>
                    <flux:label>Social Media Accounts</flux:label>
                    <div class="mt-2 space-y-2">
                        @foreach($user->socialMediaAccounts as $account)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <flux:badge color="zinc">{{ $account->platform }}</flux:badge>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $account->handle }}</span>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ number_format($account->followers) }} followers
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-12">
            <flux:icon name="user" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
            <flux:heading size="lg">No Profile Information</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-400 mt-2">
                This user has not completed their profile setup yet.
            </flux:text>
        </div>
    @endif
</flux:card>
