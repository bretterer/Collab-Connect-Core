<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <flux:heading size="xl">Link-in-Bio Analytics</flux:heading>
                    @if(!$this->hasAdvancedAnalyticsAccess)
                        <flux:badge color="amber">Elite Feature</flux:badge>
                    @endif
                </div>
                <flux:text class="mt-1">
                    Track views, clicks, and engagement on your link-in-bio page.
                </flux:text>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('link-in-bio.index') }}" wire:navigate>
                    <flux:button variant="ghost" icon="arrow-left">
                        Back to Editor
                    </flux:button>
                </a>
            </div>
        </div>

        <x-tier-locked
            :locked="!$this->hasAdvancedAnalyticsAccess"
            :requiredTier="$this->requiredTierForAnalytics"
            overlayStyle="blur"
            title="Advanced Analytics"
            description="Unlock detailed analytics for your Link-in-Bio page to understand your audience and optimize your links."
        >
            {{-- Date Range Selector --}}
            <div class="mb-6">
                <flux:card class="p-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <flux:text class="font-medium">Date Range:</flux:text>
                        <flux:radio.group wire:model.live="dateRange" variant="segmented">
                            <flux:radio value="7" label="7 days" />
                            <flux:radio value="30" label="30 days" />
                            <flux:radio value="90" label="90 days" />
                            <flux:radio value="custom" label="Custom" />
                        </flux:radio.group>

                        @if($dateRange === 'custom')
                            <div class="flex items-center gap-2">
                                <flux:input type="date" wire:model.live="customStartDate" size="sm" />
                                <flux:text>to</flux:text>
                                <flux:input type="date" wire:model.live="customEndDate" size="sm" />
                            </div>
                        @endif
                    </div>
                </flux:card>
            </div>

            {{-- Overview Metrics Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                {{-- Total Views --}}
                <flux:card class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">Total Views</flux:text>
                            <flux:heading size="xl" class="mt-1 tabular-nums">
                                {{ number_format($this->overviewMetrics['views']) }}
                            </flux:heading>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <flux:icon name="eye" class="text-white w-6 h-6" />
                        </div>
                    </div>
                </flux:card>

                {{-- Unique Visitors --}}
                <flux:card class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">Unique Visitors</flux:text>
                            <flux:heading size="xl" class="mt-1 tabular-nums">
                                {{ number_format($this->overviewMetrics['unique_visitors']) }}
                            </flux:heading>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                            <flux:icon name="users" class="text-white w-6 h-6" />
                        </div>
                    </div>
                </flux:card>

                {{-- Total Clicks --}}
                <flux:card class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">Total Clicks</flux:text>
                            <flux:heading size="xl" class="mt-1 tabular-nums">
                                {{ number_format($this->overviewMetrics['clicks']) }}
                            </flux:heading>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <flux:icon name="cursor-arrow-rays" class="text-white w-6 h-6" />
                        </div>
                    </div>
                </flux:card>

                {{-- Click-Through Rate --}}
                <flux:card class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">Click-Through Rate</flux:text>
                            <flux:heading size="xl" class="mt-1 tabular-nums">
                                {{ $this->overviewMetrics['ctr'] }}%
                            </flux:heading>
                        </div>
                        <div class="h-12 w-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center">
                            <flux:icon name="chart-bar" class="text-white w-6 h-6" />
                        </div>
                    </div>
                </flux:card>
            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Views Over Time Chart --}}
                <flux:card class="p-6">
                    <flux:heading size="base" class="mb-4">Views Over Time</flux:heading>
                    @if(count($this->viewsChartData) > 0)
                        <flux:chart :value="$this->viewsChartData" class="aspect-[3/1]">
                            <flux:chart.svg>
                                <flux:chart.line field="views" class="text-blue-500 dark:text-blue-400" />
                                <flux:chart.area field="views" class="text-blue-200/50 dark:text-blue-400/30" />
                                <flux:chart.axis axis="x" field="date">
                                    <flux:chart.axis.line />
                                    <flux:chart.axis.tick />
                                </flux:chart.axis>
                                <flux:chart.axis axis="y">
                                    <flux:chart.axis.grid />
                                    <flux:chart.axis.tick />
                                </flux:chart.axis>
                                <flux:chart.cursor />
                            </flux:chart.svg>
                            <flux:chart.tooltip>
                                <flux:chart.tooltip.heading field="date" />
                                <flux:chart.tooltip.value field="views" label="Views" />
                            </flux:chart.tooltip>
                        </flux:chart>
                    @else
                        <div class="flex items-center justify-center h-48 text-gray-400">
                            <flux:text>No view data available for this period.</flux:text>
                        </div>
                    @endif
                </flux:card>

                {{-- Clicks Over Time Chart --}}
                <flux:card class="p-6">
                    <flux:heading size="base" class="mb-4">Clicks Over Time</flux:heading>
                    @if(count($this->clicksChartData) > 0)
                        <flux:chart :value="$this->clicksChartData" class="aspect-[3/1]">
                            <flux:chart.svg>
                                <flux:chart.line field="clicks" class="text-purple-500 dark:text-purple-400" />
                                <flux:chart.area field="clicks" class="text-purple-200/50 dark:text-purple-400/30" />
                                <flux:chart.axis axis="x" field="date">
                                    <flux:chart.axis.line />
                                    <flux:chart.axis.tick />
                                </flux:chart.axis>
                                <flux:chart.axis axis="y">
                                    <flux:chart.axis.grid />
                                    <flux:chart.axis.tick />
                                </flux:chart.axis>
                                <flux:chart.cursor />
                            </flux:chart.svg>
                            <flux:chart.tooltip>
                                <flux:chart.tooltip.heading field="date" />
                                <flux:chart.tooltip.value field="clicks" label="Clicks" />
                            </flux:chart.tooltip>
                        </flux:chart>
                    @else
                        <div class="flex items-center justify-center h-48 text-gray-400">
                            <flux:text>No click data available for this period.</flux:text>
                        </div>
                    @endif
                </flux:card>
            </div>

            {{-- Device Breakdown & Top Referrers --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Device Breakdown --}}
                <flux:card class="p-6">
                    <flux:heading size="base" class="mb-4">Device Breakdown</flux:heading>
                    @php
                        $deviceTotal = array_sum($this->deviceBreakdown);
                    @endphp
                    @if($deviceTotal > 0)
                        <div class="space-y-4">
                            {{-- Mobile --}}
                            <div>
                                <div class="flex justify-between mb-1">
                                    <flux:text class="flex items-center gap-2">
                                        <flux:icon name="device-phone-mobile" class="w-4 h-4" />
                                        Mobile
                                    </flux:text>
                                    <flux:text>{{ number_format($this->deviceBreakdown['mobile']) }} ({{ round(($this->deviceBreakdown['mobile'] / $deviceTotal) * 100) }}%)</flux:text>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($this->deviceBreakdown['mobile'] / $deviceTotal) * 100 }}%"></div>
                                </div>
                            </div>
                            {{-- Desktop --}}
                            <div>
                                <div class="flex justify-between mb-1">
                                    <flux:text class="flex items-center gap-2">
                                        <flux:icon name="computer-desktop" class="w-4 h-4" />
                                        Desktop
                                    </flux:text>
                                    <flux:text>{{ number_format($this->deviceBreakdown['desktop']) }} ({{ round(($this->deviceBreakdown['desktop'] / $deviceTotal) * 100) }}%)</flux:text>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($this->deviceBreakdown['desktop'] / $deviceTotal) * 100 }}%"></div>
                                </div>
                            </div>
                            {{-- Tablet --}}
                            <div>
                                <div class="flex justify-between mb-1">
                                    <flux:text class="flex items-center gap-2">
                                        <flux:icon name="device-tablet" class="w-4 h-4" />
                                        Tablet
                                    </flux:text>
                                    <flux:text>{{ number_format($this->deviceBreakdown['tablet']) }} ({{ round(($this->deviceBreakdown['tablet'] / $deviceTotal) * 100) }}%)</flux:text>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ ($this->deviceBreakdown['tablet'] / $deviceTotal) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-center h-32 text-gray-400">
                            <flux:text>No device data available.</flux:text>
                        </div>
                    @endif
                </flux:card>

                {{-- Top Referrers --}}
                <flux:card class="p-6">
                    <flux:heading size="base" class="mb-4">Top Referrers</flux:heading>
                    @if($this->topReferrers->count() > 0)
                        <div class="space-y-3">
                            @foreach($this->topReferrers as $referrer)
                                <div class="flex justify-between items-center">
                                    <flux:text class="truncate max-w-[200px]">{{ $referrer->referrer_domain }}</flux:text>
                                    <flux:badge>{{ number_format($referrer->count) }}</flux:badge>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex items-center justify-center h-32 text-gray-400">
                            <flux:text>No referrer data available.</flux:text>
                        </div>
                    @endif
                </flux:card>
            </div>

            {{-- Link Performance Table --}}
            <flux:card class="p-6">
                <flux:heading size="base" class="mb-4">Link Performance</flux:heading>
                @if($this->linkPerformance->count() > 0)
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Link</flux:table.column>
                            <flux:table.column>Clicks</flux:table.column>
                            <flux:table.column>CTR</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($this->linkPerformance as $link)
                                <flux:table.row>
                                    <flux:table.cell>
                                        <div class="flex flex-col">
                                            <flux:text class="font-medium">{{ $link->link_title ?: 'Untitled Link' }}</flux:text>
                                            <flux:text size="sm" class="text-gray-500 truncate max-w-xs">{{ $link->link_url }}</flux:text>
                                        </div>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge>{{ number_format($link->clicks) }}</flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell>
                                        {{ $link->ctr }}%
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                @else
                    <div class="flex items-center justify-center h-32 text-gray-400">
                        <flux:text>No link click data available for this period.</flux:text>
                    </div>
                @endif
            </flux:card>
        </x-tier-locked>
    </div>
</div>
