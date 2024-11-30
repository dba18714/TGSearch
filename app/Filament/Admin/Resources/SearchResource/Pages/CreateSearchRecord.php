<?php

namespace App\Filament\Admin\Resources\SearchResource\Pages;

use App\Filament\Admin\Resources\SearchRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSearchRecord extends CreateRecord
{
    protected static string $resource = SearchRecordResource::class;
}