<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('commission_records', function (Blueprint $table) {
            $table->id();
            $table->ulid('user_id')->index();
            $table->ulid('invitee_id')->index();
            $table->decimal('amount', 8, 4);
            $table->enum('level', [1,2])
                ->comment('代理层级 1=一级邀请 2=二级邀请');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commission_records');
    }
};