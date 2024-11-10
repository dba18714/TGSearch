<?php

namespace App\Livewire;

use App\Models\Link;
use Livewire\Component;

class LinkShow extends Component
{
    public Link $link;
    
    public function mount(Link $link, bool $isModal = false)
    {
        $this->link = $link;
    }

    public function getRelatedLinks()
    {
        return Link::search($this->link->name)
            ->query(function ($query) {
                return $query->whereNot('id', $this->link->id)
                            ->where('type', $this->link->type);
            })
            // ->whereNot('id', $this->link->id)
            // ->where('type', $this->link->type)
            ->take(7)
            ->get();
    }

    public function render()
    {
        return view('livewire.link-show', [
            'relatedLinks' => $this->getRelatedLinks()
        ]);
}
}