<?php

namespace App\Filament\Admin\Resources\ChatResource\Pages;

use App\Filament\Admin\Resources\ChatResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateChat extends CreateRecord
{
    protected static string $resource = ChatResource::class;
}