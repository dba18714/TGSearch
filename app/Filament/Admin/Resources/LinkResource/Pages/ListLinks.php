<?php

namespace App\Filament\Admin\Resources\LinkResource\Pages;

use App\Filament\Admin\Resources\LinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLinks extends ListRecords
{
    protected static string $resource = LinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}