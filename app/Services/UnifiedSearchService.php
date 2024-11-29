<?php

namespace App\Services;

use App\Models\UnifiedSearch;

class UnifiedSearchService
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
        $page = isset($options['page']) ? (int)$options['page'] : null;

        $result =  $builder->paginate((int)$perPage, 'page', $page);

        $unified_searchables = [];
        foreach ($result->items() as $item) {
            $unified_searchables[] = $item->unified_searchable;
        }
        app(ImpressionStatsService::class)->recordBulkImpressions($unified_searchables, 'search_result');

        return $result;
    }
}
