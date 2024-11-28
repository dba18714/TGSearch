<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Impression extends Model
{
    protected $fillable = [
        'impressed_at',
    ];

    protected $casts = [
        'impressed_at' => 'datetime',
    ];

    public function impressionable(): MorphTo
    {
        return $this->morphTo();
    }
}
