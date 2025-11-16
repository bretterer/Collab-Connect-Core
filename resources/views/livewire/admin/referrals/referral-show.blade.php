<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Referral Program Details</p>
            </div>
            <div class="flex gap-3">
                <flux:button href="{{ route('admin.referrals.index') }}" variant="ghost">
                    Back to List
                </flux:button>
            </div>
        </div>
    </div>

    @if($user->referralEnrollment)
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <flux:card>
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Referrals</flux:text>
                    <flux:heading size="xl">{{ $stats['total_referrals'] }}</flux:heading>
                </div>
            </flux:card>

            <flux:card>
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-1">Active Referrals</flux:text>
                    <flux:heading size="xl">{{ $stats['active_referrals'] }}</flux:heading>
                </div>
            </flux:card>

            <flux:card>
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pending Referrals</flux:text>
                    <flux:heading size="xl">{{ $stats['pending_referrals'] }}</flux:heading>
                </div>
            </flux:card>

            <flux:card>
                <div>
                    <flux:text class="text-sm text-gray-600 dark:text-gray-400 mb-1">Conversion Rate</flux:text>
                    <flux:heading size="xl">{{ $stats['conversion_rate'] }}%</flux:heading>
                </div>
            </flux:card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column - Forms --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- Referral Details --}}
                <flux:card>
                    <div class="mb-6">
                        <flux:heading size="lg">Referral Partner Information</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400 mt-1">View partner details and manage payout settings</flux:text>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Name</flux:text>
                            <flux:text class="font-medium text-base">{{ $user->name }}</flux:text>
                        </div>

                        <div>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Email</flux:text>
                            <flux:text class="font-medium text-base">{{ $user->email }}</flux:text>
                        </div>

                        <flux:field>
                            <flux:label>PayPal Email</flux:label>
                            <flux:input wire:model="paypal_email" type="email" placeholder="paypal@example.com" />
                            <flux:error name="paypal_email" />
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Email address for receiving referral payments via PayPal
                            </flux:text>
                            @if($user->referralEnrollment->hasPayPalConnected())
                                <div class="mt-2 flex items-center gap-2">
                                    <flux:badge variant="success">Connected</flux:badge>
                                    <flux:button wire:click="disconnectPayPal" size="sm" variant="ghost" icon="x-mark">
                                        Disconnect
                                    </flux:button>
                                </div>
                            @endif
                            @if($paypal_email)
                                <div class="mt-2">
                                    <flux:button wire:click="save" variant="primary" size="sm">
                                        Update PayPal Email
                                    </flux:button>
                                </div>
                            @endif
                        </flux:field>

                        <div>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Joined Date</flux:text>
                            <flux:text class="font-medium">{{ $user->referralEnrollment->enrolled_at?->format('F d, Y') ?? $user->created_at->format('F d, Y') }}</flux:text>
                        </div>
                    </div>
                </flux:card>

                {{-- Referral Commission Percentage --}}
                <flux:card>
                    <div class="mb-6">
                        <flux:heading size="lg">Commission Percentage</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400 mt-1">
                            Manage commission rates for this referral partner. You can set permanent changes or temporary promotional rates that apply to all users referred during the promotional period.
                        </flux:text>
                    </div>

                    <flux:field>
                        <flux:label>Current Commission %</flux:label>
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <flux:input value="{{ $currentPercentage }}%" disabled />
                            </div>
                            <flux:button wire:click="openPercentageModal" variant="primary">
                                Change Percentage
                            </flux:button>
                        </div>
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            This is the current commission percentage from the latest history record.
                        </flux:text>
                    </flux:field>
                </flux:card>

                {{-- Share Links --}}
                <flux:card>
                    <div class="mb-6">
                        <flux:heading size="lg">Referral Links</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400 mt-1">Copy and share with this referral partner.</flux:text>
                    </div>

                    <div class="space-y-4">
                        <flux:field>
                            <flux:label>CollabConnect Partner Referral Link</flux:label>
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <flux:input value="{{ $referralLink }}" readonly />
                                </div>
                                <flux:button
                                    wire:click="copyReferralLink"
                                    variant="ghost"
                                    x-on:click="navigator.clipboard.writeText('{{ $referralLink }}')"
                                    icon="clipboard"
                                >
                                </flux:button>
                            </div>
                        </flux:field>
                    </div>
                </flux:card>

                {{-- Referred Users Table --}}
                @if($user->referralEnrollment->referrals->count() > 0)
                    <flux:card>
                        <div class="mb-6">
                            <flux:heading size="lg">Referred Users</flux:heading>
                        </div>

                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>User</flux:table.column>
                                <flux:table.column>Status</flux:table.column>
                                <flux:table.column>Joined</flux:table.column>
                                <flux:table.column>Converted</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @foreach($user->referralEnrollment->referrals as $referral)
                                    <flux:table.row>
                                        <flux:table.cell>
                                            <div>
                                                <div class="font-medium">{{ $referral->referred->name }}</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $referral->referred->email }}</div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge :variant="$referral->status->color()">
                                                {{ $referral->status->label() }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $referral->created_at->format('M d, Y') }}</flux:table.cell>
                                        <flux:table.cell>
                                            {{ $referral->converted_at?->format('M d, Y') ?? '-' }}
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </flux:card>
                @endif
            </div>

            {{-- Right Column - Info & History --}}
            <div class="space-y-8">
                {{-- Earnings Summary --}}
                <flux:card>
                    <div class="mb-4">
                        <flux:heading size="lg">Earnings</flux:heading>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Lifetime Earnings</flux:text>
                            <flux:heading size="xl">${{ number_format($stats['lifetime_earnings'], 2) }}</flux:heading>
                        </div>

                        <flux:separator />

                        <div>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Pending Payout</flux:text>
                            <flux:heading size="lg">${{ number_format($stats['pending_payout'], 2) }}</flux:heading>
                        </div>
                    </div>
                </flux:card>

                {{-- Enrollment Info --}}
                <flux:card>
                    <div class="mb-4">
                        <flux:heading size="lg">Enrollment Info</flux:heading>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Referral Code</flux:text>
                            <flux:text class="font-mono font-medium">{{ $user->referralEnrollment->code }}</flux:text>
                        </div>

                        <div>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Current Percentage</flux:text>
                            <flux:text class="font-medium">{{ $currentPercentage }}%</flux:text>
                        </div>

                        <div>
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Enrolled</flux:text>
                            <flux:text class="font-medium">{{ $user->referralEnrollment->enrolled_at?->format('M d, Y') ?? $user->created_at->format('M d, Y') }}</flux:text>
                        </div>
                    </div>
                </flux:card>

                {{-- Percentage History --}}
                @if($user->referralEnrollment->percentageHistory->count() > 0)
                    <flux:card>
                        <div class="mb-4">
                            <flux:heading size="lg">Percentage History</flux:heading>
                        </div>

                        <div class="space-y-3">
                            @foreach($user->referralEnrollment->percentageHistory->take(5)->reverse() as $history)
                                <div class="pb-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:text class="font-medium">{{ $history->old_percentage }}% → {{ $history->new_percentage }}%</flux:text>
                                        <flux:badge variant="zinc" size="sm">{{ $history->change_type->label() }}</flux:badge>
                                    </div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $history->created_at->format('M d, Y') }}
                                        @if($history->changedBy)
                                            by {{ $history->changedBy->name }}
                                        @endif
                                    </flux:text>
                                    @if($history->reason)
                                        <flux:text class="text-sm text-gray-500 dark:text-gray-400 italic mt-1">
                                            {{ $history->reason }}
                                        </flux:text>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </flux:card>
                @endif

                {{-- Recent Payouts --}}
                @if($user->referralEnrollment->payouts->count() > 0)
                    <flux:card>
                        <div class="mb-4">
                            <flux:heading size="lg">Recent Payouts</flux:heading>
                        </div>

                        <div class="space-y-3">
                            @foreach($user->referralEnrollment->payouts->take(5) as $payout)
                                <div class="pb-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <flux:text class="font-medium">${{ number_format($payout->amount, 2) }}</flux:text>
                                        <flux:badge :variant="$payout->status->color()" size="sm">
                                            {{ $payout->status->label() }}
                                        </flux:badge>
                                    </div>
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ now()->month($payout->month)->format('F') }} {{ $payout->year }}
                                    </flux:text>
                                    @if($payout->paid_at)
                                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                                            Paid: {{ $payout->paid_at->format('M d, Y') }}
                                        </flux:text>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </flux:card>
                @endif
            </div>
        </div>


        {{-- Percentage Change Modal --}}
        <flux:modal wire:model="showPercentageModal" class="max-w-lg">
            <div class="p-6">
                <flux:heading size="lg" class="mb-4">Change Commission Percentage</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
                    Set a new commission rate. Temporary rates apply to all users referred during the promotional period and remain in effect for those users permanently.
                </flux:text>

                <div class="space-y-6">
                    <flux:field>
                        <flux:label>New Percentage</flux:label>
                        <flux:input wire:model="newPercentage" type="number" min="0" max="100" />
                        <flux:error name="newPercentage" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Change Type</flux:label>
                        <flux:select wire:model.live="percentageChangeType">
                            <option value="">Select type...</option>
                            @foreach($this->getChangeTypeOptions() as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="percentageChangeType" />
                        @if($percentageChangeType === \App\Enums\PercentageChangeType::PERMANENT->value)
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                This percentage will remain in effect until you change it again. All current and future referrals will use this rate.
                            </flux:text>
                        @elseif($percentageChangeType === \App\Enums\PercentageChangeType::TEMPORARY_DATE->value)
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                Users referred during this promotional period will keep this percentage permanently, even after the promotion ends.
                            </flux:text>
                        @endif
                    </flux:field>

                    @if($percentageChangeType === \App\Enums\PercentageChangeType::TEMPORARY_DATE->value)
                        <flux:field>
                            <flux:label>Promotion Ends On</flux:label>
                            <flux:input wire:model="expiresAt" type="date" />
                            <flux:error name="expiresAt" />
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                After this date, commissions will revert to {{ $currentPercentage }}%. Existing referrals from this period will keep this rate.
                            </flux:text>
                        </flux:field>
                    @endif

                    <flux:field>
                        <flux:label>Reason <span class="text-red-600">*</span></flux:label>
                        <flux:textarea wire:model="reason" rows="3" placeholder="Why is this percentage being changed? (Required)" />
                        <flux:error name="reason" />
                        <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Provide context for this change to maintain an audit trail.
                        </flux:text>
                    </flux:field>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8">
                    <flux:button wire:click="closePercentageModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="savePercentageChange" variant="primary">
                        Save Changes
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @else
        <flux:callout variant="warning">
            This user is not enrolled in the referral program.
        </flux:callout>
    @endif
</div>
