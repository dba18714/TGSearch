<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SearchResource\Pages;
use App\Models\Search;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SearchResource extends Resource
{
    protected static ?string $model = Search::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = '搜索记录';

    protected static ?string $modelLabel = '搜索记录';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('keyword')
                    ->required()
                    ->maxLength(255)
                    ->label('搜索关键词'),

                Forms\Components\TextInput::make('searched_count')
                    ->numeric()
                    ->default(0)
                    ->label('搜索次数'),

                Forms\Components\KeyValue::make('ip_history')
                    ->label('IP历史'),

                Forms\Components\DateTimePicker::make('last_searched_at')
                    ->timezone('Asia/Shanghai')
                    ->label('最后搜索时间'),
            ]);
    }

    public static function table(Table $table): Table
    {
        $ipShow = function (Search $record) {
            return empty($record->ip_history)
                ? '无记录'
                : implode(' / ', array_reverse($record->ip_history));
        };

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('keyword')
                    ->searchable()
                    ->sortable()
                    ->label('搜索关键词')
                    ->url(fn(Search $record): string => "/?search=" . urlencode($record->keyword))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->iconPosition('after'),

                Tables\Columns\TextColumn::make('searched_count')
                    ->sortable()
                    ->label('搜索次数'),

                Tables\Columns\TextColumn::make('ip_history')
                    ->label('IP历史')
                    ->formatStateUsing($ipShow)
                    ->tooltip($ipShow)
                    ->markdown()
                    ->limit(20),

                Tables\Columns\TextColumn::make('last_searched_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->timezone('Asia/Shanghai')
                    ->sortable()
                    ->label('最后搜索时间'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('Y-m-d H:i:s')
                    ->timezone('Asia/Shanghai')
                    ->sortable()
                    ->label('创建时间'),
            ])
            ->defaultSort('searched_count', 'desc')
            ->filters([
                Tables\Filters\Filter::make('popular')
                    ->query(fn($query) => $query->where('searched_count', '>', 10))
                    ->label('热门搜索'),

                Tables\Filters\Filter::make('recent')
                    ->query(fn($query) => $query->where('last_searched_at', '>=', now()->subDays(7)))
                    ->label('最近7天'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSearches::route('/'),
            'create' => Pages\CreateSearch::route('/create'),
            'edit' => Pages\EditSearch::route('/{record}/edit'),
        ];
    }
}
