<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Chat;
use App\Services\ImpressionStatsService;
use Artesaos\SEOTools\Facades\SEOMeta;
use Livewire\Component;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Route as RouteAttribute;

class ChatShow extends Component
{
    public Chat $chat;
    public Message $message;

    public function mount(Chat $chat, ?Message $message)
    {
        app('debugbar')->debug('message', $message->exists);
        $this->chat = $chat;
        $this->message = $message;
        app('debugbar')->debug('message', $this->message->exists);

    }

    public function getRelatedChats()
    {
        $chats = Chat::search($this->chat->name)
            ->query(function ($query) {
                return $query->whereNot('id', $this->chat->id);
            })
            ->take(7)
            ->get();
        app('debugbar')->debug('chats', $chats);

        app(ImpressionStatsService::class)->recordBulkImpressions($chats->all(), 'related_recommendation');

        return $chats;
    }

    public function render()
    {
        $title = $this->chat->name;
        if ($this->message->exists) {
            $title .= ' - ' . (mb_strwidth($this->message->text) > 20 ? mb_strimwidth($this->message->text, 0, 20, '...') : $this->message->text);
        }
        SEOMeta::setTitle($title);

        // 获取曝光统计数据
        $impressionStats = app(ImpressionStatsService::class)->getImpressionStats($this->chat, 'Asia/Shanghai');
        $todayImpressions = $impressionStats->last();
        $weekImpressions = $impressionStats->sum();

        $messages = $this->chat->messages()
                ->when($this->message->exists, function ($query) {
                    $query->where('id', $this->message->id);
                })
                ->paginate(5);

        app(ImpressionStatsService::class)->recordBulkImpressions($messages->items(), 'chat_detail_page');

        app('debugbar')->debug('chat', $this->chat);
        return view('livewire.chat-show', [
            'relatedChats' => $this->getRelatedChats(),
            'messages' => $messages,
            'todayImpressions' => $todayImpressions,
            'weekImpressions' => $weekImpressions,
        ]);
    }
}
