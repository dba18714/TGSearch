<?php

/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Telegram\Handlers\SearchHandler;
use App\Telegram\Handlers\StartHandler;
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
