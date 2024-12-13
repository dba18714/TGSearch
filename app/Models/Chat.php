<?php

namespace App\Models;

use App\Jobs\ProcessUpdateChatInfoJob;
use App\Jobs\ProcessUpdateTelegramModelJob;
use App\Models\Traits\HasUnifiedSearch;
use App\Models\Traits\HasVerification;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Scout\Searchable;

class Chat extends Model
{
    use HasUlids, HasFactory;
    // use Searchable; 
    use HasVerification;
    use HasUnifiedSearch;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'introduction',
        'message',
        // 'url',
        'type',
        'username',
        'member_count',
        'photo_count',
        'video_count',
        'file_count',
        'link_count',
        // 'view_count',
        'source',
        'source_str',
        'user_id',
        'is_valid',
        'verified_at',
        'verified_start_at',
        'audit_passed',
        'audit_score',
        'audited_at',
        'audit_started_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_valid' => 'boolean',
        'audit_passed' => 'boolean',
        'verified_at' => 'datetime',
        'verified_start_at' => 'datetime',
        'member_count' => 'integer',
        // 'view_count' => 'integer',
    ];

    public function toUnifiedSearchArray(): array
    {
        return [
            'content' => implode("\n", array_filter([
                $this->name,
                $this->introduction,
                $this->username
            ])),
            'audit_passed' => !!$this->audit_passed,
            'type' => $this->type,
            'member_or_view_count' => $this->member_count,
        ];
    }

    // 存库时将 username 前面的@去掉
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = ltrim($value, '@');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function impressions(): MorphMany
    {
        return $this->morphMany(Impression::class, 'impressionable');
    }

    public function getRouteAttribute()
    {
        return route('chat.show', $this);
    }

    public function getUrlAttribute(): ?string
    {
        return "https://t.me/{$this->username}";
        return "https://t.me/s/{$this->username}";
    }

    /**
     * Get the user that added this telegram link.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include user submitted chats.
     */
    public function scopeByUser($query)
    {
        return $query->where('source', 'manual');
    }

    /**
     * Scope a query to only include crawler submitted chats.
     */
    public function scopeByCrawler($query)
    {
        return $query->where('source', 'crawler');
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

    public function isUnknown(): bool
    {
        return $this->type === null;
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
            default => '未知',
        };
    }

    // public function dispatchUpdateJob()
    // {
    //     $this->verified_start_at = now();
    //     $this->save();

    //     ProcessUpdateTelegramModelJob::dispatch($this);

    //     return $this;
    // }

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    // public function shouldBeSearchable(): bool 
    // {
    //     return $this->is_valid === true;
    // }

    /**
     * 获取模型的可索引数据数组。
     *
     * @return array<string, mixed>
     */
    // public function toSearchableArray(): array 
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name,
    //         'introduction' => $this->introduction,
    //         'message' => $this->message,
    //         'username' => $this->username,
    //     ];
    // }

    /**
     * Get a single link for verification, prioritizing unverified and in-progress chats
     *
     * @return void
     */
    // public static function dispatchNextVerificationJob(): bool
    // {
    //     $chat = self::selectForVerification()->first();

    //     if (!$chat->exists) return false;

    //     // 如果1小时之内已经验证过了，就跳过
    //     if (
    //         $chat->verified_start_at &&
    //         $chat->verified_start_at->gt(now()->subHour())
    //     ) return false;

    //     $chat->dispatchUpdateJob();

    //     return true;
    // }

    // /**
    //  * Query scope for selecting chats for verification
    //  *
    //  * @param \Illuminate\Database\Eloquent\Builder $query
    //  * @return \Illuminate\Database\Eloquent\Builder
    //  */
    // public function scopeSelectForVerification($query)
    // {
    //     return $query->orderByRaw('verified_start_at ASC NULLS FIRST')
    //         ->orderByRaw('verified_at ASC NULLS FIRST')
    //         ->orderBy('created_at');
    // }

    // protected static function booted(): void
    // {
    //     static::created(function (Chat $chat) {
    //         $chat->dispatchUpdateJob();
    //     });
    // }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($model) {

    //         // 自动修剪字符串前后空格, 并且如果修剪后是空字符串,则设置为 null
    //         foreach ($model->getAttributes() as $key => $value) {
    //             if (is_string($value)) {
    //                 $value = trim($value);
    //                 if ($value === '') $value = null;
    //                 $model->{$key} = $value;
    //             }
    //         }
    //     });
    // }
}
