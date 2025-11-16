<?php

namespace App\Livewire\Admin\LandingPages;

use App\Models\LandingPage;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class LandingPageCreate extends Component
{
    public string $title = '';

    public string $slug = '';

    protected function rules()
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:landing_pages,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        ];
    }

    public function updatedTitle($value)
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function create()
    {
        $this->validate();

        $page = LandingPage::create([
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => 'draft',
            'blocks' => [],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(text: 'Landing page created as draft', variant: 'success');

        return redirect()->route('admin.marketing.landing-pages.edit', $page);
    }

    public function render()
    {
        return view('livewire.admin.landing-pages.landing-page-create');
    }
}
