<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unified_searches', function (Blueprint $table) {
            $table->id();
            $table->text('content')->nullable();
            $table->string('type')->comment('bot/channel/group/person/message');
            $table->integer('member_or_view_count')->nullable();
            // $table->ulidMorphs('unified_searchable'); // 默认的索引名称太长了会导致mysql报错
            $table->ulid('unified_searchable_id');
            $table->string('unified_searchable_type');
            $table->index(
                ['unified_searchable_type', 'unified_searchable_id'],
                'unified_search_morph_idx' // 使用更短的索引名
            );
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unified_searches');
    }
};
