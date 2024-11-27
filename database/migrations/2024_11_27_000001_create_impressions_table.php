<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impressions', function (Blueprint $table) {
            $table->id();
            // $table->ulid('chat_id')->index();
            $table->ulidMorphs('impressionable');
            $table->string('source')->index()->comment('在哪里被展示，比如: search_result');
            $table->timestamp('impressed_at')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impressions');
    }
};
