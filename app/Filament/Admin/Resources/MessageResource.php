<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MessageResource\Pages;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Filament\Notifications\Notification;
use App\Models\Entity;
use Filament\Support\Enums\IconPosition;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\Select::make('entity_id')
                    ->label('所有者')
                    ->relationship('entity', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('original_id')
                    ->dehydrated(fn($state) => filled($state)),
                Forms\Components\Textarea::make('text')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('view_count')
                    ->dehydrated(fn($state) => filled($state))
                    ->numeric(),
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
                Forms\Components\DateTimePicker::make('created_at')
                    ->disabled()
                    ->dehydrated(false)
                    ->label('创建时间')
                    ->timezone('PRC'),
                Forms\Components\DateTimePicker::make('updated_at')
                    ->disabled()
                    ->dehydrated(false)
                    ->label('更新时间')
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
                Tables\Columns\TextColumn::make('entity.name')
                    ->limit(12)
                    ->label('所有者')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-pencil-square')
                    ->iconPosition(IconPosition::After)
                    ->action(
                        Tables\Actions\Action::make('viewEntity')
                            ->label(fn(Message $record) => "所有者详情 #{$record->entity_id}")
                            ->slideOver()
                            ->modalWidth('lg')
                            ->form(fn(Form $form) => EntityResource::form($form)->getComponents())
                            ->fillForm(fn(Message $record) => $record->entity->toArray())
                            ->action(function (array $data, Message $record) {
                                $record->entity->update($data);
                                Notification::make()
                                    ->title('所有者信息已更新')
                                    ->success()
                                    ->send();
                            })
                    )
                    ->tooltip(function (Tables\Columns\TextColumn $column): string {
                        return $column->getState();
                    }),
                Tables\Columns\TextColumn::make('original_id')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('text')
                    ->limit(16)
                    ->tooltip(function (Tables\Columns\TextColumn $column) {
                        return $column->getState();
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('url')
                    ->url(fn(Message $record): string => $record->url)
                    ->openUrlInNewTab()
                    ->limit(16)
                    ->tooltip(function (Tables\Columns\TextColumn $column): string {
                        return $column->getState();
                    }),
                Tables\Columns\IconColumn::make('is_valid')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('view_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source'),
                Tables\Columns\TextColumn::make('verified_at')
                    ->timezone('PRC')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->timezone('PRC')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('is_valid')
                    ->options([
                        '1' => '是',
                        '0' => '否',
                    ]),
                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'manual' => '用户提交',
                        'crawler' => '爬虫',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
        ];
    }
}
