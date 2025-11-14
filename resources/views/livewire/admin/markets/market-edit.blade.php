<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ $market->name }}</flux:heading>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage market settings and zipcodes</p>
            </div>
            <a href="{{ route('admin.markets.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                ← Back to Markets
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Settings --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 sticky top-6">
                <flux:heading size="lg" class="mb-6">Market Settings</flux:heading>

                <form wire:submit.prevent="updateMarket" class="space-y-6">
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

                    <flux:button type="submit" variant="primary" class="w-full">Update Market</flux:button>
                </form>
            </div>
        </div>

        {{-- Right Column: Zipcode Management --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Add Zipcodes Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <flux:heading size="lg">Add Zipcodes</flux:heading>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Choose a method to add zipcodes to this market</p>
                </div>

                <flux:tab.group>
                    <flux:tabs wire:model="activeTab" class="px-6 pt-4">
                        <flux:tab name="manual">Manual Entry</flux:tab>
                        <flux:tab name="search">Search & Select</flux:tab>
                        <flux:tab name="csv">CSV Upload</flux:tab>
                        <flux:tab name="map" x-on:click="setTimeout(() => window.initMarketEditMap && window.initMarketEditMap(), 300)">Draw on Map</flux:tab>
                    </flux:tabs>

                    <flux:tab.panel name="manual">
                        <div class="p-6">
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Add Individual Zipcode</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Enter a single zipcode to add it to this market</p>
                            </div>
                            <form wire:submit.prevent="addManualZipcode" class="flex gap-3">
                                <flux:input
                                    wire:model="manualZipcode"
                                    placeholder="e.g., 90210"
                                    class="flex-1"
                                />
                                <flux:button type="submit" variant="primary">Add Zipcode</flux:button>
                            </form>
                        </div>
                    </flux:tab.panel>

                    <flux:tab.panel name="search">
                        <div class="p-6 space-y-4">
                            <div class="mb-4">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Search for Zipcodes</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Search by zipcode, city name, or state to find and select multiple zipcodes</p>
                            </div>

                            <flux:input
                                wire:model.live.debounce.300ms="searchQuery"
                                placeholder="Search by zipcode, city, or state..."
                                label="Search Zipcodes"
                            />

                            @if(!empty($searchResults))
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <div class="max-h-96 overflow-y-auto">
                                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($searchResults as $result)
                                                <div class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors cursor-pointer"
                                                     wire:click="toggleZipcodeSelection('{{ $result->postal_code }}')">
                                                    <flux:checkbox
                                                        :checked="in_array($result->postal_code, $selectedZipcodes)"
                                                    />
                                                    <div class="flex-1 min-w-0">
                                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $result->postal_code }}</div>
                                                        <div class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                                            {{ $result->place_name }}, {{ $result->admin_name1 }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                @if(!empty($selectedZipcodes))
                                    <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                        <span class="text-sm font-medium text-blue-900 dark:text-blue-100">
                                            {{ count($selectedZipcodes) }} zipcode(s) selected
                                        </span>
                                        <flux:button wire:click="addSelectedZipcodes" variant="primary">
                                            Add Selected
                                        </flux:button>
                                    </div>
                                @endif
                            @elseif($searchQuery && empty($searchResults))
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <p class="text-sm">No results found for "{{ $searchQuery }}"</p>
                                </div>
                            @endif
                        </div>
                    </flux:tab.panel>

                    <flux:tab.panel name="csv">
                        <div class="p-6">
                            <div class="mb-6">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">Bulk Upload via CSV</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Upload a CSV file to add multiple zipcodes at once</p>
                            </div>

                            <form wire:submit.prevent="uploadCsv" class="space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-900 dark:text-gray-100 mb-3">Choose CSV File</label>
                                    <input
                                        type="file"
                                        wire:model="csvFile"
                                        accept=".csv,.txt"
                                        class="block w-full text-sm text-gray-500 dark:text-gray-400
                                               file:mr-4 file:py-2.5 file:px-4
                                               file:rounded-lg file:border-0
                                               file:text-sm file:font-semibold
                                               file:bg-blue-50 dark:file:bg-blue-900/30
                                               file:text-blue-700 dark:file:text-blue-400
                                               hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50
                                               file:cursor-pointer cursor-pointer
                                               border border-gray-300 dark:border-gray-600 rounded-lg
                                               focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    />
                                    @error('csvFile') <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                    <p class="font-medium text-sm text-gray-900 dark:text-gray-100 mb-2">CSV Format Requirements:</p>
                                    <ul class="space-y-1.5 text-sm text-gray-600 dark:text-gray-400">
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 mt-0.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>First column should contain zipcodes</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 mt-0.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Header row will be automatically skipped</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <svg class="w-4 h-4 mt-0.5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>Maximum file size: 10MB</span>
                                        </li>
                                    </ul>
                                </div>

                                <flux:button type="submit" variant="primary" :disabled="!$csvFile" class="w-full">
                                    Upload Zipcodes
                                </flux:button>
                            </form>
                        </div>
                    </flux:tab.panel>

                    <flux:tab.panel name="map">
                        <div class="p-6 space-y-6">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">Interactive Map Drawing</h3>
                                <ul class="space-y-1.5 text-sm text-blue-800 dark:text-blue-200">
                                    <li class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Use the <strong>polygon tool</strong> to draw a custom area on the map</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>All zipcodes within your drawn area will be automatically detected</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Use the <strong>trash tool</strong> to clear your drawing and start over</span>
                                    </li>
                                </ul>
                            </div>

                            <div id="map" class="w-full h-[600px] rounded-lg border-2 border-gray-300 dark:border-gray-600 shadow-lg overflow-hidden" wire:ignore></div>

                            {{-- Loading Indicator --}}
                            @if($loadingZipcodes)
                                <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-800 rounded-lg p-6 shadow-sm">
                                    <div class="flex items-center justify-center gap-3">
                                        <svg class="animate-spin h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                                                Detecting zipcodes...
                                            </p>
                                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-0.5">
                                                Searching for zipcodes within and near your drawn area
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if(count($previewZipcodes) > 0)
                                <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800 rounded-lg p-5 shadow-sm">
                                    <div class="flex items-start justify-between mb-3">
                                        <div>
                                            <p class="text-sm font-semibold text-green-900 dark:text-green-100">
                                                Found {{ count($previewZipcodes) }} zipcode{{ count($previewZipcodes) !== 1 ? 's' : '' }} in the drawn area
                                            </p>
                                            <p class="text-xs text-green-700 dark:text-green-300 mt-0.5">
                                                Preview the first 10 results below
                                            </p>
                                        </div>
                                    </div>
                                    <div class="max-h-48 overflow-y-auto mb-4 bg-white dark:bg-gray-900/50 rounded-lg border border-green-200 dark:border-green-800">
                                        <div class="divide-y divide-green-100 dark:divide-green-900">
                                            @foreach(array_slice($previewZipcodes, 0, 10) as $zipcode)
                                                <div class="px-3 py-2 text-sm text-green-900 dark:text-green-100">
                                                    <span class="font-medium">{{ $zipcode['postal_code'] }}</span>
                                                    <span class="text-green-700 dark:text-green-300"> - {{ $zipcode['place_name'] }}, {{ $zipcode['admin_name1'] }}</span>
                                                </div>
                                            @endforeach
                                            @if(count($previewZipcodes) > 10)
                                                <div class="px-3 py-2 text-sm text-green-600 dark:text-green-400 font-medium">
                                                    ... and {{ count($previewZipcodes) - 10 }} more
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <flux:button wire:click="addBoundaryZipcodes" variant="primary" class="w-full">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add {{ count($previewZipcodes) }} Zipcode{{ count($previewZipcodes) !== 1 ? 's' : '' }} to Market
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    </flux:tab.panel>
                </flux:tab.group>
            </div>

@push('scripts')
<link href='https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css' rel='stylesheet' />
<script src='https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js'></script>
<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.js'></script>
<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.css' type='text/css' />
<script src='https://unpkg.com/@turf/turf@6/turf.min.js'></script>

<script>
    let map = null;
    let draw = null;
    let mapInitialized = false;

    // Initialize map when the map container is visible
    function initMap() {
        // Check if already initialized
        if (mapInitialized || map !== null) {
            if (map) {
                setTimeout(() => map.resize(), 100);
            }
            return;
        }

        const mapContainer = document.getElementById('map');
        if (!mapContainer) return;

        // Check if container is visible (has dimensions)
        const rect = mapContainer.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            setTimeout(initMap, 100);
            return;
        }

        // Check if Mapbox is loaded
        if (typeof mapboxgl === 'undefined') {
            setTimeout(initMap, 100);
            return;
        }

        if (typeof MapboxDraw === 'undefined') {
            setTimeout(initMap, 100);
            return;
        }

        mapInitialized = true;

        try {
            mapboxgl.accessToken = '{{ config("services.mapbox.api_key") }}';

            map = new mapboxgl.Map({
                container: 'map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [-98.5795, 39.8283], // Center of USA
                zoom: 3
            });

            draw = new MapboxDraw({
                displayControlsDefault: false,
                controls: {
                    polygon: true,
                    trash: true
                },
                defaultMode: 'draw_polygon'
            });

            map.addControl(draw);
            map.addControl(new mapboxgl.NavigationControl());

            // Ensure map is fully sized after load
            map.on('load', function() {
                setTimeout(() => map.resize(), 100);
            });

            // Handle drawing complete
            map.on('draw.create', updateArea);
            map.on('draw.delete', clearArea);
            map.on('draw.update', updateArea);

            function updateArea(e) {
                const data = draw.getAll();
                if (data.features.length > 0) {
                    const feature = data.features[0];

                    if (feature.geometry.type === 'Polygon') {
                        const boundary = {
                            type: 'polygon',
                            coordinates: feature.geometry.coordinates[0]
                        };

                        @this.call('findZipcodesInBoundary', JSON.stringify(boundary));
                    }
                }
            }

            function clearArea() {
                @this.set('previewZipcodes', []);
                @this.set('loadingZipcodes', false);
            }
        } catch (error) {
            console.error('Map initialization error:', error);
            mapInitialized = false;
        }
    }

    // Expose to window for Alpine click handler
    window.initMarketEditMap = initMap;

    // Listen for Livewire navigation events
    document.addEventListener('livewire:navigated', function() {
        mapInitialized = false;
        map = null;
        draw = null;
    });

    // Check if map tab is active on page load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            try {
                const currentTab = @this && @this.activeTab;
                if (currentTab === 'map') {
                    initMap();
                }
            } catch (error) {
                // Silently ignore if @this is not available yet
            }
        }, 500);
    });
</script>
@endpush

            {{-- Current Zipcodes --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <flux:heading size="lg">Current Zipcodes</flux:heading>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ $market->zipcodes_count }} zipcode{{ $market->zipcodes_count !== 1 ? 's' : '' }} in this market
                            </p>
                        </div>
                    </div>
                </div>

                @if($zipcodes->isEmpty())
                    <div class="p-8">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No zipcodes yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by adding zipcodes using one of the methods above.</p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Zipcode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">City</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">State</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($zipcodes as $zipcode)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $zipcode->postal_code }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $zipcode->postalCodeDetails->place_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $zipcode->postalCodeDetails->admin_name1 ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
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

                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $zipcodes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
