<?php
namespace App\Filament\Console\Resources\PlanResource\Pages;

use App\Filament\Console\Resources\PlanResource;
use App\Models\Plan;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;

class ShowPlans extends Page
{
    protected static string $resource = PlanResource::class;

    public Plan $record;

    public function mount(Plan $record)
    {
        $this->record = $record;
    }

    // public function getHeader(): string
    // {
    //     return 'Plan Details';
    // }

    protected function getInfolists(): array
    {
        return [
            Infolist::make()
                ->schema([
                    TextEntry::make('name')
                        ->label('Plan Name')
                        ->state($this->record->name),
                    TextEntry::make('email')
                        ->label('Email')
                        ->state($this->record->email),
                    TextEntry::make('is_admin')
                        ->label('Is Admin')
                        ->state($this->record->is_admin ? 'Yes' : 'No'),
                    // 其他字段
                ]),
        ];
    }
}
