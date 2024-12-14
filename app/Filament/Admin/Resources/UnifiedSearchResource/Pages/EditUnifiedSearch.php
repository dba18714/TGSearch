<?php

namespace App\Filament\Admin\Resources\UnifiedSearchResource\Pages;

use App\Filament\Admin\Resources\UnifiedSearchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnifiedSearch extends EditRecord
{
    protected static string $resource = UnifiedSearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}