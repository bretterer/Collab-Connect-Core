<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <flux:heading size="xl">Edit Market: {{ $market->name }}</flux:heading>
        <p class="text-gray-600 dark:text-gray-400">Manage market settings and zipcodes</p>
    </div>

    {{-- Market Settings --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <flux:heading size="lg" class="mb-4">Market Settings</flux:heading>

        <form wire:submit.prevent="updateMarket" class="space-y-4">
            <flux:input
                wire:model="name"
                label="Market Name"
                required
            />

            <flux:textarea
                wire:model="description"
                label="Description"
                rows="3"
            />

            <flux:checkbox wire:model="is_active" label="Market is Active" />

            <flux:button type="submit" variant="primary">Update Market</flux:button>
        </form>
    </div>

    {{-- Add Zipcodes Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <flux:heading size="lg" class="mb-4">Add Zipcodes</flux:heading>

        <flux:tabs>
            <flux:tab name="manual" label="Manual Entry">
                <div class="mt-4">
                    <form wire:submit.prevent="addManualZipcode" class="flex gap-2">
                        <flux:input
                            wire:model="manualZipcode"
                            placeholder="Enter zipcode"
                            class="flex-1"
                        />
                        <flux:button type="submit" variant="primary">Add</flux:button>
                    </form>
                    <p class="mt-2 text-sm text-gray-500">Enter a single zipcode to add it to this market</p>
                </div>
            </flux:tab>

            <flux:tab name="search" label="Search & Select">
                <div class="mt-4 space-y-4">
                    <flux:input
                        wire:model.live.debounce.300ms="searchQuery"
                        wire:change="searchZipcodes"
                        placeholder="Search by zipcode, city, or state..."
                        label="Search Zipcodes"
                    />

                    @if(!empty($searchResults))
                        <div class="border rounded-lg p-4 max-h-96 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach($searchResults as $result)
                                    <div class="flex items-center gap-2 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded">
                                        <flux:checkbox
                                            wire:click="toggleZipcodeSelection('{{ $result->postal_code }}')"
                                            :checked="in_array($result->postal_code, $selectedZipcodes)"
                                        />
                                        <div class="flex-1">
                                            <div class="font-medium">{{ $result->postal_code }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ $result->place_name }}, {{ $result->admin_name1 }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if(!empty($selectedZipcodes))
                            <flux:button wire:click="addSelectedZipcodes" variant="primary">
                                Add {{ count($selectedZipcodes) }} Selected Zipcode(s)
                            </flux:button>
                        @endif
                    @endif
                </div>
            </flux:tab>

            <flux:tab name="csv" label="CSV Upload">
                <div class="mt-4">
                    <form wire:submit.prevent="uploadCsv" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Upload CSV File</label>
                            <input type="file" wire:model="csvFile" accept=".csv,.txt" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            @error('csvFile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <p class="font-medium mb-1">CSV Format:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>First column should contain zipcodes</li>
                                <li>Header row will be skipped</li>
                                <li>Maximum file size: 10MB</li>
                            </ul>
                        </div>

                        <flux:button type="submit" variant="primary" :disabled="!$csvFile">Upload Zipcodes</flux:button>
                    </form>
                </div>
            </flux:tab>
        </flux:tabs>
    </div>

    {{-- Current Zipcodes --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <flux:heading size="lg" class="mb-4">Current Zipcodes ({{ $market->zipcodes_count }})</flux:heading>

        @if($zipcodes->isEmpty())
            <flux:callout variant="info" class="text-gray-900 dark:text-gray-100">
                No zipcodes have been added to this market yet.
            </flux:callout>
        @else
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zipcode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">City</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">State</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($zipcodes as $zipcode)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
                                    {{ $zipcode->postal_code }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $zipcode->postalCodeDetails->place_name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $zipcode->postalCodeDetails->admin_name1 ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <flux:button
                                        wire:click="removeZipcode({{ $zipcode->id }})"
                                        wire:confirm="Remove this zipcode from the market?"
                                        size="sm"
                                        variant="danger">
                                        Remove
                                    </flux:button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $zipcodes->links() }}
            </div>
        @endif
    </div>
</div>
