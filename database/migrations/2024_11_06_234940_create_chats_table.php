<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

enum ChatType: string
{
    case BOT = 'bot';
    case CHANNEL = 'channel';
    case GROUP = 'group';
    case PERSON = 'person';
}

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->nullable();
            $table->text('introduction')->nullable();
            $table->enum('type', array_column(ChatType::cases(), 'value'))
                ->index()->nullable()
                ->comment(implode(' or ', array_column(ChatType::cases(), 'value')));
            $table->string('username')->unique();
            $table->integer('member_count')->nullable();
            $table->integer('photo_count')->nullable();
            $table->integer('video_count')->nullable();
            $table->integer('file_count')->nullable();
            $table->integer('link_count')->nullable();
            $table->enum('source', ['manual', 'crawler'])->index()->default('crawler')->comment('manual: 由用户手动添加 or crawler: 由爬虫添加');
            $table->string('source_str')->nullable()->comment('未经过解析的原始 url or username');
            $table->ulid('user_id')->index()->nullable()->comment('由哪个用户添加的链接, 如果是爬虫或游客添加则为空');
            $table->boolean('is_valid')->default(false)->comment('是否有效');
            $table->timestamp('verified_at')->index()->nullable();
            $table->timestamp('verified_start_at')->index()->nullable()->comment('验证开始时间，不管是否验证成功');
            $table->timestamps();

            $table->index(['type', 'is_valid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
