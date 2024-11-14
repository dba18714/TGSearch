<?php

namespace App\Livewire;

use App\Models\Owner;
use Livewire\Component;

class OwnerShow extends Component
{
    public Owner $owner;

    public function mount(Owner $owner)
    {
        $this->link = $owner;
    }

    public function getRelatedOwners()
    {
        $owners = Owner::search($this->link->name)
            ->query(function ($query) {
                return $query->whereNot('id', $this->link->id);
            })
            ->take(7)
            ->get();
        app('debugbar')->debug('owners', $owners);
        return $owners;
    }

    public function render()
    {
        return view('livewire.owner-show', [
            'relatedOwners' => $this->getRelatedOwners()
        ]);
    }
}
