@php
    use App\Models\Form;
    use App\Models\LandingPage;

    // Get all published forms
    $publishedForms = Form::where('status', 'published')
        ->orderBy('title')
        ->get()
        ->map(fn($form) => [
            'value' => $form->id,
            'label' => $form->title,
        ])
        ->toArray();

    // Get all published landing pages
    $publishedLandingPages = LandingPage::where('status', 'published')
        ->orderBy('title')
        ->get()
        ->map(fn($page) => [
            'value' => $page->id,
            'label' => $page->title,
        ])
        ->toArray();
@endphp

<x-landing-page-block.editor :tabs="$tabs" :property-prefix="$propertyPrefix">
    {{-- Content Tab Fields --}}
    <div class="space-y-6">
        {{-- Form Selection --}}
        <flux:field>
            <flux:label>Select Form</flux:label>
            <flux:description>Choose which form to display in this block</flux:description>
            @if(count($publishedForms) > 0)
                <flux:select wire:model="{{ $propertyPrefix }}.form_id">
                    <option value="">-- Select a Form --</option>
                    @foreach($publishedForms as $form)
                        <option value="{{ $form['value'] }}">{{ $form['label'] }}</option>
                    @endforeach
                </flux:select>
            @else
                <flux:callout variant="warning">
                    <div class="flex flex-col gap-2">
                        <p>No published forms available.</p>
                        <a href="{{ route('admin.marketing.forms.index') }}" class="text-sm underline">Create a form</a>
                    </div>
                </flux:callout>
            @endif
        </flux:field>

        {{-- Thank You Action --}}
        <flux:field>
            <flux:label>After Form Submission</flux:label>
            <flux:description>What happens after the form is successfully submitted</flux:description>
            <flux:select wire:model.live="{{ $propertyPrefix }}.thank_you_action">
                <option value="message">Show Success Message</option>
                <option value="landing_page">Go To Landing Page</option>
                <option value="url">Go To URL</option>
            </flux:select>
        </flux:field>

        {{-- Success Message (conditional - only for 'message' action) --}}
        @if(data_get($data, 'thank_you_action') === 'message')
            <flux:field>
                <flux:label>Success Message</flux:label>
                <flux:description>Message shown after successful form submission</flux:description>
                <flux:textarea wire:model="{{ $propertyPrefix }}.success_message" rows="3" placeholder="Thank you! Your submission has been received." />
            </flux:field>
        @endif

        {{-- Landing Page Selection (conditional - only for 'landing_page' action) --}}
        @if(data_get($data, 'thank_you_action') === 'landing_page')
            <flux:field>
                <flux:label>Select Landing Page</flux:label>
                <flux:description>Choose which landing page to redirect to after submission</flux:description>
                @if(count($publishedLandingPages) > 0)
                    <flux:select wire:model="{{ $propertyPrefix }}.thank_you_landing_page_id">
                        <option value="">-- Select a Landing Page --</option>
                        @foreach($publishedLandingPages as $page)
                            <option value="{{ $page['value'] }}">{{ $page['label'] }}</option>
                        @endforeach
                    </flux:select>
                @else
                    <flux:callout variant="warning">
                        <p>No published landing pages available.</p>
                    </flux:callout>
                @endif
            </flux:field>
        @endif

        {{-- URL Field (conditional - only for 'url' action) --}}
        @if(data_get($data, 'thank_you_action') === 'url')
            <flux:field>
                <flux:label>Thank You URL</flux:label>
                <flux:description>The URL to redirect to after form submission</flux:description>
                <flux:input wire:model="{{ $propertyPrefix }}.thank_you_url" type="url" placeholder="https://example.com/thank-you" />
            </flux:field>
        @endif

        {{-- Disclaimer Text --}}
        <flux:field>
            <flux:label>Disclaimer Text</flux:label>
            <flux:description>Optional disclaimer text shown below the form (e.g., privacy policy, terms)</flux:description>
            <flux:textarea wire:model="{{ $propertyPrefix }}.disclaimer_text" rows="2" placeholder="By submitting this form, you agree to our privacy policy..." />
        </flux:field>

        {{-- Disclaimer Text Color --}}
        @if(!empty(data_get($data, 'disclaimer_text')))
            <flux:field>
                <flux:label>Disclaimer Text Color</flux:label>
                <flux:description>The color of the disclaimer text</flux:description>
                <div class="flex items-center gap-3">
                    <input
                        type="color"
                        wire:model.blur="{{ $propertyPrefix }}.disclaimer_text_color"
                        class="h-10 w-20 rounded cursor-pointer"
                    />
                    <flux:input
                        wire:model="{{ $propertyPrefix }}.disclaimer_text_color"
                        placeholder="#6B7280"
                        class="flex-1"
                    />
                </div>
            </flux:field>
        @endif

        {{-- Fire Event Checkbox --}}
        <flux:field>
            <flux:label>
                <flux:checkbox wire:model="{{ $propertyPrefix }}.fire_event" />
                Fire Livewire Event on Submit
            </flux:label>
            <flux:description>Enable this to trigger the two-step optin modal or other components listening for 'form-submitted' event</flux:description>
        </flux:field>

        {{-- Button Text --}}
        <flux:field>
            <flux:label>Button Text</flux:label>
            <flux:description>The text displayed on the submit button</flux:description>
            <flux:input wire:model="{{ $propertyPrefix }}.button_text" placeholder="Submit" />
        </flux:field>

        {{-- Button Background Color --}}
        <flux:field>
            <flux:label>Button Background Color</flux:label>
            <flux:description>The background color of the submit button</flux:description>
            <div class="flex items-center gap-3">
                <input
                    type="color"
                    wire:model.blur="{{ $propertyPrefix }}.button_bg_color"
                    class="h-10 w-20 rounded cursor-pointer"
                />
                <flux:input
                    wire:model="{{ $propertyPrefix }}.button_bg_color"
                    placeholder="#DFAD42"
                    class="flex-1"
                />
            </div>
        </flux:field>

        {{-- Button Text Color --}}
        <flux:field>
            <flux:label>Button Text Color</flux:label>
            <flux:description>The text color of the submit button</flux:description>
            <div class="flex items-center gap-3">
                <input
                    type="color"
                    wire:model.blur="{{ $propertyPrefix }}.button_text_color"
                    class="h-10 w-20 rounded cursor-pointer"
                />
                <flux:input
                    wire:model="{{ $propertyPrefix }}.button_text_color"
                    placeholder="#000000"
                    class="flex-1"
                />
            </div>
        </flux:field>

        {{-- Button Width --}}
        <flux:field>
            <flux:label>Button Width</flux:label>
            <flux:description>How wide the submit button should be</flux:description>
            <div class="flex gap-4">
                <flux:radio.group wire:model="{{ $propertyPrefix }}.button_width">
                    <flux:radio value="full" label="Full Width" />
                    <flux:radio value="auto" label="Auto Width" />
                </flux:radio.group>
            </div>
        </flux:field>

        {{-- Button Size --}}
        <flux:field>
            <flux:label>Button Size</flux:label>
            <flux:description>The size of the submit button</flux:description>
            <div class="flex gap-4">
                <flux:radio.group wire:model="{{ $propertyPrefix }}.button_size">
                    <flux:radio value="small" label="Small" />
                    <flux:radio value="medium" label="Medium" />
                    <flux:radio value="large" label="Large" />
                </flux:radio.group>
            </div>
        </flux:field>

        {{-- Border Radius --}}
        <flux:field>
            <flux:label>Border Radius</flux:label>
            <flux:description>The roundness of the button corners (0-50px)</flux:description>
            <div class="flex gap-4 items-center">
                <input
                    type="range"
                    wire:model.live="{{ $propertyPrefix }}.border_radius"
                    min="0"
                    max="50"
                    class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                />
                <flux:input
                    wire:model="{{ $propertyPrefix }}.border_radius"
                    type="number"
                    min="0"
                    max="50"
                    class="w-20"
                />
            </div>
        </flux:field>
    </div>
</x-landing-page-block.editor>
