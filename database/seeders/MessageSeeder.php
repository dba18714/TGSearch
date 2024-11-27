<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Chat;
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
        $chats = Chat::factory(20)->create();
        
        // 为每个频道所有者创建一些消息
        foreach ($chats as $chat) {
            // 创建爬虫添加的消息
            Message::factory()
                ->count(1)
                ->crawler()
                ->create([
                    'chat_id' => $chat->id
                ]);

            // 创建手动添加的消息
            Message::factory()
                ->count(1)
                ->manual()
                ->create([
                    'chat_id' => $chat->id,
                    'user_id' => $users->random()->id
                ]);

            // 创建一些已验证的消息
            Message::factory()
                ->count(1)
                ->verified()
                ->create([
                    'chat_id' => $chat->id
                ]);
        }
    }
}