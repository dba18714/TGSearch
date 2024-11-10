<?php

namespace App\Filament\Admin\Resources\TelegramLinkResource\Pages;

use App\Filament\Admin\Resources\TelegramLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTelegramLink extends EditRecord
{
    protected static string $resource = TelegramLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}