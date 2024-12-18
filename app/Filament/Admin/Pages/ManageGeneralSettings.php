<?php

namespace App\Filament\Admin\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = '系统管理';

    protected static ?string $navigationLabel = '通用设置';

    protected static ?string $title = '通用设置';

    protected static string $settings = GeneralSettings::class;

    protected function getFormSchema(): array
    {
        return [
            Section::make('佣金设置')
                ->description('设置不同级别的邀请佣金金额')
                ->columns(2)
                ->schema([
                    TextInput::make('level1_commission_amount')
                        ->label('一级邀请奖励佣金(USDT)')
                        ->required()
                        ->numeric()
                        ->minValue(0.0001)
                        ->default(0.08),

                    TextInput::make('level2_commission_amount')
                        ->label('二级邀请奖励佣金(USDT)')
                        ->required()
                        ->numeric()
                        ->minValue(0.0001)
                        ->default(0.02),
                ]),
            Section::make('Chats/Messages更新设置')
                ->description('控制爬虫更新频率')
                ->columns(2)
                ->schema([
                    TextInput::make('items_per_update')
                        ->label('每次更新多少个资源(chats/messages)')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->helperText('设置每秒最多可以更新多少个资源，建议不要超过10'),
                    TextInput::make('update_interval_minutes')
                        ->label('更新间隔')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->default(60)
                        ->helperText('设置同一个资源至少间隔多少分钟更新一次'),
                ]),
        ];
    }
}
