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
        Schema::table('chats', function (Blueprint $table) {
            $table->boolean('audit_passed')->default(false);
            $table->decimal('audit_score', 7, 6)->default(0);
            $table->timestamp('audited_at')->index()->nullable();
            $table->timestamp('audit_started_at')->index()->nullable()->comment('审计开始时间，不管是否审计成功');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('audit_passed')->default(false);
            $table->decimal('audit_score', 7, 6)->default(0);
            $table->timestamp('audited_at')->index()->nullable();
            $table->timestamp('audit_started_at')->index()->nullable()->comment('审计开始时间，不管是否审计成功');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropColumn('audit_score');
            $table->dropColumn('audited_at');
            $table->dropColumn('audit_started_at');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('audit_score');
            $table->dropColumn('audited_at');
            $table->dropColumn('audit_started_at');
        });
    }
};
