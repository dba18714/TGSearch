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
        Schema::create('messages', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('chat_id')->index();
            // TODO original_id rename to source_id
            $table->integer('original_id')->index()->comment('Telegram 的原始消息id');
            $table->text('text')->nullable();
            $table->integer('view_count')->nullable();
            $table->string('source')->index()->default('crawler')->comment('manual: 由用户手动添加 or crawler: 由爬虫添加');
            $table->string('source_str')->nullable()->comment('未经过解析的原始 url or username');
            $table->ulid('user_id')->index()->nullable()->comment('由哪个用户添加的链接, 如果是爬虫或游客添加则为空');
            $table->boolean('is_valid')->default(false)->comment('是否有效');
            $table->timestamp('verified_at')->index()->nullable();
            $table->timestamp('verified_start_at')->index()->nullable()->comment('验证开始时间，不管是否验证成功');
            $table->timestamps();

            $table->unique(['chat_id', 'original_id']);
            $table->index(['chat_id', 'is_valid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
