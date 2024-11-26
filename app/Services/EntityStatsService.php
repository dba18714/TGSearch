<?php

namespace App\Services;

use App\Models\Entity;
use App\Models\EntityImpression;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EntityStatsService
{
    /**
     * 获取文章的曝光统计数据
     */
    public function getImpressionStats(Entity $entity, string $timezone, int $days = 7): Collection
    {
        $userNow = now()->timezone($timezone);
        $todayStr = $userNow->format('Y-m-d');
        
        $todayStats = $this->getTodayImpressions($entity, $timezone);
        $historyStats = $this->getHistoryImpressions($entity, $timezone, $days - 1);
        
        return collect($historyStats)->merge([$todayStr => $todayStats])->sortKeys();
    }

    /**
     * 获取今天的曝光统计
     */
    private function getTodayImpressions(Entity $entity, string $timezone): int
    {
        $cacheKey = "entity:{$entity->id}:impressions:today:{$timezone}";
        
        return cache()->remember(
            $cacheKey, 
            now()->addMinutes(5), 
            function () use ($entity, $timezone): int {
                $userNow = now()->timezone($timezone);
                $todayStart = Carbon::parse($userNow->format('Y-m-d 00:00:00'), $timezone)->utc();
                $todayEnd = Carbon::parse($userNow->format('Y-m-d 23:59:59'), $timezone)->utc();
                
                return EntityImpression::where('entity_id', $entity->id)
                    ->whereBetween('impressed_at', [$todayStart, $todayEnd])
                    ->count();
            }
        );
    }

    /**
     * 获取历史曝光统计
     */
    private function getHistoryImpressions(Entity $entity, string $timezone, int $days): array
    {
        if ($days <= 0) {
            return [];
        }

        $cacheKey = "entity:{$entity->id}:impressions:history:{$timezone}:{$days}";
        
        return Cache::remember(
            $cacheKey, 
            now()->endOfDay(), 
            function () use ($entity, $timezone, $days): array {
                $userNow = now()->timezone($timezone);
                $result = [];
                
                $earliestDate = $userNow->copy()->subDays($days);
                $yesterdayEnd = $userNow->copy()->subDay()->endOfDay();
                
                $utcStart = Carbon::parse($earliestDate->format('Y-m-d 00:00:00'), $timezone)->utc();
                $utcEnd = Carbon::parse($yesterdayEnd->format('Y-m-d 23:59:59'), $timezone)->utc();

                $impressions = EntityImpression::where('entity_id', $entity->id)
                    ->whereBetween('impressed_at', [$utcStart, $utcEnd])
                    ->get(['impressed_at']);

                for ($i = 1; $i <= $days; $i++) {
                    $userDate = $userNow->copy()->subDays($i);
                    $localDateStr = $userDate->format('Y-m-d');
                    
                    $dayStart = Carbon::parse("$localDateStr 00:00:00", $timezone)->utc();
                    $dayEnd = Carbon::parse("$localDateStr 23:59:59", $timezone)->utc();

                    $count = $impressions->whereBetween('impressed_at', [$dayStart, $dayEnd])->count();
                    $result[$localDateStr] = $count;
                }

                return $result;
            }
        );
    }

    /**
     * 记录文章曝光
     */
    public function recordImpression(
        Entity $entity, 
        ?string $source = null, 
        ?string $userId = null
    ): EntityImpression {
        return EntityImpression::create([
            'entity_id' => $entity->id,
            'impressed_at' => now(),
            'source' => $source,
            'user_id' => $userId,
            'session_id' => session()->getId()
        ]);
    }

    /**
     * 批量记录文章曝光
     */
    public function recordBulkImpressions(
        array $entities, 
        ?string $source = null, 
        ?string $userId = null
    ): bool {
        $now = now();
        $sessionId = session()->getId();
        
        $records = collect($entities)->map(function ($entity) use ($now, $source, $userId, $sessionId) {
            return [
                'entity_id' => $entity->id,
                'impressed_at' => $now,
                'source' => $source,
                'user_id' => $userId,
                'session_id' => $sessionId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        })->toArray();

        return EntityImpression::insert($records);
    }
}
