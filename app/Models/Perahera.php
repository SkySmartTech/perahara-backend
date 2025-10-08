<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perahera extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'name',
        'short_description',
        'description',
        'location',
        'event_time',
        'start_date',
        'end_date',
        'status',
        'image',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date'   => 'date:Y-m-d',
        'event_time' => 'datetime:H:i',
    ];

    /**
     * Relationship: Perahera belongs to a user (admin or organizer)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Optional: Accessor to get full image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
