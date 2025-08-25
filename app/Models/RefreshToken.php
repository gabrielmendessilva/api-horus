<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class RefreshToken extends Model
{
    protected $fillable = ['user_id', 'token', 'ip_address', 'user_agent', 'expires_at', 'revoked'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid()
    {
        return !$this->revoked && $this->expires_at->gt(Carbon::now());
    }
}
