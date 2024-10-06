<?php

namespace App\Filament\Console\Resources\PlanResource\Pages;

use App\Filament\Console\Resources\PlanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = PlanResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
