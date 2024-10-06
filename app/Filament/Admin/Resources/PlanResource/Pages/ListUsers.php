<?php

namespace App\Filament\Admin\Resources\PlanResource\Pages;

use App\Filament\Admin\Resources\PlanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = PlanResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
