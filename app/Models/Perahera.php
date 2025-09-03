<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perahera extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description',
        'start_date', 'end_date', 'image',
        'location', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
