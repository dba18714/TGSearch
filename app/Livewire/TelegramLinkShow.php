<?php

namespace App\Livewire;

use App\Models\TelegramLink;
use Livewire\Component;

class TelegramLinkShow extends Component
{
    public TelegramLink $telegramLink;
    
    public function mount(TelegramLink $telegramLink, bool $isModal = false)
    {
        $this->telegramLink = $telegramLink;
    }

    public function getRelatedLinks()
    {
        return TelegramLink::query()
            ->valid()
            ->where('id', '!=', $this->telegramLink->id)
            ->where('type', $this->telegramLink->type)
            ->inRandomOrder()
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.telegram-link-show', [
            'relatedLinks' => $this->getRelatedLinks()
        ]);
}
}