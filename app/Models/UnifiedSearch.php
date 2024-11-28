<?php
namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UnifiedSearch extends Model
{
    use Searchable;
    use HasFactory;

    // public $timestamps = false;

    protected $fillable = [
        'content',
        'searchable_type',
        'searchable_id',
    ];

    public function unifiedSearchable()
    {
        return $this->morphTo();
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
        ];
    }

    // protected static function booted()
    // {
    //     static::created(function ($model) {
    //         \Log::info('UnifiedSearch created', ['id' => $model->id]);
    //     });

    //     static::saved(function ($model) {
    //         \Log::info('UnifiedSearch saved', ['id' => $model->id]);
    //         // 强制触发同步
    //         $model->searchable();
    //     });
    // }

    // public function searchableAs(): string
    // {
    //     return 'unified_searches';
    // }
}
