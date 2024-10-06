<?php

namespace App\Filament\Console\Resources\PlanResource\Pages;

use App\Filament\Console\Resources\PlanResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = PlanResource::class;
}
