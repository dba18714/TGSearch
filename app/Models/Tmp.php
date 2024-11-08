<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Tmp extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
    ];

    // protected function createdAt(): Attribute
    // {
    //     return Attribute::make(
    //         // get: fn ( $value) => \Carbon\Carbon::parse($value)->setTimezone('Asia/Shanghai'),
    //         // set: fn ( $value) => ($value)->setTimezone('UTC'),
    //     );
    // }

    // public function setCreatedAtAttribute($value)
    // {
    //     $this->attributes['created_at'] = \Carbon\Carbon::parse($value)->setTimezone('UTC');
    // }

    // public function getCreatedAtAttribute($value)
    // {
    //     $userTimezone = 'Asia/Shanghai';
    //     return 222; //$value->setTimezone($userTimezone);
    // }

}
