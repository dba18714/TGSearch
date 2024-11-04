<?php

namespace App\Filament\Console\Resources;

use App\Filament\Console\Resources\PlanResource\Pages;
use App\Filament\Resources\PlanResource\RelationManagers;
use App\Models\Plan;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('title')
                        ->extraAttributes(['class' => 'text-lg'])
                        ->alignment('center')
                        ->weight(FontWeight::Bold),
                    Tables\Columns\TextColumn::make('title')
                        ->formatStateUsing(fn (string $state): string => "¥ $state")
                        ->color('gray')
                        ->limit(30),
                ]),
            ])
            ->filters([
                //
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            // ->paginated([
            //     18,
            //     36,
            //     72,
            //     'all',
            // ])
            ->paginated(false)
            ->selectable(false)
            ->actions([
                Tables\Actions\Action::make('visit')
                    ->label('购买此套餐')
                    ->color('primary')
                    ->button()
                    ->size(ActionSize::Large)
                    ->extraAttributes([
                        'class' => 'mx-auto my-8',
                    ])
                    ->url(fn (Plan $record): string => '#' . urlencode($record->url)),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function () {
                            Notification::make()
                                ->title('Now, now, don\'t be cheeky, leave some records for others to play with!')
                                ->warning()
                                ->send();
                        }),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            // 'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
