<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LinkResource\Pages;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('introduction')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->url()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->required()
                    ->options([
                        'bot' => '机器人',
                        'channel' => '频道',
                        'group' => '群组',
                        'person' => '个人',
                        'message' => '消息',
                    ]),
                Forms\Components\TextInput::make('telegram_username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('member_count')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('view_count')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_by_user')
                    ->required(),
                Forms\Components\Toggle::make('is_valid')
                    ->required(),
                Forms\Components\DateTimePicker::make('verified_at'),
                Forms\Components\DateTimePicker::make('verified_start_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->limit(16)
                    ->tooltip(function (Tables\Columns\TextColumn $column): string {
                        return $column->getState();
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('telegram_username')
                    ->limit(16)
                    ->tooltip(function (Tables\Columns\TextColumn $column){
                        return $column->getState();
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->url(fn(Link $record): string => $record->url)
                    ->openUrlInNewTab()
                    ->limit(16)
                    ->tooltip(function (Tables\Columns\TextColumn $column): string {
                        return $column->getState();
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('introduction')
                    ->limit(16)
                    ->tooltip(function (Tables\Columns\TextColumn $column): string {
                        return $column->getState();
                    }),
                Tables\Columns\IconColumn::make('is_valid')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('member_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('is_valid')
                    ->label('有效的')
                    ->options([
                        '1' => '是',
                        '0' => '否',
                    ]),
                Tables\Filters\SelectFilter::make('is_by_user')
                    ->label('由用户创建')
                    ->options([
                        '1' => '是',
                        '0' => '否',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('类型')
                    ->options([
                        'bot' => '机器人',
                        'channel' => '频道',
                        'group' => '群组',
                        'person' => '个人',
                        'message' => '消息',
                    ]),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            // 'view' => Pages\ViewLink::route('/{record}'),
            // 'edit' => Pages\EditLink::route('/{record}/edit'),
        ];
    }
}
