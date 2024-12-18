<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ServiceSettings extends Settings
{
    public ?string $google_search_api_key = null;
    public ?string $google_search_engine_id = null;
    public ?string $tencent_cloud_secret_id = null;
    public ?string $tencent_cloud_secret_key = null;
    public ?string $openai_api_key = null;
    public ?string $telegram_token = null;

    public static function group(): string
    {
        return 'service';
    }
}