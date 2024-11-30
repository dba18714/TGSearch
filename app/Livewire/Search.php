<?php

namespace App\Livewire;

use App\Jobs\ProcessGoogleCustomSearchJob;
use App\Models\Chat;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\GoogleCustomSearchService;
use App\Services\GoogleSuggestService;
use App\Models\Message;
use App\Services\ImpressionStatsService;
use App\Services\UnifiedSearchService;
use Artesaos\SEOTools\Facades\SEOMeta;

class Search extends Component
{
    use WithPagination;

    protected GoogleCustomSearchService $googleSearchService;
    protected GoogleSuggestService $googleSuggestService;

    public $q = '';
    public $searchInput = '';
    public $type = '';
    public $sortField = '';
    public $sortDirection = '';
    public $suggestions = [];
    public $showSuggestions = false;
    protected $queryString = [
        'q' => ['except' => ''],
        'type' => ['except' => ''],
        'sortField' => ['except' => ''],
        'sortDirection' => ['except' => ''],
    ];

    public function boot(
        GoogleCustomSearchService $googleSearchService,
        GoogleSuggestService $googleSuggestService,
    ) {
        $this->googleSearchService = $googleSearchService;
        $this->googleSuggestService = $googleSuggestService;
    }

    public function mount()
    {
        $this->searchInput = $this->q;
    }

    public function updatedSearchInput()
    {
        if ($this->searchInput === '') {
            $this->suggestions = [];
            $this->showSuggestions = false;
            return;
        }

        $this->suggestions = $this->googleSuggestService->getSuggestions($this->searchInput);
        app('debugbar')->debug('$this->suggestions', $this->suggestions);
        app('debugbar')->debug('$this->searchInput', $this->searchInput);
        $this->showSuggestions = !empty($this->suggestions);
    }

    public function selectSuggestion($suggestion)
    {
        $this->searchInput = $suggestion;
        $this->showSuggestions = false;
        $this->doSearch();
    }

    public function resetFilters()
    {
        $this->reset([
            'q',
            'searchInput',
            'type',
            'sortField',
            'sortDirection'
        ]);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($field === '') {
            $this->sortField = '';
            $this->sortDirection = '';
            return;
        }

        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function doSearch()
    {
        // if (empty($this->searchInput)) {
        //     return;
        // }

        $this->q = $this->searchInput;
        ProcessGoogleCustomSearchJob::dispatch($this->q);
        $this->showSuggestions = false;
        $this->resetPage();
    }

    public function render()
    {
        $title = $this->q ? "搜索 - {$this->q}" : '首页';
        SEOMeta::setTitle($title);

        $result = app(UnifiedSearchService::class)->search(
            $this->q,
            $this->type ? ['type' => $this->type] : [],
            [
                'sort' => $this->sortField,
                'direction' => $this->sortDirection,
                'per_page' => 12,
            ],
        );

        return view('livewire.search', [
            'unified_searches' => $result
        ]);
    }
}
