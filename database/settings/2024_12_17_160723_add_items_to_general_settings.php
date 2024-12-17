<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.items_per_update', 1);
        $this->migrator->add('general.update_interval_minutes', 60);
    }
};
