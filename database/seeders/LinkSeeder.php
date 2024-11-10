<?php

namespace Database\Seeders;

use App\Models\Link;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    public function run(): void
    {
        // 创建 50 个随机类型的 Link
        Link::factory()->count(2000)->create();

        // 确保每种类型至少有 5 个
        Link::factory()->bot()->count(5)->create();
        Link::factory()->channel()->count(5)->create();
        Link::factory()->group()->count(5)->create();
        Link::factory()->person()->count(5)->create();
    }
}