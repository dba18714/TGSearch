<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UnifiedSearchResource\Pages;
use App\Models\SearchRecord;
use App\Models\UnifiedSearch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UnifiedSearchResource extends Resource
{
    protected static ?string $model = UnifiedSearch::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = '统一搜索';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->disabled()
                    ->dehydrated(false),

                Forms\Components\Textarea::make('content')
                    ->required()
                    ->maxLength(3000),

                Forms\Components\Select::make('type')
                    ->options([
                        'bot' => '机器人',
                        'channel' => '频道',
                        'person' => '个人',
                        'group' => '群组',
                        'message' => '消息',
                    ])
                    ->required()
                    ->label('类型'),

                Forms\Components\Toggle::make('audit_passed')
                    ->dehydrated(fn($state) => filled($state)),

                Forms\Components\TextInput::make('member_or_view_count')
                    ->numeric()
                    ->default(0)
                    ->label('成员数/查看数'),
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

                    Tables\Columns\TextColumn::make('type'),
                    
                
                Tables\Columns\TextColumn::make('content')
                    ->limit(16)
                    ->sortable()
                    ->tooltip(function (Tables\Columns\TextColumn $column) {
                        return $column->getState();
                    }),

                Tables\Columns\IconColumn::make('audit_passed')
                    ->sortable()
                    ->boolean(),

                Tables\Columns\TextColumn::make('member_or_view_count')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
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
                Tables\Actions\EditAction::make()
                    ->slideOver(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnifiedSearches::route('/'),
            'create' => Pages\CreateUnifiedSearch::route('/create'),
            'edit' => Pages\EditUnifiedSearch::route('/{record}/edit'),
        ];
    }
}
