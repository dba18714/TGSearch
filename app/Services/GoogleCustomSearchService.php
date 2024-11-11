<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Factory as HttpFactory;

class GoogleCustomSearchService
{
    protected $apiKey;
    protected $cx;
    protected $http;

    public function __construct(HttpFactory $http, string $apiKey, string $cx)
    {
        $this->http = $http;
        $this->apiKey = $apiKey;
        $this->cx = $cx;
    }

    public function search($query)
    {
        $response = $this->http->get('https://customsearch.googleapis.com/customsearch/v1', [
            'cx' => $this->cx,
            'q' => $query,
            'key' => $this->apiKey,
        ]);

        if ($response->failed()) {
            \Log::error('Google Custom Search API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $data = $response->json();

        if (!is_array($data)) {
            \Log::error('Google Custom Search API returned invalid JSON', [
                'data' => $data,
            ]);
            return [];
        }

        if (!isset($data['items'])) {
            \Log::error('Google Custom Search API did not return any items', [
                'data' => $data,
            ]);
            return [];
        }

        return $data['items'];
    }
}
