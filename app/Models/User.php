<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public static function boot() {
        parent::boot();
        static::deleting(function($user) {
            $user->wallets()->delete();
        });
    }
}
