<div>
    <flux:card class="overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-950/50 dark:to-pink-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="space-y-1">
                    <flux:heading size="xl" class="text-gray-900 dark:text-white">Deliverables</flux:heading>
                    <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <span class="flex items-center gap-1">
                            <flux:icon name="check-circle" class="w-4 h-4 text-green-500" />
                            {{ $this->progressStats['approved'] }} approved
                        </span>
                        <span class="flex items-center gap-1">
                            <flux:icon name="clock" class="w-4 h-4 text-amber-500" />
                            {{ $this->progressStats['pending'] }} pending
                        </span>
                    </div>
                </div>
                @if($this->isBusiness && $collaboration->status->value === 'active')
                    <div class="flex items-center gap-2">
                        <flux:button
                            wire:click="completeCollaboration"
                            wire:confirm="{{ $this->allDeliverablesApproved ? 'Are you sure you want to mark this collaboration as complete?' : 'Not all deliverables are approved. Are you sure you want to complete this collaboration anyway?' }}"
                            variant="primary"
                            size="sm"
                            icon="check-circle"
                        >
                            Complete Collaboration
                        </flux:button>
                        <flux:button
                            wire:click="openAddDeliverableModal"
                            variant="ghost"
                            size="sm"
                            icon="plus"
                        >
                            Add Deliverables
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>

        <div class="p-6">
            @if($this->deliverables->isEmpty())
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <flux:icon name="document-text" class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                    </div>
                    <flux:heading size="sm" class="mb-2 text-gray-900 dark:text-white">No deliverables yet</flux:heading>
                    <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                        Deliverables will appear here once they are set up.
                    </flux:text>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($this->deliverables as $deliverable)
                        <div
                            wire:key="deliverable-{{ $deliverable->id }}"
                            class="group relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all duration-200"
                        >
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3 flex-1">
                                        <!-- Status Icon -->
                                        <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center bg-{{ $deliverable->status->color() }}-100 dark:bg-{{ $deliverable->status->color() }}-900/30">
                                            <flux:icon :name="$deliverable->status->icon()" class="w-5 h-5 text-{{ $deliverable->status->color() }}-600 dark:text-{{ $deliverable->status->color() }}-400" />
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <flux:text class="font-semibold text-gray-900 dark:text-white">
                                                    {{ $deliverable->deliverable_type->label() }}
                                                </flux:text>
                                                <flux:badge size="sm" :color="$deliverable->status->color()">
                                                    {{ $deliverable->status->label() }}
                                                </flux:badge>
                                            </div>

                                            @if($deliverable->notes)
                                                <flux:text size="sm" class="text-gray-600 dark:text-gray-400 mb-2">
                                                    {{ Str::limit($deliverable->notes, 100) }}
                                                </flux:text>
                                            @endif

                                            @if($deliverable->post_url)
                                                <div class="flex items-center gap-2 mb-2">
                                                    <flux:icon name="link" class="w-4 h-4 text-gray-400" />
                                                    <a
                                                        href="{{ $deliverable->post_url }}"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        class="text-sm text-blue-600 dark:text-blue-400 hover:underline truncate max-w-xs"
                                                    >
                                                        {{ Str::limit($deliverable->post_url, 50) }}
                                                    </a>
                                                </div>
                                            @endif

                                            @if($deliverable->revision_feedback && $deliverable->status === \App\Enums\CollaborationDeliverableStatus::REVISION_REQUESTED)
                                                <div class="mt-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800/50">
                                                    <div class="flex items-start gap-2">
                                                        <flux:icon name="exclamation-circle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
                                                        <div>
                                                            <flux:text size="sm" class="font-medium text-red-700 dark:text-red-300">Revision Requested</flux:text>
                                                            <flux:text size="sm" class="text-red-600 dark:text-red-400 mt-1">
                                                                {{ $deliverable->revision_feedback }}
                                                            </flux:text>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Attached Files -->
                                            @if($deliverable->files->isNotEmpty())
                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @foreach($deliverable->files as $file)
                                                        <a
                                                            href="{{ $file->url }}"
                                                            target="_blank"
                                                            rel="noopener noreferrer"
                                                            class="inline-flex items-center gap-1.5 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                                        >
                                                            <flux:icon name="photo" class="w-4 h-4" />
                                                            {{ Str::limit($file->file_name, 20) }}
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <!-- Timestamps -->
                                            <div class="mt-3 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                                @if($deliverable->submitted_at)
                                                    <span class="flex items-center gap-1">
                                                        <flux:icon name="paper-airplane" class="w-3.5 h-3.5" />
                                                        Submitted {{ $deliverable->submitted_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                                @if($deliverable->approved_at)
                                                    <span class="flex items-center gap-1">
                                                        <flux:icon name="check-circle" class="w-3.5 h-3.5 text-green-500" />
                                                        Approved {{ $deliverable->approved_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2">
                                        @if($this->isInfluencer && $deliverable->canSubmit())
                                            <flux:button
                                                wire:click="openSubmissionModal({{ $deliverable->id }})"
                                                variant="primary"
                                                size="sm"
                                                icon="paper-airplane"
                                            >
                                                {{ $deliverable->status === \App\Enums\CollaborationDeliverableStatus::REVISION_REQUESTED ? 'Resubmit' : 'Submit' }}
                                            </flux:button>
                                        @endif

                                        @if($this->isBusiness && $deliverable->canApprove())
                                            <flux:button
                                                wire:click="openApprovalModal({{ $deliverable->id }})"
                                                variant="primary"
                                                size="sm"
                                                icon="eye"
                                            >
                                                Review
                                            </flux:button>
                                            <flux:button
                                                wire:click="openRevisionModal({{ $deliverable->id }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="arrow-path"
                                            >
                                                Request Revision
                                            </flux:button>
                                        @endif

                                        @if($this->isBusiness && $deliverable->status === \App\Enums\CollaborationDeliverableStatus::NOT_STARTED)
                                            <flux:button
                                                wire:click="openDeleteModal({{ $deliverable->id }})"
                                                variant="ghost"
                                                size="sm"
                                                icon="trash"
                                                class="text-red-600 hover:text-red-700"
                                            />
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </flux:card>

    <!-- Approval Review Modal -->
    <flux:modal wire:model="showApprovalModal" class="max-w-2xl">
        <div class="p-6">
            @if($this->selectedDeliverable)
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-2">
                        <flux:heading size="xl">Review Submission</flux:heading>
                        <flux:badge size="sm" :color="$this->selectedDeliverable->status->color()">
                            {{ $this->selectedDeliverable->status->label() }}
                        </flux:badge>
                    </div>
                    <flux:text class="text-gray-600 dark:text-gray-400">
                        {{ $this->selectedDeliverable->deliverable_type->label() }}
                    </flux:text>
                </div>

                <!-- Post URL -->
                @if($this->selectedDeliverable->post_url)
                    <div class="mb-6">
                        <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400 mb-2">Post URL</flux:text>
                        <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <flux:icon name="link" class="w-4 h-4 text-gray-400" />
                            <a
                                href="{{ $this->selectedDeliverable->post_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-blue-600 dark:text-blue-400 hover:underline break-all"
                            >
                                {{ $this->selectedDeliverable->post_url }}
                            </a>
                            <flux:button
                                href="{{ $this->selectedDeliverable->post_url }}"
                                target="_blank"
                                variant="ghost"
                                size="sm"
                                icon="arrow-top-right-on-square"
                            />
                        </div>
                    </div>
                @endif

                <!-- Notes -->
                @if($this->selectedDeliverable->notes)
                    <div class="mb-6">
                        <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400 mb-2">Influencer Notes</flux:text>
                        <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <flux:text class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                                {{ $this->selectedDeliverable->notes }}
                            </flux:text>
                        </div>
                    </div>
                @endif

                <!-- Attached Files -->
                @if($this->selectedDeliverable->files->isNotEmpty())
                    <div class="mb-6">
                        <flux:text size="sm" class="font-medium text-gray-500 dark:text-gray-400 mb-2">Screenshots & Attachments</flux:text>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($this->selectedDeliverable->files as $file)
                                <a
                                    href="{{ $file->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group relative block overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 hover:border-blue-500 transition-colors"
                                >
                                    @if(Str::startsWith($file->file_type, 'image/'))
                                        <img
                                            src="{{ $file->url }}"
                                            alt="{{ $file->file_name }}"
                                            class="w-full h-40 object-cover"
                                        />
                                    @else
                                        <div class="w-full h-40 bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                            <flux:icon name="document" class="w-12 h-12 text-gray-400" />
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                        <flux:icon name="arrow-top-right-on-square" class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                                    </div>
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-2">
                                        <flux:text size="sm" class="text-white truncate">{{ $file->file_name }}</flux:text>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Submission Info -->
                <div class="mb-6 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800/50">
                    <div class="flex items-center gap-2">
                        <flux:icon name="clock" class="w-4 h-4 text-blue-500" />
                        <flux:text size="sm" class="text-blue-700 dark:text-blue-300">
                            Submitted {{ $this->selectedDeliverable->submitted_at?->diffForHumans() ?? 'N/A' }}
                        </flux:text>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <flux:button type="button" wire:click="closeApprovalModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button
                        wire:click="openRevisionModal({{ $this->selectedDeliverable->id }})"
                        variant="ghost"
                        icon="arrow-path"
                    >
                        Request Revision
                    </flux:button>
                    <flux:button
                        wire:click="approveDeliverable"
                        variant="primary"
                        icon="check"
                    >
                        Approve Deliverable
                    </flux:button>
                </div>
            @endif
        </div>
    </flux:modal>

    <!-- Revision Request Modal -->
    <flux:modal wire:model="showRevisionModal" class="max-w-lg">
        <div class="p-6">
            <flux:heading size="xl" class="mb-4">Request Revision</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-400 mb-4">
                Please provide feedback on what changes you'd like the influencer to make.
            </flux:text>

            <form wire:submit="requestRevision">
                <flux:textarea
                    wire:model="revisionFeedback"
                    label="Feedback"
                    placeholder="Describe what changes are needed..."
                    rows="4"
                    required
                />
                @error('revisionFeedback')
                    <flux:text size="sm" class="text-red-600 dark:text-red-400 mt-1">{{ $message }}</flux:text>
                @enderror

                <div class="flex justify-end gap-3 mt-6">
                    <flux:button type="button" wire:click="closeRevisionModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        Send Revision Request
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Add Deliverables Modal -->
    <flux:modal wire:model="showAddDeliverableModal" class="max-w-lg">
        <div class="p-6">
            <flux:heading size="xl" class="mb-2">Add Deliverables</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
                Specify the type and quantity of deliverables you need from the influencer.
            </flux:text>

            <form wire:submit="addDeliverables">
                <div class="space-y-4">
                    <flux:select
                        wire:model="newDeliverableType"
                        label="Deliverable Type"
                        placeholder="Select a deliverable type..."
                    >
                        @foreach($this->deliverableTypes as $option)
                            <flux:select.option value="{{ $option['value'] }}">
                                {{ $option['label'] }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('newDeliverableType')
                        <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror

                    <flux:input
                        wire:model="newDeliverableQuantity"
                        type="number"
                        label="Quantity"
                        min="1"
                        max="20"
                        placeholder="How many?"
                    />
                    @error('newDeliverableQuantity')
                        <flux:text size="sm" class="text-red-600 dark:text-red-400">{{ $message }}</flux:text>
                    @enderror

                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800/50">
                        <flux:text size="sm" class="text-blue-700 dark:text-blue-300">
                            <flux:icon name="information-circle" class="w-4 h-4 inline mr-1" />
                            This will create {{ $newDeliverableQuantity }} separate deliverable(s) that the influencer must complete.
                        </flux:text>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <flux:button type="button" wire:click="closeAddDeliverableModal" variant="ghost">
                        Cancel
                    </flux:button>
                    <flux:button type="submit" variant="primary" icon="plus">
                        Add Deliverables
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Delete Deliverable Confirmation Modal -->
    <flux:modal wire:model="showDeleteModal" class="max-w-md">
        <div class="p-6">
            @if($this->deliverableToDelete)
                <div class="text-center">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <flux:icon name="trash" class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>

                    <flux:heading size="xl" class="mb-2">Remove Deliverable</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
                        Are you sure you want to remove this <strong>{{ $this->deliverableToDelete->deliverable_type->label() }}</strong> deliverable? This action cannot be undone.
                    </flux:text>

                    <div class="flex justify-center gap-3">
                        <flux:button wire:click="closeDeleteModal" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button wire:click="confirmDeleteDeliverable" variant="danger" icon="trash">
                            Remove Deliverable
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
