<?php

namespace App\Livewire;

use App\Models\TelegramLink;
use Livewire\Component;

class TelegramLinkShow extends Component
{
    public TelegramLink $telegramLink;
    public bool $isModal = false; // 添加这个属性
    
    public function mount(TelegramLink $telegramLink, bool $isModal = false)
    {
        $this->telegramLink = $telegramLink;
        $this->isModal = $isModal; // 通过参数设置
    }

    public function render()
    {
        return view('livewire.telegram-link-show');
    }
}