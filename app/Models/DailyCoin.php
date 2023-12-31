<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyCoin extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'locked_supply' => 'array',
    ];
}
