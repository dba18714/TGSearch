<?php

namespace App\Filament\Admin\Resources\TelegramLinkResource\Pages;

use App\Filament\Admin\Resources\TelegramLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTelegramLink extends CreateRecord
{
    protected static string $resource = TelegramLinkResource::class;
}