<?php

namespace App\Filament\Admin\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components;
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
            Section::make('系统设置')
                ->description('关于系统的一些配置')
                ->columns(2)
                ->schema([
                    Components\Select::make('log_level')
                        ->label('日志级别')
                        ->required()
                        ->options([
                            'debug' => 'Debug (调试)',
                            'info' => 'Info (信息)',
                            'notice' => 'Notice (通知)',
                            'warning' => 'Warning (警告)',
                            'error' => 'Error (错误)',
                            'critical' => 'Critical (严重)',
                            'alert' => 'Alert (警报)',
                            'emergency' => 'Emergency (紧急)',
                        ])
                        ->default('info')
                        ->helperText('设置系统记录日志的最低级别，只有等于或高于该级别的日志才会被记录。生产环境建议设置为info或更高，以减少服务器性能开销。'),
                ]),
            Section::make('佣金设置')
                ->description('设置不同级别的邀请佣金金额')
                ->columns(2)
                ->schema([
                    Components\TextInput::make('level1_commission_amount')
                        ->label('一级邀请奖励佣金(USDT)')
                        ->required()
                        ->numeric()
                        ->minValue(0.0001)
                        ->default(0.08),

                    Components\TextInput::make('level2_commission_amount')
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
                    Components\TextInput::make('items_per_update')
                        ->label('每次更新多少个资源(chats/messages)')
                        ->required()
                        ->numeric()
                        ->minValue(1)
                        ->default(1)
                        ->helperText('设置每秒最多可以更新多少个资源，建议不要超过10'),
                    Components\TextInput::make('update_interval_minutes')
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
