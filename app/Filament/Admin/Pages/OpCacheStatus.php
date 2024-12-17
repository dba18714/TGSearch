<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class OpCacheStatus extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'OpCache Status';
    protected static ?string $navigationGroup = '监控';
    protected static ?string $title = 'OpCache Status';
    protected static ?string $slug = 'opcache-status';
    
    protected static string $view = 'filament.admin.pages.opcache-status';

    public function getViewData(): array
    {
        $opcacheStatus = opcache_get_status();
        $opcacheConfig = opcache_get_configuration();

        return [
            'status' => $this->formatOpcacheData($opcacheStatus),
            'config' => $this->formatOpcacheData($opcacheConfig),
            'memoryUsage' => $this->getMemoryUsageData($opcacheStatus),
        ];
    }

    protected function formatOpcacheData($data): string
    {
        return (new HtmlString(
            '<pre>' . print_r($data, true) . '</pre>'
        ));
    }

    protected function getMemoryUsageData($status): array
    {
        if (!$status || !isset($status['memory_usage'])) {
            return [];
        }

        $memory = $status['memory_usage'];
        
        return [
            'used' => $this->formatBytes($memory['used_memory']),
            'free' => $this->formatBytes($memory['free_memory']),
            'wasted' => $this->formatBytes($memory['wasted_memory']),
            'current_wasted_percentage' => number_format($memory['current_wasted_percentage'], 2) . '%',
        ];
    }

    protected function formatBytes($bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}