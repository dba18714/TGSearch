<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.level1_commission_amount', 0.08);
        $this->migrator->add('general.level2_commission_amount', 0.02);
    }
};