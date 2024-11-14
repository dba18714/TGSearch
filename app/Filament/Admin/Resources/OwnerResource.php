<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OwnerResource\Pages;
use App\Models\Owner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;

class OwnerResource extends Resource
{
    protected static ?string $model = Owner::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\TextInput::make('name')
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255),
                Forms\Components\Textarea::make('introduction')
                    ->dehydrated(fn($state) => filled($state))
                    ->maxLength(255),
                // Forms\Components\Textarea::make('message')
                //     ->dehydrated(fn($state) => filled($state))
                //     ->maxLength(65535),
                // Forms\Components\TextInput::make('url')
                //     ->required()
                //     ->url()
                //     ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->dehydrated(fn($state) => filled($state))
                    ->options([
                        'bot' => '机器人',
                        'channel' => '频道',
                        'group' => '群组',
                        'person' => '个人',
                        // 'message' => '消息',
                    ]),
                Forms\Components\TextInput::make('username')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('member_count')
                    ->dehydrated(fn($state) => filled($state))
                    ->numeric(),
                // Forms\Components\TextInput::make('view_count')
                //     ->dehydrated(fn($state) => filled($state))
                //     ->numeric(),
                Forms\Components\Select::make('source')
                    ->dehydrated(fn($state) => filled($state))
                    ->options([
                        'manual' => '用户提交',
                        'crawler' => '爬虫',
                    ]),
                Forms\Components\Toggle::make('is_valid')
                    ->dehydrated(fn($state) => filled($state)),
                Forms\Components\DateTimePicker::make('verified_at')
                    ->dehydrated(fn($state) => filled($state))
                    ->timezone('PRC'),
                Forms\Components\DateTimePicker::make('verified_start_at')
                    ->dehydrated(fn($state) => filled($state))
                    ->timezone('PRC'),
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
                    ->limit(16)
                    ->tooltip(function (Tables\Columns\TextColumn $column) {
                        return $column->getState();
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('username')
                    ->limit(16)
                    ->tooltip(function (Tables\Columns\TextColumn $column) {
                        return $column->getState();
                    })
                    ->searchable(),
                // Tables\Columns\TextColumn::make('url')
                //     ->url(fn(Owner $record): string => $record->url)
                //     ->openUrlInNewTab()
                //     ->limit(16)
                //     ->tooltip(function (Tables\Columns\TextColumn $column): string {
                //         return $column->getState();
                //     })
                //     ->searchable(),
                Tables\Columns\TextColumn::make('introduction')
                    ->limit(16)
                    ->tooltip(function (Tables\Columns\TextColumn $column) {
                        return $column->getState();
                    }),
                Tables\Columns\IconColumn::make('is_valid')
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('member_count')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('view_count')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('verified_start_at')
                    ->timezone('PRC')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->timezone('PRC')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->timezone('PRC')
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
                Tables\Filters\SelectFilter::make('source')
                    ->label('来源')
                    ->options([
                        'manual' => '用户提交',
                        'crawler' => '爬虫',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('类型')
                    ->options([
                        'bot' => '机器人',
                        'channel' => '频道',
                        'group' => '群组',
                        'person' => '个人',
                        // 'message' => '消息',
                    ]),
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('update')
                        ->label('爬取更新信息')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Collection $records) {
                            $records->each->dispatchUpdateJob();
                            Notification::make()
                                ->title('已派发爬取更新任务')
                                ->success()
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
            'index' => Pages\ListOwners::route('/'),
            'create' => Pages\CreateOwner::route('/create'),
            // 'view' => Pages\ViewOwner::route('/{record}'),
            // 'edit' => Pages\EditOwner::route('/{record}/edit'),
        ];
    }
}
