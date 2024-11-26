<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityImpression extends Model
{
    protected $fillable = [
        'entity_id',
        'impressed_at',
    ];

    protected $casts = [
        'impressed_at' => 'datetime',
    ];

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class);
    }
}
