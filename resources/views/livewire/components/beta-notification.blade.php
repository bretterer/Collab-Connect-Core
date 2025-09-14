<div>
    @if($showModal)
        <flux:modal wire:model="showModal" position="center">
            <div class="p-8 text-center space-y-6">
                <div class="mx-auto w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center">
                    <flux:icon.information-circle class="w-8 h-8 text-amber-600 dark:text-amber-400" />
                </div>

                <div>
                    <flux:heading size="lg" class="mb-3">Welcome to CollabConnect Beta!</flux:heading>
                    <div class="text-gray-600 dark:text-gray-300 space-y-3 text-left max-w-md">
                        <p>
                            We're excited to have you on board! Please note that CollabConnect is currently in beta,
                            which means you may encounter some bugs or areas for improvement.
                        </p>
                        <p>
                            If you have suggestions, questions, or encounter any errors, we encourage you to use our
                            reporting widget to let us know. Your feedback helps us improve!
                        </p>
                        <p>
                            We're still building our userbase, so please be patient as we grow our community of
                            businesses and influencers. More opportunities will become available as we expand!
                        </p>
                    </div>
                </div>

                <div class="pt-4">
                    <flux:button wire:click="closeModal" variant="filled" class="w-full sm:w-auto">
                        Get Started
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
