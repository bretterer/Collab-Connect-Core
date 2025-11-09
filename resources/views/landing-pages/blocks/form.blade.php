@php
    $formId = $data['form_id'] ?? null;
    $form = $formId ? \App\Models\Form::find($formId) : null;
@endphp

@if($form && $form->status === 'published')
    <div class="w-full max-w-2xl mx-auto py-8 px-4">
        @if(!empty($form->title))
            <h2 class="text-2xl font-bold mb-2" style="color: inherit;">
                {{ $form->title }}
            </h2>
        @endif

        @if(!empty($form->description))
            <p class="mb-6" style="color: inherit; opacity: 0.8;">
                {{ $form->description }}
            </p>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            @livewire('public-form-display', ['formId' => $form->id], key('form-' . $form->id))
        </div>
    </div>
@elseif($formId)
    <div class="w-full max-w-2xl mx-auto py-8 px-4">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                Form not found or not published.
            </p>
        </div>
    </div>
@else
    <div class="w-full max-w-2xl mx-auto py-8 px-4">
        <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                No form selected. Please configure this block in the editor.
            </p>
        </div>
    </div>
@endif
