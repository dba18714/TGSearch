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

$bot->onCommand('tmp', StartHandler::class)
    ->description('tmp description')->isHidden();

$bot->onCommand('start', StartHandler::class)
    ->description('The start command!22');

$bot->onText('^[^/].*', SearchHandler::class);

// $bot->onCallbackQueryData('q:{query}|t:{type}|p:{page}', [SearchHandler::class, 'handleSearchCallback']);
// $bot->onCallbackQueryData('q:{query}|t:{type}|p:{page}|s:{sort}|d:{direction}', [SearchHandler::class, 'handleSearchCallback']);
// 使用正则表达式匹配可选的排序参数
// $bot->onCallbackQueryData('q:{query}|t:{type}|p:{page}(\|s:{sort}\|d:{direction})?', [SearchHandler::class, 'handleSearchCallback']);
$bot->onCallbackQueryData('search:{action}:{value}', [SearchHandler::class, 'handleSearchCallback']);

$bot->onCommand('add', RecruitConversation::class)
    ->description('提交收录');

$bot->onCommand('tmp2', ChooseColorMenu::class)
    ->description('tmp');
