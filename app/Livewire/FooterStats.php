<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Link;
use Illuminate\Support\Facades\Cache;

class FooterStats extends Component
{
    public $totalLinks;
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
        $this->totalLinks = Cache::remember('total_links', now()->addHours(1), function () {
            return Link::count();
        });
        
        $this->totalChannels = Cache::remember('total_channels', now()->addHours(1), function () {
            return Link::where('type', 'channel')->count();
        });
        
        $this->totalGroups = Cache::remember('total_groups', now()->addHours(1), function () {
            return Link::where('type', 'group')->count();
        });
        
        $this->totalBots = Cache::remember('total_bots', now()->addHours(1), function () {
            return Link::where('type', 'bot')->count();
        });
        
        $this->totalPersons = Cache::remember('total_persons', now()->addHours(1), function () {
            return Link::where('type', 'person')->count();
        });
        
        $this->totalMessages = Cache::remember('total_messages', now()->addHours(1), function () {
            return Link::where('type', 'message')->count();
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
