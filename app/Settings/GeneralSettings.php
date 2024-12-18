<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?float $level1_commission_amount = 0.08;
    public ?float $level2_commission_amount = 0.02;

    // 设置每次更新 N 个资源(chats/messages)
    public ?int $items_per_update = 1;

    // 设置同一个资源至少间隔多久更新一次
    public ?int $update_interval_minutes = 60;

    public static function group(): string
    {
        return 'general';
    }
}