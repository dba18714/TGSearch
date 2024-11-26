<?php

namespace App\Livewire;

use App\Models\Message;
use Livewire\Component;
use App\Models\Entity;
use Illuminate\Support\Facades\Cache;

class FooterStats extends Component
{
    public $totalRecords;
    public $totalChannels;
    public $totalGroups;
    public $totalBots;
    public $totalPersons;
    public $totalMessages;

    public function mount()
    {
        $this->loadStats();
    }

    private function loadStats()
    {
        $cacheDuration = config('app.debug') ? now()->addSeconds(0) : now()->addHours(1);

        $this->totalRecords = Cache::remember('total_records', $cacheDuration, function () {
            return Entity::count() + Message::count();
        });

        $this->totalChannels = Cache::remember('total_channels', $cacheDuration, function () {
            return Entity::where('type', 'channel')->count();
        });

        $this->totalGroups = Cache::remember('total_groups', $cacheDuration, function () {
            return Entity::where('type', 'group')->count();
        });

        $this->totalBots = Cache::remember('total_bots', $cacheDuration, function () {
            return Entity::where('type', 'bot')->count();
        });

        $this->totalPersons = Cache::remember('total_persons', $cacheDuration, function () {
            return Entity::where('type', 'person')->count();
        });

        $this->totalMessages = Cache::remember('total_messages', $cacheDuration, function () {
            return Message::count();
        });
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            <p>加载统计信息...</p>
        </div>
        HTML;
    }

    public function render()
    {
        return view('livewire.footer-stats');
    }
}
