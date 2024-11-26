<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Entity;
use Artesaos\SEOTools\Facades\SEOMeta;
use Livewire\Component;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Route as RouteAttribute;

class EntityShow extends Component
{
    public Entity $entity;
    public Message $message;

    public function mount(Entity $entity, ?Message $message)
    {
        app('debugbar')->debug('message', $message->exists);
        $this->entity = $entity;
        $this->message = $message;
        app('debugbar')->debug('message', $this->message->exists);

    }

    public function getRelatedEntities()
    {
        $entities = Entity::search($this->entity->name)
            ->query(function ($query) {
                return $query->whereNot('id', $this->entity->id);
            })
            ->take(7)
            ->get();
        app('debugbar')->debug('entities', $entities);
        return $entities;
    }

    public function render()
    {
        $title = $this->entity->name;
        if ($this->message->exists) {
            $title .= ' - ' . (mb_strwidth($this->message->text) > 20 ? mb_strimwidth($this->message->text, 0, 20, '...') : $this->message->text);
        }
        SEOMeta::setTitle($title);

        app('debugbar')->debug('entity', $this->entity);
        return view('livewire.entity-show', [
            'relatedEntities' => $this->getRelatedEntities(),
            'messages' => $this->entity->messages()
                ->when($this->message->exists, function ($query) {
                    $query->where('id', $this->message->id);
                })
                ->paginate(5)
        ]);
    }
}
