<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 先修改 password 为可空
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('tg_id')->nullable()->unique();
            $table->ulid('parent_id')->nullable()->index()->comment('邀请人(即上级)的ID');
            $table->decimal('balance', 12, 4)->default(0)->index()->comment('用户余额');
            $table->decimal('commission_balance', 12, 4)->default(0)->index()->comment('佣金余额');
            $table->decimal('total_commission', 12, 4)->default(0)->index()->comment('累计获得佣金');
            $table->timestamp('last_login_at')->nullable()->index()->comment('最后登录时间');

            $table->unique(['id', 'parent_id']);
        });

        // PostgreSQL CHECK 约束
        DB::statement('
            ALTER TABLE users
            ADD CONSTRAINT chk_user_auth 
            CHECK (
                (password IS NOT NULL) OR (tg_id IS NOT NULL)
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 在 PostgreSQL 中删除约束
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstraint('chk_user_auth');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tg_id',
                'balance',
                'commission_balance',
                'total_commission',
                'parent_id',
                'last_login_at'
            ]);
        });

        // 恢复 password 为必填
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
        });
    }
};
