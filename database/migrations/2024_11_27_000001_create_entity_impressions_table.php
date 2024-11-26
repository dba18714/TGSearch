<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entity_impressions', function (Blueprint $table) {
            $table->id();
            $table->ulid('entity_id')->index();
            $table->timestamp('impressed_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entity_impressions');
    }
};
