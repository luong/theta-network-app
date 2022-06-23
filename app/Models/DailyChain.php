<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyChain extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'nodes' => 'array',
        'drops' => 'array'
    ];
}
