<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.new_links_update_enabled', true);
        $this->migrator->add('general.existing_links_update_enabled', true);

        $this->migrator->add('general.content_audit_driver', config('content-audit.default', 'tencent'));
        $this->migrator->add('general.content_changed_audit_enabled', true);
        $this->migrator->add('general.existing_content_audit_enabled', false);
        $this->migrator->add('general.audit_items_per_update', 1);
        $this->migrator->add('general.audit_interval_hours', 24*30);
    }
};
