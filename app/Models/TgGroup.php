<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TgGroup extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'user_id',
        'source_id',
        'direct_search_enabled',
        'command_search_enabled',
        'bot_left_at',
    ];

    protected $guarded = ['bot_joined_at'];

    protected $casts = [
        'direct_search_enabled' => 'boolean',
        'command_search_enabled' => 'boolean',
        'bot_joined_at' => 'datetime',
        'bot_left_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($group) {
            $group->bot_joined_at = now();
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tg_group_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function isDirectSearchEnabled(): bool
    {
        return $this->direct_search_enabled;
    }

    public function isCommandSearchEnabled(): bool
    {
        return $this->command_search_enabled;
    }

    public function isBotInGroup(): bool
    {
        return is_null($this->bot_left_at);
    }

    public function setBotLeft(): void
    {
        $this->update(['bot_left_at' => now()]);
    }

    public function setBotJoined(): void
    {
        $this->update(['bot_left_at' => null]);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('bot_left_at');
    }

    public function scopeInactive($query)
    {
        return $query->whereNotNull('bot_left_at');
    }
}