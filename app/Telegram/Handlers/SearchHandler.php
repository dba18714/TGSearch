<?php

namespace App\Telegram\Handlers;

use App\Models\Message;
use App\Models\Chat;
use App\Models\Search;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class SearchHandler
{
    public function __invoke(Nutgram $bot): void
    {
        $query = $bot->message()->text;
        
        if (strlen($query) < 2) {
            $bot->sendMessage("❌ 搜11索关键词至少需要2个字符");
            return;
        }

        // 记录搜索
        Search::recordSearch($query);

        // 搜索消息
        $messageChatIds = Message::search($query)
            ->get(['id', 'chat_id', 'text'])
            ->groupBy('chat_id')
            ->map(function ($messages) {
                return $messages->take(1);
            });

        // 搜索所有者
        $chats = Chat::search($query)->get();

        // 合并两种搜索结果的 chat_id
        $allChatIds = $messageChatIds->keys()->merge($chats->pluck('id'))->unique();

        // 获取最终结果
        $results = Chat::whereIn('id', $allChatIds)
            ->take(10)
            ->get();

        if ($results->isEmpty()) {
            $bot->sendMessage("❌ 未找到相关结果");
            return;
        }

        $response = "🔍 搜索结果：\n\n";
        
        foreach ($results as $chat) {
            $response .= "📢 <b>{$chat->name}</b>\n";
            $response .= "🔗 @{$chat->username}\n";
            
            // 如果有匹配的消息，显示第一条
            if (isset($messageChatIds[$chat->id])) {
                foreach ($messageChatIds[$chat->id] as $message) {
                    $response .= "💬 {$message->text}\n";
                    break;
                }
            }
            
            $response .= "\n";
        }

        $response .= "\n共找到 {$results->count()} 个结果";

        $bot->sendMessage(
            text: $response,
            parse_mode: ParseMode::HTML
        );
    }
}