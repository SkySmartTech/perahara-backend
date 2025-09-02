<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $fillable = [
        'user_id', 'ip_address', 'login_at', 'logout_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
