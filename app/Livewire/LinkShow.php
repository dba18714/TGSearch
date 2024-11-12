<?php

namespace App\Livewire;

use App\Models\Link;
use Livewire\Component;

class LinkShow extends Component
{
    public Link $link;

    public function mount(Link $link)
    {
        $this->link = $link;
    }

    public function getRelatedLinks()
    {
        $links = Link::search($this->link->name)
            ->query(function ($query) {
                return $query->whereNot('id', $this->link->id);
            })
            ->take(7)
            ->get();
        app('debugbar')->debug('links', $links);
        return $links;
    }

    public function render()
    {
        return view('livewire.link-show', [
            'relatedLinks' => $this->getRelatedLinks()
        ]);
    }
}
