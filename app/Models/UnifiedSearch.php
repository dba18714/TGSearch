<?php
namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    public function unified_searchable(): MorphTo
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
}
