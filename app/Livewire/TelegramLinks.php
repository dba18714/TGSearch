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
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
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
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.telegram-links', [
            'links' => $links
        ]);
    }
}