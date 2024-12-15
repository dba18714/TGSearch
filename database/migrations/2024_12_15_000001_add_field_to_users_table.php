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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('tg_id')->nullable();
            $table->decimal('balance', 14, 6)->default(0)->comment('用户余额');
            $table->decimal('commission_balance', 14, 6)->default(0)->comment('佣金余额');
            $table->decimal('total_commission', 10, 2)->default(0)->comment('累计获得佣金');
            $table->unsignedInteger('invite_count')->default(0)->comment('邀请人数');
            $table->unsignedBigInteger('prev_id')->nullable()->comment('邀请人(上级)ID');
            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'tg_id',
                'balance',
                'commission_balance',
                'total_commission',
                'invite_count',
                'prev_id',
                'last_login_at'
            ]);
        });
    }
};
