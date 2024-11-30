<?php

namespace App\Filament\Admin\Resources\SearchResource\Pages;

use App\Filament\Admin\Resources\SearchRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSearchRecords extends ListRecords
{
    protected static string $resource = SearchRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}