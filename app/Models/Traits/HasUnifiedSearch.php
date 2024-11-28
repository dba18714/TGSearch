<?php

namespace App\Models\Traits;

use App\Models\UnifiedSearch;

trait HasUnifiedSearch
{
    public static function bootHasUnifiedSearch()
    {
        static::saved(function ($model) {
            $model->unifiedSearch()->updateOrCreate(
                [],
                $model->toUnifiedSearchArray()
            );
        });

        static::deleted(function ($model) {
            $model->unifiedSearch?->delete();
        });
    }

    public function unifiedSearch()
    {
        return $this->morphOne(UnifiedSearch::class, 'unified_searchable');
    }

    abstract public function toUnifiedSearchArray(): array;
}
