<?php

namespace App\Livewire;

use App\Models\TelegramLink;
use Livewire\Component;

class TelegramLinkShow extends Component
{
    public TelegramLink $telegramLink;
    
    public function mount(TelegramLink $telegramLink)
    {
        $this->telegramLink = $telegramLink;
    }

    public function render()
    {
        return view('livewire.telegram-link-show');
    }
}