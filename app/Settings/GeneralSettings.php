<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $log_level = null;

    public ?float $level1_commission_amount;
    public ?float $level2_commission_amount;

    public ?bool $new_links_update_enabled;
    public ?bool $existing_links_update_enabled;
    public ?int $items_per_update;
    public ?int $update_interval_minutes;

    public ?string $content_audit_driver;
    public ?bool $content_changed_audit_enabled;
    public ?bool $existing_content_audit_enabled;
    public ?int $audit_items_per_update;
    public ?int $audit_interval_hours;

    public static function group(): string
    {
        return 'general';
    }
}