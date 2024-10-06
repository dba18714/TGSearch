<?php

namespace App\Filament\Console\Resources\PlanResource\Pages;

use App\Filament\Console\Resources\PlanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = PlanResource::class;

    protected function getActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
