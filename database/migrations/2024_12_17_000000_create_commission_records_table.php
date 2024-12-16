<?php

use App\Enums\CommissionLevel;
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
            $table->enum('level', array_column(CommissionLevel::cases(), 'value'))
                ->comment('代理层级 1=直接邀请 2=二级代理');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('commission_records');
    }
};