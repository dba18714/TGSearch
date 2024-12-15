<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable;
    use HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'tg_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 获取所有层级的下级数量
     */
    public function getDescendantsCountByLevel(): array
    {
        $result = DB::select("
            WITH RECURSIVE descendants AS (
                -- 基础查询：直接下级
                SELECT id, parent_id, 1 as level
                FROM users
                WHERE parent_id = ?
                
                UNION ALL
                
                -- 递归查询：下一级
                SELECT u.id, u.parent_id, d.level + 1
                FROM users u
                INNER JOIN descendants d ON u.parent_id = d.id
                WHERE d.level < 3
            )
            SELECT level, COUNT(*) as count
            FROM descendants
            GROUP BY level
            ORDER BY level
        ", [$this->id]);

        return collect($result)
            ->mapWithKeys(fn($item) => [$item->level => $item->count])
            ->toArray();
    }

    // 上级
    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    // 直接下级
    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // 判断面板id是否为admin
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }
        return true;
    }

    public function isAdmin(): bool
    {
        return !!$this->is_admin;
    }

    public function getFilamentName(): string
    {
        return "{$this->email}";
    }
}
