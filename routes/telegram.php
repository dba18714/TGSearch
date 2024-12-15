<?php

/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Telegram\Conversations\RecruitConversation;
use App\Telegram\Handlers\SearchHandler;
use App\Telegram\Handlers\StartHandler;
use App\Telegram\InlineMenu\ChooseColorMenu;
use SergiX44\Nutgram\Nutgram;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/

$bot->middleware(function (Nutgram $bot, $next) {
    // 获取当前用户
    $user = get_current_user_from_db($bot->userId());
    
    // 处理 start 命令的邀请参数
    $text = $bot->message()?->text;
    if ($text && str_starts_with($text, '/start')) {
        $parts = explode(' ', $text, 2);
        if (isset($parts[1]) && str_starts_with($parts[1], 'a_')) {
            $inviterId = (int)substr($parts[1], 2);
            // 如果是新用户且有邀请人ID，设置邀请关系
            if ($user && !$user->parent_id && $inviterId != $user->id) {
                $inviter = \App\Models\User::find($inviterId);
                if ($inviter) {
                    $user->parent_id = $inviter->id;
                    $user->save();
                    
                    // 更新邀请人的邀请计数
                    $inviter->increment('invite_count');
                }
            }
        }
    }
    
    // 将用户实例存储在 bot 容器中
    $bot->set('user', $user);
    
    $next($bot);
});

$bot->onCommand('tmp', StartHandler::class)
    ->description('tmp description')->isHidden();

$bot->onCommand('start{aff}', StartHandler::class)
    ->description('开始使用!');

$bot->onText('^[^/].*', SearchHandler::class);

$bot->onCallbackQueryData('search:{action}:{value}', [SearchHandler::class, 'handleSearchCallback']);

$bot->onCommand('add', RecruitConversation::class)
    ->description('提交收录');

$bot->onCommand('tmp2', ChooseColorMenu::class)
    ->description('tmp')->isHidden();
