<?php

use App\Providers\SettingsServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    SettingsServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\HorizonServiceProvider::class,
    App\ContentAudit\ContentAuditServiceProvider::class,
];
