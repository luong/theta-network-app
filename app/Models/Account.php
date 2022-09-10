<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getTagsAttribute($value)
    {
        if (is_null($value)) {
            $value = [];
        }
        return json_decode($value, 1);
    }
}
