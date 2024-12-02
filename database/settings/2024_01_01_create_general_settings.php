<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.google_search_api_key', '');
        $this->migrator->add('general.google_search_engine_id', '');
        $this->migrator->add('general.tencent_cloud_secret_id', '');
        $this->migrator->add('general.tencent_cloud_secret_key', '');
        $this->migrator->add('general.openai_api_key', '');
        $this->migrator->add('general.telegram_token', '');
    }
};