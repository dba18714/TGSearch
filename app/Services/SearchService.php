<?php

namespace App\Services;

use App\Models\UnifiedSearch;

class SearchService
{
    public function search(string $query, array $filters = [], array $options = [])
    {
        $builder = UnifiedSearch::search($query);

        foreach ($filters as $key => $value) {
            $builder->where($key, $value);
        }

        if (!empty($options['sort'])) {
            $builder->orderBy($options['sort'], $options['direction'] ?? 'desc');
        }

        $perPage = $options['per_page'] ?? 15;
        $page = $options['page'] ?? 1;

        return $builder->paginate($perPage, 'page', $page);
    }
}