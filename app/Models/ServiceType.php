<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['service_type', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
