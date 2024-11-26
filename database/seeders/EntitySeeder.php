<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        // 创建 50 个随机类型的 Entity
        Entity::factory()->count(2)->create();

        // 确保每种类型至少有 1 个
        Entity::factory()->bot()->count(1)->create();
        Entity::factory()->channel()->count(1)->create();
        Entity::factory()->group()->count(1)->create();
        Entity::factory()->person()->count(1)->create();
    }
}