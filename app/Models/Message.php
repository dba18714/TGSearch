<?php

namespace App\Models;

use App\Models\Traits\HasUnifiedSearch;
use App\Models\Traits\HasVerification;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;

class Message extends Model
{
    use HasUlids, HasFactory;
    use HasVerification;
    // use Searchable; 
    use HasUnifiedSearch;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chat_id',
        'original_id',
        'text',
        'view_count',
        'source',
        'source_str',
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
        'is_valid' => 'boolean',
        'verified_at' => 'datetime',
        'verified_start_at' => 'datetime',
        'view_count' => 'integer',
    ];

    public function toUnifiedSearchArray(): array
    {
        return [
            'content' => $this->text,
            'type' => 'message',
            'member_or_view_count' => $this->view_count,
        ];
    }

    public function getUrlAttribute(): ?string
    {
        if ($this->chat) {
            return "https://t.me/{$this->chat->username}/{$this->original_id}";
            return "https://t.me/s/{$this->chat->username}/{$this->original_id}";
        }
        return "404";
    }

    // public function toSearchableArray(): array 
    // {
    //     return [
    //         'id' => $this->id,
    //         'text' => $this->text,
    //         'chat_id' => $this->chat_id
    //     ];
    // }

    /**
     * 获取添加此消息的用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function impressions(): MorphMany
    {
        return $this->morphMany(Impression::class, 'impressionable');
    }

    /**
     * 获取消息所有者
     */
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function getRouteAttribute()
    {
        if ($this->chat) {
            return route('chat.show', [$this->chat, $this]);
        }
        return null;
    }

    /**
     * 判断消息是否由爬虫添加
     */
    public function isCrawler(): bool
    {
        return $this->source === 'crawler';
    }

    /**
     * 判断消息是否由用户手动添加
     */
    public function isManual(): bool
    {
        return $this->source === 'manual';
    }
}
