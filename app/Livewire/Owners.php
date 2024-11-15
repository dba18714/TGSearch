<?php

namespace App\Livewire;

use App\Jobs\ProcessGoogleCustomSearchJob;
use App\Models\Owner;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\GoogleCustomSearchService;
use App\Services\TelegramCrawlerService;
use App\Jobs\ProcessUpdateOwnerInfoJob;
use App\Models\Message;

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
            // 搜索消息
            $messageOwnerIds = Message::search($this->search)
                ->get(['id', 'owner_id', 'text'])
                ->groupBy('owner_id');

            // 搜索所有者
            $owners = Owner::search($this->search)->get();

            // 合并两种搜索结果的 owner_id
            $allOwnerIds = $messageOwnerIds->keys()->merge($owners->pluck('id'))->unique();

            $query->whereIn('id', $allOwnerIds);
        }

        $owners = $query
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->sortField, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(12);

        // 如果有搜索词，添加匹配的消息到结果中
        if (!empty($this->search) && isset($messageOwnerIds)) {
            $owners->each(function ($owner) use ($messageOwnerIds) {
                $owner->matched_messages = $messageOwnerIds->get($owner->id);
            });
        }

        return view('livewire.owners', [
            'owners' => $owners
        ]);
    }

    public function render2()
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
