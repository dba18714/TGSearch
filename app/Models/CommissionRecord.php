<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * CommissionRecord Model
 * 
 * 用于记录用户邀请奖励的佣金发放记录
 * - user_id: 获得佣金的用户(邀请人)
 * - invitee_id: 被邀请的用户
 * - amount: 佣金金额
 * - status: 发放状态
 */
class CommissionRecord extends Model
{
    protected $fillable = [
        'user_id',
        'invitee_id',
        'amount',
        'level',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invitee_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($record) {
            if ($record->amount <= 0) {
                throw new \Exception('佣金金额必须大于0');
            }

            if ($record->user_id === $record->invitee_id) {
                throw new \Exception('邀请人和被邀请人不能是同一个人');
            }
        });
    }
}
