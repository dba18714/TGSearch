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
        'type',
        'member_or_view_count',
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
            'type' => $this->type,
            'member_or_view_count' => $this->member_or_view_count,
        ];
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

    public function getUrlAttribute(): ?string
    {
        return $this->unified_searchable->url;
    }

    public function getTitle($limit = 200): ?string
    {
        $searchable = $this->unified_searchable;
        $title = $searchable->name ?? str_replace(["\n", "#"], "", $searchable->text);
        $title = \Str::limit($title, $limit);
        return $title;
    }

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

    public function getTypeEmojiAttribute(): string
    {
        return match ($this->type) {
            'bot' => '🤖',
            'channel' => '📢',
            'group' => '👥',
            'person' => '👤',
            'message' => '💬',
            default => '未知',
        };
    }

}
