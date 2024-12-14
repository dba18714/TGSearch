<?php

namespace App\Filament\Admin\Resources\UnifiedSearchResource\Pages;

use App\Filament\Admin\Resources\UnifiedSearchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnifiedSearches extends ListRecords
{
    protected static string $resource = UnifiedSearchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}