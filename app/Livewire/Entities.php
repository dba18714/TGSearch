<?php

namespace App\Livewire;

use App\Jobs\ProcessGoogleCustomSearchJob;
use App\Models\Entity;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\GoogleCustomSearchService;
use App\Services\GoogleSuggestService;
use App\Models\Message;
use App\Models\Search;
use Artesaos\SEOTools\Facades\SEOMeta;

class Entities extends Component
{
    use WithPagination;

    protected GoogleCustomSearchService $googleSearchService;
    protected $googleSuggestService;

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
        GoogleSuggestService $googleSuggestService
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
        // $this->resetPage();
    }

    public function render()
    {
        $title = $this->q ? "搜索 - {$this->q}" : '首页';
        SEOMeta::setTitle($title);

        $query = Entity::query();

        if (!empty($this->q)) {
            Search::recordSearch($this->q);

            // 搜索消息
            $messageEntityIds = Message::search($this->q)
                ->get(['id', 'entity_id', 'text'])
                ->groupBy('entity_id')
                ->map(function ($messages) {
                    return $messages->take(1);
                });

            // 搜索所有者
            $entities = Entity::search($this->q)->get();

            // 合并两种搜索结果的 entity_id
            $allEntityIds = $messageEntityIds->keys()->merge($entities->pluck('id'))->unique();

            $query->whereIn('id', $allEntityIds);
        }

        $entities = $query
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->sortField, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(12);

        // 如果有搜索词，添加匹配的消息到结果中
        if (!empty($this->q) && isset($messageEntityIds)) {
            foreach ($entities as $entity) {
                $entity->matched_messages = $messageEntityIds->get($entity->id);
            }
        }

        return view('livewire.entities', [
            'entities' => $entities
        ]);
    }
}
