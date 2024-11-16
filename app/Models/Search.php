<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword',
        'searched_count',
        'ip_history',
        'last_searched_at',
    ];

    protected $casts = [
        'last_searched_at' => 'datetime',
        'ip_history' => 'array',
    ];

    /**
     * 记录搜索
     */
    public static function recordSearch(string $keyword): void
    {
        if (empty(trim($keyword))) {
            return;
        }

        $search = static::firstOrNew(['keyword' => $keyword]);

        $currentIp = request()->ip();
        $ipHistory = $search->ip_history ?? [];

        // 如果当前IP与最后一个记录的IP不同，才添加新记录
        if (empty($ipHistory) || end($ipHistory) !== $currentIp) {
            // 添加新IP到历史记录
            $ipHistory[] = $currentIp;
            // 只保留最后5个记录
            $ipHistory = array_slice($ipHistory, -5);
        }

        $search->searched_count = ($search->searched_count ?? 0) + 1;
        $search->ip_history = $ipHistory;
        $search->last_searched_at = now();

        $search->save();
    }
}
