<?php

namespace App\Livewire;

use App\Livewire\BaseComponent;
use App\Services\SearchService;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Search extends BaseComponent
{
    use WithPagination;

    public $search = '';

    public $selectedNiches = [];

    public $selectedPlatforms = [];

    public $minFollowers = '';

    public $maxFollowers = '';

    public $location = '';

    public $sortBy = 'name';

    public $searchRadius = 50; // Default search radius in miles

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedNiches' => ['except' => []],
        'selectedPlatforms' => ['except' => []],
        'minFollowers' => ['except' => ''],
        'maxFollowers' => ['except' => ''],
        'location' => ['except' => ''],
        'sortBy' => ['except' => 'name'],
        'searchRadius' => ['except' => 50],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedNiches()
    {
        $this->resetPage();
    }

    public function updatingSelectedPlatforms()
    {
        $this->resetPage();
    }

    public function updatingMinFollowers()
    {
        $this->resetPage();
    }

    public function updatingMaxFollowers()
    {
        $this->resetPage();
    }

    public function updatingLocation()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function updatingSearchRadius()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedNiches = [];
        $this->selectedPlatforms = [];
        $this->minFollowers = '';
        $this->maxFollowers = '';
        $this->location = '';
        $this->sortBy = 'name';
        $this->searchRadius = 50;
        $this->resetPage();
    }

    public function render()
    {
        $currentUser = $this->getAuthenticatedUser();

        $criteria = [
            'search' => $this->search,
            'selectedNiches' => $this->selectedNiches,
            'selectedPlatforms' => $this->selectedPlatforms,
            'minFollowers' => $this->minFollowers,
            'maxFollowers' => $this->maxFollowers,
            'location' => $this->location,
            'sortBy' => $this->sortBy,
            'searchRadius' => $this->searchRadius,
        ];

        // Auto-set distance sorting for proximity searches
        if ($this->location && preg_match('/^\d{5}$/', $this->location) && $this->sortBy === 'name') {
            $this->sortBy = 'distance';
            $criteria['sortBy'] = 'distance';
        }

        $results = SearchService::searchUsers($criteria, $currentUser, 12);
        $metadata = SearchService::getSearchMetadata($criteria, $currentUser);

        return view('livewire.search', array_merge([
            'results' => $results,
        ], $metadata));
    }
}
