<!-- Step 2: Social Media Connections -->
<div>
    <h2 class="mb-6 text-xl font-semibold text-gray-900 dark:text-white">Social Media Connections</h2>

    <div class="space-y-6">
        @foreach ($socialMediaAccounts as $index => $account)
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div
                                 class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-pink-500">
                                <svg class="h-4 w-4 text-white"
                                     fill="none"
                                     stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                    Account {{ $index + 1 }}
                                </h4>
                                @if ($account['is_primary'])
                                    <span
                                          class="inline-flex items-center rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-800 dark:bg-purple-900/20 dark:text-purple-200">
                                        <svg class="mr-1 h-3 w-3"
                                             fill="currentColor"
                                             viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                  d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                  clip-rule="evenodd"></path>
                                        </svg>
                                        Primary Account
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if (!$account['is_primary'])
                                <flux:button class="text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300"
                                             type="button"
                                             variant="ghost"
                                             size="sm"
                                             wire:click="setPrimaryAccount({{ $index }})">
                                    Set as Primary
                                </flux:button>
                            @else
                                <flux:button class="text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                                             type="button"
                                             variant="ghost"
                                             size="sm"
                                             wire:click="removePrimaryAccount({{ $index }})">
                                    Remove Primary
                                </flux:button>
                            @endif
                            @if (count($socialMediaAccounts) > 1)
                                <button class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-red-500 dark:text-gray-500 dark:hover:bg-gray-700 dark:hover:text-red-400"
                                        type="button"
                                        wire:click="removeSocialMediaAccount({{ $index }})">
                                    <svg class="h-4 w-4"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Platform -->
                        <div>
                            <flux:select wire:model="socialMediaAccounts.{{ $index }}.platform"
                                         label="Platform"
                                         variant="listbox"
                                         placeholder="Select your platform"
                                         required>
                            @foreach ($platformOptions as $option)
                                <flux:select.option value="{{ $option->value }}">{{ $option->label() }}</flux:select.option>
                            @endforeach
                            </flux:select>
                        </div>

                        <!-- Username and Follower Count -->
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <!-- Username -->
                            <flux:field>
                                <flux:label class="text-sm font-medium text-gray-700 dark:text-gray-300">Username</flux:label>
                                <flux:input class="mt-1"
                                            type="text"
                                            wire:model="socialMediaAccounts.{{ $index }}.username"
                                            placeholder="username"
                                            required />
                                <flux:error name="socialMediaAccounts.{{ $index }}.username" />
                            </flux:field>

                            <!-- Follower Count -->
                            <flux:field>
                                <flux:label class="text-sm font-medium text-gray-700 dark:text-gray-300">Follower Count</flux:label>
                                <flux:input class="mt-1"
                                            type="number"
                                            wire:model="socialMediaAccounts.{{ $index }}.follower_count"
                                            min="0"
                                            placeholder="0"
                                            required />
                                <flux:error name="socialMediaAccounts.{{ $index }}.follower_count" />
                            </flux:field>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="flex justify-center">
            <flux:button class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
                         type="button"
                         variant="ghost"
                         wire:click="addSocialMediaAccount"
                         icon="plus">
                Add Social Media Account
            </flux:button>
        </div>
    </div>
</div>