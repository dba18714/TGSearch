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
        Schema::create('search_records', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->unique();
            $table->integer('searched_count')->default(0)->index();
            $table->json('ip_history')->nullable();
            $table->timestamp('last_searched_at')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_records');
    }
};
