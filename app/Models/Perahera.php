<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perahera extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', //have to keep user_id here otherwise it is like trying to make the user_id null which is not nullable. even though we manually assign user_id it always assigns the logged_in user_id.
        'start_date', 'end_date', 'image',
        'location', 'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
