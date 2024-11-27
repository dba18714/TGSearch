<?php

namespace Database\Seeders;

use App\Models\Chat;
use Illuminate\Database\Seeder;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        // 创建 50 个随机类型的 Chat
        Chat::factory()->count(2)->create();

        // 确保每种类型至少有 1 个
        Chat::factory()->bot()->count(1)->create();
        Chat::factory()->channel()->count(1)->create();
        Chat::factory()->group()->count(1)->create();
        Chat::factory()->person()->count(1)->create();
    }
}