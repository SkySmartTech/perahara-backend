<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'user_id', 
        'name', 
        'description', 
        'location', 
        'phone', 
        'service_type_id', 
        'price', 
        'status', 
        'image'
    ];

    // Rename provider to user so your controller works
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}