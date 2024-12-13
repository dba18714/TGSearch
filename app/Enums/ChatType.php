<?php

namespace App\Enums;

enum ChatType: string
{
    case BOT = 'bot';
    case CHANNEL = 'channel';
    case GROUP = 'group';
    case PERSON = 'person';
}