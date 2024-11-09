<?php

namespace Database\Seeders;

use App\Models\TelegramLink;
use Illuminate\Database\Seeder;

class TelegramLinkSeeder extends Seeder
{
    public function run(): void
    {
        // 创建 50 个随机类型的 TelegramLink
        TelegramLink::factory()->count(50)->create();

        // 确保每种类型至少有 5 个
        TelegramLink::factory()->bot()->count(5)->create();
        TelegramLink::factory()->channel()->count(5)->create();
        TelegramLink::factory()->group()->count(5)->create();
        TelegramLink::factory()->person()->count(5)->create();
    }
}