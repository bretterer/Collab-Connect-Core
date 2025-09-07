<!-- Professional Business Dashboard -->
<div class="space-y-8">
    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <flux:heading size="xl" level="1" class="text-gray-900 dark:text-white">
                        Good {{ now()->format('A') === 'AM' ? 'morning' : 'afternoon' }}, {{ auth()->user()->name }}
                    </flux:heading>
                    <flux:badge variant="success" size="sm" class="ml-2">Active</flux:badge>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <flux:icon name="calendar" class="w-4 h-4" />
                        {{ now()->format('l, F j, Y') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <flux:icon name="chart-bar" class="w-4 h-4" />
                        {{ $this->getTotalApplicationsCount() }} applications received
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <flux:button href="{{ route('campaigns.index') }}" variant="ghost" icon="rectangle-stack" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    All Campaigns
                </flux:button>
                <flux:button href="{{ route('campaigns.create') }}" variant="primary" icon="plus" class="shadow-lg shadow-blue-500/25">
                    New Campaign
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Enhanced Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <flux:card class="p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <flux:text size="sm" class="text-gray-500 dark:text-gray-400 font-medium">Active Campaigns</flux:text>
                        @if($this->getPublishedCampaigns()->count() > 0)
                            <flux:badge size="sm" variant="success">Live</flux:badge>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-3">
                        <flux:text size="3xl" class="font-bold text-gray-900 dark:text-white">
                            {{ $this->getPublishedCampaigns()->count() }}
                        </flux:text>
                        <div class="flex items-center gap-1 text-green-600 dark:text-green-400">
                            <flux:icon name="arrow-trending-up" class="w-4 h-4" />
                            <flux:text size="xs" class="font-medium">+2.3%</flux:text>
                        </div>
                    </div>
                </div>
                <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <flux:icon name="megaphone" class="text-white w-6 h-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="p-6 hover:shadow-lg transition-shadow duration-200 {{ $this->getPendingApplications()->count() > 0 ? 'ring-2 ring-amber-200 dark:ring-amber-800' : '' }}">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <flux:text size="sm" class="text-gray-500 dark:text-gray-400 font-medium">Pending Review</flux:text>
                        @if($this->getPendingApplications()->count() > 0)
                            <flux:badge size="sm" variant="warning" class="animate-pulse">
                                {{ $this->getPendingApplications()->count() }}
                            </flux:badge>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-3">
                        <flux:text size="3xl" class="font-bold text-gray-900 dark:text-white">
                            {{ $this->getPendingApplications()->count() }}
                        </flux:text>
                        @if($this->getPendingApplications()->count() > 0)
                            <flux:text size="xs" class="text-amber-600 dark:text-amber-400 font-medium">
                                Needs attention
                            </flux:text>
                        @endif
                    </div>
                </div>
                <div class="h-12 w-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <flux:icon name="clock" class="text-white w-6 h-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <flux:text size="sm" class="text-gray-500 dark:text-gray-400 font-medium">Total Applications</flux:text>
                    <div class="flex items-baseline gap-3">
                        <flux:text size="3xl" class="font-bold text-gray-900 dark:text-white">
                            {{ $this->getTotalApplicationsCount() }}
                        </flux:text>
                        <flux:text size="xs" class="text-gray-400 font-medium">all time</flux:text>
                    </div>
                </div>
                <div class="h-12 w-12 bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl flex items-center justify-center">
                    <flux:icon name="document-text" class="text-white w-6 h-6" />
                </div>
            </div>
        </flux:card>

        <flux:card class="p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <flux:text size="sm" class="text-gray-500 dark:text-gray-400 font-medium">Approved</flux:text>
                        @if($this->getAcceptedApplications()->count() > 0)
                            <flux:badge size="sm" variant="success">Active</flux:badge>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-3">
                        <flux:text size="3xl" class="font-bold text-gray-900 dark:text-white">
                            {{ $this->getAcceptedApplications()->count() }}
                        </flux:text>
                        <div class="flex items-center gap-1 text-green-600 dark:text-green-400">
                            <flux:icon name="check-circle" class="w-4 h-4" />
                            <flux:text size="xs" class="font-medium">{{ round(($this->getAcceptedApplications()->count() / max($this->getTotalApplicationsCount(), 1)) * 100) }}% rate</flux:text>
                        </div>
                    </div>
                </div>
                <div class="h-12 w-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <flux:icon name="check-circle" class="text-white w-6 h-6" />
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Main Content Areas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Primary Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Campaign Overview Section -->
            @if($this->getPublishedCampaigns()->count() > 0 || $this->getDraftCampaigns()->count() > 0)
                <div class="space-y-6">
                    @if($this->getPublishedCampaigns()->count() > 0)
                        <flux:card class="overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/50 dark:to-indigo-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="space-y-1">
                                        <flux:heading size="lg" class="text-gray-900 dark:text-white">Active Campaigns</flux:heading>
                                        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <flux:icon name="fire" class="w-4 h-4 text-orange-500" />
                                                {{ $this->getPublishedCampaigns()->count() }} live campaigns
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <flux:icon name="eye" class="w-4 h-4 text-blue-500" />
                                                High visibility
                                            </span>
                                        </div>
                                    </div>
                                    <flux:button href="{{ route('campaigns.index') }}" variant="ghost" icon-trailing="arrow-right" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                                        View All
                                    </flux:button>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach($this->getPublishedCampaigns()->take(3) as $campaign)
                                        <div class="group relative bg-gradient-to-r from-white to-gray-50/50 dark:from-gray-800 dark:to-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300">
                                            <div class="p-4">
                                                <div class="flex items-start justify-between mb-3">
                                                    <div class="flex items-center gap-2">
                                                        <div class="flex items-center gap-2 px-2 py-1 bg-green-100 dark:bg-green-900/30 rounded-full">
                                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                                                            <flux:text size="xs" class="font-medium text-green-700 dark:text-green-300">Live</flux:text>
                                                        </div>
                                                        <flux:text size="xs" class="text-gray-500 dark:text-gray-400">
                                                            {{ $campaign->published_at?->diffForHumans() }}
                                                        </flux:text>
                                                    </div>
                                                    <flux:button href="{{ route('campaigns.show', $campaign) }}" variant="ghost" size="sm" icon="arrow-top-right-on-square" class="opacity-0 group-hover:opacity-100 transition-all text-blue-600 dark:text-blue-400">
                                                    </flux:button>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <flux:heading size="sm" class="text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                        {{ $campaign->project_name }}
                                                    </flux:heading>
                                                </div>
                                                
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                                        <div class="flex items-center gap-1">
                                                            <flux:icon name="currency-dollar" class="w-4 h-4 text-green-500" />
                                                            <span class="font-medium">{{ $campaign->compensation_display }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <flux:icon name="users" class="w-4 h-4 text-blue-500" />
                                                            <span>{{ $campaign->influencer_count }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400 font-medium opacity-0 group-hover:opacity-100 transition-all">
                                                        <span>View Campaign</span>
                                                        <flux:icon name="arrow-right" class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" />
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Subtle accent line -->
                                            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 to-green-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </flux:card>
                    @endif

                    @if($this->getDraftCampaigns()->count() > 0)
                        <flux:card class="overflow-hidden">
                            <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-950/50 dark:to-orange-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div class="space-y-1">
                                        <flux:heading size="lg" class="text-gray-900 dark:text-white">Draft Campaigns</flux:heading>
                                        <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                            <flux:icon name="pencil-square" class="w-4 h-4 text-amber-500" />
                                            {{ $this->getDraftCampaigns()->count() }} campaigns in progress
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <div class="space-y-3">
                                    @foreach($this->getDraftCampaigns()->take(3) as $campaign)
                                        <div class="group relative bg-gradient-to-r from-white to-amber-50/30 dark:from-gray-800 dark:to-amber-900/10 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-amber-300 dark:hover:border-amber-600 transition-all duration-300">
                                            <div class="p-4">
                                                <div class="flex items-start justify-between mb-3">
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex items-center gap-1 px-2 py-1 bg-amber-100 dark:bg-amber-900/30 rounded-full">
                                                            <flux:icon name="pencil" class="w-3 h-3 text-amber-600 dark:text-amber-400" />
                                                            <flux:text size="xs" class="font-medium text-amber-700 dark:text-amber-300">Draft</flux:text>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-1">
                                                                <div class="bg-amber-500 h-1 rounded-full transition-all duration-500" style="width: {{ ($campaign->current_step / 4) * 100 }}%"></div>
                                                            </div>
                                                            <flux:text size="xs" class="text-gray-500 dark:text-gray-400 whitespace-nowrap font-medium">
                                                                {{ $campaign->current_step }}/4
                                                            </flux:text>
                                                        </div>
                                                    </div>
                                                    <flux:button href="{{ route('campaigns.edit', $campaign) }}" variant="primary" size="xs" class="shadow-sm px-3">
                                                        Continue
                                                    </flux:button>
                                                </div>
                                                
                                                <div class="mb-2">
                                                    <flux:heading size="sm" class="text-gray-900 dark:text-white mb-1">
                                                        {{ $campaign->project_name ?: 'Untitled Campaign' }}
                                                    </flux:heading>
                                                    <flux:text size="xs" class="text-gray-500 dark:text-gray-400">
                                                        Last edited {{ $campaign->updated_at->diffForHumans() }}
                                                    </flux:text>
                                                </div>
                                                
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-1 text-xs text-amber-600 dark:text-amber-400">
                                                        <flux:icon name="clock" class="w-3 h-3" />
                                                        <span>{{ ['Basic Info', 'Campaign Details', 'Requirements', 'Review'][$campaign->current_step - 1] ?? 'In Progress' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Subtle accent line -->
                                            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-amber-500 to-orange-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </flux:card>
                    @endif
                </div>
            @else
                <!-- Enhanced Empty State -->
                <flux:card class="text-center">
                    <div class="p-12">
                        <div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-2xl flex items-center justify-center mb-6">
                            <flux:icon name="megaphone" class="w-10 h-10 text-blue-600 dark:text-blue-400" />
                        </div>
                        <flux:heading size="xl" class="mb-3 text-gray-900 dark:text-white">Ready to launch your first campaign?</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
                            Connect with talented influencers and grow your brand reach. Our smart matching system will help you find the perfect collaborators.
                        </flux:text>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <flux:button href="{{ route('campaigns.create') }}" variant="primary" icon="plus" class="shadow-lg shadow-blue-500/25">
                                Create Your First Campaign
                            </flux:button>
                            <flux:button href="{{ route('search') }}" variant="ghost" icon="magnifying-glass">
                                Browse Influencers
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @endif
        </div>

        <!-- Enhanced Sidebar -->
        <div class="space-y-8">
            <!-- Recent Applications -->
            <flux:card class="overflow-hidden">
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-950/50 dark:to-pink-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <flux:heading size="lg" class="text-gray-900 dark:text-white">Recent Applications</flux:heading>
                            <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                @if($this->getPendingApplications()->count() > 0)
                                    <flux:icon name="bell" class="w-4 h-4 text-purple-500" />
                                    <span>{{ $this->getPendingApplications()->count() }} needs attention</span>
                                @else
                                    <flux:icon name="check-circle" class="w-4 h-4 text-green-500" />
                                    <span>All reviewed</span>
                                @endif
                            </div>
                        </div>
                        @if($this->getPendingApplications()->count() > 0)
                            <flux:button href="{{ route('applications.index') }}" variant="ghost" icon-trailing="arrow-right" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-200">
                                View All
                            </flux:button>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    @php
                        $recentApplications = $this->getPendingApplications()->take(4);
                    @endphp

                    @if($recentApplications->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentApplications as $application)
                                <div class="group relative bg-gradient-to-r from-white to-purple-50/30 dark:from-gray-800 dark:to-purple-900/10 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-300">
                                    <!-- Status indicator -->
                                    <div class="absolute top-3 right-3">
                                        <div class="flex items-center gap-1 px-2 py-1 bg-amber-100 dark:bg-amber-900/30 rounded-full">
                                            <div class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></div>
                                            <flux:text size="xs" class="font-medium text-amber-700 dark:text-amber-300">Pending</flux:text>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4">
                                        <div class="flex items-start gap-3 mb-4">
                                            <flux:avatar 
                                                name="{{ $application->user->name }}" 
                                                size="sm"
                                                class="flex-shrink-0 shadow-sm"
                                            />
                                            <div class="flex-1 min-w-0 space-y-1 pr-16">
                                                <flux:heading size="sm" class="text-gray-900 dark:text-white font-medium">
                                                    {{ $application->user->name }}
                                                </flux:heading>
                                                <flux:text size="xs" class="text-gray-600 dark:text-gray-400 line-clamp-1">
                                                    {{ Str::limit($application->campaign->project_name, 40) }}
                                                </flux:text>
                                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <flux:icon name="clock" class="w-3 h-3" />
                                                    <span>{{ $application->submitted_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-2">
                                            <flux:button 
                                                href="{{ route('applications.show', $application->id) }}" 
                                                variant="ghost" 
                                                size="xs"
                                                class="flex-1 text-center bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium"
                                            >
                                                Review Application
                                            </flux:button>
                                            <flux:button 
                                                wire:click="acceptApplication({{ $application->id }})" 
                                                variant="ghost" 
                                                size="xs"
                                                class="w-8 h-8 p-0 bg-green-50 hover:bg-green-100 dark:bg-green-900/30 dark:hover:bg-green-900/50 text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 rounded-lg"
                                                icon="check"
                                            >
                                            </flux:button>
                                            <flux:button 
                                                wire:click="declineApplication({{ $application->id }})" 
                                                variant="ghost" 
                                                size="xs"
                                                class="w-8 h-8 p-0 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 rounded-lg"
                                                icon="x-mark"
                                            >
                                            </flux:button>
                                        </div>
                                    </div>
                                    
                                    <!-- Subtle accent line -->
                                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-purple-500 to-pink-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <flux:icon name="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400" />
                            </div>
                            <flux:heading size="sm" class="mb-2 text-gray-900 dark:text-white">All caught up!</flux:heading>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                                New applications will appear here
                            </flux:text>
                        </div>
                    @endif
                </div>
            </flux:card>

            <!-- Enhanced Quick Actions -->
            <flux:card class="overflow-hidden">
                <div class="bg-gradient-to-r from-slate-50 to-gray-50 dark:from-slate-950/50 dark:to-gray-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
                    <flux:heading size="lg" class="text-gray-900 dark:text-white">Quick Actions</flux:heading>
                    <flux:text size="sm" class="text-gray-600 dark:text-gray-400">Streamline your workflow</flux:text>
                </div>
                
                <div class="p-6">
                    <div class="space-y-3">
                        <flux:button href="{{ route('search') }}" variant="ghost" icon="magnifying-glass" class="w-full justify-start p-4 text-left hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-200 dark:hover:border-blue-700 group transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                                    <flux:icon name="magnifying-glass" class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                </div>
                                <div class="flex-1">
                                    <flux:text class="font-medium text-gray-900 dark:text-white">Find Influencers</flux:text>
                                    <flux:text size="xs" class="text-gray-500 dark:text-gray-400">Browse our talent network</flux:text>
                                </div>
                                <flux:icon name="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all" />
                            </div>
                        </flux:button>
                        
                        <flux:button href="{{ route('analytics') }}" variant="ghost" icon="chart-bar" class="w-full justify-start p-4 text-left hover:bg-green-50 dark:hover:bg-green-900/20 hover:border-green-200 dark:hover:border-green-700 group transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-800/50 transition-colors">
                                    <flux:icon name="chart-bar" class="w-4 h-4 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="flex-1">
                                    <flux:text class="font-medium text-gray-900 dark:text-white">View Analytics</flux:text>
                                    <flux:text size="xs" class="text-gray-500 dark:text-gray-400">Campaign performance insights</flux:text>
                                </div>
                                <flux:icon name="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all" />
                            </div>
                        </flux:button>
                        
                        <flux:button href="{{ route('campaigns.index') }}" variant="ghost" icon="cog-6-tooth" class="w-full justify-start p-4 text-left hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:border-purple-200 dark:hover:border-purple-700 group transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 transition-colors">
                                    <flux:icon name="cog-6-tooth" class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                </div>
                                <div class="flex-1">
                                    <flux:text class="font-medium text-gray-900 dark:text-white">Manage Campaigns</flux:text>
                                    <flux:text size="xs" class="text-gray-500 dark:text-gray-400">Edit and organize campaigns</flux:text>
                                </div>
                                <flux:icon name="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-purple-500 group-hover:translate-x-1 transition-all" />
                            </div>
                        </flux:button>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</div>