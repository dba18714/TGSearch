<?php

/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Models\User;
use App\Telegram\Conversations\RecruitConversation;
use App\Telegram\Handlers\SearchHandler;
use App\Telegram\Handlers\StartHandler;
use App\Telegram\InlineMenu\ChooseColorMenu;
use Illuminate\Support\Facades\Log;
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

function get_inviter_form_the_start_command(string $text)
{
    if ($text && str_starts_with($text, '/start')) {
        $parts = explode(' ', $text, 2);
        if (isset($parts[1])) {
            $inviter_tg_id = (int)$parts[1];
            if (! $inviter_tg_id) return false;

            $inviter = User::where('tg_id', $inviter_tg_id)->first();
            if($inviter->exists){
                return $inviter;
            }
        }
    }
    return false;
}

$bot->middleware(function (Nutgram $bot, $next) {
    Log::info('$bot->userId()', [$bot->userId()]);

    // 检索或创建用户
    $user = User::firstOrCreate(
        ['tg_id' => $bot->userId()],
        ['name' => $bot->user()?->first_name . ' ' . $bot->user()?->last_name]
    );

    $inviter = get_inviter_form_the_start_command($bot->message()?->text);

    if (
        $user->wasRecentlyCreated &&
        $inviter !== false &&
        $inviter->tg_id != $bot->userId()
    ) {
            $user->parent_id = $inviter->id;
            $user->save();

            // TODO 发送消息给邀请人，告知他们有人通过他们的邀请链接注册了账号
            // TODO 佣金发放
            // TODO 佣金发放记录

            // 更新邀请人的邀请计数
            $inviter->increment('invite_count');
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
