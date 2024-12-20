<?php

namespace App\Models\Traits;

use App\Jobs\ProcessAuditModelJob;
use App\Jobs\ProcessUpdateTelegramModelJob;
use App\Settings\GeneralSettings;
use Carbon\Carbon;

trait HasVerification
{
    public function dispatchUpdateJob()
    {
        $this->verified_start_at = now();
        $this->save();

        ProcessUpdateTelegramModelJob::dispatch($this);

        return $this;
    }

    public function dispatchAuditJob()
    {
        $this->audit_started_at = now();
        $this->save();

        ProcessAuditModelJob::dispatch($this);

        return $this;
    }

    public function scopeValid($query)
    {
        return $query->where('is_valid', true);
    }

    public static function dispatchNextVerificationJob(): bool
    {
        $general = app(GeneralSettings::class);

        // 如果定期更新被禁用，直接返回
        if (!$general->existing_links_update_enabled) {
            return false;
        }

        $model = static::selectForVerification()->first();

        if (!$model) return false;

        // 如果N分钟之内已经验证过了，就跳过
        if (
            $model->verified_start_at &&
            $model->verified_start_at->gt(now()->subMinutes($general->update_interval_minutes))
        ) return false;

        $model->dispatchUpdateJob();

        return true;
    }

    public static function dispatchNextAuditJob(): bool
    {
        $general = app(GeneralSettings::class);

        // 如果定期审查被禁用，直接返回
        if (!$general->existing_content_audit_enabled) {
            return false;
        }

        $model = static::selectForAudit()->first();

        if (!$model) return false;

        // 如果 N 小时之内已经审计过了，就跳过
        if (
            $model->audit_started_at &&
            $model->audit_started_at->gt(now()->subHour($general->audit_interval_hours))
        ) return false;

        $model->dispatchAuditJob();

        return true;
    }

    public function scopeSelectForVerification($query)
    {
        // pgsql
        return $query->orderByRaw('verified_start_at ASC NULLS FIRST')
            ->orderByRaw('verified_at ASC NULLS FIRST')
            ->orderBy('created_at');

        // // mysql
        // return $query
        //     ->orderByRaw('ISNULL(verified_start_at) DESC, verified_start_at ASC')
        //     ->orderByRaw('ISNULL(verified_at) DESC, verified_at ASC')
        //     ->orderBy('created_at');
    }

    public function scopeSelectForAudit($query)
    {
        // pgsql
        return $query->valid()->whereNull('audited_at')
            ->orderByRaw('audit_started_at ASC NULLS FIRST')
            ->orderBy('created_at');
    }

    public function initializeHasVerification()
    {
        $this->mergeCasts([
            'verified_start_at' => 'datetime',
            'verified_at' => 'datetime',
            'audit_started_at' => 'datetime',
            'audited_at' => 'datetime',
        ]);
    }

    protected static function bootHasVerification()
    {

        static::created(function ($model) {

            // 检查是否允许更新新链接
            if (app(GeneralSettings::class)->new_links_update_enabled) {
                $model->dispatchUpdateJob();
            }
        });

        static::saving(function ($model) {
            // 自动修剪字符串前后空格, 并且如果修剪后是空字符串,则设置为 null
            foreach ($model->getAttributes() as $key => $value) {
                if (is_string($value)) {
                    $value = trim($value);
                    if ($value === '') $value = null;
                    $model->{$key} = $value;
                }
            }
        });

        static::saved(function ($model) {
            $general = app(GeneralSettings::class);

            // 如果内容审查开关关闭，则不执行审计
            if (!$general->content_changed_audit_enabled) {
                return;
            }

            // 防止无限循环：如果正在更新 audit_started_at，则不触发审计
            if ($model->wasChanged('audit_started_at')) {
                return;
            }

            // 如果内容有更新则派遣审计任务
            $model_class_name = class_basename($model);
            if ($model_class_name == 'Chat') {
                if (
                    $model->wasChanged('name') ||
                    $model->wasChanged('introduction')
                ) {
                    if (!empty($model->name) && $model->is_valid) {
                        $model->dispatchAuditJob();
                    }
                }
            }
            if ($model_class_name == 'Message') {
                if ($model->wasChanged('text')) {
                    if (!empty($model->text) && $model->is_valid) {
                        $model->dispatchAuditJob();
                    }
                }
            }
        });
    }
}
