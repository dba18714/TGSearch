<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Enums\ActionsPosition;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),

                Forms\Components\Select::make('parent_id')
                    ->label('上级')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),

                // 密码
                Forms\Components\TextInput::make('password')
                    ->dehydrateStateUsing(fn($state) => $state ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255)
                    ->placeholder('留空以保留当前密码'),

                Forms\Components\DateTimePicker::make('email_verified_at')
                    ->timezone('Asia/Shanghai'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->limit(4)
                    ->tooltip(function (Tables\Columns\TextColumn $column): string {
                        return $column->getState();
                    })
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->limit(12)
                    ->tooltip(function (Tables\Columns\TextColumn $column) {
                        return $column->getState();
                    }),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('上级')
                    ->searchable()
                    ->sortable()
                    ->limit(12)
                    ->tooltip(function (Tables\Columns\TextColumn $column) {
                        return $column->getState();
                    }),

                Tables\Columns\TextColumn::make('children_count')
                    ->label('下级统计')
                    ->state(function (User $record) {
                        return collect($record->getDescendantsCountByLevel())
                            ->map(function ($count, $level) {
                                return "{$level}级: {$count} 个";
                            });
                    })
                    ->counts('children')
                    ->sortable()
                    ->listWithLineBreaks()
                    ->limitList(1)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->since()
                    ->dateTimeTooltip('Y-m-d H:i:s', 'Asia/Shanghai')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_admin')
                    ->sortable()
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->since()
                    ->dateTimeTooltip('Y-m-d H:i:s', 'Asia/Shanghai')
                    ->searchable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->recordUrl(false);
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
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
