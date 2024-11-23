<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\StatsOverviewWidget;

class Dashboard extends BaseDashboard
{
    // public function getHeaderWidgets(): array
    // {
    //     return [
    //         StatsOverviewWidget::class,
    //     ];
    // }

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }
}
