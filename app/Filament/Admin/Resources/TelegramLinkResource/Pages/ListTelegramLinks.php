<?php

namespace App\Filament\Admin\Resources\TelegramLinkResource\Pages;

use App\Filament\Admin\Resources\TelegramLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTelegramLinks extends ListRecords
{
    protected static string $resource = TelegramLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}