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
        return Link::query()
            ->valid()
            ->where('id', '!=', $this->link->id)
            ->where('type', $this->link->type)
            ->inRandomOrder()
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.link-show', [
            'relatedLinks' => $this->getRelatedLinks()
        ]);
}
}