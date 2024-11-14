<?php

namespace App\Livewire;

use App\Jobs\ProcessGoogleCustomSearchJob;
use App\Models\Owner;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\GoogleCustomSearchService;
use App\Services\TelegramCrawlerService;
use App\Jobs\ProcessUpdateOwnerInfoJob;

class Owners extends Component
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

        ProcessGoogleCustomSearchJob::dispatch($this->search);

        $this->resetPage();
    }

    public function render()
    {
        $query = Owner::query();

        if (!empty($this->search)) {
            $query = Owner::search($this->search);
        }

        $owners = $query
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->sortField, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(12);
        app('debugbar')->debug('$this->sortField: ' . $this->sortField);
        app('debugbar')->debug($owners);

        return view('livewire.owners', [
            'owners' => $owners
        ]);
    }
}
