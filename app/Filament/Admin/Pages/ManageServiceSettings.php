<?php

namespace App\Filament\Admin\Pages;

use App\Settings\ServiceSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\MaxWidth;

class ManageServiceSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = '系统管理';
    protected static ?string $navigationLabel = '第三方服务设置';
    protected static ?string $title = '第三方服务设置';

    protected static string $settings = ServiceSettings::class;

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::SevenExtraLarge;
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Google')
                ->columns(2)
                ->schema([
                    TextInput::make('google_search_api_key')
                        ->label('Google Search API Key'),
                    TextInput::make('google_search_engine_id')
                        ->label('Google Search Engine ID')
                ]),
            Section::make('腾讯云')
                ->columns(2)
                ->schema([
                    TextInput::make('tencent_cloud_secret_id')
                        ->label('腾讯云 Secret ID'),
                    TextInput::make('tencent_cloud_secret_key')
                        ->label('腾讯云 Secret Key'),
                ]),
            Section::make('OpenAI')
                ->columns(2)
                ->schema([
                    TextInput::make('openai_base_uri')
                        ->label('OpenAI BASE URI'),
                    TextInput::make('openai_api_key')
                        ->label('OpenAI API Key'),
                ]),
            Section::make('Telegram')
                ->columns(2)
                ->schema([
                    TextInput::make('telegram_token')
                        ->label('Telegram Bot Token')
                        ->required()
                        ->helperText('如果不设置请随便填写任何字符，因为如果空值系统会出错'),
                ])
        ];
    }
}
