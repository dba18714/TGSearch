<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Owner;
use Artesaos\SEOTools\Facades\SEOMeta;
use Livewire\Component;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Route as RouteAttribute;

class OwnerShow extends Component
{
    public Owner $owner;
    public Message $message;

    public function mount(Owner $owner, ?Message $message)
    {
        app('debugbar')->debug('message', $message->exists);
        $this->owner = $owner;
        $this->message = $message;
        app('debugbar')->debug('message', $this->message->exists);

    }

    public function getRelatedOwners()
    {
        $owners = Owner::search($this->owner->name)
            ->query(function ($query) {
                return $query->whereNot('id', $this->owner->id);
            })
            ->take(7)
            ->get();
        app('debugbar')->debug('owners', $owners);
        return $owners;
    }

    public function render()
    {
        $title = $this->owner->name;
        if ($this->message->exists) {
            $title .= ' - ' . (mb_strwidth($this->message->text) > 20 ? mb_strimwidth($this->message->text, 0, 20, '...') : $this->message->text);
        }
        SEOMeta::setTitle($title);

        app('debugbar')->debug('owner', $this->owner);
        return view('livewire.owner-show', [
            'relatedOwners' => $this->getRelatedOwners(),
            'messages' => $this->owner->messages()
                ->when($this->message->exists, function ($query) {
                    $query->where('id', $this->message->id);
                })
                ->paginate(5)
        ]);
    }
}
