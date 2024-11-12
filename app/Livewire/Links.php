<?php

namespace App\Livewire;

use App\Models\Link;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\GoogleCustomSearchService;
use App\Services\TelegramCrawlerService;
use App\Jobs\UpdateLinkInfoJob;

class Links extends Component
{
    use WithPagination;

    protected GoogleCustomSearchService $googleSearchService;

    public $search = '';
    public $type = '';
    public $sortField = '';
    public $sortDirection = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'sortField' => ['except' => ''],
        'sortDirection' => ['except' => ''],
    ];

    public function boot(
        GoogleCustomSearchService $googleSearchService
    ) {
        $this->googleSearchService = $googleSearchService;
    }

    public function resetFilters()
    {
        $this->reset(['search', 'type', 'sortField', 'sortDirection']);
        $this->resetPage();
    }

    public function updatedType()
    {
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
        if (empty($this->search)) {
            return;
        }

        $results = $this->googleSearchService->search($this->search);

        $this->processSearchResults($results);

        $this->resetPage();
    }

    protected function processSearchResults($results)
    {
        app('debugbar')->debug($results);
        foreach ($results as $item) {
            // if 以 https://web.t.me 开头的链接，跳过
            if (strpos($item['link'], 'https://web.t.me') === 0) {
                continue;
            }
            $link = Link::firstOrCreate(
                [
                    'url' => $item['link']
                ],
                [
                    'name' => $item['title'],
                    'type' => 'message',
                ]
            );

            app('debugbar')->debug($link);

            $link->dispatchUpdateJob();
        }
    }

    public function render()
    {
        $query = Link::query();

        if (!empty($this->search)) {
            $query = Link::search($this->search);
        }

        $links = $query
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->sortField, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(12);
        app('debugbar')->debug('$this->sortField: ' . $this->sortField);
        app('debugbar')->debug($links);

        return view('livewire.links', [
            'links' => $links
        ]);
    }
}
