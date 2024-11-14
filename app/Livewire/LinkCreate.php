<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Link;

class LinkCreate extends Component
{
    public $urls = '';
    
    public function submit()
    {
        $this->validate([
            'urls' => 'required|string'
        ]);

        $lines = array_filter(explode("\n", $this->urls));
        
        $count = 0;
        foreach ($lines as $line) {
            $url = trim($line);
            if (empty($url)) continue;
            
            // 如果是以@开头或者不包含/的，认为是用户名
            if (str_starts_with($url, '@') || !str_contains($url, '/')) {
                $username = ltrim($url, '@');
                $url = "https://t.me/{$username}";
            }
            
            // 创建或更新链接
            Link::firstOrCreate(
                ['url' => $url],
                [
                    'type' => 'message', // 默认类型
                    'is_by_user' => true,
                ]
            );
            
            $count++;
        }

        session()->flash('message', "成功添加 {$count} 条链接");
        $this->urls = ''; // 清空输入
    }

    public function render()
    {
        return view('livewire.link-create');
    }
}