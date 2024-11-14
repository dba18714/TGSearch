<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    public function run(): void
    {
        // 创建 50 个随机类型的 Owner
        Owner::factory()->count(2)->create();

        // 确保每种类型至少有 1 个
        Owner::factory()->bot()->count(1)->create();
        Owner::factory()->channel()->count(1)->create();
        Owner::factory()->group()->count(1)->create();
        Owner::factory()->person()->count(1)->create();
    }
}