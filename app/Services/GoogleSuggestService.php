<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleSuggestService
{
    /**
     * 获取Google搜索建议
     */
    public function getSuggestions(string $query): array
    {
        if (empty(trim($query))) {
            return [];
        }

        $cacheDuration = config('app.debug') ? now()->addSeconds(0) : now()->addMinutes(5);
        return Cache::remember(
            "google_suggest_{$query}",
            $cacheDuration,
            function () use ($query) {
                try {
                    $response = Http::get('https://suggestqueries.google.com/complete/search', [
                        'client' => 'chrome',
                        'q' => $query,
                    ]);

                    // app('debugbar')->debug('$response->json()', $response->body() ?? []);


                    if ($response->successful()) {
                        // app('debugbar')->debug('$response->json()', $response->json() ?? []);
                        Log::debug('$response->json()', $response->json() ?? []);
                        return $response->json()[1] ?? [];
                    }

                    Log::warning('Google Suggest API request failed', [
                        'query' => $query,
                        'status' => $response->status()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Google Suggest API error', [
                        'query' => $query,
                        'error' => $e->getMessage()
                    ]);
                }

                return [];
            }
        );
    }
}
