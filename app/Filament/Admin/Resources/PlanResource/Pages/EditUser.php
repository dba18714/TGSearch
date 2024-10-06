<?php

namespace App\Filament\Admin\Resources\PlanResource\Pages;

use App\Filament\Admin\Resources\PlanResource;
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
