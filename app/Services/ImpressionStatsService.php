<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Impression;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ImpressionStatsService
{
    public function getImpressionStats($impressionable, string $timezone, int $days = 7): Collection
    {
        $userNow = now()->timezone($timezone);
        $todayStr = $userNow->format('Y-m-d');

        $todayStats = $this->getTodayImpressions($impressionable, $timezone);
        $historyStats = $this->getHistoryImpressions($impressionable, $timezone, $days - 1);

        return collect($historyStats)->merge([$todayStr => $todayStats])->sortKeys();
    }

    private function getTodayImpressions($impressionable, string $timezone): int
    {
        $cacheKey = sprintf(
            "%s:%s:impressions:today:%s",
            $impressionable->getMorphClass(),
            $impressionable->id,
            $timezone
        );

        return cache()->remember(
            $cacheKey,
            now()->addMinutes(5),
            function () use ($impressionable, $timezone): int {
                $userNow = now()->timezone($timezone);
                $todayStart = Carbon::parse($userNow->format('Y-m-d 00:00:00'), $timezone)->utc();
                $todayEnd = Carbon::parse($userNow->format('Y-m-d 23:59:59'), $timezone)->utc();

                return $impressionable->impressions()
                    ->whereBetween('impressed_at', [$todayStart, $todayEnd])
                    ->count();
            }
        );
    }

    private function getHistoryImpressions($impressionable, string $timezone, int $days): array
    {
        if ($days <= 0) {
            return [];
        }

        $cacheKey = sprintf(
            "%s:%s:impressions:history:%s:%d",
            $impressionable->getMorphClass(),
            $impressionable->id,
            $timezone,
            $days
        );

        return Cache::remember(
            $cacheKey,
            now()->endOfDay(),
            function () use ($impressionable, $timezone, $days): array {
                $userNow = now()->timezone($timezone);
                $result = [];

                $earliestDate = $userNow->copy()->subDays($days);
                $yesterdayEnd = $userNow->copy()->subDay()->endOfDay();

                $utcStart = Carbon::parse($earliestDate->format('Y-m-d 00:00:00'), $timezone)->utc();
                $utcEnd = Carbon::parse($yesterdayEnd->format('Y-m-d 23:59:59'), $timezone)->utc();

                $impressions = $impressionable->impressions()
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

    public function recordImpression(Model $impressionable, string $source): Impression
    {
        return $impressionable->impressions()->create([
            'source' => $source,
            'impressed_at' => now(),
        ]);
    }

    public function recordBulkImpressions(array $impressionables, string $source): bool
    {
        $now = now();

        $records = collect($impressionables)->map(function ($impressionable) use ($now, $source) {
            return [
                'impressionable_type' => $impressionable->getMorphClass(),
                'impressionable_id' => $impressionable->id,
                'source' => $source,
                'impressed_at' => $now,
            ];
        })->toArray();

        return Impression::insert($records);
    }
}
