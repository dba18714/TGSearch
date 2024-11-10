<?php

namespace App\Livewire;

use App\Models\TelegramLink;
use Livewire\Component;
use Livewire\WithPagination;

class TelegramLinks extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $sortField = '';
    public $sortDirection = '';
    public $selectedLink = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

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
        $this->resetPage();
    }

    public function selectLink($linkId)
    {
        $this->selectedLink = TelegramLink::findOrFail($linkId);
    }

    public function render()
    {
        $links = TelegramLink::query()
            ->valid()
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('introduction', 'like', '%' . $this->search . '%')
                        ->orWhere('telegram_username', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->sortField, function ($query) {
                $query->orderBy($this->sortField, $this->sortDirection);
            })
            ->paginate(12);

        return view('livewire.telegram-links', [
            'links' => $links
        ]);
    }
}
