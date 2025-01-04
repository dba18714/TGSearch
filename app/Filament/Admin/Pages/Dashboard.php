<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Admin\Widgets\StatsOverviewWidget;
use Filament\Support\Enums\MaxWidth;

class Dashboard extends BaseDashboard
{
    // public function getHeaderWidgets(): array
    // {
    //     return [
    //         StatsOverviewWidget::class,
    //     ];
    // }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::SevenExtraLarge;
    }

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
        ];
    }
}
