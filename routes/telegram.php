<?php

/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Models\CommissionRecord;
use App\Models\User;
use App\Settings\GeneralSettings;
use App\Telegram\Conversations\RecruitConversation;
use App\Telegram\Handlers\SearchHandler;
use App\Telegram\Handlers\StartHandler;
use App\Telegram\InlineMenu\ChooseColorMenu;
use App\Telegram\Services\InviterService;
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

// function get_inviter_form_the_start_command(string $text)
// {
//     if ($text && str_starts_with($text, '/start')) {
//         $parts = explode(' ', $text, 2);
//         if (isset($parts[1])) {
//             $inviter_tg_id = (int)$parts[1];
//             if (! $inviter_tg_id) return false;

//             $inviter = User::where('tg_id', $inviter_tg_id)->first();
//             if ($inviter->exists) {
//                 return $inviter;
//             }
//         }
//     }
//     return false;
// }

$bot->middleware(function (Nutgram $bot, $next) {
    Log::info('$bot->userId()', [$bot->userId()]);

    $name = trim($bot->user()?->first_name . ' ' . $bot->user()?->last_name);
    if (empty($name)) $name = ' unknown';

    // 检索或创建用户
    $user = User::firstOrCreate(
        ['tg_id' => $bot->userId()],
        ['name' => $name]
    );

    // $inviter = get_inviter_form_the_start_command($bot->message()?->text);
    $inviter = InviterService::getInviterFromStartCommand($bot->message()?->text);

    if (
        $user->wasRecentlyCreated &&
        $inviter !== false &&
        $inviter->tg_id != $bot->userId()
    ) {
        $user->parent_id = $inviter->id;
        $user->save();

        // TODO 队列处理
        try {
            DB::transaction(function () use ($inviter, $user, $bot) {
                $settings = app(GeneralSettings::class);

                // 1. 设置直接邀请人
                $user->parent_id = $inviter->id;
                $user->save();
        
                // 2. 处理一级代理佣金(直接邀请)
                $level1_amount = $settings->level1_commission_amount;
                CommissionRecord::create([
                    'user_id' => $inviter->id,
                    'invitee_id' => $user->id,
                    'amount' => $level1_amount,
                    'level' => 1,
                ]);
                $inviter->increment('commission_balance', $level1_amount);
        
                // 3. 处理二级代理佣金
                $parent_of_inviter = $inviter->parent;
                if ($parent_of_inviter) {
                    $level2_amount = $settings->level2_commission_amount;
                    CommissionRecord::create([
                        'user_id' => $parent_of_inviter->id,
                        'invitee_id' => $user->id,
                        'amount' => $level2_amount,
                        'level' => 2,
                    ]);
                    $parent_of_inviter->increment('commission_balance', $level2_amount);
        
                    // 发送消息给二级代理
                    $bot->sendMessage(
                        "恭喜! [{$inviter->name}]邀请了[{$user->name}]，您获得了{$level2_amount}个USDT奖励！",
                        chat_id: $parent_of_inviter->tg_id,
                    );
                }
        
                // 发送消息给直接邀请人
                $bot->sendMessage(
                    "恭喜! 用户[{$user->name}]接受了您的邀请，您获得了{$level1_amount}个USDT奖励！",
                    chat_id: $inviter->tg_id,
                );
            });
        } catch (\Exception $e) {
            // 记录错误日志
            Log::error('佣金发放失败', [
                'inviter_id' => $inviter->id,
                'invitee_id' => $user->id,
                'error' => $e->getMessage()
            ]);
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

$bot->onCommand('menu', RecruitConversation::class)
    ->description('菜单');

$bot->onCommand('tmp2', ChooseColorMenu::class)
    ->description('tmp')->isHidden();
