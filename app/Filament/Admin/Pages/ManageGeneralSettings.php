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
                        ->label('系统日志级别')
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
                        ->minValue(0.0001),

                    Components\TextInput::make('level2_commission_amount')
                        ->label('二级邀请奖励佣金(USDT)')
                        ->required()
                        ->numeric()
                        ->minValue(0.0001),
                ]),
            Section::make('Chats/Messages 链接更新设置')
                ->description('控制爬虫更新频率和行为')
                ->schema([
                    Components\Toggle::make('new_links_update_enabled')
                        ->label('启用新链接更新')
                        ->helperText('控制是否自动更新首次收录的新链接'),

                    Components\Fieldset::make('已有链接更新设置')
                        ->schema([
                            Components\Toggle::make('existing_links_update_enabled')
                                ->label('启用已有链接更新')
                                ->helperText('控制是否定期更新已收录的旧链接')
                                ->reactive(),

                            Components\Grid::make(2)
                                ->schema([
                                    Components\TextInput::make('items_per_update')
                                        ->label('每次更新多少个资源(chats/messages)')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->helperText('设置已有链接重新更新每秒更新多少个链接，建议不要超过10')
                                        ->disabled(fn(callable $get) => ! $get('existing_links_update_enabled'))
                                        ->dehydrated(fn(callable $get) => $get('existing_links_update_enabled')),

                                    Components\TextInput::make('update_interval_minutes')
                                        ->label('更新间隔')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->helperText('设置已有链接重新更新的最小时间间隔（分钟）')
                                        ->disabled(fn(callable $get) => ! $get('existing_links_update_enabled'))
                                        ->dehydrated(fn(callable $get) => $get('existing_links_update_enabled')),
                                ]),
                        ]),

                ]),

            Section::make('内容审查设置')
                ->description('控制内容审查的行为和频率')
                ->schema([
                    Components\Select::make('content_audit_driver')
                        ->label('内容审查驱动')
                        ->required()
                        ->options([
                            'tencent' => '腾讯云内容安全',
                            'openai' => 'OpenAI 审核',
                        ])
                        ->helperText('选择内容审查服务提供商，确保在第三方服务设置中配置了对应的密钥'),

                    Components\Toggle::make('content_changed_audit_enabled')
                        ->label('启用内容变更审查')
                        ->helperText('当内容发生变更时（如名称、简介、消息等），是否自动执行内容审查'),

                    Components\Fieldset::make('定期审查设置')
                        ->schema([
                            Components\Toggle::make('existing_content_audit_enabled')
                                ->label('启用定期重新审查')
                                ->helperText('是否定期对已有内容进行重新审查')
                                ->reactive(),

                            Components\Grid::make(2)
                                ->schema([
                                    Components\TextInput::make('audit_items_per_update')
                                        ->label('批量审查数量')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->helperText('每秒执行审查的资源数量，建议不要超过10')
                                        ->disabled(fn(callable $get) => ! $get('existing_content_audit_enabled'))
                                        ->dehydrated(fn(callable $get) => $get('existing_content_audit_enabled')),

                                    Components\TextInput::make('audit_interval_hours')
                                        ->label('审查间隔')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->helperText('已审查内容的重新审查间隔（小时）')
                                        ->disabled(fn(callable $get) => ! $get('existing_content_audit_enabled'))
                                        ->dehydrated(fn(callable $get) => $get('existing_content_audit_enabled')),
                                ]),
                        ]),
                ]),
        ];
    }
}
