<x-landing-page-block.wrapper :data="$data">
    <div class="max-w-3xl mx-auto">
        @if($data['form_id'] ?? null)
            @livewire('landing-pages.form-block-renderer', ['formId' => $data['form_id'], 'blockData' => $data], key('form-block-'.$data['form_id']))
        @else
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center">
                <p class="text-gray-600 dark:text-gray-400">Please select a form to display.</p>
            </div>
        @endif
    </div>
</x-landing-page-block.wrapper>
