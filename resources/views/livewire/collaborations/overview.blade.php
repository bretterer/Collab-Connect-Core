<div>
    <flux:card class="overflow-hidden">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/50 dark:to-indigo-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <flux:heading size="lg" class="text-gray-900 dark:text-white">Campaign Details</flux:heading>
                    <flux:text size="sm" class="text-gray-600 dark:text-gray-400">
                        Everything you need to know about this collaboration
                    </flux:text>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Campaign Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Compensation -->
                <div class="space-y-2">
                    <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400">Compensation</flux:text>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                            <flux:icon name="currency-dollar" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            <flux:text class="font-semibold text-gray-900 dark:text-white">
                                {{ $collaboration->campaign->compensation_display }}
                            </flux:text>
                            <flux:text size="xs" class="text-gray-500 dark:text-gray-400">
                                {{ $collaboration->campaign->compensation_type->label() }}
                            </flux:text>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="space-y-2">
                    <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400">Timeline</flux:text>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <flux:icon name="calendar-days" class="w-5 h-5 text-white" />
                        </div>
                        <div>
                            @if($collaboration->campaign->start_date && $collaboration->campaign->end_date)
                                <flux:text class="font-semibold text-gray-900 dark:text-white">
                                    {{ $collaboration->campaign->start_date->format('M j') }} - {{ $collaboration->campaign->end_date->format('M j, Y') }}
                                </flux:text>
                                <flux:text size="xs" class="text-gray-500 dark:text-gray-400">
                                    {{ $collaboration->campaign->start_date->diffInDays($collaboration->campaign->end_date) }} days
                                </flux:text>
                            @else
                                <flux:text class="font-semibold text-gray-900 dark:text-white">Flexible</flux:text>
                                <flux:text size="xs" class="text-gray-500 dark:text-gray-400">No fixed timeline</flux:text>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Platforms -->
                <div class="space-y-2">
                    <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400">Platforms</flux:text>
                    <div class="flex flex-wrap gap-2">
                        @forelse($collaboration->campaign->target_platforms ?? [] as $platform)
                            @php
                                $platformEnum = \App\Enums\TargetPlatform::tryFrom($platform);
                            @endphp
                            @if($platformEnum)
                                <flux:badge size="sm" color="blue">
                                    {{ $platformEnum->label() }}
                                </flux:badge>
                            @endif
                        @empty
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">No platforms specified</flux:text>
                        @endforelse
                    </div>
                </div>

                <!-- Campaign Type -->
                <div class="space-y-2">
                    <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400">Campaign Type</flux:text>
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <flux:icon name="megaphone" class="w-5 h-5 text-white" />
                        </div>
                        <div class="flex flex-wrap gap-1">
                            @forelse($collaboration->campaign->campaign_type ?? [] as $type)
                                <flux:badge size="sm" color="purple">
                                    {{ $type->label() }}
                                </flux:badge>
                            @empty
                                <flux:text class="font-semibold text-gray-900 dark:text-white">General</flux:text>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campaign Brief -->
            @if($collaboration->campaign->description)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400 mb-3">Campaign Brief</flux:text>
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                        <flux:text class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                            {{ $collaboration->campaign->description }}
                        </flux:text>
                    </div>
                </div>
            @endif

            <!-- Key Requirements -->
            @if($collaboration->campaign->requirements)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                    <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400 mb-3">Key Requirements</flux:text>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-800/50">
                        <flux:text class="text-amber-800 dark:text-amber-200 whitespace-pre-wrap">
                            {{ $collaboration->campaign->requirements }}
                        </flux:text>
                    </div>
                </div>
            @endif

            <!-- Contact Info -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400 mb-3">Your Contact</flux:text>
                <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
                    @if($this->isBusiness)
                        <flux:avatar
                            name="{{ $collaboration->influencer?->name ?? 'Influencer' }}"
                            size="lg"
                        />
                        <div class="flex-1">
                            <flux:text class="font-semibold text-gray-900 dark:text-white">
                                {{ $collaboration->influencer?->influencer?->display_name ?? $collaboration->influencer?->name ?? 'Influencer' }}
                            </flux:text>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                                Influencer
                            </flux:text>
                        </div>
                        @if($collaboration->influencer)
                            <flux:button
                                href="{{ route('influencer.profile', $collaboration->influencer->username()) }}"
                                variant="ghost"
                                size="sm"
                                icon="arrow-top-right-on-square"
                            >
                                View Profile
                            </flux:button>
                        @endif
                    @else
                        <flux:avatar
                            name="{{ $collaboration->business?->name ?? 'Business' }}"
                            size="lg"
                        />
                        <div class="flex-1">
                            <flux:text class="font-semibold text-gray-900 dark:text-white">
                                {{ $collaboration->business?->name ?? 'Business' }}
                            </flux:text>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                                Business
                            </flux:text>
                        </div>
                        @if($collaboration->business?->users->first())
                            <flux:button
                                href="{{ route('business.profile', $collaboration->business->users->first()->username()) }}"
                                variant="ghost"
                                size="sm"
                                icon="arrow-top-right-on-square"
                            >
                                View Profile
                            </flux:button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </flux:card>
</div>
