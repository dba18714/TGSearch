<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('service.google_search_api_key');
        $this->migrator->add('service.google_search_engine_id');
        $this->migrator->add('service.tencent_cloud_secret_id');
        $this->migrator->add('service.tencent_cloud_secret_key');
        $this->migrator->add('service.openai_base_uri', config('services.openai.base_uri'));
        $this->migrator->add('service.openai_api_key');
        $this->migrator->add('service.telegram_token', config('nutgram.token'));
    }
};