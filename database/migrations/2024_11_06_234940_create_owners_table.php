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
        Schema::create('owners', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('name')->nullable();
            $table->string('introduction')->nullable();
            // $table->text('message')->nullable();
            // $table->string('url')->unique();
            $table->string('type')->index()->nullable()->comment('bot or channel or group or person');
            $table->string('username')->unique();
            $table->integer('member_count')->nullable();
            $table->integer('photo_count')->nullable();
            $table->integer('video_count')->nullable();
            $table->integer('file_count')->nullable();
            $table->integer('link_count')->nullable();

            // $table->integer('view_count')->default(0);
            // $table->boolean('is_by_user')->default(false)->comment('true: 由用户添加 or false: 由爬虫添加');
            $table->string('source')->index()->default('crawler')->comment('manual: 由用户手动添加 or crawler: 由爬虫添加');
            $table->string('source_str')->nullable()->comment('未经过解析的原始 url or username');
            $table->ulid('user_id')->index()->nullable()->comment('由哪个用户添加的链接, 如果是爬虫或游客添加则为空');
            // TODO is_valid 重命名为 is_active
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
        Schema::dropIfExists('owners');
    }
};
