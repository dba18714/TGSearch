<?php

namespace App\Enums;

enum CommissionLevel: int
{
    case DIRECT = 1;    // 直接邀请
    case INDIRECT = 2;  // 二级代理

    public function label(): string
    {
        return match($this) {
            self::DIRECT => '直接邀请',
            self::INDIRECT => '二级代理',
        };
    }
}