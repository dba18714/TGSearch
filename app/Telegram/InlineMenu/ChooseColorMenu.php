<?php

namespace App\Telegram\InlineMenu;

use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class ChooseColorMenu extends InlineMenu
{
    protected ?int $tmp = null;

    public function start(Nutgram $bot)
    {
        $this->menuText('Choose a color:')
            ->addButtonRow(InlineKeyboardButton::make('Red', callback_data: 'red@handleColor'))
            ->addButtonRow(InlineKeyboardButton::make('Green', callback_data: 'green@handleColor'))
            ->addButtonRow(InlineKeyboardButton::make('Yellow', callback_data: 'yellow@handleColor'))
            ->orNext('none')
            ->showMenu();

        $this->tmp = time();
    }

    public function handleColor(Nutgram $bot)
    {
        $color = $bot->callbackQuery()->data;
        $this->menuText("Choosen: $color!".$this->tmp)
            ->clearButtons()
            ->addButtonRow(InlineKeyboardButton::make('Red2', callback_data: 'red2@handleColor'))
            ->showMenu();
    }

    public function none(Nutgram $bot)
    {
        $bot->sendMessage('Bye!');
        $this->end();
    }
}
