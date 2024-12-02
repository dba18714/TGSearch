<?php

namespace App\Filament\Admin\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;

class ManageGeneralSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = '系统设置';
    protected static ?string $title = '系统设置';

    protected static string $settings = GeneralSettings::class;

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('google_search_api_key')
                ->label('Google Search API Key')
                ->required(),
            TextInput::make('google_search_engine_id')
                ->label('Google Search Engine ID')
                ->required(),
            TextInput::make('tencent_cloud_secret_id')
                ->label('腾讯云 Secret ID')
                ->required(),
            TextInput::make('tencent_cloud_secret_key')
                ->label('腾讯云 Secret Key')
                ->required(),
            TextInput::make('openai_api_key')
                ->label('OpenAI API Key')
                ->required(),
            TextInput::make('telegram_token')
                ->label('Telegram Bot Token')
                ->required(),
        ];
    }
}