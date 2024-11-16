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
        'last_searched_at'
    ];

    protected $casts = [
        'last_searched_at' => 'datetime',
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
        
        $search->searched_count = ($search->searched_count ?? 0) + 1;
        $search->last_searched_at = now();
        
        $search->save();
    }
}