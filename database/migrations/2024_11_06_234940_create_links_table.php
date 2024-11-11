<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // TODO add nullable
        Schema::create('links', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->default('None');
            $table->string('introduction')->default('None');
            $table->string('url')->unique();
            $table->string('type')->default('unknown')->comment('bot or channel or group or person or message');
            $table->string('telegram_username')->unique()->nullable();
            $table->integer('member_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->boolean('is_by_user')->default(false)->comment('true: 由用户添加 or false: 由爬虫添加');
            $table->ulid('user_id')->nullable()->comment('由哪个用户添加的链接, 如果是爬虫或游客添加则为空');
            $table->boolean('is_valid')->default(false)->comment('是否有效');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('verified_start_at')->nullable()->comment('验证开始时间，不管是否验证成功');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
