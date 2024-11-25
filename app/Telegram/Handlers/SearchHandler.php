<?php

namespace App\Telegram\Handlers;

use App\Models\Message;
use App\Models\Owner;
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
        $messageOwnerIds = Message::search($query)
            ->get(['id', 'owner_id', 'text'])
            ->groupBy('owner_id')
            ->map(function ($messages) {
                return $messages->take(1);
            });

        // 搜索所有者
        $owners = Owner::search($query)->get();

        // 合并两种搜索结果的 owner_id
        $allOwnerIds = $messageOwnerIds->keys()->merge($owners->pluck('id'))->unique();

        // 获取最终结果
        $results = Owner::whereIn('id', $allOwnerIds)
            ->take(10)
            ->get();

        if ($results->isEmpty()) {
            $bot->sendMessage("❌ 未找到相关结果");
            return;
        }

        $response = "🔍 搜索结果：\n\n";
        
        foreach ($results as $owner) {
            $response .= "📢 <b>{$owner->name}</b>\n";
            $response .= "🔗 @{$owner->username}\n";
            
            // 如果有匹配的消息，显示第一条
            if (isset($messageOwnerIds[$owner->id])) {
                foreach ($messageOwnerIds[$owner->id] as $message) {
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