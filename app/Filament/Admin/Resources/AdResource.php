<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdResource\Pages;
use App\Models\Ad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdResource extends Resource
{
    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationLabel = '广告管理';
    
    protected static ?string $modelLabel = '广告';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('广告名称'),
                    
                Forms\Components\Select::make('position')
                    ->options([
                        'sidebar' => '侧边栏',
                        'header' => '顶部横幅',
                        'footer' => '底部',
                        'content' => '内容区域',
                    ])
                    ->required()
                    ->label('展示位置'),
                    
                Forms\Components\FileUpload::make('image_url')
                    ->image()
                    ->directory('ads')
                    ->label('广告图片'),
                    
                Forms\Components\TextInput::make('url')
                    ->url()
                    ->label('跳转链接'),
                    
                Forms\Components\RichEditor::make('content')
                    ->label('广告内容'),
                    
                Forms\Components\DateTimePicker::make('start_at')
                    ->label('开始时间'),
                    
                Forms\Components\DateTimePicker::make('end_at')
                    ->label('结束时间'),
                    
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->label('是否启用'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('广告名称'),
                    
                Tables\Columns\TextColumn::make('position')
                    ->sortable()
                    ->label('展示位置'),
                    
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('广告图片'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable()
                    ->label('状态'),
                    
                Tables\Columns\TextColumn::make('view_count')
                    ->sortable()
                    ->label('展示次数'),
                    
                Tables\Columns\TextColumn::make('click_count')
                    ->sortable()
                    ->label('点击次数'),

                Tables\Columns\TextColumn::make('start_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->label('开始时间'),

                Tables\Columns\TextColumn::make('end_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->label('结束时间'),
            ])
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->query(fn ($query) => $query->where('is_active', true))
                    ->label('仅显示启用'),
                
                Tables\Filters\Filter::make('current')
                    ->query(function ($query) {
                        return $query->where(function ($query) {
                            $now = now();
                            $query->where('start_at', '<=', $now)
                                ->orWhereNull('start_at');
                        })->where(function ($query) {
                            $now = now();
                            $query->where('end_at', '>=', $now)
                                ->orWhereNull('end_at');
                        });
                    })
                    ->label('当前有效'),
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
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}