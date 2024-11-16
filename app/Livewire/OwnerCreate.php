<?php

namespace App\Livewire;

use App\Models\Message;
use Livewire\Component;
use App\Models\Owner;

class OwnerCreate extends Component
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
            $line = trim($line);
            if (empty($line)) continue;

            // 如果是以@开头或者不包含/的，认为是用户名
            if (str_starts_with($line, '@') || !str_contains($line, '/')) {
                $username = ltrim($line, '@');
            } else {
                $username = extract_telegram_username_by_url($line);
                $message_id = extract_telegram_message_id_by_url($line);
            }

            // 创建或更新模型
            if ($username) {
                $owner = Owner::firstOrCreate(
                    ['username' => $username],
                    [
                        'source' => 'manual',
                        'source_str' => $line,
                    ]
                );
                if (isset($message_id)) {
                    Message::firstOrCreate(
                        ['owner_id' => $owner->id, 'original_id' => $message_id],
                        [
                            'source' => 'manual',
                            'source_str' => $line,
                        ]
                    );
                }    
            }

            $count++;
        }

        session()->flash('message', "成功添加 {$count} 条链接");
    }

    public function render()
    {
        return view('livewire.owner-create');
    }
}
