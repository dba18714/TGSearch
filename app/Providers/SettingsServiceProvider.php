<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try {
            $settings = app(GeneralSettings::class);
            config([
                'services.google.search_api_key' => $settings->google_search_api_key,
                'services.google.search_engine_id' => $settings->google_search_engine_id,
                'services.tencent.secret_id' => $settings->tencent_cloud_secret_id,
                'services.tencent.secret_key' => $settings->tencent_cloud_secret_key,
                'services.openai.api_key' => $settings->openai_api_key,
                'nutgram.token' => $settings->telegram_token,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Settings table not found or migration not run yet: ' . $e->getMessage());
            // 使用环境变量作为后备
            config([
                'services.google.search_api_key' => env('GOOGLE_SEARCH_API_KEY'),
                'services.google.search_engine_id' => env('GOOGLE_SEARCH_ENGINE_ID'),
                'services.tencent.secret_id' => env('TENCENT_CLOUD_SECRET_ID'),
                'services.tencent.secret_key' => env('TENCENT_CLOUD_SECRET_KEY'),
                'services.openai.api_key' => env('OPENAI_API_KEY'),
                'nutgram.token' => env('TELEGRAM_TOKEN'),
            ]);
        }
    }
}