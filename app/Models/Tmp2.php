<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tmp2 extends Model
{
    use HasFactory;

    protected $dateFormat = 'U';

    protected $fillable = [
        'id',
    ];

    // protected $dates = [
    //     'created_at',  
    //     'updated_at',  
    // ];

}
