<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <flux:heading size="xl">Edit Email Sequence</flux:heading>
        <flux:text class="mt-1">
            Manage automated email sequences triggered by form submissions, landing page visits, or purchases
        </flux:text>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <flux:card>
                <flux:heading size="base" class="mb-4">Sequence Details</flux:heading>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Sequence Name</flux:label>
                        <flux:input wire:model="name" placeholder="e.g., Welcome Series" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Description</flux:label>
                        <flux:textarea wire:model="description" placeholder="Brief description of this sequence" rows="3" />
                        <flux:error name="description" />
                    </flux:field>
                </div>
            </flux:card>

            <!-- Subscribe Triggers -->
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <flux:heading size="base">Subscribe Triggers</flux:heading>
                        <flux:text class="text-sm">When should users be added to this sequence?</flux:text>
                    </div>
                    <flux:button wire:click="addTrigger('subscribe')" size="sm" variant="primary" icon="plus">
                        Add Trigger
                    </flux:button>
                </div>

                @if(count($subscribeTriggers) > 0)
                    <div class="space-y-2">
                        @foreach($subscribeTriggers as $index => $trigger)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <flux:badge color="green">{{ $index + 1 }}</flux:badge>
                                    <div>
                                        <div class="font-medium text-sm">
                                            {{ \App\Enums\EmailTriggerType::from($trigger['type'])->label() }}
                                        </div>
                                        @if(isset($trigger['form_id']))
                                            <div class="text-xs text-zinc-600 dark:text-zinc-400">
                                                Form: {{ $forms->find($trigger['form_id'])?->title ?? 'Unknown' }}
                                            </div>
                                        @endif
                                        @if(isset($trigger['landing_page_id']))
                                            <div class="text-xs text-zinc-600 dark:text-zinc-400">
                                                Landing Page: {{ $landingPages->find($trigger['landing_page_id'])?->title ?? 'Unknown' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <flux:button
                                    wire:click="removeTrigger('subscribe', {{ $index }})"
                                    size="sm"
                                    variant="ghost"
                                    class="text-red-600"
                                    icon="trash"
                                >
                                </flux:button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-zinc-500 dark:text-zinc-400">
                        No triggers configured. Add a trigger to start collecting subscribers.
                    </div>
                @endif
            </flux:card>

            @if(true === false)
            <!-- Unsubscribe Triggers -->
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <flux:heading size="base">Unsubscribe Triggers</flux:heading>
                        <flux:text class="text-sm">When should users be removed from this sequence?</flux:text>
                    </div>
                    <flux:button wire:click="addTrigger('unsubscribe')" size="sm" variant="ghost" icon="plus">
                        Add Trigger
                    </flux:button>
                </div>

                @if(count($unsubscribeTriggers) > 0)
                    <div class="space-y-2">
                        @foreach($unsubscribeTriggers as $index => $trigger)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <flux:badge color="red">{{ $index + 1 }}</flux:badge>
                                    <div>
                                        <div class="font-medium text-sm">
                                            {{ \App\Enums\EmailTriggerType::from($trigger['type'])->label() }}
                                        </div>
                                    </div>
                                </div>
                                <flux:button
                                    wire:click="removeTrigger('unsubscribe', {{ $index }})"
                                    size="sm"
                                    variant="ghost"
                                    class="text-red-600"
                                    icon="trash"
                                >
                                </flux:button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-sm text-zinc-500 dark:text-zinc-400">
                        No unsubscribe triggers configured (optional)
                    </div>
                @endif
            </flux:card>
            @endif
            <!-- Emails List -->
            <flux:card>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <flux:heading size="base">Email Sequence</flux:heading>
                        <flux:text class="text-sm">{{ count($emails) }} {{ Str::plural('email', count($emails)) }} in sequence</flux:text>
                    </div>
                    <flux:button wire:click="addEmail" variant="primary" icon="plus">
                        Add Email
                    </flux:button>
                </div>

                @if(count($emails) > 0)
                    <div class="space-y-3">
                        @foreach($emails as $index => $email)
                            <div class="p-4 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <flux:badge color="blue">Day {{ $email['delay_days'] }}</flux:badge>
                                            <div class="font-medium">{{ $email['name'] }}</div>
                                        </div>
                                        <div class="text-sm text-zinc-600 dark:text-zinc-400 mb-2">
                                            Subject: {{ $email['subject'] }}
                                        </div>
                                        <div class="text-xs text-zinc-500">
                                            Sends at {{ Carbon\Carbon::parse($email['send_time'])->format('g:i A') }} {{ $email['timezone'] }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        @if($index > 0)
                                            <flux:button wire:click="moveEmailUp({{ $index }})" size="sm" variant="ghost" icon="arrow-up">
                                            </flux:button>
                                        @endif
                                        @if($index < count($emails) - 1)
                                            <flux:button wire:click="moveEmailDown({{ $index }})" size="sm" variant="ghost" icon="arrow-down">
                                            </flux:button>
                                        @endif
                                        <flux:button wire:click="editEmail({{ $index }})" size="sm" variant="ghost" icon="pencil">
                                        </flux:button>
                                        <flux:button
                                            wire:click="deleteEmail({{ $index }})"
                                            wire:confirm="Are you sure you want to delete this email?"
                                            size="sm"
                                            variant="ghost"
                                            class="text-red-600"
                                            icon="trash"
                                        >
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <flux:icon.envelope class="size-12 mx-auto text-zinc-400 mb-4" />
                        <flux:heading size="sm" class="mb-2">No emails in sequence</flux:heading>
                        <flux:text>Add your first email to get started</flux:text>
                        <flux:button wire:click="addEmail" variant="primary" class="mt-4">
                            Add Email
                        </flux:button>
                    </div>
                @endif
            </flux:card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats -->
            <flux:card>
                <flux:heading size="base" class="mb-4">Statistics</flux:heading>
                <div class="space-y-3">
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Total Subscribers</div>
                        <div class="text-2xl font-bold">{{ $emailSequence->total_subscribers }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-zinc-600 dark:text-zinc-400">Active Subscribers</div>
                        <div class="text-2xl font-bold text-green-600">
                            {{ $emailSequence->activeSubscribers()->count() }}
                        </div>
                    </div>
                </div>
            </flux:card>

            <!-- Actions -->
            <flux:card>
                <flux:heading size="base" class="mb-4">Actions</flux:heading>
                <div class="space-y-2">
                    <flux:button wire:click="save" variant="primary" class="w-full" icon="check" >
                        Save Sequence
                    </flux:button>
                    <flux:button href="{{ route('admin.marketing.email-sequences.index') }}" wire:navigate variant="ghost" class="w-full">
                        Cancel
                    </flux:button>
                </div>
            </flux:card>

            <!-- Help -->
            <flux:card x-data="{
                init() {
                    this.$nextTick(() => {
                        const savedFormId = localStorage.getItem('mergeTagFormId_{{ $emailSequence->id }}');
                        if (savedFormId && savedFormId !== '' && !$wire.mergeTagFormId) {
                            $wire.set('mergeTagFormId', parseInt(savedFormId));
                        }
                    });
                }
            }">
                <flux:heading size="sm" class="mb-3">Merge Tags</flux:heading>

                <flux:field class="mb-4">
                    <flux:label>Select Form</flux:label>
                    <flux:select
                        wire:model.live="mergeTagFormId"
                        x-on:change="localStorage.setItem('mergeTagFormId_{{ $emailSequence->id }}', $event.target.value)">
                        <option value="">Choose a form...</option>
                        @foreach($forms as $form)
                            <option value="{{ $form->id }}">{{ $form->title }}</option>
                        @endforeach
                    </flux:select>
                    <flux:description class="text-xs">
                        Select a form to see available merge tags from its fields
                    </flux:description>
                </flux:field>

                <flux:text class="text-sm mb-3">Available merge tags:</flux:text>
                <div class="space-y-2">
                    @foreach($this->getAvailableMergeTags() as $mergeTag)
                        <div class="group">
                            <div class="p-2 bg-zinc-100 dark:bg-zinc-800 rounded transition-colors hover:bg-zinc-200 dark:hover:bg-zinc-700 cursor-pointer"
                                 x-on:click="navigator.clipboard.writeText('{{ $mergeTag['tag'] }}')">
                                <div class="text-xs font-mono text-zinc-900 dark:text-zinc-100">
                                    {{ $mergeTag['tag'] }}
                                </div>
                                <div class="text-xs text-zinc-600 dark:text-zinc-400 mt-0.5">
                                    {{ $mergeTag['description'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($mergeTagFormId)
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 mt-3">
                        Click any tag to copy to clipboard
                    </flux:text>
                @endif
            </flux:card>
        </div>
    </div>

    <!-- Trigger Selection Modal -->
    @if($showTriggerModal)
        <flux:modal name="trigger-modal" class="max-w-lg" wire:model="showTriggerModal">
            <div class="p-6">
                <flux:heading size="base" class="mb-6">
                    {{ $triggerType === 'subscribe' ? 'Add Subscribe Trigger' : 'Add Unsubscribe Trigger' }}
                </flux:heading>

                <div class="space-y-4">
                    <flux:field>
                        <flux:label>Select Form</flux:label>
                        <flux:select wire:model="selectedFormId">
                            <option value="">Choose a form...</option>
                            @foreach($forms as $form)
                                <option value="{{ $form->id }}">{{ $form->title }}</option>
                            @endforeach
                        </flux:select>
                        <flux:description>
                            {{ $triggerType === 'subscribe' ? 'Users will be added to this sequence when they submit this form' : 'Users will be removed from this sequence when they submit this form' }}
                        </flux:description>
                    </flux:field>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <flux:button wire:click="cancelTrigger" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="confirmTrigger" variant="primary">
                        Add Trigger
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif

    <!-- Email Editor Modal -->
    @if($showEmailEditor)
        <flux:modal name="email-editor" class="max-w-4xl" wire:model="showEmailEditor">
            <div class="p-6">
                <flux:heading size="base" class="mb-6">
                    {{ $editingEmailIndex !== null ? 'Edit Email' : 'Add Email' }}
                </flux:heading>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Day</flux:label>
                            <flux:input type="number" wire:model="emailData.delay_days" min="0" placeholder="0" />
                            <flux:description>Number of days after trigger</flux:description>
                            <flux:error name="emailData.delay_days" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Time</flux:label>
                            <flux:input type="time" wire:model="emailData.send_time" />
                            <flux:description>Time of day to send</flux:description>
                            <flux:error name="emailData.send_time" />
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Internal Title</flux:label>
                        <flux:input wire:model="emailData.name" placeholder="e.g., Welcome Email - Day 0" />
                        <flux:description>Used in reports, not shown to recipients</flux:description>
                        <flux:error name="emailData.name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Subject</flux:label>
                        <flux:input wire:model="emailData.subject" placeholder="Welcome to our community!" maxlength="140" />
                        <flux:description>Max 140 characters</flux:description>
                        <flux:error name="emailData.subject" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Preview Text</flux:label>
                        <flux:input wire:model="emailData.preview_text" placeholder="This appears in the inbox preview..." maxlength="140" />
                        <flux:description>Max 140 characters - appears in email client preview</flux:description>
                        <flux:error name="emailData.preview_text" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Email Body</flux:label>
                        <flux:editor wire:model="emailData.body" placeholder="Write your email content here. Use merge tags like {first_name} to personalize." />
                        <flux:error name="emailData.body" />
                    </flux:field>
                </div>

                <div class="mt-6 flex justify-end gap-2">
                    <flux:button wire:click="cancelEmailEdit" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button wire:click="saveEmail" variant="primary">
                        Save Email
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
