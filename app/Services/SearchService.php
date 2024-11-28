<?php

namespace App\Services;

use App\Models\UnifiedSearch;

class SearchService
{
    public function search(string $query, array $filters = [], array $options = [])
    {
        $query = trim($query);
        $builder = UnifiedSearch::search($query);

        foreach ($filters as $key => $value) {
            $builder->where($key, $value);
        }

        if (!empty($options['sort'])) {
            $builder->orderBy($options['sort'], $options['direction'] ?? 'desc');
        }

        $perPage = $options['per_page'] ?? 10;
        $page = $options['page'] ?? 1;

        $result =  $builder->paginate($perPage, 'page', $page);

        $unified_searchables = [];
        foreach ($result->items() as $item) {
            $unified_searchables[] = $item->unified_searchable;
        }
        app(ImpressionStatsService::class)->recordBulkImpressions($unified_searchables, 'search_result');

        return $result;
    }
}