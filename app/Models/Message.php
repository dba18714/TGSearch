<?php

namespace App\Models;

use App\Models\Traits\HasVerification;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasUlids, HasFactory;
    use HasVerification;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'owner_id',
        'original_id',
        'text',
        'view_count',
        'source',
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

    public function getUrlAttribute(): ?string
    {
        return "https://t.me/{$this->owner->username}/{$this->original_id}";
    }

    /**
     * 获取添加此消息的用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 获取消息所有者
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class);
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
