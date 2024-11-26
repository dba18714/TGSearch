<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Message;
use App\Models\Entity;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('机器人', Entity::where('type', 'bot')->count())
                ->description('机器人链接数量')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('info'),

            Stat::make('频道', Entity::where('type', 'channel')->count())
                ->description('频道链接数量')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('info'),

            Stat::make('群组', Entity::where('type', 'group')->count())
                ->description('群组链接数量')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('消息', Message::count())
                ->description('消息链接数量')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),

            Stat::make('总帐号数', Entity::count())
                ->description('所有提交的帐号总数')
                ->descriptionIcon('heroicon-m-link')
                ->color('success'),

            Stat::make('总消息数', Message::count())
                ->description('所有提交的消息总数')
                ->descriptionIcon('heroicon-m-link')
                ->color('success'),

            Stat::make('待审核', Entity::where('is_valid', false)->count())
                ->description('等待审核的链接数量')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('用户提交', Entity::where('source', 'manual')->count())
                ->description('用户提交的链接数量')
                ->descriptionIcon('heroicon-m-user')
                ->color('primary'),
        ];
    }
}
