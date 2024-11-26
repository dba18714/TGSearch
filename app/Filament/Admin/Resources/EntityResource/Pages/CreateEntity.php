<?php

namespace App\Filament\Admin\Resources\EntityResource\Pages;

use App\Filament\Admin\Resources\EntityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEntity extends CreateRecord
{
    protected static string $resource = EntityResource::class;
}