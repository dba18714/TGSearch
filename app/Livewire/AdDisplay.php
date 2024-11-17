<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ad;

class AdDisplay extends Component
{
    public $position;
    
    public function mount($position)
    {
        $this->position = $position;
    }
    
    public function render()
    {
        $ad = Ad::where('position', $this->position)
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            })
            ->first();
            
        if ($ad) {
            $ad->increment('view_count');
        }
        
        return view('livewire.ad-display', [
            'ad' => $ad
        ]);
    }
    
    public function clickAd($id)
    {
        $ad = Ad::find($id);
        if ($ad) {
            $ad->increment('click_count');
            return redirect()->away($ad->url);
        }
    }
}