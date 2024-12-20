<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tg_groups', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name');
            $table->bigInteger('source_id')->unique()->comment('Telegram 原始群组 ID');

            // TODO 用于记录由谁拉进群，目前用于搜索佣金发放
            $table->ulid('user_id')->index()->comment('用于记录由谁拉进群，目前用于搜索佣金发放');
            
            // 设置
            $table->boolean('direct_search_enabled')->index()->default(true)->comment('是否开启关键词直接搜索');
            $table->boolean('command_search_enabled')->index()->default(true)->comment('是否开启 /q +关键词搜索');

            $table->timestamp('bot_joined_at')->index()->comment('机器人首次加入群组的时间');
            $table->timestamp('bot_left_at')->index()->nullable()->comment('机器人离开群组的时间，如果再次被拉进群，则应该设置为null');
            
            $table->timestamps();
        });

        Schema::create('tg_group_user', function (Blueprint $table) {
            $table->id();
            $table->ulid('tg_group_id');
            $table->ulid('user_id');
            $table->enum('role', ['member', 'admin'])->index()->default('member')->comment('角色: member, admin');
            $table->timestamps();

            $table->unique(['tg_group_id', 'user_id']);
            $table->index(['tg_group_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tg_group_user');
        Schema::dropIfExists('tg_groups');
    }
};