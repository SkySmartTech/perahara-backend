<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'user_id','service_type_id','service_name','description','price'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}
