<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <flux:heading size="xl">Market Waitlist Management</flux:heading>
        <p class="text-gray-600 dark:text-gray-400">View and approve users waiting for market access</p>
    </div>

    {{-- Demand Heatmap --}}
    <div class="mb-8 bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <flux:heading size="lg">Waitlist Demand Map</flux:heading>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Visual representation of where users are waiting for market access. Larger circles indicate more demand.
            </p>
        </div>

        <div class="p-6">
            <div id="waitlist-map" class="w-full h-[600px] rounded-lg border-2 border-gray-300 dark:border-gray-600 shadow-lg overflow-hidden" wire:ignore></div>

            @if($mapData->isNotEmpty())
                {{-- Legend --}}
                <div class="mt-4 flex items-center justify-center gap-6 text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 rounded-full bg-green-500"></div>
                        <span>1-50 users</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-yellow-400"></div>
                        <span>51-200 users</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-red-600"></div>
                        <span>200+ users</span>
                    </div>
                </div>
            @else
                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 text-center">
                    No waitlist locations to display. Users with valid coordinates will appear here.
                </p>
            @endif
        </div>
    </div>

    @if(!empty($selectedPostalCodes))
        <div class="mb-6 flex items-center gap-4">
            <flux:badge variant="info">{{ count($selectedPostalCodes) }} zipcode(s) selected</flux:badge>
            <flux:button wire:click="approveSelected" variant="primary">
                Approve Selected
            </flux:button>
            <flux:button wire:click="$set('selectedPostalCodes', [])" variant="ghost">
                Clear Selection
            </flux:button>
        </div>
    @endif

    @if($waitlistData->isEmpty())
        <flux:callout variant="success" class="text-gray-900 dark:text-gray-100">
            No users are currently on the waitlist!
        </flux:callout>
    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            <input type="checkbox" class="rounded" wire:click="$set('selectedPostalCodes', {{ $waitlistData->pluck('postal_code')->toJson() }})" />
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zipcode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">City, State</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Users Waiting</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($waitlistData as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:checkbox
                                    wire:click="togglePostalCodeSelection('{{ $item->postal_code }}')"
                                    :checked="in_array($item->postal_code, $selectedPostalCodes)"
                                />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-medium text-gray-900 dark:text-gray-300">{{ $item->postal_code }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->postal_code_details->place_name ?? 'Unknown' }},
                                {{ $item->postal_code_details->admin_name1 ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge variant="info">{{ $item->user_count }} {{ Str::plural('user', $item->user_count) }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <flux:button
                                    wire:click="openApprovalModal('{{ $item->postal_code }}', {{ $item->user_count }})"
                                    size="sm"
                                    variant="primary">
                                    Approve All
                                </flux:button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-4">
                {{ $waitlistData->links() }}
            </div>
        </div>
    @endif

    <div class="mt-6">
        <flux:callout variant="info" class="text-gray-900 dark:text-gray-100">
            <strong>Note:</strong> When you approve users for a zipcode, they will immediately gain access to the platform on their next login.
            Make sure you've added the zipcode to an active market before approving users.
        </flux:callout>
    </div>

    {{-- Approval Modal --}}
    <flux:modal name="approval-modal" wire:model="showApprovalModal" class="max-w-6xl">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Approve Waitlist & Create Market</flux:heading>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Approving {{ $approvalUserCount }} {{ Str::plural('user', $approvalUserCount) }} from zipcode {{ $approvalPostalCode }}
                    @if($approvalPostalCodeDetails)
                        ({{ $approvalPostalCodeDetails->place_name }}, {{ $approvalPostalCodeDetails->admin_name1 }})
                    @endif
                </p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                    <p class="text-sm font-semibold text-red-900 dark:text-red-100 mb-2">Please fix the following errors:</p>
                    <ul class="list-disc list-inside text-sm text-red-800 dark:text-red-200">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <flux:separator />

            {{-- Market Selection --}}
            <div class="space-y-4">
                <div>
                    <label class="flex items-center gap-2">
                        <input type="radio" wire:model.live="createNewMarket" value="1" class="rounded-full" />
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Create New Market</span>
                    </label>
                </div>

                @if($createNewMarket)
                    <div class="ml-6">
                        <flux:input
                            wire:model="newMarketName"
                            label="Market Name"
                            placeholder="e.g., Greater Los Angeles"
                            required
                        />
                    </div>
                @endif

                <div>
                    <label class="flex items-center gap-2">
                        <input type="radio" wire:model.live="createNewMarket" value="0" class="rounded-full" />
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Add to Existing Market</span>
                    </label>
                </div>

                @if(!$createNewMarket)
                    <div class="ml-6">
                        <flux:select wire:model="existingMarketId" label="Select Market" placeholder="Choose a market...">
                            @foreach($markets as $market)
                                <option value="{{ $market->id }}">
                                    {{ $market->name }}@if(!$market->is_active) (Inactive)@endif
                                </option>
                            @endforeach
                        </flux:select>
                    </div>
                @endif
            </div>

            <flux:separator />

            {{-- Map Drawing --}}
            <div class="space-y-4">
                <div>
                    <flux:heading size="md">Define Market Area</flux:heading>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Draw a polygon or circle around {{ $approvalPostalCode }} to include additional zipcodes in this market
                    </p>
                </div>

                <div id="approval-map" class="w-full h-[500px] rounded-lg border-2 border-gray-300 dark:border-gray-600 shadow-lg overflow-hidden" wire:ignore></div>

                @if(count($previewZipcodes) > 0)
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
                        <p class="text-sm font-semibold text-green-900 dark:text-green-100 mb-2">
                            Found {{ count($previewZipcodes) + 1 }} total zipcode(s) (including {{ $approvalPostalCode }})
                        </p>
                        <div class="max-h-32 overflow-y-auto text-xs text-green-800 dark:text-green-200">
                            @foreach(array_slice($previewZipcodes, 0, 10) as $zipcode)
                                <div>{{ $zipcode['postal_code'] }} - {{ $zipcode['place_name'] }}, {{ $zipcode['admin_name1'] }}</div>
                            @endforeach
                            @if(count($previewZipcodes) > 10)
                                <div class="text-green-600 dark:text-green-400">... and {{ count($previewZipcodes) - 10 }} more</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <flux:separator />

            {{-- Notification Option --}}
            <div>
                <flux:checkbox wire:model="sendNotification" label="Send email notification to approved users" />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 ml-6">
                    Users will receive an email letting them know their market is now open
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 justify-end">
                <flux:button type="button" variant="ghost" wire:click="$set('showApprovalModal', false)">
                    Cancel
                </flux:button>
                <flux:button type="button" variant="primary" wire:click="processApproval" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="processApproval">Approve & Create Market</span>
                    <span wire:loading wire:target="processApproval">Processing...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>

@push('scripts')
<link href='https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.css' rel='stylesheet' />
<script src='https://api.mapbox.com/mapbox-gl-js/v3.0.1/mapbox-gl.js'></script>
<script src='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.js'></script>
<link rel='stylesheet' href='https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-draw/v1.4.3/mapbox-gl-draw.css' type='text/css' />

<script>
    let waitlistMapInitialized = false;
    let waitlistMap = null;

    function initWaitlistMap() {
        if (waitlistMapInitialized || waitlistMap !== null) {
            return;
        }

        const mapContainer = document.getElementById('waitlist-map');
        if (!mapContainer) return;

        // Check if Mapbox is loaded
        if (typeof mapboxgl === 'undefined') {
            setTimeout(initWaitlistMap, 100);
            return;
        }

        // Check if container is visible
        const rect = mapContainer.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            setTimeout(initWaitlistMap, 100);
            return;
        }

        waitlistMapInitialized = true;

        try {
            mapboxgl.accessToken = '{{ config("services.mapbox.api_key") }}';

            // Prepare the data
            const waitlistData = @json($mapData);

            // Create the map centered on USA
            waitlistMap = new mapboxgl.Map({
                container: 'waitlist-map',
                style: 'mapbox://styles/mapbox/light-v11',
                center: [-98.5795, 39.8283], // Center of USA
                zoom: 3.5 // Show full continental US
            });

            waitlistMap.addControl(new mapboxgl.NavigationControl());

            // Only add data layers if we have data
            if (waitlistData && waitlistData.length > 0) {
                // Create GeoJSON features for all waitlist locations
                const features = waitlistData.map(item => ({
                    type: 'Feature',
                    geometry: {
                        type: 'Point',
                        coordinates: [parseFloat(item.longitude), parseFloat(item.latitude)]
                    },
                    properties: {
                        postal_code: item.postal_code,
                        user_count: item.user_count,
                        place_name: item.place_name,
                        admin_name1: item.admin_name1
                    }
                }));

                waitlistMap.on('load', function() {
                    // Add the data source
                    waitlistMap.addSource('waitlist', {
                        type: 'geojson',
                        data: {
                            type: 'FeatureCollection',
                            features: features
                        }
                    });

                    // Add circle layer with dynamic sizing and coloring
                    waitlistMap.addLayer({
                        id: 'waitlist-circles',
                        type: 'circle',
                        source: 'waitlist',
                        paint: {
                            'circle-radius': [
                                'interpolate', ['linear'], ['get', 'user_count'],
                                1, 12,   // 1 user = 12px radius
                                50, 20,  // 50 users = 20px radius
                                100, 30, // 100 users = 30px radius
                                200, 40, // 200 users = 40px radius
                                500, 50  // 500+ users = 50px radius
                            ],
                            'circle-color': [
                                'step', ['get', 'user_count'],
                                '#10B981', // green for 1-50
                                51, '#FBBF24', // yellow for 51-200
                                201, '#DC2626' // red for 200+
                            ],
                            'circle-opacity': 0.8,
                            'circle-stroke-width': 2,
                            'circle-stroke-color': '#ffffff'
                        }
                    });

                    // Add labels showing user count
                    waitlistMap.addLayer({
                        id: 'waitlist-labels',
                        type: 'symbol',
                        source: 'waitlist',
                        layout: {
                            'text-field': ['get', 'user_count'],
                            'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
                            'text-size': 12
                        },
                        paint: {
                            'text-color': '#ffffff'
                        }
                    });

                    // Create popups on hover
                    const popup = new mapboxgl.Popup({
                        closeButton: false,
                        closeOnClick: false
                    });

                    waitlistMap.on('mouseenter', 'waitlist-circles', function(e) {
                        waitlistMap.getCanvas().style.cursor = 'pointer';

                    const coordinates = e.features[0].geometry.coordinates.slice();
                    const { postal_code, user_count, place_name, admin_name1 } = e.features[0].properties;

                        const userText = user_count === 1 ? 'user' : 'users';

                        popup.setLngLat(coordinates)
                            .setHTML(`
                                <div class="text-sm">
                                    <div class="font-semibold text-gray-900">${postal_code}</div>
                                    <div class="text-gray-600">${place_name}, ${admin_name1}</div>
                                    <div class="mt-1 text-blue-600 font-medium">${user_count} ${userText} waiting</div>
                                </div>
                            `)
                            .addTo(waitlistMap);
                    });

                    waitlistMap.on('mouseleave', 'waitlist-circles', function() {
                        waitlistMap.getCanvas().style.cursor = '';
                        popup.remove();
                    });

                    // Click to select zipcode
                    waitlistMap.on('click', 'waitlist-circles', function(e) {
                        const postalCode = e.features[0].properties.postal_code;
                        @this.call('togglePostalCodeSelection', postalCode);
                    });
                });
            }
        } catch (error) {
            console.error('Waitlist map initialization error:', error);
            waitlistMapInitialized = false;
        }
    }

    // Initialize on DOMContentLoaded and Livewire navigation
    document.addEventListener('DOMContentLoaded', initWaitlistMap);
    document.addEventListener('livewire:navigated', function() {
        waitlistMapInitialized = false;
        waitlistMap = null;
        setTimeout(initWaitlistMap, 300);
    });

    // Approval Modal Map
    let approvalMap = null;
    let approvalDraw = null;

    document.addEventListener('livewire:init', function() {
        Livewire.on('approvalModalOpened', (event) => {
            const coordinates = event[0] || { lat: 39.8283, lng: -98.5795 };
            setTimeout(() => initApprovalMap(coordinates), 300);
        });
    });

    function initApprovalMap(coordinates) {
        const mapContainer = document.getElementById('approval-map');
        if (!mapContainer) return;

        // Check if Mapbox is loaded
        if (typeof mapboxgl === 'undefined' || typeof MapboxDraw === 'undefined') {
            setTimeout(() => initApprovalMap(coordinates), 100);
            return;
        }

        // Check if container is visible
        const rect = mapContainer.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            setTimeout(() => initApprovalMap(coordinates), 100);
            return;
        }

        // Clean up existing map
        if (approvalMap) {
            approvalMap.remove();
            approvalMap = null;
            approvalDraw = null;
        }

        try {
            mapboxgl.accessToken = '{{ config("services.mapbox.api_key") }}';

            const centerLat = coordinates.lat;
            const centerLng = coordinates.lng;

            approvalMap = new mapboxgl.Map({
                container: 'approval-map',
                style: 'mapbox://styles/mapbox/streets-v12',
                center: [centerLng, centerLat],
                zoom: 10
            });

            approvalDraw = new MapboxDraw({
                displayControlsDefault: false,
                controls: {
                    polygon: true,
                    trash: true
                },
                defaultMode: 'draw_polygon'
            });

            approvalMap.addControl(approvalDraw);
            approvalMap.addControl(new mapboxgl.NavigationControl());

            // Add marker for the approval zipcode
            new mapboxgl.Marker({ color: '#DC2626' })
                .setLngLat([centerLng, centerLat])
                .addTo(approvalMap);

            approvalMap.on('load', function() {
                approvalMap.resize();
            });

            // Handle drawing complete
            approvalMap.on('draw.create', updateApprovalArea);
            approvalMap.on('draw.delete', clearApprovalArea);
            approvalMap.on('draw.update', updateApprovalArea);

            function updateApprovalArea(e) {
                const data = approvalDraw.getAll();
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

            function clearApprovalArea() {
                @this.set('previewZipcodes', []);
            }
        } catch (error) {
            console.error('Approval map initialization error:', error);
            if (approvalMap) {
                approvalMap.remove();
                approvalMap = null;
            }
        }
    }
    </script>
@endpush
