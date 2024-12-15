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
    // $user = get_current_user_from_db($bot->userId());
    // $bot->set('user', $user);
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
