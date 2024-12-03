<?php

namespace App\Providers;

use App\Settings\ServiceSettings;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        return;
        $settings = app(ServiceSettings::class);
        config([
            'xxx.xxx' => $settings->xxx ?: 'xxx',
        ]);
    }
}
