@php
    $publishedForms = \App\Models\Form::where('status', 'published')
        ->orderBy('title')
        ->get();
@endphp

<div class="space-y-4">
    <flux:field>
        <flux:label>Select Form</flux:label>
        <flux:select wire:model="blockData.form_id">
            <option value="">Choose a form...</option>
            @foreach($publishedForms as $form)
                <option value="{{ $form->id }}">
                    {{ $form->title }}
                    @if($form->internal_title && $form->internal_title !== $form->title)
                        ({{ $form->internal_title }})
                    @endif
                </option>
            @endforeach
        </flux:select>
        <flux:description>
            Select a published form to display in this block.
        </flux:description>
    </flux:field>

    @if(empty($publishedForms->count()))
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                <strong>No published forms available.</strong><br>
                Create and publish a form in the
                <a href="{{ route('admin.marketing.forms.index') }}" class="underline" wire:navigate>Forms section</a>
                to use it here.
            </p>
        </div>
    @endif

    @if(!empty($blockData['form_id']))
        @php
            $selectedForm = \App\Models\Form::find($blockData['form_id']);
        @endphp

        @if($selectedForm)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Selected Form Preview</h4>
                <p class="text-sm text-blue-700 dark:text-blue-300 mb-2">
                    <strong>{{ $selectedForm->title }}</strong>
                </p>
                @if($selectedForm->description)
                    <p class="text-sm text-blue-600 dark:text-blue-400 mb-2">
                        {{ $selectedForm->description }}
                    </p>
                @endif
                <p class="text-xs text-blue-600 dark:text-blue-400">
                    {{ count($selectedForm->fields) }} field(s)
                </p>
            </div>
        @else
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                <p class="text-red-800 dark:text-red-200 text-sm">
                    The selected form could not be found. It may have been deleted.
                </p>
            </div>
        @endif
    @endif
</div>
