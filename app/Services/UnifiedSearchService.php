<?php

namespace App\Services;

use App\Models\UnifiedSearch;

class UnifiedSearchService
{
    public function search(string $query, array $filters = [], array $options = [], $excludeIds = [])
    {
        $query = trim($query);
        $builder = UnifiedSearch::search($query, function($meilisearch, $query, $options) use ($excludeIds) {
            $validIds = array_filter($excludeIds, function($id) {
                return $id !== null && $id !== '';
            });
            if (!empty($validIds)) {
                $options['filter'] = ['id NOT IN [' . implode(',', $validIds) . ']'];
            }
            return $meilisearch->search($query, $options);
        });

        foreach ($filters as $key => $value) {
            $builder->where($key, $value);
        }

        if (!empty($options['sort'])) {
            $builder->orderBy($options['sort'], $options['direction'] ?? 'desc');
        }

        $perPage = $options['per_page'] ?? 10;
        $page = isset($options['page']) ? (int)$options['page'] : null;

        $results =  $builder->paginate((int)$perPage, 'page', $page);

        $unified_searchables = [];
        foreach ($results->items() as $item) {
            $unified_searchables[] = $item->unified_searchable;
        }
        app(ImpressionStatsService::class)->recordBulkImpressions($unified_searchables, 'search_result');

        return $results;
    }
}
