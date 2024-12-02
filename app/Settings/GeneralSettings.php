<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $google_search_api_key;
    public ?string $google_search_engine_id;
    public ?string $tencent_cloud_secret_id;
    public ?string $tencent_cloud_secret_key;
    public ?string $openai_api_key;
    public ?string $telegram_token;

    public static function group(): string
    {
        return 'general';
    }
}