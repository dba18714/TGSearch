<?php

namespace App\Filament\Admin\Resources\SearchResource\Pages;

use App\Filament\Admin\Resources\SearchRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSearchRecord extends EditRecord
{
    protected static string $resource = SearchRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}