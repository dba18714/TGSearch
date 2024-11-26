<?php

namespace App\Filament\Admin\Resources\EntityResource\Pages;

use App\Filament\Admin\Resources\EntityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEntity extends EditRecord
{
    protected static string $resource = EntityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}