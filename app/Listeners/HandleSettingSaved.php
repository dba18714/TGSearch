<?php

namespace App\Listeners;

use App\Jobs\MakeCache;
use App\Settings\GeneralSettings;
use App\Settings\OAuthSettings;
use App\Settings\ServiceSettings;
use App\Settings\SiteSettings;
use App\Utils\Helper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelSettings\Events\SettingsSaved;

class HandleSettingSaved
{
    public function handle(SettingsSaved $event)
    {
        $general = app(GeneralSettings::class);
        $service = app(ServiceSettings::class);
        $maps = [
            [
                'env_name' => 'LOG_LEVEL', 
                'value_old' => config('logging.channels.daily.level'), 
                'value_new' =>  $general->log_level
            ],
            [
                'env_name' => 'GOOGLE_SEARCH_API_KEY', 
                'value_old' => config('services.google.search_api_key'), 
                'value_new' =>  $service->google_search_api_key
            ],
            [
                'env_name' => 'GOOGLE_SEARCH_ENGINE_ID', 
                'value_old' => config('services.google.search_engine_id'), 
                'value_new' =>  $service->google_search_engine_id
            ],
            [
                'env_name' => 'TENCENT_CLOUD_SECRET_ID', 
                'value_old' => config('services.tencent.secret_id'), 
                'value_new' =>  $service->tencent_cloud_secret_id
            ],
            [
                'env_name' => 'TENCENT_CLOUD_SECRET_KEY', 
                'value_old' => config('services.tencent.secret_key'), 
                'value_new' =>  $service->tencent_cloud_secret_key
            ],
            [
                'env_name' => 'OPENAI_API_KEY', 
                'value_old' => config('services.openai.api_key'), 
                'value_new' =>  $service->openai_api_key
            ],
            [
                'env_name' => 'TELEGRAM_TOKEN', 
                'value_old' => config('nutgram.token'), 
                'value_new' =>  $service->telegram_token
            ],
        ];

        $env_has_changed = false;
        foreach ($maps as $map) {
            if ($map['value_old'] !== $map['value_new']) {
                saveToEnv([
                    $map['env_name'] => $map['value_new'],
                ]);
                $env_has_changed = true;
            }
        }
        
        if (App::environment() == 'production') {
            if ($env_has_changed) {
                dispatch(function () {
                    Artisan::call('config:cache');
                });
            }
        }
    }
}
