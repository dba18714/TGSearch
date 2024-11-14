<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 创建一些用户和频道所有者
        $users = User::factory(3)->create();
        $owners = Owner::factory(5)->create();
        
        // 为每个频道所有者创建一些消息
        foreach ($owners as $owner) {
            // 创建爬虫添加的消息
            Message::factory()
                ->count(5)
                ->crawler()
                ->create([
                    'owner_id' => $owner->id
                ]);

            // 创建手动添加的消息
            Message::factory()
                ->count(3)
                ->manual()
                ->create([
                    'owner_id' => $owner->id,
                    'user_id' => $users->random()->id
                ]);

            // 创建一些已验证的消息
            Message::factory()
                ->count(2)
                ->verified()
                ->create([
                    'owner_id' => $owner->id
                ]);
        }
    }
}