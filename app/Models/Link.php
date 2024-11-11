<?php

namespace App\Models;

use App\Jobs\UpdateLinkInfoJob;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Link extends Model
{
    use HasUlids, HasFactory;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'introduction',
        'url',
        'type',
        'telegram_username',
        'member_count',
        'view_count',
        'is_by_user',
        'user_id',
        'is_valid',
        'verified_at',
        'verified_start_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_by_user' => 'boolean',
        'is_valid' => 'boolean',
        'verified_at' => 'datetime',
        'verified_start_at' => 'datetime',
        'member_count' => 'integer',
        'view_count' => 'integer',
    ];

    /**
     * Get the user that added this telegram link.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include valid links.
     */
    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    /**
     * Scope a query to only include user submitted links.
     */
    public function scopeByUser($query)
    {
        return $query->where('is_by_user', true);
    }

    /**
     * Scope a query to only include crawler submitted links.
     */
    public function scopeByCrawler($query)
    {
        return $query->where('is_by_user', false);
    }

    /**
     * Check if the link is a bot.
     */
    public function isBot(): bool
    {
        return $this->type === 'bot';
    }

    /**
     * Check if the link is a channel.
     */
    public function isChannel(): bool
    {
        return $this->type === 'channel';
    }

    /**
     * Check if the link is a group.
     */
    public function isGroup(): bool
    {
        return $this->type === 'group';
    }

    /**
     * Check if the link is a person.
     */
    public function isPerson(): bool
    {
        return $this->type === 'person';
    }

    public function isMessage(): bool
    {
        return $this->type === 'message';
    }

    /**
     * Get the type name of the Telegram link.
     */
    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            'bot' => '机器人',
            'channel' => '频道',
            'group' => '群组',
            'person' => '个人',
            'message' => '消息',
            default => '未知',
        };
    }

    public function dispatchUpdateJob()
    {
        $this->verified_start_at = now();
        $this->save();

        UpdateLinkInfoJob::dispatch($this);

        return $this;
    }
}
