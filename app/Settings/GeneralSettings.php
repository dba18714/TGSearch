<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?float $level1_commission_amount = 0.02;
    public ?float $level2_commission_amount = 0.01;

    public static function group(): string
    {
        return 'general';
    }
}