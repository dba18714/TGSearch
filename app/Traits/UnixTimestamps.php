<?php

namespace App\Traits;

use Illuminate\Support\Facades\Schema;

trait UnixTimestamps
{
    public static function bootUnixTimestamps()
    {
        static::creating(function ($model) {
            if (!$model->isDirty('created_at')) {
                $model->created_at = time();
            }
            if (!$model->isDirty('updated_at')) {
                $model->updated_at = time();
            }
        });

        static::updating(function ($model) {
            if (!$model->isDirty('updated_at')) {
                $model->updated_at = time();
            }
        });
    }

    public function initializeUnixTimestamps()
    {
        if (!isset($this->attributes['created_at']) && Schema::hasColumn($this->getTable(), 'created_at')) {
            $this->attributes['created_at'] = time();
        }

        if (!isset($this->attributes['updated_at']) && Schema::hasColumn($this->getTable(), 'updated_at')) {
            $this->attributes['updated_at'] = time();
        }
    }

    public function freshTimestamp()
    {
        return time();
    }

    public function fromDateTime($value)
    {
        return $value instanceof \DateTimeInterface ? $value->getTimestamp() : strtotime($value);
    }

    public function getDateFormat()
    {
        return 'U';
    }
}